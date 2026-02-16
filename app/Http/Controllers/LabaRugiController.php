<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Djurnal;
use App\Models\Coa;
use Illuminate\Support\Facades\DB;

class LabaRugiController extends Controller
{
    public function index(Request $request)
    {
        $tgl_mulai = $request->tgl_mulai ?? date('Y-m-01');
        $tgl_selesai = $request->tgl_selesai ?? date('Y-m-d');
        $search = $request->search;

        // Query Utama: Pastikan nama tabel 'djurnal' dan 'jurnal' sesuai foto phpMyAdmin kamu
        $query = Djurnal::query()
            ->join('jurnal', 'djurnal.kd_jurnal', '=', 'jurnal.kd_jurnal')
            ->join('coa', 'djurnal.kd_akun', '=', 'coa.kd_akun')
            ->whereBetween('jurnal.tgl_jurnal', [$tgl_mulai, $tgl_selesai]);

        if ($search) {
            $query->where('coa.nama_akun', 'like', "%{$search}%");
        }

        // 1. PENDAPATAN (Kepala 4) -> Kredit - Debit
        $pendapatan = (clone $query)->where('coa.kd_akun', 'like', '4%')
            ->select('coa.nama_akun', DB::raw('SUM(kredit - debit) as total'))
            ->groupBy('coa.kd_akun', 'coa.nama_akun')->get();

        // 2. COGS (Kepala 5) -> Debit - Kredit
        $cogs = (clone $query)->where('coa.kd_akun', 'like', '5%')
            ->select('coa.nama_akun', DB::raw('SUM(debit - kredit) as total'))
            ->groupBy('coa.kd_akun', 'coa.nama_akun')->get();

        // 3. BEBAN (Kepala 6 ke atas) -> Debit - Kredit
        $beban = (clone $query)->where('coa.kd_akun', '>=', '6000')
            ->select('coa.nama_akun', DB::raw('SUM(debit - kredit) as total'))
            ->groupBy('coa.kd_akun', 'coa.nama_akun')->get();

        // Kirim data sebagai OBJECT (jangan di-cast ke array agar tidak error undefined key)
        return view('labarugi.index', compact('pendapatan', 'cogs', 'beban', 'tgl_mulai', 'tgl_selesai', 'search'));
    }
}