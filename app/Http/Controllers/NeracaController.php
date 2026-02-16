<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NeracaController extends Controller
{
    public function index(Request $request)
    {
        $tgl_akhir = $request->tgl_akhir ?? date('Y-m-d');

        // Helper untuk ambil saldo akhir per akun
        $getBalances = function($range) use ($tgl_akhir) {
            return DB::table('coa')
                ->leftJoin('djurnal', 'coa.kd_akun', '=', 'djurnal.kd_akun')
                ->leftJoin('jurnal', 'djurnal.kd_jurnal', '=', 'jurnal.kd_jurnal')
                ->where('coa.kd_akun', 'like', $range . '%')
                ->where(function($q) use ($tgl_akhir) {
                    $q->where('jurnal.tgl_jurnal', '<=', $tgl_akhir)
                      ->orWhereNull('jurnal.tgl_jurnal');
                })
                ->select('coa.nama_akun', DB::raw('SUM(djurnal.debit) - SUM(djurnal.kredit) as total'))
                ->groupBy('coa.kd_akun', 'coa.nama_akun')
                ->get()
                ->map(fn($item) => [
                    'nama_akun' => $item->nama_akun,
                    'total' => abs($item->total ?? 0) // Gunakan abs untuk tampilan laporan
                ]);
        };

        // 1. Ambil Aset (Kepala 1)
        $aset_lancar = $getBalances('11'); // Contoh 1101, 1102
        $aset_tetap = $getBalances('12');  // Contoh 1201

        // 2. Ambil Liabilitas (Kepala 2)
        $liabilitas = $getBalances('2');

        // 3. Ambil Ekuitas (Kepala 3)
        $ekuitas = $getBalances('3');

        // 4. HITUNG LABA BERSIH (Pendapatan - Beban)
        $pendapatan = DB::table('djurnal')
            ->join('jurnal', 'djurnal.kd_jurnal', '=', 'jurnal.kd_jurnal')
            ->where('kd_akun', 'like', '4%')
            ->where('jurnal.tgl_jurnal', '<=', $tgl_akhir)
            ->sum(DB::raw('kredit - debit'));

        $beban = DB::table('djurnal')
            ->join('jurnal', 'djurnal.kd_jurnal', '=', 'jurnal.kd_jurnal')
            ->where(function($q) {
                $q->where('kd_akun', 'like', '5%')->orWhere('kd_akun', 'like', '6%');
            })
            ->where('jurnal.tgl_jurnal', '<=', $tgl_akhir)
            ->sum(DB::raw('debit - kredit'));

        $laba_berjalan = $pendapatan - $beban;

        return view('neraca.index', compact(
            'aset_lancar', 'aset_tetap', 'liabilitas', 'ekuitas', 'laba_berjalan', 'tgl_akhir'
        ));
    }
}