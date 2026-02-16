<?php

namespace App\Http\Controllers;

use App\Models\Pemasukan;
use App\Models\Penjualan; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

// Import Google API
use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;
use Google\Service\Sheets\ClearValuesRequest;
use GuzzleHttp\Client as GuzzleClient;

class PemasukanController extends Controller
{
    // --- KONFIGURASI GOOGLE SHEETS ---
    protected $spreadsheetId = '1NdOklcic_smC4tNVbieME9s_CWOUeINl1Yb64BSS6mg'; 
    protected $sheetPemasukan = 'pemasukan'; 
    protected $sheetPenjualan = 'penjualan'; 

    // --- HELPER: KONEKSI GOOGLE ---
    private function getGoogleService()
    {
        $client = new Client();
        $client->setAuthConfig(storage_path('app/google-auth.json'));
        $client->addScope(Sheets::SPREADSHEETS);
        $guzzleClient = new GuzzleClient(['verify' => false]);
        $client->setHttpClient($guzzleClient);
        return new Sheets($client);
    }

    // --- HELPER: CARI BARIS (ROW) ---
    private function findRowIndex($service, $sheetName, $kode)
    {
        $range = "'" . $sheetName . "'!A:A"; 
        $response = $service->spreadsheets_values->get($this->spreadsheetId, $range);
        $rows = $response->getValues();

        if (empty($rows)) return -1;

        foreach ($rows as $index => $row) {
            if (isset($row[0]) && $row[0] == $kode) {
                return $index + 1; 
            }
        }
        return -1;
    }

    // --- HELPER SAKTI: UPDATE SALDO DI SHEET PENJUALAN ---
    private function updateSaldoDiSheetPenjualan($kodePenjualan)
    {
        $penjualan = Penjualan::where('kd_penj', $kodePenjualan)->first();
        if (!$penjualan) return;

        $totalBayar = Pemasukan::where('kd_penj', $kodePenjualan)->sum('jml_pem');
        $sisaPiutang = $penjualan->tot_akhir - $totalBayar;

        $penjualan->telah_byr = $totalBayar;
        $penjualan->sisa_piutang = $sisaPiutang;
        $penjualan->save();

        try {
            $service = $this->getGoogleService();
            $rowIndex = $this->findRowIndex($service, $this->sheetPenjualan, $kodePenjualan);

            if ($rowIndex != -1) {
                $range = "'" . $this->sheetPenjualan . "'!H" . $rowIndex . ":I" . $rowIndex;
                $values = [[(int) $totalBayar, (int) $sisaPiutang]];
                $body = new ValueRange(['values' => $values]);
                $service->spreadsheets_values->update($this->spreadsheetId, $range, $body, ['valueInputOption' => 'RAW']);
            }
        } catch (\Exception $e) {
            // Silent fail
        }
    }

    // --- SYNC ALL DATA ---
    public function syncAllData()
    {
        $semuaData = Pemasukan::orderBy('tgl_pem', 'asc')->get();
        if ($semuaData->isEmpty()) return redirect()->route('pemasukan.index')->with('warning', 'Data Pemasukan kosong.');

        $dataSheet = [];
        foreach ($semuaData as $p) {
            $dataSheet[] = [$p->kd_pem, $p->kd_penj, $p->tgl_pem, $p->nm_kons, (int) $p->jml_pem, $p->ket_pem];
        }

        try {
            $service = $this->getGoogleService();
            $rangeClear = "'" . $this->sheetPemasukan . "'!A2:F2000"; 
            $service->spreadsheets_values->clear($this->spreadsheetId, $rangeClear, new ClearValuesRequest());
            
            $body = new ValueRange(['values' => $dataSheet]);
            $service->spreadsheets_values->append($this->spreadsheetId, "'" . $this->sheetPemasukan . "'!A1", $body, ['valueInputOption' => 'RAW']);

            return redirect()->route('pemasukan.index')->with('success', 'Sukses! Semua data Pemasukan tersinkron.');
        } catch (\Exception $e) {
            return redirect()->route('pemasukan.index')->with('error', 'Gagal Sync: ' . $e->getMessage());
        }
    }

    // --- CRUD METHODS ---

    public function index(Request $request)
    {
        $query = Pemasukan::query();

        // 1. Filter Rentang Tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('tgl_pem', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tgl_pem', '<=', $request->end_date);
        }

        // 2. Filter Pencarian Kode & Teks (Global Search)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kd_pem', 'like', '%' . $search . '%')
                  ->orWhere('kd_penj', 'like', '%' . $search . '%')
                  ->orWhere('nm_kons', 'like', '%' . $search . '%')
                  ->orWhere('ket_pem', 'like', '%' . $search . '%');
            });
        }

        // 3. Limit Baris & Pagination
        $perPage = $request->get('per_page', 10);
        $data = $query->orderBy('tgl_pem', 'desc')
                      ->orderBy('kd_pem', 'desc')
                      ->paginate($perPage);

        // Menjaga parameter filter agar tetap ada saat klik pagination
        $data->appends($request->all());

        return view('pemasukan.index', compact('data'));
    }

    public function create()
    {
        // KITA FILTER: Cuma cari kode yang panjangnya 7 karakter (Format IN-0001)
        // Data lama yang panjang (IN-2602xxx) bakal diabaikan sama query ini.
        $maxKode = Pemasukan::whereRaw('LENGTH(kd_pem) = 7')
            ->where('kd_pem', 'like', 'IN-%')
            ->selectRaw('MAX(CAST(SUBSTRING(kd_pem, 4) AS UNSIGNED)) as max_no')
            ->first();

        // Kalau nggak ada data yang 7 karakter, dia mulai dari 1
        $nextNumber = $maxKode->max_no ? $maxKode->max_no + 1 : 1;
        
        // Bungkus jadi format IN-0001
        $kd_otomatis = 'IN-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        $penjualan = Penjualan::select('kd_penj', 'nm_kons', 'invoice')
            ->orderBy('tgl_penj', 'desc')
            ->get();

        return view('pemasukan.create', compact('kd_otomatis', 'penjualan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kd_pem'      => 'required|unique:pemasukan,kd_pem',
            'tgl_pem'     => 'required|date',
            'jml_pem'     => 'required|numeric',
            'kd_penj'     => 'required', 
            'buktitf_pem' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        DB::beginTransaction();
        try {
            $dataPenj = Penjualan::where('kd_penj', $request->kd_penj)->first();

            $pemasukan = new Pemasukan();
            $pemasukan->kd_pem  = $request->kd_pem;
            $pemasukan->kd_penj = $request->kd_penj;
            $pemasukan->tgl_pem = $request->tgl_pem;
            $pemasukan->nm_kons = $dataPenj->nm_kons ?? '-'; 
            $pemasukan->ket_pem = $request->ket_pem;
            $pemasukan->jml_pem = $request->jml_pem;

            if ($request->hasFile('buktitf_pem')) {
                $file = $request->file('buktitf_pem');
                $namaFile = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                $file->move(public_path('gambar'), $namaFile); 
                $pemasukan->buktitf_pem = $namaFile;
            }
            $pemasukan->save();
            
            $this->updateSaldoDiSheetPenjualan($request->kd_penj);
            DB::commit();

            try {
                $service = $this->getGoogleService();
                $values = [[
                    $pemasukan->kd_pem, $pemasukan->kd_penj, $pemasukan->tgl_pem,
                    $pemasukan->nm_kons, (int) $pemasukan->jml_pem, $pemasukan->ket_pem
                ]];
                $service->spreadsheets_values->append($this->spreadsheetId, "'" . $this->sheetPemasukan . "'!A1", new ValueRange(['values' => $values]), ['valueInputOption' => 'RAW']);
            } catch (\Exception $e) {
                return redirect()->route('pemasukan.index')->with('warning', 'DB Aman, tapi Sheets Gagal: ' . $e->getMessage());
            }

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal simpan: ' . $e->getMessage());
        }

        return redirect()->route('pemasukan.index')->with('success', 'Pemasukan dicatat. Saldo Penjualan terupdate.');
    }

    public function edit($id)
    {
        $k = Pemasukan::where('kd_pem', $id)->firstOrFail();
        $penjualan = Penjualan::select('kd_penj', 'nm_kons', 'invoice')->orderBy('tgl_penj', 'desc')->get();
        return view('pemasukan.edit', compact('k', 'penjualan'));
    }

    public function update(Request $request, $id)
    {
        $pemasukan = Pemasukan::where('kd_pem', $id)->firstOrFail();
        $kodeLama = $pemasukan->kd_pem;
        
        $request->validate([
            'tgl_pem' => 'required|date',
            'jml_pem' => 'required|numeric',
            'kd_penj' => 'required'
        ]);

        DB::beginTransaction();
        try {
            $dataPenj = Penjualan::where('kd_penj', $request->kd_penj)->first();

            $pemasukan->tgl_pem = $request->tgl_pem;
            $pemasukan->jml_pem = $request->jml_pem;
            $pemasukan->kd_penj = $request->kd_penj;
            $pemasukan->nm_kons = $dataPenj->nm_kons ?? '-';
            $pemasukan->ket_pem = $request->ket_pem;

            if ($request->hasFile('buktitf_pem')) {
                if ($pemasukan->buktitf_pem && File::exists(public_path('gambar/' . $pemasukan->buktitf_pem))) {
                    File::delete(public_path('gambar/' . $pemasukan->buktitf_pem));
                }
                $file = $request->file('buktitf_pem');
                $namaFile = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                $file->move(public_path('gambar'), $namaFile);
                $pemasukan->buktitf_pem = $namaFile;
            }
            $pemasukan->save();
            
            $this->updateSaldoDiSheetPenjualan($request->kd_penj);
            DB::commit();

            try {
                $service = $this->getGoogleService();
                $rowIndex = $this->findRowIndex($service, $this->sheetPemasukan, $kodeLama);
                $values = [[$pemasukan->kd_pem, $pemasukan->kd_penj, $pemasukan->tgl_pem, $pemasukan->nm_kons, (int) $pemasukan->jml_pem, $pemasukan->ket_pem]];
                
                if ($rowIndex != -1) {
                    $service->spreadsheets_values->update($this->spreadsheetId, "'" . $this->sheetPemasukan . "'!A" . $rowIndex, new ValueRange(['values' => $values]), ['valueInputOption' => 'RAW']);
                }
            } catch (\Exception $e) {}

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }

        return redirect()->route('pemasukan.index')->with('success', 'Data diperbarui.');
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $pemasukan = Pemasukan::findOrFail($id);
            $kode = $pemasukan->kd_pem;
            $kodePenjualan = $pemasukan->kd_penj;
            
            if ($pemasukan->buktitf_pem && File::exists(public_path('gambar/' . $pemasukan->buktitf_pem))) {
                File::delete(public_path('gambar/' . $pemasukan->buktitf_pem));
            }

            $pemasukan->delete();
            $this->updateSaldoDiSheetPenjualan($kodePenjualan);
            DB::commit();

            try {
                $service = $this->getGoogleService();
                $rowIndex = $this->findRowIndex($service, $this->sheetPemasukan, $kode);
                if ($rowIndex != -1) {
                    $service->spreadsheets_values->clear($this->spreadsheetId, "'" . $this->sheetPemasukan . "'!A" . $rowIndex . ':F' . $rowIndex, new ClearValuesRequest());
                }
            } catch (\Exception $e) {}

            return redirect()->route('pemasukan.index')->with('success', 'Data dihapus.');

        } catch (QueryException $e) {
            DB::rollback();
            return redirect()->route('pemasukan.index')->with('error', 'Gagal hapus data.');
        }
    }
}