<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Input Pemasukan Manual — LabMoneyLens</title>
  @vite(['resources/css/style.css','resources/css/welcome.css','resources/js/script.js'])

  <style>
    /* ── Override layout: centered main ── */
    .main {
      flex-direction: column;
      align-items: center;
      padding: 32px 24px;
      gap: 28px;
      overflow-y: auto;
    }

    .page-wrapper {
      width: 100%;
      max-width: 800px;
      display: flex;
      flex-direction: column;
      gap: 28px;
    }

    .page-hero {
      background: linear-gradient(135deg, #0f766e 0%, #0d9488 60%, #10b981 100%);
      border-radius: 20px;
      padding: 28px 32px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 20px;
      box-shadow: 0 8px 24px rgba(13, 148, 136, 0.25);
    }

    .page-hero-info h1 {
      font-size: 24px;
      font-weight: 800;
      color: #fff;
      letter-spacing: -0.3px;
    }

    .page-hero-info p {
      font-size: 13px;
      color: rgba(255,255,255,0.8);
      margin-top: 6px;
      line-height: 1.5;
    }

    .hero-balance-badge {
      background: rgba(255,255,255,0.18);
      border: 1.5px solid rgba(255,255,255,0.3);
      border-radius: 16px;
      padding: 14px 22px;
      text-align: center;
      backdrop-filter: blur(8px);
      white-space: nowrap;
      flex-shrink: 0;
    }

    .hero-balance-badge .badge-label {
      font-size: 10px;
      font-weight: 700;
      color: rgba(255,255,255,0.75);
      text-transform: uppercase;
      letter-spacing: 0.8px;
    }

    .hero-balance-badge .badge-value {
      font-size: 20px;
      font-weight: 800;
      color: #fff;
      margin-top: 4px;
    }

    .form-card {
      background: linear-gradient(135deg, #ffffff 0%, #f0fdf9 100%);
      border-radius: 20px;
      border: 2px solid #a7f3d0;
      box-shadow: 0 4px 16px rgba(13, 148, 136, 0.1);
      overflow: hidden;
    }

    .form-card-header {
      background: linear-gradient(90deg, #f0fdf9 0%, #ecfdf5 100%);
      border-bottom: 2px solid #a7f3d0;
      padding: 18px 28px;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .form-card-header .header-icon {
      width: 40px;
      height: 40px;
      background: linear-gradient(135deg, #10b981, #0d9488);
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .form-card-header .header-icon svg {
      width: 22px;
      height: 22px;
      stroke: #fff;
    }

    .form-card-header h2 {
      font-size: 16px;
      font-weight: 700;
      color: #0f766e;
    }

    .form-card-header p {
      font-size: 12px;
      color: #0d9488;
      margin-top: 4px;
    }

    .form-card-body {
      padding: 28px;
    }

    .saldo-info {
      background: linear-gradient(135deg, #d1fae5 0%, #f0fdf9 100%);
      border: 1.5px solid #a7f3d0;
      border-radius: 12px;
      padding: 14px 16px;
      margin-bottom: 22px;
    }

    .saldo-info-text {
      font-size: 13px;
      color: #0f766e;
    }

    .input-form {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .form-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 16px;
    }

    .form-grid.span-2 {
      grid-column: 1 / -1;
    }

    .form-group {
      display: flex;
      flex-direction: column;
      gap: 8px;
      height: 100%;
    }

    .form-label {
      font-size: 13px;
      font-weight: 700;
      color: #0f766e;
      line-height: 1.4;
      min-height: 32px;
      display: flex;
      align-items: center;
    }

    .required-dot {
      color: #10b981;
    }

    .form-input {
      background: #fff;
      border: 1.5px solid #a7f3d0;
      border-radius: 10px;
      padding: 10px 14px;
      font-size: 13px;
      font-family: inherit;
      transition: all 0.2s;
    }

    .form-input:focus {
      outline: none;
      border-color: #0d9488;
      box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.1);
    }

    .item-row {
      position: relative;
      padding-bottom: 20px;
      margin-bottom: 20px;
      border-bottom: 1px dashed #a7f3d0;
    }

    .item-row:last-child {
      border-bottom: none;
      margin-bottom: 0;
      padding-bottom: 0;
    }

    .btn-hapus-baris {
      background: none;
      border: none;
      color: #10b981;
      cursor: pointer;
      font-size: 18px;
      position: absolute;
      top: 0;
      right: 0;
      padding: 0;
    }

    .nominal-qty-group {
      display: grid;
      grid-template-columns: 1fr auto;
      gap: 8px;
    }

    .nominal-wrapper {
      display: flex;
      align-items: center;
      position: relative;
    }

    .nominal-prefix {
      position: absolute;
      left: 12px;
      font-size: 13px;
      font-weight: 600;
      color: #0d9488;
      pointer-events: none;
    }

    .form-input.with-prefix {
      padding-left: 32px;
    }

    .qty-wrapper {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .qty-label {
      font-size: 12px;
      font-weight: 600;
      color: #0f766e;
      white-space: nowrap;
    }

    .qty-input {
      width: 60px;
      background: #fff;
      border: 1.5px solid #a7f3d0;
      border-radius: 8px;
      padding: 8px 10px;
      font-size: 13px;
      text-align: center;
      font-family: inherit;
    }

    .qty-input:focus {
      outline: none;
      border-color: #0d9488;
    }

    .form-actions {
      display: flex;
      gap: 12px;
      margin-top: 20px;
    }

    .save-btn,
    .reset-btn {
      flex: 1;
      padding: 12px 18px;
      border-radius: 10px;
      font-size: 13px;
      font-weight: 700;
      border: 1.5px solid;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      transition: all 0.2s;
    }

    .save-btn {
      background: linear-gradient(135deg, #10b981, #0d9488);
      color: #fff;
      border-color: #0d9488;
    }

    .save-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(13, 148, 136, 0.3);
    }

    .reset-btn {
      background: #fff;
      color: #0d9488;
      border-color: #a7f3d0;
    }

    .reset-btn:hover {
      background: #f0fdf9;
    }

    .table-section {
      margin-top: 20px;
    }

    .table-section-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 16px;
      gap: 12px;
    }

    .table-section-header h3 {
      font-size: 16px;
      font-weight: 700;
      color: #1f2937;
      margin: 0;
    }

    .entry-count {
      font-size: 12px;
      background: #d1fae5;
      color: #0f766e;
      padding: 4px 10px;
      border-radius: 6px;
      font-weight: 600;
    }

    .table-wrap {
      background: #fff;
      border: 1.5px solid #a7f3d0;
      border-radius: 12px;
      overflow: hidden;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 13px;
    }

    table thead {
      background: linear-gradient(90deg, #f0fdf9 0%, #f0fdf9 100%);
      border-bottom: 2px solid #a7f3d0;
    }

    table th {
      padding: 12px;
      text-align: left;
      font-weight: 700;
      color: #0f766e;
    }

    table td {
      padding: 12px;
      border-bottom: 1px solid #a7f3d0;
      color: #374151;
    }

    table tbody tr:hover {
      background: #f0fdf9;
    }

    table tbody tr:last-child td {
      border-bottom: none;
    }

    .action-cell {
      display: flex;
      align-items: center;
      gap: 6px;
      font-size: 12px;
    }

    .btn-edit,
    .btn-hapus {
      background: none;
      border: none;
      color: #2563eb;
      cursor: pointer;
      font-weight: 600;
      padding: 0;
      text-decoration: none;
    }

    .btn-hapus {
      color: #10b981;
    }

    .btn-edit:hover,
    .btn-hapus:hover {
      text-decoration: underline;
    }

    .sep {
      color: #d1d5db;
    }

    .back-btn {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      color: #0d9488;
      text-decoration: none;
      font-weight: 600;
      font-size: 13px;
      margin-bottom: 20px;
    }

    .back-btn:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="app">
    <!-- Hamburger -->
    <button id="hamburger-menu" class="hamburger-menu" aria-label="Toggle Menu">
      <span class="hamburger-line"></span>
      <span class="hamburger-line"></span>
      <span class="hamburger-line"></span>
    </button>

    @include('includes.sidebar')

    <main class="main">
      <div class="page-wrapper">

        <!-- Back Button -->
        <a href="{{ route('pemasukan.pilih') }}" class="back-btn">
          <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
          Kembali ke Pilihan Input
        </a>

        <!-- Page Hero -->
        @php
          $totalIncome  = \Illuminate\Support\Facades\DB::table('pemasukan')->where('is_confirmed',1)->whereNull('deleted_at')->sum('nominal');
          $totalExpense = \Illuminate\Support\Facades\DB::table('pengeluaran')->where('is_confirmed',1)->whereNull('deleted_at')->sum('nominal');
          $saldo = $totalIncome - $totalExpense;
        @endphp
        <div class="page-hero">
          <div class="page-hero-info">
            <h1>Input Pemasukan Manual</h1>
            <p>Catat pemasukan baru secara manual tanpa perlu foto bukti.</p>
          </div>
          <div class="hero-balance-badge">
            <div class="badge-label">Saldo Tersedia</div>
            <div class="badge-value">Rp {{ number_format($saldo, 0, ',', '.') }}</div>
          </div>
        </div>

        <!-- Form Card -->
        <div class="form-card">
          <div class="form-card-header">
            <div class="header-icon">
              <svg viewBox="0 0 24 24" stroke-width="1.8"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </div>
            <div>
              <h2>Form Pemasukan Manual Baru</h2>
              <p>Semua field bertanda <span style="color:#10b981;font-weight:700;">●</span> wajib diisi</p>
            </div>
          </div>

          <div class="form-card-body">
            @if ($errors->any())
              <div style="background-color: #d1fae5; border: 1.5px solid #a7f3d0; border-radius: 12px; padding: 16px; margin-bottom: 20px; color: #0f766e; font-size: 13px;">
                <strong style="display: block; margin-bottom: 8px;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15" style="display:inline-block;vertical-align:middle;margin-right:3px;"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"/><path d="M12 9v4M12 17h.01"/></svg> Gagal Menyimpan:</strong>
                <ul style="margin: 0; padding-left: 20px;">
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            <!-- Saldo info bar -->
            <div class="saldo-info">
              <div class="saldo-info-text">
                Saldo saat ini: <strong>Rp {{ number_format($saldo, 0, ',', '.') }}</strong>.
              </div>
            </div>

            <form id="manual_form" method="POST" action="{{ route('pemasukan.store-manual') }}" enctype="multipart/form-data" class="input-form">
              @csrf
              <input type="file" id="receipt_image" name="receipt_image" accept="image/*" required hidden>
              <input type="hidden" name="input_type" value="manual">

              <!-- Upload Zone (horizontal compact) - WAJIB PERTAMA -->
              <div class="upload-row" id="upload-row" role="button" tabindex="0" aria-label="Unggah foto struk" style="background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 12px; padding: 16px; display: flex; align-items: center; justify-content: space-between; cursor: pointer; margin-bottom: 20px; transition: all 0.2s;">
                <div style="display: flex; align-items: center; gap: 16px;">
                  <div style="width: 48px; height: 48px; background: #e2e8f0; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                    <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" fill="none" width="24" height="24" style="color: #64748b;"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                  </div>
                  <div style="display: flex; flex-direction: column; gap: 4px;">
                    <strong id="upload-filename" style="font-size: 14px; color: #334155;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14" style="display:inline-block;vertical-align:middle;margin-right:3px;"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg> Upload Foto Struk Dulu <span style="color:#ef4444">*</span></strong>
                    <span style="font-size: 12px; color: #64748b;">Maksimal 5MB. Format JPG, PNG, WEBP.</span>
                  </div>
                </div>
                <button type="button" class="upload-row-btn" onclick="document.getElementById('receipt_image').click()" style="background: #0f766e; color: #fff; border: none; padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 12px; cursor: pointer;">Pilih File</button>
              </div>

              <!-- Preview Foto Struk -->
              <div id="preview-container" style="display:none; margin-bottom:20px; background: linear-gradient(135deg, #f0fdf9 0%, #ecfdf5 100%); border: 2px solid #a5e8e3; border-radius: 14px; padding: 16px; text-align: center;">
                <div style="font-size: 12px; font-weight: 700; color: #0f766e; margin-bottom: 12px;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="15" height="15" style="display:inline-block;vertical-align:middle;margin-right:3px;"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg> Foto Struk Tersimpan</div>
                <img id="preview-image" src="" alt="Preview Struk" style="max-width: 100%; max-height: 300px; border-radius: 10px; box-shadow: 0 4px 12px rgba(13, 148, 136, 0.15);">
                <div style="margin-top: 12px; display: flex; gap: 10px;">
                  <button type="button" class="reset-btn" onclick="clearPreview()" style="flex: 1; border-color: #0d9488; color: #0d9488;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13" style="display:inline-block;vertical-align:middle;margin-right:3px;"><polyline points="3 6 5 6 21 6"/><path d="m19 6-.867 12.142A2 2 0 0 1 16.138 20H7.862a2 2 0 0 1-1.995-1.858L5 6"/><path d="M10 11v6M14 11v6M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg> Hapus Foto</button>
                </div>
              </div>

              <!-- Form overlay blocker -->
              <div id="form-blocker" style="position:relative;">
                <div id="form-overlay" style="position:absolute;inset:0;background:rgba(255,255,255,0.85);z-index:10;border-radius:12px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:12px;backdrop-filter:blur(2px);">
                  <div style="margin-bottom:8px;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="56" height="56" style="display:inline-block;vertical-align:middle;opacity:0.3;"><path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z"/><path d="M16 8H8M16 12H8M12 16H8"/></svg></div>
                  <div style="font-size:14px;font-weight:700;color:#0f766e;text-align:center;">Upload Foto Struk / Kwitansi Terlebih Dahulu</div>
                  <div style="font-size:12px;color:#64748b;text-align:center;">Form akan terbuka setelah foto struk berhasil diunggah.</div>
                  <button type="button" onclick="document.getElementById('receipt_image').click()" style="background:linear-gradient(135deg,#0d9488,#059669);color:#fff;border:none;border-radius:10px;padding:10px 20px;font-size:13px;font-weight:700;cursor:pointer;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14" style="display:inline-block;vertical-align:middle;margin-right:3px;"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg> Pilih File Struk</button>
                </div>

              <!-- Tanggal & Kategori (Fixed) -->
              <div class="form-grid">
                <div class="form-group">
                  <label class="form-label" for="tanggal">
                    <span class="required-dot">●</span> Tanggal Transaksi (berlaku untuk semua baris)
                  </label>
                  <input type="date" class="form-input" id="tanggal" name="tanggal" value="{{ date('Y-m-d') }}" required />
                </div>

                <div class="form-group">
                  <label class="form-label" for="kategori_pemasukan">
                    <span class="required-dot">●</span> Kategori Default Pemasukan
                  </label>
                  <select class="form-input" id="kategori_pemasukan" name="kategori_pemasukan" required>
                    <option value="">— Pilih Kategori —</option>
                    @foreach($jenis as $j)
                      <option value="{{ $j->id }}">{{ $j->nama }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <!-- Item rows: Keterangan + Nominal -->
              <div id="items-container">
                <div class="form-grid item-row" style="position: relative; padding-bottom: 20px; margin-bottom: 20px; border-bottom: 1px dashed #a7f3d0;">
                  <!-- Hidden kategori sync -->
                  <input type="hidden" name="id_jenis_penerimaan[]" class="row-kategori-sync" value="">
                  <!-- Keterangan -->
                  <div class="form-group span-2">
                    <label class="form-label" for="uraian_0"><span class="required-dot">●</span> Keterangan / Uraian</label>
                    <input type="text" class="form-input uraian-input" id="uraian_0" name="uraian[]"
                           placeholder="Contoh: Penjualan, Komisi, Bonus..." maxlength="255" required />
                  </div>

                  <!-- Nominal + Kuantiti -->
                  <div class="form-group">
                    <label class="form-label" for="nominal_0">
                      <span class="required-dot">●</span> Nominal (IDR)
                    </label>
                    <div class="nominal-qty-group">
                      <div class="nominal-wrapper">
                        <span class="nominal-prefix">Rp</span>
                        <input type="number" class="form-input with-prefix nominal-input" id="nominal_0"
                               name="nominal[]" placeholder="0" min="1" required />
                      </div>
                      <div class="qty-wrapper">
                        <span class="qty-label">Qty</span>
                        <input type="number" class="qty-input kuantiti-input" id="kuantiti_0"
                               name="kuantiti[]" placeholder="1" min="1" value="1" required />
                      </div>
                    </div>
                  </div>
                  
                  <button type="button" class="btn-hapus-baris" onclick="hapusBaris(this)" style="display: none; position: absolute; top: 0; right: 0;" aria-label="Hapus Baris"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="12" height="12" style="display:inline-block;vertical-align:middle;"><path d="M18 6 6 18M6 6l12 12"/></svg></button>
                </div>
              </div>

              <!-- Total Section -->
              <div class="form-group span-2" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.08) 0%, rgba(13, 148, 136, 0.04) 100%); border: 2px solid #a7f3d0; border-radius: 12px; padding: 16px 18px; margin-top: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px;">
                  <span style="font-size: 13px; font-weight: 700; color: #0f766e; text-transform: uppercase; letter-spacing: 0.5px;">Total Pemasukan</span>
                  <div style="display: flex; align-items: center; gap: 6px;">
                    <span style="font-size: 12px; font-weight: 600; color: #10b981;">Rp</span>
                    <span id="total-nominal" style="font-size: 18px; font-weight: 800; color: #10b981;">0</span>
                  </div>
                </div>
              </div>
              
              <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                <button type="button" class="reset-btn" onclick="tambahBarisManual()" style="flex: 1; border-style: dashed; color: #0d9488; border-color: #a7f3d0;">
                  <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" width="16" height="16"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                  Tambah Baris Item
                </button>
                <button type="button" class="reset-btn" onclick="tambahKategoriManual()" style="flex: 1; border-style: dashed; color: #0d9488; border-color: #a7f3d0;">
                  <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" width="16" height="16"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><line x1="14" y1="17" x2="21" y2="17"/><line x1="17" y1="14" x2="17" y2="21"/></svg>
                  Tambah Kategori Lain
                </button>
              </div>

              </div><!-- /form-blocker -->

              <!-- Actions -->
              <div class="form-actions">
                <button type="reset" class="reset-btn">
                  <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" width="16" height="16"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                  Reset
                </button>
                <button id="save-btn" class="save-btn" type="submit">
                  <svg viewBox="0 0 24 24" stroke-width="1.8"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                  Simpan Pemasukan
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Table Section -->
        <div class="table-section">
          <div class="table-section-header">
            <h3><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15" style="display:inline-block;vertical-align:middle;margin-right:4px;"><rect x="9" y="2" width="6" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M12 11h4M12 16h4M8 11h.01M8 16h.01"/></svg> Riwayat Pemasukan</h3>
            <span class="entry-count">{{ $incomes->total() }} entri</span>
          </div>
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th style="width:5%">#</th>
                  <th style="width:18%">Tanggal</th>
                  <th style="width:22%">Kategori</th>
                  <th style="width:22%">Keterangan</th>
                  <th style="width:18%">Jumlah</th>
                  <th style="width:15%">Aksi</th>
                </tr>
              </thead>
              <tbody>
                @forelse($incomes as $i => $income)
                  <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($income->tanggal)->format('d M Y') }}</td>
                    <td>{{ $income->kategori }}</td>
                    <td>
                      @if(!empty($income->uraian ?? ''))
                        <span style="color:#374151;">{{ $income->uraian }}</span>
                      @else
                        <span style="color:#94a3b8;font-style:italic;">—</span>
                      @endif
                    </td>
                    <td>
                      <span style="font-weight:700;color:#10b981;">+ Rp {{ number_format($income->jumlah, 0, ',', '.') }}</span>
                    </td>
                    <td>
                      <div class="action-cell">
                        <a href="{{ route('pemasukan.edit', $income->id) }}" class="btn-edit">Edit</a>
                        <span class="sep">|</span>
                        <form method="POST" action="{{ route('pemasukan.delete', $income->id) }}" style="display:inline;"
                              data-confirm="soft">
                          @csrf
                          <button type="submit" class="btn-hapus">Hapus</button>
                        </form>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" style="text-align:center;padding:20px;color:#94a3b8;font-style:italic;">
                      Belum ada data pemasukan.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div style="margin-top: 20px; padding: 0 20px;">
            <style>
              .pagination { display: flex; list-style: none; padding: 0; margin: 0; justify-content: flex-end; gap: 4px; }
              .page-item .page-link { display: block; padding: 6px 12px; border: 1px solid #cbd5e1; border-radius: 6px; color: #0f766e; text-decoration: none; font-size: 13px; background: #fff; transition: all 0.2s ease; }
              .page-item .page-link:hover { background: #f1f5f9; }
              .page-item.active .page-link { background: #0d9488; color: #fff; border-color: #0d9488; }
              .page-item.disabled .page-link { color: #94a3b8; background: #f8fafc; cursor: not-allowed; border-color: #e2e8f0; }
            </style>
            {{ $incomes->links('pagination::bootstrap-4') }}
          </div>
          @if($incomes->total() > 0)
            <div class="laporan-tip">
              Lihat ringkasan lengkap dan ekspor data di halaman <a href="{{ route('laporan') }}">Laporan</a>.
            </div>
          @endif
        </div>

      </div>
    </main>
  </div>

  <script>
    // Upload filename preview
    const receiptInput = document.getElementById('receipt_image');
    const formOverlay = document.getElementById('form-overlay');
    const previewContainer = document.getElementById('preview-container');
    const previewImage = document.getElementById('preview-image');

    receiptInput.addEventListener('change', function() {
      if (this.files[0]) {
        const file = this.files[0];
        const name = file.name;
        
        // Validate file size (5MB = 5242880 bytes)
        if (file.size > 5242880) {
          alert('Ukuran file tidak boleh lebih dari 5MB');
          this.value = '';
          previewContainer.style.display = 'none';
          formOverlay.style.display = 'flex';
          return;
        }
        
        // Update upload row status
        document.getElementById('upload-filename').innerHTML = '' + name;
        document.getElementById('upload-filename').style.color = '#16a34a';
        document.getElementById('upload-row').style.borderColor = '#16a34a';
        document.getElementById('upload-row').style.background = 'rgba(220,252,231,0.5)';
        
        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
          previewImage.src = e.target.result;
          previewContainer.style.display = 'block';
        };
        reader.readAsDataURL(file);
        
        // Hide overlay to unlock form
        formOverlay.style.display = 'none';
      } else {
        document.getElementById('upload-filename').innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14" style="display:inline-block;vertical-align:middle;margin-right:3px;"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg> Upload Foto Struk Dulu <span style="color:#ef4444">*</span>';
        document.getElementById('upload-filename').style.color = '';
        document.getElementById('upload-row').style.borderColor = '';
        document.getElementById('upload-row').style.background = '';
        previewContainer.style.display = 'none';
        formOverlay.style.display = 'flex';
      }
    });

    function clearPreview() {
      receiptInput.value = '';
      previewContainer.style.display = 'none';
      document.getElementById('upload-filename').innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14" style="display:inline-block;vertical-align:middle;margin-right:3px;"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg> Upload Foto Struk Dulu <span style="color:#ef4444">*</span>';
      document.getElementById('upload-filename').style.color = '';
      document.getElementById('upload-row').style.borderColor = '';
      document.getElementById('upload-row').style.background = '';
      formOverlay.style.display = 'flex';
    }

    document.getElementById('upload-row').addEventListener('click', function(e) {
      if (!e.target.classList.contains('upload-row-btn')) {
        document.getElementById('receipt_image').click();
      }
    });
    // Data kategori
    const jenisDataManual = {!! json_encode($jenis) !!};

    document.addEventListener('DOMContentLoaded', function() {
      syncKategoriSync();
      updateTotalManual();
    });

    function syncKategoriSync() {
      const currentVal = document.getElementById('kategori_pemasukan').value;
      document.querySelectorAll('.row-kategori-sync').forEach(el => {
        el.value = currentVal;
      });
    }

    document.getElementById('kategori_pemasukan').addEventListener('change', syncKategoriSync);

    function tambahBarisManual() {
      const container = document.getElementById('items-container');
      const rowCount = container.querySelectorAll('.item-row').length;
      const currentVal = document.getElementById('kategori_pemasukan').value;
      
      const newRow = document.createElement('div');
      newRow.className = 'form-grid item-row';
      newRow.style.cssText = 'position: relative; padding-bottom: 20px; margin-bottom: 20px; border-bottom: 1px dashed #a7f3d0;';
      
      newRow.innerHTML = `
        <input type="hidden" name="id_jenis_penerimaan[]" class="row-kategori-sync" value="${currentVal}">
        <div class="form-group span-2">
          <label class="form-label" for="uraian_${rowCount}"><span class="required-dot">●</span> Keterangan / Uraian</label>
          <input type="text" class="form-input uraian-input" id="uraian_${rowCount}" name="uraian[]"
                 placeholder="Contoh: Penjualan, Komisi, Bonus..." maxlength="255" required />
        </div>
        <div class="form-group">
          <label class="form-label" for="nominal_${rowCount}">
            <span class="required-dot">●</span> Nominal (IDR)
          </label>
          <div class="nominal-qty-group">
            <div class="nominal-wrapper">
              <span class="nominal-prefix">Rp</span>
              <input type="number" class="form-input with-prefix nominal-input" id="nominal_${rowCount}"
                     name="nominal[]" placeholder="0" min="1" required />
            </div>
            <div class="qty-wrapper">
              <span class="qty-label">Qty</span>
              <input type="number" class="qty-input kuantiti-input" id="kuantiti_${rowCount}"
                     name="kuantiti[]" placeholder="1" min="1" value="1" required />
            </div>
          </div>
        </div>
        <button type="button" class="btn-hapus-baris" onclick="hapusBaris(this)" style="position: absolute; top: 0; right: 0;" aria-label="Hapus Baris"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="12" height="12" style="display:inline-block;vertical-align:middle;"><path d="M18 6 6 18M6 6l12 12"/></svg></button>
      `;
      
      container.appendChild(newRow);
      attachNominalListeners();
      updateTotalManual();
    }

    function tambahKategoriManual() {
      const container = document.getElementById('items-container');
      const rowCount = container.querySelectorAll('.item-row').length;
      
      const newRow = document.createElement('div');
      newRow.className = 'form-grid item-row';
      newRow.style.cssText = 'position: relative; padding-bottom: 20px; margin-bottom: 20px; border-bottom: 1px dashed #a7f3d0;';
      
      let kategoriOptions = '<option value="">— Pilih Kategori —</option>';
      jenisDataManual.forEach(j => {
        kategoriOptions += `<option value="${j.id}">${j.nama}</option>`;
      });
      
      newRow.innerHTML = `
        <div class="form-group">
          <label class="form-label" for="kategori_${rowCount}"><span class="required-dot">●</span> Kategori</label>
          <select class="form-input" id="kategori_${rowCount}" name="id_jenis_penerimaan[]" required>
            ${kategoriOptions}
          </select>
        </div>
        <div class="form-group">
          <label class="form-label" for="uraian_${rowCount}"><span class="required-dot">●</span> Keterangan / Uraian</label>
          <input type="text" class="form-input uraian-input" id="uraian_${rowCount}" name="uraian[]"
                 placeholder="Contoh: Penjualan, Komisi, Bonus..." maxlength="255" required />
        </div>
        <div class="form-group">
          <label class="form-label" for="nominal_${rowCount}">
            <span class="required-dot">●</span> Nominal (IDR)
          </label>
          <div class="nominal-qty-group">
            <div class="nominal-wrapper">
              <span class="nominal-prefix">Rp</span>
              <input type="number" class="form-input with-prefix nominal-input" id="nominal_${rowCount}"
                     name="nominal[]" placeholder="0" min="1" required />
            </div>
            <div class="qty-wrapper">
              <span class="qty-label">Qty</span>
              <input type="number" class="qty-input kuantiti-input" id="kuantiti_${rowCount}"
                     name="kuantiti[]" placeholder="1" min="1" value="1" required />
            </div>
          </div>
        </div>
        <button type="button" class="btn-hapus-baris" onclick="hapusBaris(this)" style="position: absolute; top: 0; right: 0;" aria-label="Hapus Baris"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="12" height="12" style="display:inline-block;vertical-align:middle;"><path d="M18 6 6 18M6 6l12 12"/></svg></button>
      `;
      
      container.appendChild(newRow);
      attachNominalListeners();
      updateTotalManual();
    }

    function hapusBaris(btn) {
      btn.closest('.item-row').remove();
      updateTotalManual();
    }

    function attachNominalListeners() {
      document.querySelectorAll('.nominal-input, .kuantiti-input').forEach(el => {
        el.addEventListener('input', updateTotalManual);
      });
    }

    function updateTotalManual() {
      let total = 0;
      document.querySelectorAll('.item-row').forEach(row => {
        const nominal = parseFloat(row.querySelector('.nominal-input')?.value) || 0;
        const kuantiti = parseFloat(row.querySelector('.kuantiti-input')?.value) || 1;
        total += nominal * kuantiti;
      });
      
      document.getElementById('total-nominal').textContent = total.toLocaleString('id-ID');
      
      const rows = document.querySelectorAll('.item-row');
      rows.forEach((row, index) => {
        const btn = row.querySelector('.btn-hapus-baris');
        if (btn) {
          btn.style.display = rows.length > 1 ? 'block' : 'none';
        }
      });
    }

    attachNominalListeners();
  </script>
</body>
</html>
