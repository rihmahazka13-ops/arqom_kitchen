<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\Konsumen;
use App\Models\Jurnal;
use App\Models\Djurnal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// Import Library Google
use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;
use Google\Service\Sheets\ClearValuesRequest;
// Import Guzzle untuk bypass SSL XAMPP
use GuzzleHttp\Client as GuzzleClient;

class PenjualanController extends Controller
{
    // --- KONFIGURASI GOOGLE SHEETS ---
    protected $spreadsheetId = '1NdOklcic_smC4tNVbieME9s_CWOUeINl1Yb64BSS6mg'; 
    protected $sheetName = 'penjualan'; 

    // --- HELPER: KONEKSI KE GOOGLE (DENGAN BYPASS SSL) ---
    private function getGoogleService()
    {
        $client = new Client();
        $client->setAuthConfig(storage_path('app/google-auth.json'));
        $client->addScope(Sheets::SPREADSHEETS);

        // Matikan verifikasi SSL untuk mencegah timeout di Localhost/XAMPP
        $guzzleClient = new GuzzleClient(['verify' => false]);
        $client->setHttpClient($guzzleClient);

        return new Sheets($client);
    }

    // --- HELPER: CARI BARIS DI SHEETS ---
    private function findRowIndex($service, $kodeTransaksi)
    {
        $range = $this->sheetName . '!A:A';
        $response = $service->spreadsheets_values->get($this->spreadsheetId, $range);
        $rows = $response->getValues();

        if (empty($rows)) return -1;

        foreach ($rows as $index => $row) {
            if (isset($row[0]) && $row[0] == $kodeTransaksi) {
                return $index + 1; // Sheets mulai baris 1, Array mulai 0
            }
        }
        return -1;
    }

    // --- FITUR BARU: SYNC ALL DATA (UPLOAD SEMUA DATA LAMA) ---
    public function sync()
    {
        // 1. Ambil Semua Data Penjualan dari Database MySQL
        $semuaPenjualan = Penjualan::orderBy('tgl_penj', 'asc')->get();

        if ($semuaPenjualan->isEmpty()) {
            return redirect()->route('penjualan.index')->with('warning', 'Database kosong, tidak ada yang bisa disinkronkan.');
        }

        // 2. Format Data untuk Google Sheets
        $dataSheet = [];
        foreach ($semuaPenjualan as $penjualan) {
            $dataSheet[] = [
                $penjualan->kd_penj,
                $penjualan->tgl_penj,
                $penjualan->nm_kons,
                $penjualan->invoice,
                (int) $penjualan->tot_awal,
                (int) $penjualan->bud_pengiriman,
                (int) $penjualan->tot_akhir,
                (int) $penjualan->telah_byr,
                (int) $penjualan->sisa_piutang
            ];
        }

        try {
            $service = $this->getGoogleService();
            
            // 3. BERSIHKAN SHEET DULU (Hapus data lama mulai baris 2 s.d 2000)
            // Header di baris 1 aman tidak terhapus
            $rangeClear = $this->sheetName . '!A2:I2000'; 
            $requestBody = new ClearValuesRequest();
            $service->spreadsheets_values->clear($this->spreadsheetId, $rangeClear, $requestBody);

            // 4. KIRIM DATA BARU (Bulk Insert)
            $body = new ValueRange(['values' => $dataSheet]);
            $params = ['valueInputOption' => 'RAW'];
            
            $service->spreadsheets_values->append(
                $this->spreadsheetId, 
                $this->sheetName . '!A1', 
                $body, 
                $params
            );

            return redirect()->route('penjualan.index')->with('success', 'Sukses! Semua data lama berhasil dikirim ke Google Sheets.');

        } catch (\Exception $e) {
            return redirect()->route('penjualan.index')->with('error', 'Gagal Sinkronisasi: ' . $e->getMessage());
        }
    }

    // --- MAIN CRUD METHODS ---

    public function index(Request $request)
    {
        $query = Penjualan::query();

        if ($request->has('search') && $request->search != '') {
            $query->where('nm_kons', 'like', '%' . $request->search . '%')
                  ->orWhere('invoice', 'like', '%' . $request->search . '%')
                  ->orWhere('kd_penj', 'like', '%' . $request->search . '%');
        }

        $data = $query->withSum('pemasukans', 'jml_pem')
                      ->orderBy('tgl_penj', 'desc') 
                      ->paginate(10);

        $konsumen = Konsumen::all();
        return view('penjualan.index', compact('data', 'konsumen'));
    }

    public function create()
    {
        // LOGIKA BARU: Generate kode PJ-0001 secara otomatis
        // Filter LENGTH(kd_penj) = 7 untuk mengabaikan kode lama yang panjang (PJ-2602...)
        $maxKode = Penjualan::whereRaw('LENGTH(kd_penj) = 7')
            ->where('kd_penj', 'like', 'PJ-%')
            ->selectRaw('MAX(CAST(SUBSTRING(kd_penj, 4) AS UNSIGNED)) as max_no')
            ->first();

        $nextNumber = $maxKode->max_no ? $maxKode->max_no + 1 : 1;
        $kd_otomatis = 'PJ-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        $konsumen = Konsumen::all(); 
        return view('penjualan.create', compact('konsumen', 'kd_otomatis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kd_penj'   => 'required|unique:penjualan,kd_penj',
            'tgl_penj'  => 'required|date',
            'nm_kons'   => 'required',
            'tot_akhir' => 'required|numeric',
        ]);

        DB::beginTransaction(); 
        try {
            // 1. Simpan ke Database MySQL
            $penjualan = new Penjualan();
            $penjualan->kd_penj         = $request->kd_penj;
            $penjualan->tgl_penj        = $request->tgl_penj;
            $penjualan->nm_kons         = $request->nm_kons;
            $penjualan->tot_awal        = $request->tot_awal ?? 0;
            $penjualan->tot_akhir       = $request->tot_akhir;
            $penjualan->bud_pengiriman = $request->bud_pengiriman ?? 0;
            $penjualan->invoice         = $request->invoice;
            $penjualan->telah_byr       = 0; 
            $penjualan->sisa_piutang    = $request->tot_akhir; 
            $penjualan->save();

            // 2. Buat Jurnal Otomatis
            $jurnal = Jurnal::create([
                'kd_jurnal'  => $request->kd_penj,
                'tgl_jurnal' => $request->tgl_penj,
                'ket_jurnal' => "Penjualan Konsumen: " . $request->nm_kons . " (" . $request->invoice . ")",
            ]);
            Djurnal::create(['kd_jurnal' => $jurnal->kd_jurnal, 'kd_akun' => '1103', 'debit' => $request->tot_akhir, 'kredit' => 0]);
            Djurnal::create(['kd_jurnal' => $jurnal->kd_jurnal, 'kd_akun' => '4101', 'debit' => 0, 'kredit' => $request->tot_akhir]);

            DB::commit();

            // 3. Simpan ke Google Sheets (Append)
            try {
                $service = $this->getGoogleService();
                $values = [[
                    $penjualan->kd_penj,
                    $penjualan->tgl_penj,
                    $penjualan->nm_kons,
                    $penjualan->invoice,
                    (int) $penjualan->tot_awal,
                    (int) $penjualan->bud_pengiriman,
                    (int) $penjualan->tot_akhir,
                    0, 
                    (int) $penjualan->tot_akhir 
                ]];
                $body = new ValueRange(['values' => $values]);
                $service->spreadsheets_values->append(
                    $this->spreadsheetId, 
                    $this->sheetName . '!A1', 
                    $body, 
                    ['valueInputOption' => 'RAW']
                );
            } catch (\Exception $e) {
                return redirect()->route('penjualan.index')->with('warning', 'Data tersimpan di DB, tapi gagal kirim ke Sheets: ' . $e->getMessage());
            }

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
        return redirect()->route('penjualan.index')->with('success', 'Data Berhasil Disimpan & Sinkron ke Google Sheets.');
    }

    public function edit($id)
    {
        $penjualan = Penjualan::findOrFail($id);
        $konsumen = Konsumen::all();
        return view('penjualan.edit', compact('penjualan', 'konsumen'));
    }

    public function update(Request $request, $id)
    {
        $penjualan = Penjualan::findOrFail($id);
        $kodeLama = $penjualan->kd_penj; 

        $request->validate([
            'kd_penj'   => 'required|unique:penjualan,kd_penj,' . $id . ',kd_penj',
            'nm_kons'   => 'required',
            'tot_akhir' => 'required|numeric',
        ]);

        DB::beginTransaction();
        try {
            // 1. Update Database MySQL
            $penjualan->kd_penj   = $request->kd_penj;
            $penjualan->tgl_penj  = $request->tgl_penj;
            $penjualan->nm_kons   = $request->nm_kons;
            $penjualan->tot_akhir = $request->tot_akhir;
            
            $totalBayarReal = $penjualan->pemasukans()->sum('jml_pem');
            $penjualan->telah_byr    = $totalBayarReal;
            $penjualan->sisa_piutang = $penjualan->tot_akhir - $totalBayarReal;
            $penjualan->save();

            // Update Jurnal
            $jurnal = Jurnal::where('kd_jurnal', $kodeLama)->first();
            if ($jurnal) {
                $jurnal->update(['kd_jurnal' => $request->kd_penj, 'tgl_jurnal' => $request->tgl_penj]);
                Djurnal::where('kd_jurnal', $request->kd_penj)->delete(); 
                Djurnal::create(['kd_jurnal' => $request->kd_penj, 'kd_akun' => '1103', 'debit' => $request->tot_akhir, 'kredit' => 0]);
                Djurnal::create(['kd_jurnal' => $request->kd_penj, 'kd_akun' => '4101', 'debit' => 0, 'kredit' => $request->tot_akhir]);
            }
            DB::commit();

            // 2. Update Google Sheets
            try {
                $service = $this->getGoogleService();
                $rowIndex = $this->findRowIndex($service, $kodeLama);

                if ($rowIndex != -1) {
                    $range = $this->sheetName . '!A' . $rowIndex; 
                    $values = [[
                        $penjualan->kd_penj,
                        $penjualan->tgl_penj,
                        $penjualan->nm_kons,
                        $penjualan->invoice,
                        (int) $penjualan->tot_awal,
                        (int) $penjualan->bud_pengiriman,
                        (int) $penjualan->tot_akhir,
                        (int) $penjualan->telah_byr,
                        (int) $penjualan->sisa_piutang
                    ]];
                    $body = new ValueRange(['values' => $values]);
                    $service->spreadsheets_values->update(
                        $this->spreadsheetId, 
                        $range, 
                        $body, 
                        ['valueInputOption' => 'RAW']
                    );
                }
            } catch (\Exception $e) {
                return redirect()->route('penjualan.index')->with('warning', 'Data update di DB, tapi gagal di Sheets: ' . $e->getMessage());
            }

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
        return redirect()->route('penjualan.index')->with('success', 'Update Berhasil & Sinkron ke Sheets.');
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $penjualan = Penjualan::findOrFail($id);
            $kodeTransaksi = $penjualan->kd_penj; 

            // 1. Hapus di Database
            Jurnal::where('kd_jurnal', $kodeTransaksi)->delete();
            Djurnal::where('kd_jurnal', $kodeTransaksi)->delete();
            $penjualan->delete();
            DB::commit();

            // 2. Hapus di Google Sheets
            try {
                $service = $this->getGoogleService();
                $rowIndex = $this->findRowIndex($service, $kodeTransaksi);

                if ($rowIndex != -1) {
                    $range = $this->sheetName . '!A' . $rowIndex . ':I' . $rowIndex;
                    $requestBody = new ClearValuesRequest();
                    $service->spreadsheets_values->clear($this->spreadsheetId, $range, $requestBody);
                }
            } catch (\Exception $e) {
                // Silent fail
            }

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('penjualan.index')->with('error', 'Gagal Hapus: ' . $e->getMessage());
        }
        return redirect()->route('penjualan.index')->with('success', 'Data Berhasil Terhapus.');
    }
}