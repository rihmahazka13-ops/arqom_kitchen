@extends('layout')

@section('content')
<style>
    /* STYLE TETAP DIJAGA */
    .page-title { font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: -0.02em; }
    .form-container { 
        background: #ffffff; padding: 40px; border-radius: 20px; 
        border: 1px solid #e2e8f0; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.02); 
        max-width: 1150px; margin: 0 auto;
    }
    .form-label-custom { 
        font-size: 0.75rem; font-weight: 800; text-transform: uppercase; 
        color: #64748b; margin-bottom: 8px; display: block; letter-spacing: 0.5px;
    }
    .input-group-text-custom {
        background-color: #f8fafc; border: 1px solid #cbd5e1; border-right: none;
        border-radius: 10px 0 0 10px; color: #94a3b8; width: 45px;
        display: flex; align-items: center; justify-content: center;
    }
    .form-control-custom, .form-select-custom { 
        border-radius: 0 10px 10px 0; border: 1px solid #cbd5e1; 
        padding: 12px 15px; font-weight: 600; color: #0f172a;
        transition: all 0.3s ease;
    }
    .form-control-custom:focus, .form-select-custom:focus {
        border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); outline: none;
    }
    .section-divider { border-bottom: 2px dashed #e2e8f0; margin: 30px 0; position: relative; }
    .section-badge {
        position: absolute; top: -12px; left: 50%; transform: translateX(-50%);
        background: #f1f5f9; padding: 0 15px; color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;
    }
    .btn-save {
        background: #0f172a; color: white; border-radius: 12px; padding: 15px; font-weight: 700; border: none; width: 100%; transition: 0.3s;
    }
    .btn-save:hover { background: #1e293b; transform: translateY(-2px); }
    
    .item-card {
        background: #fdfdfd; border: 1px solid #e2e8f0; border-radius: 15px;
        padding: 25px; margin-bottom: 25px; position: relative;
        transition: all 0.3s ease; border-left: 5px solid #3b82f6;
    }
    .btn-remove-item {
        position: absolute; top: -10px; right: -10px; background: #ef4444;
        color: white; width: 28px; height: 28px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; border: 2px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        z-index: 10;
    }
    .btn-auto-fill {
        font-size: 0.75rem; font-weight: 700; color: #3b82f6; 
        background: #eff6ff; padding: 5px 12px; border-radius: 20px; border: 1px solid #dbeafe;
        cursor: pointer;
    }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="page-title mb-1">Tambah Pengeluaran</h4>
            <p class="text-muted small mb-0 fw-bold text-uppercase">Arqom Kitchen • Multi-Category Transaction</p>
        </div>
        <a href="{{ route('pengeluaran.index') }}" class="btn btn-outline-secondary px-4 fw-bold rounded-3 shadow-sm">KEMBALI</a>
    </div>

    <div class="form-container">
        <form action="{{ route('pengeluaran.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- HEADER --}}
            <div class="row mb-2">
                <div class="col-md-6">
                    <label class="form-label-custom">Kode Transaksi</label>
                    <div class="input-group">
                        <span class="input-group-text input-group-text-custom"><i class="fas fa-barcode"></i></span>
                        <input type="text" name="kd_peng" class="form-control form-control-custom bg-light fw-bold" value="{{ $kd_otomatis }}" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label-custom">Tanggal</label>
                    <div class="input-group">
                        <span class="input-group-text input-group-text-custom"><i class="fas fa-calendar-alt"></i></span>
                        <input type="date" name="tgl_peng" class="form-control form-control-custom" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
            </div>

            <div class="section-divider">
                <span class="section-badge"><i class="fas fa-shopping-cart me-2"></i> Rincian Pengeluaran</span>
            </div>

            <div id="item-container">
                {{-- ITEM CARD 0 --}}
                <div class="item-card shadow-sm" id="item-0">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label-custom text-primary">Kategori</label>
                            <div class="input-group">
                                <span class="input-group-text input-group-text-custom bg-primary text-white border-primary"><i class="fas fa-filter"></i></span>
                                <select name="items[0][kategori]" class="form-select form-select-custom border-primary select-kategori" onchange="updateItemUI(0)" required>
                                    <option value="operasional">Operasional</option>
                                    <option value="bahan_baku">Bahan Baku</option>
                                    <option value="barang_jadi">Barang Jadi</option>
                                    <option value="cogs_lainnya">COGS Lainnya</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label-custom label-uraian">Keterangan Pengeluaran</label>
                            <input type="text" name="items[0][nm_bhn]" class="form-control form-control-custom input-nama text-uppercase rounded-3" placeholder="Masukkan Deskripsi...">
                            {{-- SELECT UNTUK BARANG JADI (DIAMBIL DARI MASTER JUAL) --}}
                            <select name="items[0][kd_barang_jual]" class="form-select form-select-custom select-barang-jual d-none rounded-3">
                                <option value="" selected disabled>-- Pilih Master Barang Jual --</option>
                                @foreach($barang_jual as $bj)
                                    <option value="{{ $bj->kd_brgjual }}">{{ $bj->kd_brgjual }} - {{ $bj->nm_brgjual }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 div-konsumen d-none">
                            <label class="form-label-custom text-danger">Proyek / Konsumen</label>
                            <div class="input-group">
                                <span class="input-group-text input-group-text-custom bg-danger text-white border-danger"><i class="fas fa-user-tag"></i></span>
                                <select name="items[0][nm_kons]" class="form-select form-select-custom border-danger">
                                    <option value="-" selected>- Internal -</option>
                                    @foreach($konsumen as $k)<option value="{{ $k->nm_kons }}">{{ $k->nm_kons }}</option>@endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 div-vendor d-none">
                            <label class="form-label-custom text-success">Vendor / Supplier</label>
                            <div class="input-group">
                                <span class="input-group-text input-group-text-custom bg-success text-white border-success"><i class="fas fa-store"></i></span>
                                <select name="nm_vendor" class="form-select form-select-custom border-success">
                                    <option value="-" selected>- Tanpa Vendor -</option>
                                    @foreach($vendor as $v)<option value="{{ $v->nm_vendor }}">{{ $v->nm_vendor }}</option>@endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12 div-spec">
                            <label class="form-label-custom">Spesifikasi / Catatan</label>
                            <input type="text" name="items[0][spek_peng]" class="form-control form-control-custom rounded-3" value="-">
                        </div>

                        <div class="col-md-2 div-qty d-none">
                            <label class="form-label-custom">Qty</label>
                            <input type="number" step="any" name="items[0][qty]" class="form-control form-control-custom text-center input-qty rounded-3" value="1" oninput="calculateTotal(0)">
                        </div>
                        <div class="col-md-2 div-satuan d-none">
                            <label class="form-label-custom">Satuan</label>
                            <input type="text" name="items[0][satuan]" class="form-control form-control-custom text-center rounded-3" placeholder="Pcs">
                        </div>
                        <div class="col-md-4 div-harga">
                            <label class="form-label-custom label-harga">Nominal (Rp)</label>
                            <input type="number" name="items[0][harga]" class="form-control form-control-custom text-end input-harga rounded-3" placeholder="0" oninput="calculateTotal(0)">
                        </div>
                        <div class="col-md-4 div-subtotal">
                            <label class="form-label-custom text-primary">Sub-Total</label>
                            <input type="number" name="items[0][subtotal]" class="form-control form-control-custom text-end input-subtotal bg-light fw-bold rounded-3" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <button type="button" class="btn btn-sm btn-dark mb-4 fw-bold px-3 py-2" onclick="addItemRow()">
                <i class="fas fa-plus me-1"></i> TAMBAH ITEM
            </button>

            <div class="row align-items-end mb-4">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-6"><label class="form-label-custom">Bukti Nota</label><input type="file" name="buktinot_peng" class="form-control form-control-custom rounded-3"></div>
                        <div class="col-md-6"><label class="form-label-custom">Bukti Transfer</label><input type="file" name="buktitf_peng" class="form-control form-control-custom rounded-3"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label-custom text-danger fw-800">TOTAL AKHIR</label>
                    <input type="number" id="grand_total" name="tot_peng" class="form-control form-control-custom fs-4 fw-bold text-end border-danger text-danger rounded-3" readonly>
                </div>
            </div>

            <div class="section-divider"><span class="section-badge">ALOKASI JURNAL</span></div>
            <div class="d-flex justify-content-end mb-2"><button type="button" class="btn-auto-fill" onclick="syncJurnal()"><i class="fas fa-magic me-1"></i> ISI NOMINAL JURNAL</button></div>

            <div class="table-responsive mb-4">
                <table class="table align-middle">
                    <thead><tr><th width="50%">Akun (COA)</th><th width="25%">Debit</th><th width="25%">Kredit</th></tr></thead>
                    <tbody>
                        <tr>
                            <td><select name="jurnal[0][kd_akun]" class="form-select form-select-custom rounded-3" required><option value="" disabled selected>Pilih Akun Debit</option>@foreach($coas as $coa)<option value="{{ $coa->kd_akun }}">{{ $coa->kd_akun }} - {{ $coa->nama_akun }}</option>@endforeach</select></td>
                            <td><input type="number" name="jurnal[0][debit]" class="form-control form-control-custom text-end input-jurnal-debit rounded-3"></td>
                            <td><input type="number" class="form-control form-control-custom text-end bg-light rounded-3" readonly></td>
                        </tr>
                        <tr>
                            <td><select name="jurnal[1][kd_akun]" class="form-select form-select-custom rounded-3" required><option value="" disabled selected>Pilih Akun Kredit</option>@foreach($coas as $coa)<option value="{{ $coa->kd_akun }}">{{ $coa->kd_akun }} - {{ $coa->nama_akun }}</option>@endforeach</select></td>
                            <td><input type="number" class="form-control form-control-custom text-end bg-light rounded-3" readonly></td>
                            <td><input type="number" name="jurnal[1][kredit]" class="form-control form-control-custom text-end input-jurnal-kredit rounded-3"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <button type="submit" class="btn btn-save shadow-lg">SIMPAN TRANSAKSI</button>
        </form>
    </div>
</div>

<script>
    let itemIdx = 1;

    function updateItemUI(index) {
        const card = document.getElementById(`item-${index}`);
        const kategori = card.querySelector('.select-kategori').value;
        
        const divQty = card.querySelector('.div-qty');
        const divSatuan = card.querySelector('.div-satuan');
        const divVendor = card.querySelector('.div-vendor');
        const divKonsumen = card.querySelector('.div-konsumen');
        const divSpec = card.querySelector('.div-spec');
        const labelHarga = card.querySelector('.label-harga');
        const labelUraian = card.querySelector('.label-uraian');
        const inputNama = card.querySelector('.input-nama');
        const selectBJ = card.querySelector('.select-barang-jual');

        // Reset default view
        divQty.classList.add('d-none');
        divSatuan.classList.add('d-none');
        divVendor.classList.add('d-none');
        divKonsumen.classList.add('d-none');
        divSpec.className = "col-md-12 div-spec";
        labelHarga.innerText = "Nominal (Rp)";
        labelUraian.innerText = "Keterangan Pengeluaran";
        inputNama.classList.remove('d-none');
        selectBJ.classList.add('d-none');

        if (kategori === 'operasional') {
            // Operasional tetap standard
        } else if (kategori === 'cogs_lainnya') {
            divKonsumen.classList.remove('d-none');
            divSpec.className = "col-md-9 div-spec";
        } else if (kategori === 'bahan_baku' || kategori === 'barang_jadi') {
            divQty.classList.remove('d-none');
            divSatuan.classList.remove('d-none');
            divVendor.classList.remove('d-none');
            divKonsumen.classList.remove('d-none');
            divSpec.className = "col-md-6 div-spec";
            labelHarga.innerText = "Harga Satuan";
            
            if(kategori === 'barang_jadi') {
                labelUraian.innerText = "Pilih Master Barang Jual";
                inputNama.classList.add('d-none');
                selectBJ.classList.remove('d-none');
            } else {
                labelUraian.innerText = "Nama Bahan Baku";
            }
        }
        calculateTotal(index);
    }

    function calculateTotal(index) {
        const card = document.getElementById(`item-${index}`);
        if (!card) return;

        const kategori = card.querySelector('.select-kategori').value;
        const harga = parseFloat(card.querySelector('.input-harga').value) || 0;
        
        let subtotal = 0;
        if (kategori === 'bahan_baku' || kategori === 'barang_jadi') {
            const qty = parseFloat(card.querySelector('.input-qty').value) || 0;
            subtotal = qty * harga;
        } else {
            subtotal = harga;
        }
        
        card.querySelector('.input-subtotal').value = subtotal;
        
        // Re-calculate Grand Total
        let total = 0;
        document.querySelectorAll('.input-subtotal').forEach(el => {
            total += parseFloat(el.value) || 0;
        });
        document.getElementById('grand_total').value = total;
    }

    function syncJurnal() {
        const total = document.getElementById('grand_total').value;
        document.querySelector('.input-jurnal-debit').value = total;
        document.querySelector('.input-jurnal-kredit').value = total;
    }

    function addItemRow() {
        const container = document.getElementById('item-container');
        const html = `
            <div class="item-card shadow-sm" id="item-${itemIdx}">
                <span class="btn-remove-item" onclick="removeItem(${itemIdx})"><i class="fas fa-times"></i></span>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label-custom text-primary">Kategori</label>
                        <select name="items[${itemIdx}][kategori]" class="form-select form-select-custom border-primary select-kategori" onchange="updateItemUI(${itemIdx})" required>
                            <option value="operasional">Operasional</option>
                            <option value="bahan_baku">Bahan Baku</option>
                            <option value="barang_jadi">Barang Jadi</option>
                            <option value="cogs_lainnya">COGS Lainnya</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom label-uraian">Keterangan Pengeluaran</label>
                        <input type="text" name="items[${itemIdx}][nm_bhn]" class="form-control form-control-custom input-nama text-uppercase rounded-3">
                        <select name="items[${itemIdx}][kd_barang_jual]" class="form-select form-select-custom select-barang-jual d-none rounded-3">
                            <option value="" selected disabled>-- Pilih Master Barang Jual --</option>
                            @foreach($barang_jual as $bj)<option value="{{ $bj->kd_brgjual }}">{{ $bj->kd_brgjual }} - {{ $bj->nm_brgjual }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-md-3 div-konsumen d-none">
                        <label class="form-label-custom text-danger">Proyek / Konsumen</label>
                        <select name="items[${itemIdx}][nm_kons]" class="form-select form-select-custom border-danger">
                            <option value="-" selected>- Internal -</option>
                            @foreach($konsumen as $k)<option value="{{ $k->nm_kons }}">{{ $k->nm_kons }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-md-6 div-vendor d-none">
                        <label class="form-label-custom text-success">Vendor / Supplier</label>
                        <select name="nm_vendor" class="form-select form-select-custom border-success">
                            <option value="-" selected>- Tanpa Vendor -</option>
                            @foreach($vendor as $v)<option value="{{ $v->nm_vendor }}">{{ $v->nm_vendor }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-md-12 div-spec">
                        <label class="form-label-custom">Spesifikasi</label>
                        <input type="text" name="items[${itemIdx}][spek_peng]" class="form-control form-control-custom rounded-3" value="-">
                    </div>
                    <div class="col-md-2 div-qty d-none"><label class="form-label-custom">Qty</label><input type="number" step="any" name="items[${itemIdx}][qty]" class="form-control form-control-custom text-center input-qty" value="1" oninput="calculateTotal(${itemIdx})"></div>
                    <div class="col-md-2 div-satuan d-none"><label class="form-label-custom">Satuan</label><input type="text" name="items[${itemIdx}][satuan]" class="form-control form-control-custom text-center"></div>
                    <div class="col-md-4 div-harga"><label class="form-label-custom label-harga">Nominal (Rp)</label><input type="number" name="items[${itemIdx}][harga]" class="form-control form-control-custom text-end input-harga" oninput="calculateTotal(${itemIdx})"></div>
                    <div class="col-md-4 div-subtotal"><label class="form-label-custom text-primary">Sub-Total</label><input type="number" name="items[${itemIdx}][subtotal]" class="form-control form-control-custom text-end input-subtotal bg-light fw-bold" readonly></div>
                </div>
            </div>`;
        container.insertAdjacentHTML('beforeend', html);
        itemIdx++;
    }

    function removeItem(index) { 
        document.getElementById(`item-${index}`).remove(); 
        // Trigger hitung total setelah hapus
        let total = 0;
        document.querySelectorAll('.input-subtotal').forEach(el => {
            total += parseFloat(el.value) || 0;
        });
        document.getElementById('grand_total').value = total;
    }

    window.onload = function() { updateItemUI(0); };
</script>
@endsection