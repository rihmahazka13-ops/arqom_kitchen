<?php

namespace App\Http\Controllers;

use App\Models\Konsumen;
use Illuminate\Http\Request;

class KonsumenController extends Controller
{
    /**
     * Menampilkan daftar konsumen dengan fitur pencarian dan paginasi.
     */
    public function index(Request $request)
    {
        $query = Konsumen::query();

        // 🔍 Pencarian berdasarkan nama konsumen
        if ($request->has('search') && $request->search != '') {
            $query->where('nm_kons', 'like', '%' . $request->search . '%');
        }

        // Mengurutkan berdasarkan kode konsumen dan membatasi 10 data per halaman
        $data = $query->orderBy('kd_kons', 'asc')->paginate(10);

        return view('konsumen.index', compact('data'));
    }

    public function create()
    {
        // ✅ LOGIKA GENERATE KODE OTOMATIS (K-0001)
        $maxKode = Konsumen::where('kd_kons', 'like', 'K-%')
            ->selectRaw('MAX(CAST(SUBSTRING(kd_kons, 3) AS UNSIGNED)) as max_no')
            ->first();

        $nextNumber = $maxKode->max_no ? $maxKode->max_no + 1 : 1;
        $kd_otomatis = 'K-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        return view('konsumen.create', compact('kd_otomatis'));
    }

    public function store(Request $req)
    {
        $req->validate([
            'kd_kons' => 'required|unique:konsumen',
            'nm_kons' => 'required',
        ]);

        Konsumen::create($req->all());

        return redirect()
            ->route('konsumen.index')
            ->with('success', 'Konsumen berhasil ditambahkan');
    }

    public function edit($id)
    {
        $k = Konsumen::findOrFail($id);
        return view('konsumen.edit', compact('k'));
    }

    public function update(Request $req, $id)
    {
        $req->validate([
            'nm_kons' => 'required',
        ]);

        $k = Konsumen::findOrFail($id);
        $k->update($req->all());

        return redirect()
            ->route('konsumen.index')
            ->with('success', 'Data konsumen berhasil diperbarui');
    }

    public function destroy($id)
    {
        Konsumen::findOrFail($id)->delete();

        return redirect()
            ->route('konsumen.index')
            ->with('success', 'Data konsumen berhasil dihapus');
    }
}