<?php

namespace App\Http\Controllers;

use App\Models\Barangjual;
use Illuminate\Http\Request;

class BarangjualController extends Controller
{
    public function index(Request $request)
    {
        $query = Barangjual::query();

        if ($request->has('search')) {
            $query->where('nm_brgjual', 'like', '%' . $request->search . '%');
        }

        $data = $query->get();
        return view('barangjual.index', compact('data'));
    }

    public function create()
    {
        $lastBarang = Barangjual::orderBy('kd_brgjual', 'desc')->first();
        if (!$lastBarang) {
            $nextKode = 'BJ-0001';
        } else {
            $lastNumber = (int) substr($lastBarang->kd_brgjual, 3);
            $nextKode = 'BJ-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        }

        return view('barangjual.create', compact('nextKode'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kd_brgjual' => 'required|unique:barangjual',
            'nm_brgjual' => 'required'
        ]);

        Barangjual::create($request->all());
        return redirect()->route('barangjual.index')->with('success', 'Data Barang Berhasil Ditambahkan');
    }

    public function edit($id)
    {
        $item = Barangjual::findOrFail($id);
        return view('barangjual.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $request->validate(['nm_brgjual' => 'required']);
        $item = Barangjual::findOrFail($id);
        $item->update($request->only('nm_brgjual'));
        
        return redirect()->route('barangjual.index')->with('success', 'Data Barang Berhasil Diperbarui');
    }

    public function destroy($id)
    {
        Barangjual::findOrFail($id)->delete();
        return redirect()->route('barangjual.index')->with('success', 'Data Barang Berhasil Dihapus');
    }
}