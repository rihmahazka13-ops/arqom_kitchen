<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    /**
     * Menampilkan daftar vendor dengan fitur pencarian dan paginasi.
     */
    public function index(Request $request)
    {
        $query = Vendor::query();

        // 🔍 Pencarian berdasarkan nama vendor
        if ($request->has('search') && $request->search != '') {
            $query->where('nm_vendor', 'like', '%' . $request->search . '%');
        }

        // Paginasi 10 data per halaman dan urutkan berdasarkan kode vendor
        $data = $query->orderBy('kd_vendor', 'asc')->paginate(10);

        return view('vendor.index', compact('data'));
    }

    public function create()
    {
        // ✅ LOGIKA GENERATE KODE OTOMATIS (V-0001)
        $maxKode = Vendor::where('kd_vendor', 'like', 'V-%')
            ->selectRaw('MAX(CAST(SUBSTRING(kd_vendor, 3) AS UNSIGNED)) as max_no')
            ->first();

        $nextNumber = $maxKode->max_no ? $maxKode->max_no + 1 : 1;
        $kd_otomatis = 'V-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        return view('vendor.create', compact('kd_otomatis'));
    }

    public function store(Request $req)
    {
        $req->validate([
            'kd_vendor' => 'required|unique:vendor',
            'nm_vendor' => 'required',
        ]);

        Vendor::create($req->all());

        return redirect()->route('vendor.index')
            ->with('success', 'Vendor berhasil ditambahkan');
    }

    public function edit($id)
    {
        $s = Vendor::findOrFail($id);
        return view('vendor.edit', compact('s'));
    }

    public function update(Request $req, $id)
    {
        $req->validate([
            'nm_vendor' => 'required',
        ]);

        $s = Vendor::findOrFail($id);
        $s->update($req->all());

        return redirect()->route('vendor.index')
            ->with('success', 'Data vendor berhasil diperbarui');
    }

    public function destroy($id)
    {
        Vendor::findOrFail($id)->delete();

        return redirect()->route('vendor.index')
            ->with('success', 'Data vendor berhasil dihapus');
    }
}