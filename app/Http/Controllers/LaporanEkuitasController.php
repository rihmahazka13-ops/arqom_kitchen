<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanEkuitasController extends Controller
{
    public function index(Request $request)
    {
        $tgl_mulai = $request->tgl_mulai ?? date('Y-m-01');
        $tgl_selesai = $request->tgl_selesai ?? date('Y-m-d');

        // 1. MODAL AWAL (Saldo awal akun Modal - Kepala 31 sebelum tgl_mulai)
        $modal_awal = DB::table('djurnal')
            ->join('jurnal', 'djurnal.kd_jurnal', '=', 'jurnal.kd_jurnal')
            ->where('kd_akun', 'like', '31%')
            ->where('jurnal.tgl_jurnal', '<', $tgl_mulai)
            ->sum(DB::raw('kredit - debit'));

        // 2. LABA DITAHAN (Saldo laba periode-periode sebelumnya - Kepala 32)
        $laba_ditahan = DB::table('djurnal')
            ->join('jurnal', 'djurnal.kd_jurnal', '=', 'jurnal.kd_jurnal')
            ->where('kd_akun', 'like', '32%')
            ->where('jurnal.tgl_jurnal', '<', $tgl_mulai)
            ->sum(DB::raw('kredit - debit'));

        // 3. LABA TAHUN BERJALAN (Sama dengan hasil Laba Rugi periode berjalan)
        $pendapatan = DB::table('djurnal')
            ->join('jurnal', 'djurnal.kd_jurnal', '=', 'jurnal.kd_jurnal')
            ->where('kd_akun', 'like', '4%')
            ->whereBetween('jurnal.tgl_jurnal', [$tgl_mulai, $tgl_selesai])
            ->sum(DB::raw('kredit - debit'));

        $beban = DB::table('djurnal')
            ->join('jurnal', 'djurnal.kd_jurnal', '=', 'jurnal.kd_jurnal')
            ->where(function($q) {
                $q->where('kd_akun', 'like', '5%')->orWhere('kd_akun', 'like', '6%');
            })
            ->whereBetween('jurnal.tgl_jurnal', [$tgl_mulai, $tgl_selesai])
            ->sum(DB::raw('debit - kredit'));
        
        $laba_bersih = $pendapatan - $beban;

        // 4. DIVIDEN / PRIVE (Kepala 33 - Pengambilan pemilik di periode berjalan)
        $dividen = DB::table('djurnal')
            ->join('jurnal', 'djurnal.kd_jurnal', '=', 'jurnal.kd_jurnal')
            ->where('kd_akun', 'like', '33%')
            ->whereBetween('jurnal.tgl_jurnal', [$tgl_mulai, $tgl_selesai])
            ->sum(DB::raw('debit - kredit'));

        return view('ekuitas.index', compact(
            'modal_awal', 'laba_ditahan', 'laba_bersih', 'dividen', 'tgl_mulai', 'tgl_selesai'
        ));
    }
}