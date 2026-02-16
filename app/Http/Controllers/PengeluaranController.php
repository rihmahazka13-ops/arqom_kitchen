<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaran;
use App\Models\Vendor;
use App\Models\Konsumen;
use App\Models\Bahanbaku;
use App\Models\Barangproduksi;
use App\Models\Barangjadi; 
use App\Models\Coa;
use App\Models\Jurnal;
use App\Models\Djurnal; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengeluaranController extends Controller
{
    // --- HELPER METHODS ---

    private function generateKeteranganJurnal($namaBarang, $namaProyek, $kodePengeluaran)
    {
        if (empty($namaProyek) || $namaProyek == '-') {
            return "Biaya " . $namaBarang . " - Operasional (" . $kodePengeluaran . ")";
        } 
        return "Beli " . $namaBarang . " - Proyek: " . $namaProyek . " (" . $kodePengeluaran . ")";
    }

    private function generateKodeOtomatis()
    {
        $lastData = Pengeluaran::where('kd_peng', 'like', 'OUT-%')->orderBy('kd_peng', 'desc')->first();
        if ($lastData) {
            $lastNo = substr($lastData->kd_peng, 4); 
            $nextNo = str_pad((int)$lastNo + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNo = '0001';
        }
        return 'OUT-' . $nextNo;
    }

    // --- MAIN METHODS ---

    public function index(Request $request)
    {
        $query = Pengeluaran::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('kd_peng', 'like', '%' . $search . '%')
                  ->orWhere('nm_bhn', 'like', '%' . $search . '%');
        }
        $data = $query->orderBy('tgl_peng', 'desc')->paginate($request->get('per_page', 10));
        
        return view('pengeluaran.index', compact('data'));
    }

    public function create()
    {
        $kd_otomatis = $this->generateKodeOtomatis();
        $vendor = Vendor::all(); 
        $konsumen = Konsumen::all();
        $coas = Coa::orderBy('kd_akun', 'asc')->get(); 
        $barang_jual = DB::table('barangjual')->get();
        return view('pengeluaran.create', compact('vendor', 'konsumen', 'coas', 'kd_otomatis', 'barang_jual'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tgl_peng'  => 'required|date',
            'nm_vendor' => 'required',
            'items'     => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            $kodeFix = $this->generateKodeOtomatis();
            $firstItem = $request->items[0];
            
            $pengeluaran = new Pengeluaran();
            $pengeluaran->kd_peng     = $kodeFix;
            $pengeluaran->tgl_peng    = $request->tgl_peng;
            $pengeluaran->nm_vendor   = $request->nm_vendor;
            $pengeluaran->nm_kons     = $firstItem['nm_kons'] ?? '-';
            $pengeluaran->kategori    = $firstItem['kategori'];
            $pengeluaran->spek_peng   = $firstItem['spek_peng'] ?? '-';

            if($firstItem['kategori'] == 'barang_jadi') {
                $katalog = DB::table('barangjual')->where('kd_brgjual', $firstItem['kd_barang_jual'])->first();
                $namaBarang = $katalog ? $katalog->nm_brgjual : ($firstItem['nm_bhn'] ?? 'Barang Jadi');
                $pengeluaran->nm_bhn = $namaBarang;

                // Konsep Baru: Selalu buat baris baru di Barangjadi agar HPP per proyek terpisah
                Barangjadi::create([
                    'kd_brgjadi'   => $firstItem['kd_barang_jual'],
                    'nm_brgjadi'   => $namaBarang,
                    'nm_kons'      => $pengeluaran->nm_kons, // Menambahkan info proyek
                    'jmlh_brgjadi' => $firstItem['qty'],
                    'hpp_total'    => $firstItem['subtotal'],
                    'hpp_unit'     => $firstItem['harga']
                ]);
            } else {
                $pengeluaran->nm_bhn = $firstItem['nm_bhn'];
            }

            $pengeluaran->jml_peng    = $firstItem['qty'];
            $pengeluaran->satuan_peng = $firstItem['satuan'] ?? 'Pcs';
            $pengeluaran->hrg_peng    = $firstItem['harga'];
            $pengeluaran->tot_peng    = $firstItem['subtotal'];
            $pengeluaran->save();

            // Jurnal
            $jurnalHeader = Jurnal::create([
                'kd_jurnal'  => 'JRN-' . $kodeFix, 
                'tgl_jurnal' => $request->tgl_peng,
                'ket_jurnal' => $this->generateKeteranganJurnal($pengeluaran->nm_bhn, $pengeluaran->nm_kons, $kodeFix),
                'reff_type'  => 'Pengeluaran',
                'reff_id'    => $kodeFix
            ]);

            if ($request->has('jurnal')) {
                foreach ($request->jurnal as $row) {
                    if (($row['debit'] ?? 0) > 0 || ($row['kredit'] ?? 0) > 0) {
                        Djurnal::create([
                            'kd_jurnal' => $jurnalHeader->kd_jurnal, 
                            'kd_akun'   => $row['kd_akun'],
                            'debit'     => $row['debit'] ?? 0,
                            'kredit'    => $row['kredit'] ?? 0,
                        ]);
                    }
                }
            }

            if($pengeluaran->kategori == 'bahan_baku') {
                $this->handleBahanBakuDB($pengeluaran);
            }

            DB::commit();
            return redirect()->route('pengeluaran.index')->with('success', 'Data Tersimpan & Stok Terupdate per Proyek.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $k = Pengeluaran::where('kd_peng', $id)->firstOrFail();
        $items = [$k];
        $jurnalExisting = Jurnal::with('details')->where('reff_id', $id)->where('reff_type', 'Pengeluaran')->first();
        $vendor = Vendor::all();
        $konsumen = Konsumen::all();
        $coas = Coa::orderBy('kd_akun', 'asc')->get(); 
        $barang_jual = DB::table('barangjual')->get();
        
        return view('pengeluaran.edit', compact('k', 'items', 'jurnalExisting', 'vendor', 'konsumen', 'coas', 'barang_jual'));
    }

    public function update(Request $request, $id)
    {
        $pengeluaran = Pengeluaran::where('kd_peng', $id)->firstOrFail();
        DB::beginTransaction();
        try {
            $item = $request->items[0];

            // 1. Hapus stok lama di Barang Jadi (karena kita pakai sistem baris baru)
            if($pengeluaran->kategori == 'barang_jadi') {
                Barangjadi::where('nm_brgjadi', $pengeluaran->nm_bhn)
                           ->where('nm_kons', $pengeluaran->nm_kons)
                           ->where('hpp_total', $pengeluaran->tot_peng)
                           ->first()?->delete();
            }

            // 2. Update Data Pengeluaran
            $pengeluaran->update([
                'tgl_peng'  => $request->tgl_peng,
                'nm_vendor' => $request->nm_vendor,
                'nm_kons'   => $item['nm_kons'] ?? '-',
                'kategori'  => $item['kategori'],
                'spek_peng' => $item['spek_peng'] ?? '-',
                'jml_peng'  => $item['qty'],
                'hrg_peng'  => $item['harga'],
                'tot_peng'  => $item['subtotal'],
                'nm_bhn'    => ($item['kategori'] == 'barang_jadi') ? 
                               (DB::table('barangjual')->where('kd_brgjual', $item['kd_barang_jual'] ?? '')->value('nm_brgjual')) : 
                               $item['nm_bhn']
            ]);

            // 3. Update Jurnal (Logika tetap sama)
            $jurnal = Jurnal::where('reff_id', $id)->where('reff_type', 'Pengeluaran')->first();
            if ($jurnal) {
                $jurnal->update([
                    'tgl_jurnal' => $request->tgl_peng,
                    'ket_jurnal' => $this->generateKeteranganJurnal($pengeluaran->nm_bhn, $pengeluaran->nm_kons, $id)
                ]);
                Djurnal::where('kd_jurnal', $jurnal->kd_jurnal)->delete();
                if ($request->has('jurnal')) {
                    foreach ($request->jurnal as $row) {
                        if (($row['debit'] ?? 0) > 0 || ($row['kredit'] ?? 0) > 0) {
                            Djurnal::create([
                                'kd_jurnal' => $jurnal->kd_jurnal, 
                                'kd_akun'   => $row['kd_akun'],
                                'debit'     => $row['debit'] ?? 0,
                                'kredit'    => $row['kredit'] ?? 0,
                            ]);
                        }
                    }
                }
            }

            // 4. Buat stok baru di Barang Jadi
            if($item['kategori'] == 'barang_jadi') {
                Barangjadi::create([
                    'kd_brgjadi'   => $item['kd_barang_jual'],
                    'nm_brgjadi'   => $pengeluaran->nm_bhn,
                    'nm_kons'      => $pengeluaran->nm_kons,
                    'jmlh_brgjadi' => $item['qty'],
                    'hpp_total'    => $item['subtotal'],
                    'hpp_unit'     => $item['harga']
                ]);
            }

            DB::commit();
            return redirect()->route('pengeluaran.index')->with('success', 'Data Diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal Update: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $pengeluaran = Pengeluaran::where('kd_peng', $id)->firstOrFail();

            // Hapus baris spesifik di Barang Jadi
            if ($pengeluaran->kategori == 'barang_jadi') {
                Barangjadi::where('nm_brgjadi', $pengeluaran->nm_bhn)
                           ->where('nm_kons', $pengeluaran->nm_kons)
                           ->where('hpp_total', $pengeluaran->tot_peng)
                           ->first()?->delete();
            }

            // Hapus Jurnal
            $jurnal = Jurnal::where('reff_id', $id)->where('reff_type', 'Pengeluaran')->first();
            if ($jurnal) {
                Djurnal::where('kd_jurnal', $jurnal->kd_jurnal)->delete();
                $jurnal->delete();
            }

            if ($pengeluaran->kategori == 'bahan_baku') {
                Bahanbaku::where('kd_bhn', 'BB-' . $pengeluaran->kd_peng)->delete();
                $this->updateHppProyek($pengeluaran->nm_kons);
            }

            $pengeluaran->delete();
            DB::commit();
            return redirect()->route('pengeluaran.index')->with('success', 'Data dihapus.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal Hapus: ' . $e->getMessage());
        }
    }

    private function handleBahanBakuDB($p) {
        Bahanbaku::updateOrCreate(['kd_bhn' => 'BB-' . $p->kd_peng], [
            'nm_kons' => $p->nm_kons, 'nm_bhn' => $p->nm_bhn, 'satuan_bhn' => $p->satuan_peng,
            'harga_bhn' => $p->hrg_peng, 'jml_bhn' => $p->jml_peng, 'tot_bhn' => $p->tot_peng
        ]);
        $this->updateHppProyek($p->nm_kons);
    }

    private function updateHppProyek($nm) {
        $pro = Barangproduksi::where('nm_kons', $nm)->first();
        if ($pro) {
            $total = Bahanbaku::where('nm_kons', $nm)->sum('tot_bhn');
            $pro->estrl_hpp = $total;
            if ($pro->jml_pro > 0) $pro->rl_hpp = $total / $pro->jml_pro;
            $pro->save();
        }
    }
}