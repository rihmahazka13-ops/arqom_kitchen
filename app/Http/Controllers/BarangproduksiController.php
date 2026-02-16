<?php

namespace App\Http\Controllers;

use App\Models\Barangproduksi;
use App\Models\Bahanbaku;
use App\Models\Konsumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; 
use Illuminate\Support\Facades\DB;

// Import Google API
use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;
use Google\Service\Sheets\ClearValuesRequest;
use GuzzleHttp\Client as GuzzleClient;

class BarangproduksiController extends Controller
{
    // --- KONFIGURASI GOOGLE SHEETS ---
    protected $spreadsheetId = '1NdOklcic_smC4tNVbieME9s_CWOUeINl1Yb64BSS6mg'; 
    protected $sheetName = 'barang produksi'; 

    // --- HELPER: KONEKSI GOOGLE (BYPASS SSL) ---
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

    // --- SYNC ALL DATA (MANUAL) ---
    public function syncAllData()
    {
        $semuaData = Barangproduksi::all();

        if ($semuaData->isEmpty()) {
            return redirect()->route('barangproduksi.index')->with('warning', 'Data Barang Produksi kosong.');
        }

        $dataSheet = [];
        foreach ($semuaData as $b) {
            $dataSheet[] = [
                $b->kd_bhnpro,
                $b->nm_kons,
                $b->bhnpro,
                (int) $b->jml_pro,
                (int) $b->est_hpp,
                (int) $b->tot_est,
                (int) $b->rl_hpp,
                (int) $b->estrl_hpp,
                (int) $b->selisih
            ];
        }

        try {
            $service = $this->getGoogleService();
            $rangeClear = "'" . $this->sheetName . "'!A2:I2000"; 
            $service->spreadsheets_values->clear($this->spreadsheetId, $rangeClear, new ClearValuesRequest());
            
            $body = new ValueRange(['values' => $dataSheet]);
            $service->spreadsheets_values->append(
                $this->spreadsheetId, 
                "'" . $this->sheetName . "'!A1", 
                $body, 
                ['valueInputOption' => 'RAW']
            );

            return redirect()->route('barangproduksi.index')->with('success', 'Sukses Sinkronisasi Google Sheets.');
        } catch (\Exception $e) {
            return redirect()->route('barangproduksi.index')->with('error', 'Gagal Sync: ' . $e->getMessage());
        }
    }

    // --- CRUD METHODS ---

    public function index(Request $request)
    {
        $query = Barangproduksi::query();

        // 1. HITUNG NILAI ABSOLUT (SELURUH DATA) - Sebagai pengunci rasio
        $absoluteTotalEst = Barangproduksi::sum('tot_est');
        $absoluteTotalBahanBaku = Bahanbaku::sum('tot_bhn');

        // 2. LOGIKA FILTER SEARCH
        if ($request->has('search') && $request->search != '') {
            $keyword = $request->search;
            $query->where(function($q) use ($keyword) {
                $q->where('bhnpro', 'like', "%{$keyword}%")
                  ->orWhere('nm_kons', 'like', "%{$keyword}%");
            });
        }

        // 3. HITUNG TOTAL UNTUK FOOTER (HASIL FILTER)
        $sumTotEst = (clone $query)->sum('tot_est');
        
        // Catatan: grandTotalRealHpp di footer tetap mengikuti proporsi belanja bahan baku yang relevan
        $sumTotBahanBaku = Bahanbaku::when($request->search, function($q) use ($request) {
            return $q->where('nm_kons', 'like', "%{$request->search}%");
        })->sum('tot_bhn');

        $bahans = $query->orderBy('kd_bhnpro', 'desc')->paginate(10);
        
        // Jika sedang filter, grand total real mengikuti proporsi belanja yang tampil
        $grandTotalRealHpp = ($absoluteTotalEst > 0) ? ($sumTotEst * ($absoluteTotalBahanBaku / $absoluteTotalEst)) : 0;

        return view('barangproduksi.index', compact(
            'bahans', 
            'sumTotEst', 
            'sumTotBahanBaku', 
            'grandTotalRealHpp',
            'absoluteTotalEst',
            'absoluteTotalBahanBaku'
        ));
    }

    public function create()
    {
        $maxKode = Barangproduksi::where('kd_bhnpro', 'like', 'BP-%')
            ->selectRaw('MAX(CAST(SUBSTRING(kd_bhnpro, 4) AS UNSIGNED)) as max_no')
            ->first();

        $nextNumber = $maxKode->max_no ? $maxKode->max_no + 1 : 1;
        $kd_otomatis = 'BP-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        $konsumen = Konsumen::all();
        return view('barangproduksi.create', compact('konsumen', 'kd_otomatis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kd_bhnpro' => 'required|unique:barangproduksi,kd_bhnpro',
            'nm_kons'   => 'required',
            'bhnpro'    => 'required',
            'est_hpp'   => 'nullable|numeric',
            'jml_pro'   => 'required|numeric',
        ]);

        DB::beginTransaction();
        try {
            $qty = $request->jml_pro;
            $est_hpp_unit = $request->est_hpp ?? 0;
            $total_est_item = $qty * $est_hpp_unit;

            // Rasio Global tetap digunakan agar konsisten
            $absEst = Barangproduksi::sum('tot_est') + $total_est_item;
            $absBahan = Bahanbaku::sum('tot_bhn');
            $ratio = ($absEst > 0) ? ($absBahan / $absEst) : 0;

            $estrl_hpp_total = $total_est_item * $ratio; 
            $rl_hpp_unit = ($qty > 0) ? ($estrl_hpp_total / $qty) : 0;

            $barangproduksi = new Barangproduksi();
            $barangproduksi->kd_bhnpro = $request->kd_bhnpro;
            $barangproduksi->nm_kons   = $request->nm_kons;
            $barangproduksi->bhnpro    = $request->bhnpro;
            $barangproduksi->jml_pro   = $qty;
            $barangproduksi->est_hpp   = $est_hpp_unit;
            $barangproduksi->tot_est   = $total_est_item;
            $barangproduksi->estrl_hpp = $estrl_hpp_total; 
            $barangproduksi->rl_hpp    = $rl_hpp_unit;     
            $barangproduksi->selisih   = $total_est_item - $estrl_hpp_total;
            $barangproduksi->save();

            DB::commit();

            // Sync ke Sheet
            try {
                $service = $this->getGoogleService();
                $values = [[
                    $barangproduksi->kd_bhnpro, $barangproduksi->nm_kons, $barangproduksi->bhnpro,
                    (int)$qty, (int)$est_hpp_unit, (int)$total_est_item,
                    (int)$rl_hpp_unit, (int)$estrl_hpp_total, (int)$barangproduksi->selisih
                ]];
                $service->spreadsheets_values->append($this->spreadsheetId, "'" . $this->sheetName . "'!A1", 
                    new ValueRange(['values' => $values]), ['valueInputOption' => 'RAW']);
            } catch (\Exception $e) {}

            return redirect()->route('barangproduksi.index')->with('success', 'Data Berhasil Disimpan.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal Simpan: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $barangproduksi = Barangproduksi::where('kd_bhnpro', $id)->firstOrFail();
        $konsumen = Konsumen::all(); 
        return view('barangproduksi.edit', compact('barangproduksi', 'konsumen'));
    }

    public function update(Request $request, $id)
    {
        $barangproduksi = Barangproduksi::where('kd_bhnpro', $id)->firstOrFail();
        $kodeLama = $barangproduksi->kd_bhnpro;

        $request->validate([
            'nm_kons' => 'required',
            'bhnpro'  => 'required',
            'est_hpp' => 'nullable|numeric',
            'jml_pro' => 'required|numeric',
        ]);

        DB::beginTransaction();
        try {
            $qty = $request->jml_pro;
            $est_hpp_unit = $request->est_hpp ?? 0;
            $total_est_item = $qty * $est_hpp_unit;

            // Hitung Rasio Global Terbaru
            $absEst = Barangproduksi::where('kd_bhnpro', '!=', $id)->sum('tot_est') + $total_est_item;
            $absBahan = Bahanbaku::sum('tot_bhn');
            $ratio = ($absEst > 0) ? ($absBahan / $absEst) : 0;

            $estrl_hpp_total = $total_est_item * $ratio;
            $rl_hpp_unit = ($qty > 0) ? ($estrl_hpp_total / $qty) : 0;

            $barangproduksi->update([
                'nm_kons' => $request->nm_kons,
                'bhnpro'  => $request->bhnpro,
                'jml_pro' => $qty,
                'est_hpp' => $est_hpp_unit,
                'tot_est' => $total_est_item,
                'estrl_hpp' => $estrl_hpp_total,
                'rl_hpp'  => $rl_hpp_unit,
                'selisih' => $total_est_item - $estrl_hpp_total
            ]);

            DB::commit();

            // Sync Update Sheet
            try {
                $service = $this->getGoogleService();
                $rowIndex = $this->findRowIndex($service, $kodeLama);
                if ($rowIndex != -1) {
                    $values = [[
                        $kodeLama, $request->nm_kons, $request->bhnpro, (int)$qty, (int)$est_hpp_unit, 
                        (int)$total_est_item, (int)$rl_hpp_unit, (int)$estrl_hpp_total, (int)($total_est_item - $estrl_hpp_total)
                    ]];
                    $service->spreadsheets_values->update($this->spreadsheetId, "'" . $this->sheetName . "'!A" . $rowIndex, 
                        new ValueRange(['values' => $values]), ['valueInputOption' => 'RAW']);
                }
            } catch (\Exception $e) {}

            return redirect()->route('barangproduksi.index')->with('success', 'Data Berhasil Diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal Update: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $barangproduksi = Barangproduksi::where('kd_bhnpro', $id)->firstOrFail();
            $kode = $barangproduksi->kd_bhnpro;

            try {
                $service = $this->getGoogleService();
                $rowIndex = $this->findRowIndex($service, $kode);
                if ($rowIndex != -1) {
                    $service->spreadsheets_values->clear($this->spreadsheetId, 
                        "'" . $this->sheetName . "'!A" . $rowIndex . ':I' . $rowIndex, new ClearValuesRequest());
                }
            } catch (\Exception $e) {}

            $barangproduksi->delete();
            DB::commit();
            return redirect()->route('barangproduksi.index')->with('success', 'Data berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('barangproduksi.index')->with('error', 'Gagal hapus data.');
        }
    }
}