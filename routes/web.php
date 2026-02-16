<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    BahanbakuController, 
    BarangproduksiController, 
    KonsumenController, 
    VendorController, 
    PenjualanController, 
    PemasukanController, 
    PengeluaranController,
    CoaController,
    JurnalController,
    AuthController, 
    LabarugiController,
    NeracaController,
    LaporanEkuitasController,
    UserManagementController, 
    BarangjualController, 
    BarangjadiController
};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. Route Default
Route::get('/', function () {
    return redirect()->route('login');
});

// 2. Route untuk Guest (Belum Login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'loginProcess'])->name('login.process');
});

// 3. Route untuk Auth (Sudah Login)
Route::middleware('auth')->group(function () {
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // ==================================================
    // ROUTE SYNC KHUSUS (WAJIB DI ATAS RESOURCE)
    // ==================================================
    
    // Sinkronisasi Google Sheets
    Route::get('/penjualan/sync-now', [PenjualanController::class, 'sync'])->name('penjualan.sync');
    Route::get('/pengeluaran/sync-now', [PengeluaranController::class, 'syncAllData'])->name('pengeluaran.sync');
    Route::get('/pemasukan/sync-now', [PemasukanController::class, 'syncAllData'])->name('pemasukan.sync');
    Route::get('/bahanbaku/sync-now', [BahanbakuController::class, 'syncAllData'])->name('bahanbaku.sync');
    Route::get('/barangproduksi/sync-now', [BarangproduksiController::class, 'syncAllData'])->name('barangproduksi.sync');
    
    // Sync Akuntansi
    Route::get('/coa-sync', [CoaController::class, 'syncAllData'])->name('coa.sync');
    Route::get('/jurnal-sync', [JurnalController::class, 'syncAllData'])->name('jurnal.sync');

    // ==================================================
    // MASTER DATA & AKUNTANSI
    // ==================================================
        
    Route::resource('coa', CoaController::class);

        // Khusus COA: Route Show (Buku Besar) ditaruh SEBELUM resource agar aman
    Route::get('/coa/{kd_akun}', [CoaController::class, 'show'])->name('coa.show');


    Route::resource('jurnal', JurnalController::class);
    Route::resource('users', UserManagementController::class);
    Route::resource('bahanbaku', BahanbakuController::class);
    Route::resource('barangproduksi', BarangproduksiController::class);
    Route::resource('konsumen', KonsumenController::class);
    Route::resource('vendor', VendorController::class);
    Route::resource('barangjual', BarangjualController::class);
    Route::resource('barangjadi', BarangjadiController::class);
    // ==================================================
    // TRANSAKSI
    // ==================================================
    Route::resource('penjualan', PenjualanController::class);
    Route::resource('pemasukan', PemasukanController::class);
    Route::resource('pengeluaran', PengeluaranController::class);


    Route::get('/labarugi', [LabaRugiController::class, 'index'])->name('labarugi.index');
    Route::get('/neraca', [NeracaController::class, 'index'])->name('neraca.index');
    Route::get('/ekuitas', [LaporanEkuitasController::class, 'index'])->name('ekuitas.index');
});