<?php

namespace App\Http\Controllers;

use App\Models\Bahanbaku;
use App\Models\Konsumen;
use App\Models\Barangproduksi;
use App\Models\Pengeluaran; // Tambahan untuk akses hapus pengeluaran
use App\Models\Jurnal;      // Tambahan
use App\Models\Djurnal;     // Tambahan
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;   // Tambahan untuk Transaksi
use Illuminate\Support\Facades\File; // Tambahan untuk hapus gambar

// Import Google API
use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;
use Google\Service\Sheets\ClearValuesRequest;
use GuzzleHttp\Client as GuzzleClient;

class BahanbakuController extends Controller
{
    // --- KONFIGURASI GOOGLE SHEETS ---
    protected $spreadsheetId = '1NdOklcic_smC4tNVbieME9s_CWOUeINl1Yb64BSS6mg'; 
    protected $sheetName = 'bahan baku';

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
    private function findRowIndex($service, $kode)
    {
        $range = "'" . $this->sheetName . "'!A:A"; 
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

    // --- SYNC ALL DATA ---
    public function syncAllData()
    {
        $semuaData = Bahanbaku::all();

        if ($semuaData->isEmpty()) {
            return redirect()->route('bahanbaku.index')->with('warning', 'Data Bahan Baku kosong.');
        }

        $dataSheet = [];
        foreach ($semuaData as $b) {
            $dataSheet[] = [
                $b->kd_bhn,
                $b->nm_kons,
                $b->nm_bhn,
                (int) $b->jml_bhn,
                (int) $b->harga_bhn,
                (int) $b->tot_bhn
            ];
        }

        try {
            $service = $this->getGoogleService();
            $rangeClear = "'" . $this->sheetName . "'!A2:F2000"; 
            $service->spreadsheets_values->clear($this->spreadsheetId, $rangeClear, new ClearValuesRequest());
            
            $body = new ValueRange(['values' => $dataSheet]);
            $service->spreadsheets_values->append(
                $this->spreadsheetId, 
                "'" . $this->sheetName . "'!A1", 
                $body, 
                ['valueInputOption' => 'RAW']
            );

            return redirect()->route('bahanbaku.index')->with('success', 'Sukses! Semua stok Bahan Baku disinkronkan ulang.');

        } catch (\Exception $e) {
            return redirect()->route('bahanbaku.index')->with('error', 'Gagal Sync: ' . $e->getMessage());
        }
    }

    // --- CRUD METHODS ---

    public function index(Request $request)
    {
        $query = Bahanbaku::query();

        // ✅ TAMBAHAN LOGIKA: jika quantity (jml_bhn) = 0, tidak ditampilkan
        $query->where('jml_bhn', '>', 0);

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nm_bhn', 'like', '%' . $search . '%')      
                  ->orWhere('nm_kons', 'like', '%' . $search . '%');  
            });
        }

        $bahans = $query->orderBy('kd_bhn', 'desc')->paginate(10);
        return view('bahanbaku.index', compact('bahans'));
    }

    public function create()
    {
        // ✅ LOGIKA GENERATE KODE OTOMATIS (BB-0001)
        $maxKode = Bahanbaku::where('kd_bhn', 'like', 'BB-%')
            ->selectRaw('MAX(CAST(SUBSTRING(kd_bhn, 4) AS UNSIGNED)) as max_no')
            ->first();

        $nextNumber = $maxKode->max_no ? $maxKode->max_no + 1 : 1;
        $kd_otomatis = 'BB-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        $konsumen = Konsumen::all(); 
        return view('bahanbaku.create', compact('konsumen', 'kd_otomatis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kd_bhn'     => 'required|unique:bahanbaku,kd_bhn',
            'nm_kons'    => 'required',
            'nm_bhn'     => 'required',
            'jml_bhn'    => 'required|numeric',
            'satuan_bhn' => 'required',
            'harga_bhn'  => 'required|numeric',
        ]);

        // Tetap pertahankan data baru sebagai input manual
        $bahanbaku = new Bahanbaku();
        $bahanbaku->kd_bhn     = $request->kd_bhn;
        $bahanbaku->nm_kons    = $request->nm_kons;
        $bahanbaku->nm_bhn     = $request->nm_bhn;
        $bahanbaku->jml_bhn    = $request->jml_bhn;
        $bahanbaku->satuan_bhn = $request->satuan_bhn;
        $bahanbaku->harga_bhn  = $request->harga_bhn;
        $bahanbaku->tot_bhn    = $request->jml_bhn * $request->harga_bhn;
        $bahanbaku->save();

        // Sync ke Google Sheet
        try {
            $service = $this->getGoogleService();
            $values = [[
                $bahanbaku->kd_bhn, $bahanbaku->nm_kons, $bahanbaku->nm_bhn,
                (int) $bahanbaku->jml_bhn, (int) $bahanbaku->harga_bhn, (int) $bahanbaku->tot_bhn
            ]];
            $service->spreadsheets_values->append(
                $this->spreadsheetId, 
                "'" . $this->sheetName . "'!A1", 
                new ValueRange(['values' => $values]), 
                ['valueInputOption' => 'RAW']
            );
        } catch (\Exception $e) {
            return redirect()->route('bahanbaku.index')->with('warning', 'DB OK, Sheet Gagal: ' . $e->getMessage());
        }

        return redirect()->route('bahanbaku.index')->with('success', 'Bahan baku manual berhasil disimpan.');
    }

    public function edit(Bahanbaku $bahanbaku)
    {
        $konsumen = Konsumen::all(); 
        return view('bahanbaku.edit', compact('bahanbaku', 'konsumen'));
    }

    public function update(Request $request, Bahanbaku $bahanbaku)
    {
        // LOGIKA PROTEKSI: Cek apakah ini data otomatis dari Pengeluaran?
        // Data dari pengeluaran biasanya punya reff di Jurnal, tapi kita cek string BB- 
        // yang kodenya sesuai dengan kode pengeluaran asli.
        if (str_starts_with($bahanbaku->kd_bhn, 'BB-')) {
            // Kita cek apakah kode setelah 'BB-' terdaftar di tabel Pengeluaran
            $kodeAsli = str_replace('BB-', '', $bahanbaku->kd_bhn);
            $isFromPengeluaran = Pengeluaran::where('kd_peng', $kodeAsli)->exists();
            
            if ($isFromPengeluaran) {
                return back()->with('error', 'Data ini berasal dari Pengeluaran. Silakan edit melalui menu Pengeluaran!');
            }
        }

        $kodeLama = $bahanbaku->kd_bhn;

        $request->validate([
            'nm_kons'    => 'required',
            'nm_bhn'     => 'required',
            'jml_bhn'    => 'required|numeric',
            'satuan_bhn' => 'required',
            'harga_bhn'  => 'required|numeric',
        ]);

        $bahanbaku->nm_kons    = $request->nm_kons;
        $bahanbaku->nm_bhn     = $request->nm_bhn;
        $bahanbaku->jml_bhn    = $request->jml_bhn;
        $bahanbaku->satuan_bhn = $request->satuan_bhn;
        $bahanbaku->harga_bhn  = $request->harga_bhn;
        $bahanbaku->tot_bhn    = $request->jml_bhn * $request->harga_bhn;
        $bahanbaku->save();

        // Update Google Sheet
        try {
            $service = $this->getGoogleService();
            $rowIndex = $this->findRowIndex($service, $kodeLama);

            if ($rowIndex != -1) {
                $values = [[
                    $bahanbaku->kd_bhn, $bahanbaku->nm_kons, $bahanbaku->nm_bhn,
                    (int) $bahanbaku->jml_bhn, (int) $bahanbaku->harga_bhn, (int) $bahanbaku->tot_bhn
                ]];
                $service->spreadsheets_values->update(
                    $this->spreadsheetId, 
                    "'" . $this->sheetName . "'!A" . $rowIndex, 
                    new ValueRange(['values' => $values]), 
                    ['valueInputOption' => 'RAW']
                );
            }
        } catch (\Exception $e) {
            return redirect()->route('bahanbaku.index')->with('warning', 'DB Updated, Sheet Error: ' . $e->getMessage());
        }

        return redirect()->route('bahanbaku.index')->with('success', 'Bahan baku manual berhasil diperbarui.');
    }

    public function destroy(Bahanbaku $bahanbaku)
    {
        DB::beginTransaction();
        try {
            $kode = $bahanbaku->kd_bhn;

            // === LOGIKA HAPUS CASCADING (JIKA DATA DARI PENGELUARAN) ===
            if (str_starts_with($kode, 'BB-')) {
                $kodePengeluaran = str_replace('BB-', '', $kode);
                $pengeluaran = Pengeluaran::where('kd_peng', $kodePengeluaran)->first();

                if ($pengeluaran) {
                    if ($pengeluaran->buktinot_peng && File::exists(public_path('gambar/' . $pengeluaran->buktinot_peng))) {
                        File::delete(public_path('gambar/' . $pengeluaran->buktinot_peng));
                    }
                    if ($pengeluaran->buktitf_peng && File::exists(public_path('gambar/' . $pengeluaran->buktitf_peng))) {
                        File::delete(public_path('gambar/' . $pengeluaran->buktitf_peng));
                    }

                    $jurnal = Jurnal::where('reff_id', $kodePengeluaran)->where('reff_type', 'Pengeluaran')->first();
                    if ($jurnal) {
                        Djurnal::where('kd_jurnal', $jurnal->kd_jurnal)->delete();
                        $jurnal->delete();
                    }

                    $pengeluaran->delete();
                }
            }

            $bahanbaku->delete();
            DB::commit();

            try {
                $service = $this->getGoogleService();
                $rowIndex = $this->findRowIndex($service, $kode);
                if ($rowIndex != -1) {
                    $service->spreadsheets_values->clear(
                        $this->spreadsheetId, 
                        "'" . $this->sheetName . "'!A" . $rowIndex . ':F' . $rowIndex, 
                        new ClearValuesRequest()
                    );
                }
            } catch (\Exception $e) {}

            return redirect()->route('bahanbaku.index')->with('success', 'Bahan baku berhasil dihapus.');
            
        } catch (QueryException $e) {
            DB::rollback();
            return redirect()->route('bahanbaku.index')->with('error', 'Gagal hapus: ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('bahanbaku.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}