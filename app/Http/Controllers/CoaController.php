<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use App\Models\Djurnal; 
use Illuminate\Http\Request;
use App\Http\Controllers\JurnalController;

// Import Google API
use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;
use Google\Service\Sheets\ClearValuesRequest;
use GuzzleHttp\Client as GuzzleClient;

class CoaController extends Controller
{
    // --- KONFIGURASI GOOGLE SHEETS ---
    protected $spreadsheetId = '1NdOklcic_smC4tNVbieME9s_CWOUeINl1Yb64BSS6mg'; 
    protected $sheetName = 'chart of account'; 

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
        return redirect()->route('coa.index')->with('success', 'Data Akun & Saldo berhasil disinkronkan ke Google Sheets.');
    }

    public function syncAllDataInternal()
    {
        try {
            $semuaAkun = Coa::withSum('jurnals as total_debit', 'debit')
                            ->withSum('jurnals as total_kredit', 'kredit')
                            ->orderBy('kd_akun', 'asc')
                            ->get();

            $dataSheet = [];
            foreach ($semuaAkun as $c) {
                $jenis = strtoupper($c->jenis_akun);
                $isDebitAccount = in_array($jenis, ['ASET', 'AKTIVA', 'BEBAN', 'BEBAN COGS', 'BEBAN OPERASIONAL']);
                $saldo = $isDebitAccount 
                    ? ($c->total_debit - $c->total_kredit) 
                    : ($c->total_kredit - $c->total_debit);

                $dataSheet[] = [$c->kd_akun, $c->nama_akun, $c->jenis_akun, (int) $saldo];
            }

            $service = $this->getGoogleService();
            $rangeClear = "'" . $this->sheetName . "'!A2:D2000"; 
            $service->spreadsheets_values->clear($this->spreadsheetId, $rangeClear, new ClearValuesRequest());
            
            if (!empty($dataSheet)) {
                $body = new ValueRange(['values' => $dataSheet]);
                $service->spreadsheets_values->append($this->spreadsheetId, "'" . $this->sheetName . "'!A1", $body, ['valueInputOption' => 'RAW']);
            }
        } catch (\Exception $e) {
            \Log::error("Gagal Sync COA: " . $e->getMessage());
        }
    }

    // --- CRUD METHODS ---

    public function index(Request $request)
    {
        $query = Coa::query();
        $query->withSum('jurnals as total_debit', 'debit')
              ->withSum('jurnals as total_kredit', 'kredit');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_akun', 'like', '%' . $search . '%')
                  ->orWhere('kd_akun', 'like', '%' . $search . '%')
                  ->orWhere('jenis_akun', 'like', '%' . $search . '%');
            });
        }

        $coas = $query->orderBy('kd_akun', 'asc')->get(); 
        return view('coa.index', compact('coas'));
    }

    /**
     * FUNGSI BUKU BESAR (SHOW) DENGAN FILTER CANGGIH
     */
    public function show(Request $request, $kd_akun)
    {
        // 1. Ambil data Master Akun
        $account = Coa::where('kd_akun', $kd_akun)->firstOrFail();

        // 2. Query Detail Jurnal Join ke Induk Jurnal
        $query = Djurnal::select(
                'djurnal.*', 
                'jurnal.tgl_jurnal as tanggal', 
                'jurnal.kd_jurnal as no_bukti', 
                'jurnal.ket_jurnal as keterangan'
            )
            ->join('jurnal', 'djurnal.kd_jurnal', '=', 'jurnal.kd_jurnal')
            ->where('djurnal.kd_akun', $kd_akun);

        // --- LOGIKA FILTER ---
        // Filter Rentang Tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('jurnal.tgl_jurnal', [$request->start_date, $request->end_date]);
        }

        // Filter Pencarian (No Bukti atau Keterangan)
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($s) use ($search) {
                $s->where('jurnal.kd_jurnal', 'like', "%$search%")
                  ->orWhere('jurnal.ket_jurnal', 'like', "%$search%");
            });
        }

        $transactions = $query->orderBy('jurnal.tgl_jurnal', 'asc')
                              ->orderBy('jurnal.kd_jurnal', 'asc')
                              ->get();

        return view('coa.show', compact('account', 'transactions'));
    }

    public function create()
    {
        return view('coa.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kd_akun'    => 'required|unique:coa,kd_akun',
            'nama_akun'  => 'required',
            'jenis_akun' => 'required'
        ]);

        Coa::create($request->all());
        $this->syncAllDataInternal();
        return redirect()->route('coa.index')->with('success', 'Akun berhasil ditambahkan & disinkron.');
    }

    public function edit($kd_akun)
    {
        $coa = Coa::where('kd_akun', $kd_akun)->firstOrFail();
        return view('coa.edit', compact('coa'));
    }

    public function update(Request $request, $kd_akun)
    {
        $coa = Coa::where('kd_akun', $kd_akun)->firstOrFail();
        
        $request->validate([
            'kd_akun'    => 'required|unique:coa,kd_akun,' . $coa->kd_akun . ',kd_akun',
            'nama_akun'  => 'required',
            'jenis_akun' => 'required'
        ]);

        $coa->update($request->all());
        $this->syncAllDataInternal();
        
        if (class_exists(JurnalController::class)) {
            try {
                app(JurnalController::class)->syncAllDataInternal();
            } catch (\Exception $e) {}
        }

        return redirect()->route('coa.index')->with('success', 'Akun berhasil diperbarui.');
    }

    public function destroy($kd_akun)
    {
        try {
            $coa = Coa::where('kd_akun', $kd_akun)->firstOrFail();
            $coa->delete();
            $this->syncAllDataInternal();
            return redirect()->route('coa.index')->with('success', 'Akun berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('coa.index')->with('error', 'Gagal: Akun masih digunakan di transaksi.');
        }
    }
}