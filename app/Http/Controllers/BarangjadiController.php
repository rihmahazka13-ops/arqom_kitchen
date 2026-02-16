<?php

namespace App\Http\Controllers;

use App\Models\Barangjadi;
use App\Models\Konsumen; // Tambahkan ini untuk filter proyek
use Illuminate\Http\Request;

class BarangjadiController extends Controller
{
    /**
     * Menampilkan daftar master barang jadi dengan fitur Filter
     */
    public function index(Request $request)
    {
        $query = Barangjadi::query();

        // Fitur Filter per Proyek/Konsumen
        if ($request->filled('filter_konsumen')) {
            $query->where('nm_kons', $request->filter_konsumen);
        }

        // Fitur Search Nama Barang
        if ($request->filled('search')) {
            $query->where('nm_brgjadi', 'like', '%' . $request->search . '%');
        }

        $barangjadi = $query->orderBy('id', 'desc')->get();
        $list_konsumen = Konsumen::all(); // Untuk dropdown filter di view

        return view('barangjadi.index', compact('barangjadi', 'list_konsumen'));
    }

    /**
     * Form tambah barang jadi
     */
    public function create()
    {
        $konsumen = Konsumen::all();
        return view('barangjadi.create', compact('konsumen'));
    }

    /**
     * Menyimpan data barang jadi baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'kd_brgjadi'   => 'required', // Unique dihapus agar satu kode bisa banyak proyek
            'nm_brgjadi'   => 'required',
            'nm_kons'      => 'required', // Wajib diisi untuk membedakan proyek
            'jmlh_brgjadi' => 'required|numeric',
            'hpp_unit'     => 'required|numeric',
        ]);

        $hpp_total = $request->jmlh_brgjadi * $request->hpp_unit;

        Barangjadi::create([
            'kd_brgjadi'   => $request->kd_brgjadi,
            'nm_brgjadi'   => $request->nm_brgjadi,
            'nm_kons'      => $request->nm_kons,
            'jmlh_brgjadi' => $request->jmlh_brgjadi,
            'hpp_unit'     => $request->hpp_unit,
            'hpp_total'    => $hpp_total,
        ]);

        return redirect()->route('barangjadi.index')->with('success', 'Data Barang Jadi berhasil ditambahkan');
    }

    /**
     * Form edit barang jadi
     */
    public function edit($id)
    {
        // Menggunakan findOrFail berdasarkan ID (Primary Key baru), bukan kode barang
        $barangjadi = Barangjadi::findOrFail($id);
        $konsumen = Konsumen::all();
        return view('barangjadi.edit', compact('barangjadi', 'konsumen'));
    }

    /**
     * Memperbarui data barang jadi
     */
    public function update(Request $request, $id)
    {
        $barangjadi = Barangjadi::findOrFail($id);

        $request->validate([
            'nm_brgjadi'   => 'required',
            'nm_kons'      => 'required',
            'jmlh_brgjadi' => 'required|numeric',
            'hpp_unit'     => 'required|numeric',
        ]);

        $hpp_total = $request->jmlh_brgjadi * $request->hpp_unit;

        $barangjadi->update([
            'nm_brgjadi'   => $request->nm_brgjadi,
            'nm_kons'      => $request->nm_kons,
            'jmlh_brgjadi' => $request->jmlh_brgjadi,
            'hpp_unit'     => $request->hpp_unit,
            'hpp_total'    => $hpp_total,
        ]);

        return redirect()->route('barangjadi.index')->with('success', 'Data Barang Jadi berhasil diperbarui');
    }

    /**
     * Menghapus data barang jadi
     */
    public function destroy($id)
    {
        $barangjadi = Barangjadi::findOrFail($id);
        $barangjadi->delete();

        return redirect()->route('barangjadi.index')->with('success', 'Data Barang Jadi berhasil dihapus');
    }
}