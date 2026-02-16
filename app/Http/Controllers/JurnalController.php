<?php

namespace App\Http\Controllers;

use App\Models\Jurnal;
use App\Models\Djurnal;
use App\Models\Coa; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client as GuzzleClient;
use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;
use Google\Service\Sheets\ClearValuesRequest;

class JurnalController extends Controller
{
    protected $spreadsheetId = '1NdOklcic_smC4tNVbieME9s_CWOUeINl1Yb64BSS6mg'; 
    protected $sheetName = 'jurnal umum'; 

    private function getGoogleService()
    {
        $client = new Client();
        $client->setAuthConfig(storage_path('app/google-auth.json'));
        $client->addScope(Sheets::SPREADSHEETS);
        $guzzleClient = new GuzzleClient(['verify' => false]);
        $client->setHttpClient($guzzleClient);
        return new Sheets($client);
    }

    public function syncAllData()
    {
        $this->syncAllDataInternal();
        return redirect()->route('jurnal.index')->with('success', 'Data telah disinkronkan!');
    }

    public function syncAllDataInternal()
    {
        try {
            $jurnals = Jurnal::with('details.coa')->orderBy('tgl_jurnal', 'asc')->get();
            $dataSheet = [];
            foreach ($jurnals as $j) {
                foreach ($j->details as $d) {
                    $dataSheet[] = [
                        $j->tgl_jurnal, $j->kd_jurnal, $j->ket_jurnal,
                        $d->kd_akun, $d->coa->nama_akun ?? '-',
                        (int) $d->debit, (int) $d->kredit
                    ];
                }
            }
            $service = $this->getGoogleService();
            $rangeClear = "'" . $this->sheetName . "'!A2:G5000"; 
            $service->spreadsheets_values->clear($this->spreadsheetId, $rangeClear, new ClearValuesRequest());
            if (!empty($dataSheet)) {
                $body = new ValueRange(['values' => $dataSheet]);
                $service->spreadsheets_values->append($this->spreadsheetId, "'" . $this->sheetName . "'!A1", $body, ['valueInputOption' => 'RAW']);
            }
            app(CoaController::class)->syncAllDataInternal();
        } catch (\Exception $e) {
            \Log::error("Gagal Sync: " . $e->getMessage());
        }
    }

    public function index()
    {
        $jurnals = Jurnal::with('details')->orderBy('tgl_jurnal', 'desc')->get();
        return view('jurnal.index', compact('jurnals'));
    }

    public function create()
    {
        $coas = Coa::all(); 

        // ✅ LOGIKA GENERATE KODE OTOMATIS KHUSUS JURNAL MANUAL (JRN-0001)
        // Hanya mencari yang berawalan JRN- agar tidak bentrok dengan kode transaksi lain
        $maxKode = Jurnal::where('kd_jurnal', 'like', 'JRN-%')
            ->selectRaw('MAX(CAST(SUBSTRING(kd_jurnal, 5) AS UNSIGNED)) as max_no')
            ->first();

        $nextNumber = $maxKode->max_no ? $maxKode->max_no + 1 : 1;
        $kd_otomatis = 'JRN-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        return view('jurnal.create', compact('coas', 'kd_otomatis'));
    }

    public function store(Request $request) 
    {
        // VALIDASI: Sekarang debit/kredit boleh kosong (nullable)
        $request->validate([
            'kd_jurnal'  => 'required|unique:jurnal,kd_jurnal',
            'tgl_jurnal' => 'required|date',
            'ket_jurnal' => 'required|string',
            'details'    => 'required|array|min:2',
            'details.*.kd_akun' => 'required|exists:coa,kd_akun',
            'details.*.debit'   => 'nullable|numeric|min:0',
            'details.*.kredit'  => 'nullable|numeric|min:0',
        ]);

        // HITUNG BALANCE
        $totalDebit = collect($request->details)->sum(fn($item) => $item['debit'] ?? 0);
        $totalKredit = collect($request->details)->sum(fn($item) => $item['kredit'] ?? 0);

        if ($totalDebit != $totalKredit) {
            return back()->with('error', "Jurnal tidak balance! (D: $totalDebit, K: $totalKredit)")->withInput();
        }

        DB::beginTransaction();
        try {
            $jurnal = Jurnal::create([
                'kd_jurnal'  => $request->kd_jurnal,
                'tgl_jurnal' => $request->tgl_jurnal,
                'ket_jurnal' => $request->ket_jurnal,
                'reff_type'  => null, 
                'reff_id'    => null, 
            ]);

            foreach ($request->details as $detail) {
                Djurnal::create([
                    'kd_jurnal' => $jurnal->kd_jurnal,
                    'kd_akun'   => $detail['kd_akun'],
                    'debit'     => $detail['debit'] ?? 0,
                    'kredit'    => $detail['kredit'] ?? 0,
                ]);
            }

            DB::commit();
            $this->syncAllDataInternal(); 
            return redirect()->route('jurnal.index')->with('success', 'Jurnal berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $jurnal = Jurnal::with('details.coa')->findOrFail($id);
        $source = null; $sourceType = null;
        if (preg_match('/\(([^)]+)\)/', $jurnal->ket_jurnal, $matches)) {
            $extractedCode = $matches[1]; 
            $source = $extractedCode;
            if (str_contains(strtoupper($extractedCode), 'PG')) $sourceType = 'pengeluaran';
            elseif (str_contains(strtoupper($extractedCode), 'INV')) $sourceType = 'pemasukan';
        }
        return view('jurnal.show', compact('jurnal', 'source', 'sourceType'));
    }

    public function edit($id)
    {
        $jurnal = Jurnal::with('details')->findOrFail($id);
        $coas = Coa::all();
        return view('jurnal.edit', compact('jurnal', 'coas'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kd_jurnal'  => 'required|unique:jurnal,kd_jurnal,' . $id . ',kd_jurnal',
            'tgl_jurnal' => 'required|date',
            'ket_jurnal' => 'required|string',
            'details'    => 'required|array|min:2',
            'details.*.kd_akun' => 'required|exists:coa,kd_akun',
            'details.*.debit'   => 'nullable|numeric|min:0',
            'details.*.kredit'  => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $jurnal = Jurnal::findOrFail($id);
            $jurnal->update([
                'kd_jurnal'  => $request->kd_jurnal,
                'tgl_jurnal' => $request->tgl_jurnal,
                'ket_jurnal' => $request->ket_jurnal,
            ]);

            Djurnal::where('kd_jurnal', $id)->delete();
            foreach ($request->details as $detail) {
                Djurnal::create([
                    'kd_jurnal' => $jurnal->kd_jurnal,
                    'kd_akun'   => $detail['kd_akun'],
                    'debit'     => $detail['debit'] ?? 0,
                    'kredit'    => $detail['kredit'] ?? 0,
                ]);
            }
            DB::commit();
            $this->syncAllDataInternal(); 
            return redirect()->route('jurnal.index')->with('success', 'Jurnal diperbarui!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            Jurnal::findOrFail($id)->delete(); 
            $this->syncAllDataInternal();
            return redirect()->route('jurnal.index')->with('success', 'Jurnal dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('jurnal.index')->with('error', 'Gagal: ' . $e->getMessage());
        }
    }
}