<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
  <title>Pengeluaran — LabMoneyLens</title>
  <?php echo app('Illuminate\Foundation\Vite')(['resources/css/style.css','resources/css/welcome.css','resources/js/script.js']); ?>
  <script>window.receiptParseUrl = "<?php echo e(route('receipt.parse')); ?>";</script>

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

    /* ── Page Header ── */
    .page-hero {
      background: linear-gradient(135deg, #991b1b 0%, #dc2626 55%, #ef4444 100%);
      border-radius: 20px;
      padding: 28px 32px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 20px;
      box-shadow: 0 8px 24px rgba(220, 38, 38, 0.25);
    }

    .page-hero-info h1 {
      font-size: 24px;
      font-weight: 800;
      color: #fff;
      letter-spacing: -0.3px;
    }

    .page-hero-info p {
      font-size: 13px;
      color: rgba(255,255,255,0.82);
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

    .hero-balance-badge .badge-warning {
      font-size: 10px;
      color: rgba(255,255,255,0.75);
      margin-top: 4px;
    }

    /* ── Form Card ── */
    .form-card {
      background: linear-gradient(135deg, #ffffff 0%, #fff9f9 100%);
      border-radius: 20px;
      border: 2px solid #fecaca;
      box-shadow: 0 4px 16px rgba(239, 68, 68, 0.08);
      overflow: hidden;
    }

    .form-card-header {
      background: linear-gradient(90deg, #fff5f5 0%, #fee2e2 100%);
      border-bottom: 2px solid #fecaca;
      padding: 18px 28px;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .form-card-header .header-icon {
      width: 40px;
      height: 40px;
      background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3);
    }

    .form-card-header .header-icon svg {
      width: 20px;
      height: 20px;
      stroke: #fff;
      fill: none;
    }

    .form-card-header h2 {
      font-size: 16px;
      font-weight: 800;
      color: #991b1b;
    }

    .form-card-header p {
      font-size: 12px;
      color: #dc2626;
      opacity: 0.8;
      margin-top: 2px;
    }

    .form-card-body {
      padding: 28px;
    }

    /* ── Upload zone ── */
    .upload-row {
      display: flex;
      align-items: center;
      gap: 16px;
      background: linear-gradient(135deg, rgba(254,226,226,0.4) 0%, transparent 100%);
      border: 2px dashed rgba(239, 68, 68, 0.35);
      border-radius: 14px;
      padding: 16px 20px;
      cursor: pointer;
      transition: all 0.25s ease;
      margin-bottom: 24px;
    }

    .upload-row:hover {
      border-color: #ef4444;
      background: rgba(254,226,226,0.6);
    }

    .upload-row-icon {
      width: 44px;
      height: 44px;
      background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .upload-row-icon svg {
      width: 22px;
      height: 22px;
      stroke: #ef4444;
      fill: none;
    }

    .upload-row-text {
      flex: 1;
    }

    .upload-row-text strong {
      font-size: 13px;
      font-weight: 700;
      color: #991b1b;
      display: block;
    }

    .upload-row-text span {
      font-size: 11px;
      color: #64748b;
      margin-top: 2px;
      display: block;
    }

    .upload-row-btn {
      background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
      color: #fff;
      border: none;
      border-radius: 10px;
      padding: 8px 18px;
      font-size: 12px;
      font-weight: 700;
      cursor: pointer;
      font-family: inherit;
      transition: all 0.2s ease;
      white-space: nowrap;
    }

    .upload-row-btn:hover {
      background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
      box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
      transform: translateY(-1px);
    }

    /* ── Form Grid ── */
    .form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 18px;
    }

    .form-grid .span-2 { grid-column: span 2; }

    .form-group {
      display: flex;
      flex-direction: column;
      gap: 7px;
    }

    .form-label {
      font-size: 11px;
      font-weight: 700;
      color: #991b1b;
      text-transform: uppercase;
      letter-spacing: 0.6px;
      display: flex;
      align-items: center;
      gap: 5px;
    }

    .form-label .required-dot {
      width: 5px;
      height: 5px;
      background: #ef4444;
      border-radius: 50%;
    }

    .form-input {
      padding: 11px 14px;
      border: 2px solid #fecaca;
      border-radius: 10px;
      font-size: 13px;
      font-family: inherit;
      color: #7f1d1d;
      background: #ffffff;
      outline: none;
      width: 100%;
      transition: all 0.2s ease;
    }

    .form-input:hover { border-color: #fca5a5; }
    .form-input:focus {
      border-color: #ef4444;
      box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
    }

    select.form-input {
      appearance: none;
      -webkit-appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23ef4444' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 12px center;
      padding-right: 34px;
    }

    .nominal-wrapper {
      position: relative;
    }

    .nominal-prefix {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 13px;
      font-weight: 700;
      color: #ef4444;
      pointer-events: none;
    }

    .form-input.with-prefix {
      padding-left: 38px;
    }

    /* ── Saldo info bar ── */
    .saldo-info {
      background: linear-gradient(135deg, #fff1f2 0%, #fff5f5 100%);
      border: 1.5px solid #fecaca;
      border-radius: 12px;
      padding: 14px 18px;
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 20px;
    }

    .saldo-info-icon {
      font-size: 22px;
    }

    .saldo-info-text {
      flex: 1;
      font-size: 12px;
      color: #7f1d1d;
    }

    .saldo-info-text strong {
      color: #dc2626;
      font-size: 14px;
    }

    /* ── Form Actions ── */
    .form-actions {
      display: flex;
      gap: 12px;
      margin-top: 24px;
      padding-top: 20px;
      border-top: 2px solid #fff1f2;
    }

    .save-btn {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
      color: #ffffff;
      border: none;
      border-radius: 12px;
      padding: 14px 24px;
      font-size: 14px;
      font-weight: 700;
      cursor: pointer;
      font-family: inherit;
      letter-spacing: 0.3px;
      transition: all 0.2s ease;
      box-shadow: 0 4px 14px rgba(239, 68, 68, 0.3);
    }

    .save-btn:hover {
      background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
      box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
      transform: translateY(-2px);
    }

    .save-btn:active { transform: translateY(0); }

    .save-btn svg {
      width: 18px;
      height: 18px;
      stroke: currentColor;
      fill: none;
    }

    .reset-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      background: transparent;
      color: #64748b;
      border: 2px solid #e2e8f0;
      border-radius: 12px;
      padding: 14px 20px;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      font-family: inherit;
      transition: all 0.2s ease;
    }

    .reset-btn:hover {
      border-color: #cbd5e1;
      background: #f8fafc;
      color: #475569;
    }

    /* ── Table Section ── */
    .table-section {
      background: linear-gradient(135deg, #ffffff 0%, #fff9f9 100%);
      border-radius: 20px;
      border: 2px solid #fecaca;
      box-shadow: 0 4px 16px rgba(239, 68, 68, 0.07);
      overflow: hidden;
    }

    .table-section-header {
      background: linear-gradient(90deg, #fff5f5 0%, #fee2e2 100%);
      border-bottom: 2px solid #fecaca;
      padding: 16px 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
    }

    .table-section-header h3 {
      font-size: 14px;
      font-weight: 800;
      color: #991b1b;
      text-transform: uppercase;
      letter-spacing: 0.6px;
    }

    .table-section-header .entry-count {
      font-size: 12px;
      font-weight: 600;
      color: #dc2626;
      background: rgba(239, 68, 68, 0.1);
      padding: 4px 12px;
      border-radius: 20px;
    }

    .table-wrap { overflow-x: auto; }

    thead tr { background: linear-gradient(90deg, #991b1b 0%, #dc2626 100%) !important; }

    .laporan-tip {
      text-align: center;
      font-size: 12px;
      color: #64748b;
      padding: 16px;
    }

    .laporan-tip a {
      color: #dc2626;
      font-weight: 700;
      text-decoration: none;
    }

    .laporan-tip a:hover { text-decoration: underline; }

    /* ── Responsive ── */
    @media (max-width: 1024px) {
      .hamburger-menu { display: flex !important; }
      .sidebar {
        position: fixed !important; left: 0 !important; top: 0 !important;
        width: 220px !important; height: 100vh !important;
        transform: translateX(-100%) !important;
        transition: transform 0.3s ease !important; z-index: 999 !important;
      }
      .sidebar.active { transform: translateX(0) !important; }
    }

    @media (max-width: 700px) {
      .form-grid { grid-template-columns: 1fr; }
      .form-grid .span-2 { grid-column: span 1; }
      .page-hero { flex-direction: column; align-items: flex-start; }
      .hero-balance-badge { align-self: stretch; }
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

    <!-- Overlay -->
    <?php echo $__env->make('includes.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Main Content -->
    <main class="main">
      <div class="page-wrapper">

        <!-- Page Hero -->
        <?php
          $totalIncome  = \Illuminate\Support\Facades\DB::table('pemasukan')->where('is_confirmed',1)->whereNull('deleted_at')->sum('nominal');
          $totalExpense = \Illuminate\Support\Facades\DB::table('pengeluaran')->where('is_confirmed',1)->whereNull('deleted_at')->sum('nominal');
          $saldo = $totalIncome - $totalExpense;
        ?>
        <div class="page-hero">
          <div class="page-hero-info">
            <h1>🧾 Input Pengeluaran</h1>
            <p>Catat pengeluaran baru secara manual atau scan foto struk belanja.<br>Pengeluaran tidak boleh melebihi saldo yang tersedia.</p>
          </div>
          <div class="hero-balance-badge">
            <div class="badge-label">Saldo Tersedia</div>
            <div class="badge-value">Rp <?php echo e(number_format($saldo, 0, ',', '.')); ?></div>
            <div class="badge-warning">Maks. pengeluaran yang bisa diinput</div>
          </div>
        </div>

        <!-- Form Card -->
        <div class="form-card">
          <div class="form-card-header">
            <div class="header-icon">
              <svg viewBox="0 0 24 24" stroke-width="1.8"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </div>
            <div>
              <h2>Form Pengeluaran Baru</h2>
              <p>Semua field bertanda <span style="color:#ef4444;font-weight:700;">●</span> wajib diisi</p>
            </div>
          </div>

          <div class="form-card-body">
            <?php if($errors->any()): ?>
              <div style="background-color: #fee2e2; border: 1.5px solid #fecaca; border-radius: 12px; padding: 16px; margin-bottom: 20px; color: #991b1b; font-size: 13px;">
                <strong style="display: block; margin-bottom: 8px;">⚠️ Gagal Menyimpan:</strong>
                <ul style="margin: 0; padding-left: 20px;">
                  <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
              </div>
            <?php endif; ?>

            <!-- Saldo warning bar -->
            <div class="saldo-info">
              <span class="saldo-info-icon">⚠️</span>
              <div class="saldo-info-text">
                Saldo saat ini: <strong>Rp <?php echo e(number_format($saldo, 0, ',', '.')); ?></strong>.
                Pengeluaran tidak boleh melebihi nominal ini.
              </div>
            </div>

            <!-- Upload Zone - WAJIB PERTAMA -->
            <div class="upload-row" id="upload-row" role="button" tabindex="0" aria-label="Unggah foto struk">
              <div class="upload-row-icon">
                <svg viewBox="0 0 24 24" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
              </div>
              <div class="upload-row-text">
                <strong id="upload-filename">📎 Upload Foto Struk Dulu <span style="color:#ef4444">*</span></strong>
                <span>Struk wajib diunggah sebelum mengisi form. Format: JPG, PNG, WEBP — Maks. 5MB.</span>
              </div>
              <button type="button" class="upload-row-btn" onclick="document.getElementById('receipt_image').click()">Pilih File</button>
            </div>

            <!-- Preview Foto Struk -->
            <div id="preview-container" style="display:none; margin-bottom:20px; background: linear-gradient(135deg, #ffffff 0%, #fff9f9 100%); border: 2px solid #fecaca; border-radius: 14px; padding: 16px; text-align: center;">
              <div style="font-size: 12px; font-weight: 700; color: #991b1b; margin-bottom: 12px;">✅ Foto Struk Tersimpan</div>
              <img id="preview-image" src="" alt="Preview Struk" style="max-width: 100%; max-height: 300px; border-radius: 10px; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.15);">
              <div style="margin-top: 12px; display: flex; gap: 10px;">
                <button type="button" class="reset-btn" onclick="clearPreview()" style="flex: 1; border-color: #ef4444; color: #ef4444;">🗑 Hapus Foto</button>
              </div>
            </div>

            <div id="upload-required-notice" style="display:none; background:#fee2e2; border:1.5px solid #fecaca; border-radius:10px; padding:10px 16px; margin-bottom:16px; font-size:12px; color:#991b1b; font-weight:600;">
              ⚠️ Foto struk harus diunggah terlebih dahulu sebelum form dapat diisi.
            </div>

            <form id="receipt_form" method="POST" action="<?php echo e(route('pengeluaran.store')); ?>" enctype="multipart/form-data" class="input-form">
              <?php echo csrf_field(); ?>
              <input type="file" id="receipt_image" name="receipt_image" accept="image/*" hidden>
              <input type="hidden" id="receipt_type" name="type" value="pengeluaran">

              <!-- Form overlay blocker -->
              <div id="form-blocker" style="position:relative;">
                <div id="form-overlay" style="position:absolute;inset:0;background:rgba(255,255,255,0.85);z-index:10;border-radius:12px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:12px;backdrop-filter:blur(2px);">
                  <div style="font-size:48px;">🧾</div>
                  <div style="font-size:14px;font-weight:700;color:#991b1b;text-align:center;">Upload Foto Struk Terlebih Dahulu</div>
                  <div style="font-size:12px;color:#64748b;text-align:center;">Form akan terbuka setelah foto struk berhasil diunggah.</div>
                  <button type="button" onclick="document.getElementById('receipt_image').click()" style="background:linear-gradient(135deg,#ef4444,#dc2626);color:#fff;border:none;border-radius:10px;padding:10px 20px;font-size:13px;font-weight:700;cursor:pointer;">📎 Pilih File Struk</button>
                </div>

              <!-- Tanggal & Kategori (Fixed) -->
              <div class="form-grid">
                <div class="form-group">
                  <label class="form-label" for="tanggal">
                    <span class="required-dot"></span> Tanggal Transaksi (berlaku untuk semua baris)
                  </label>
                  <input type="date" class="form-input" id="tanggal" name="tanggal" value="<?php echo e(date('Y-m-d')); ?>" required />
                </div>

                <div class="form-group">
                  <label class="form-label" for="kategori_pengeluaran">
                    <span class="required-dot"></span> Kategori Default Pengeluaran
                  </label>
                  <select class="form-input" id="kategori_pengeluaran">
                    <option value="">— Pilih Kategori —</option>
                    <?php $__currentLoopData = $jenis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <option value="<?php echo e($j->id); ?>"><?php echo e($j->nama); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </select>
                </div>
              </div>

              <!-- Item rows: Keterangan + Nominal -->
              <div id="items-container">
                <div class="form-grid item-row" style="position: relative; padding-bottom: 20px; margin-bottom: 20px; border-bottom: 1px dashed #fecaca;">
                  <!-- Hidden kategori sync -->
                  <input type="hidden" name="id_jenis_pengeluaran[]" class="row-kategori-sync">
                  <!-- Keterangan -->
                  <div class="form-group span-2">
                    <label class="form-label" for="uraian_0">Keterangan / Uraian</label>
                    <input type="text" class="form-input uraian-input" id="uraian_0" name="uraian[]"
                           placeholder="Contoh: Pembelian reagen kimia, ATK..." maxlength="255" />
                  </div>

                  <!-- Nominal -->
                  <div class="form-group">
                    <label class="form-label" for="nominal_0">
                      <span class="required-dot"></span> Nominal (IDR)
                    </label>
                    <div class="nominal-wrapper">
                      <span class="nominal-prefix">Rp</span>
                      <input type="number" class="form-input with-prefix nominal-input" id="nominal_0"
                             name="nominal[]" placeholder="0" min="1" required />
                    </div>
                  </div>
                  
                  <button type="button" class="btn-hapus-baris" onclick="hapusBaris(this)" style="display: none; position: absolute; top: 0; right: 0; background: none; border: none; color: #ef4444; cursor: pointer; font-size: 18px;" aria-label="Hapus Baris">✖</button>
                </div>
              </div>

              <!-- Total Section -->
              <div class="form-group span-2" style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.08) 0%, rgba(220, 38, 38, 0.04) 100%); border: 2px solid #fecaca; border-radius: 12px; padding: 16px 18px; margin-top: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px;">
                  <span style="font-size: 13px; font-weight: 700; color: #7f1d1d; text-transform: uppercase; letter-spacing: 0.5px;">Total Pengeluaran</span>
                  <div style="display: flex; align-items: center; gap: 6px;">
                    <span style="font-size: 12px; font-weight: 600; color: #dc2626;">Rp</span>
                    <span id="total-nominal" style="font-size: 18px; font-weight: 800; color: #dc2626;">0</span>
                  </div>
                </div>
              </div>
              
              <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                <button type="button" class="reset-btn" onclick="tambahBaris()" style="flex: 1; border-style: dashed; color: #dc2626; border-color: #fca5a5;">
                  <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" width="16" height="16"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                  Tambah Baris Item
                </button>
                <button type="button" class="reset-btn" onclick="tambahKategori()" style="flex: 1; border-style: dashed; color: #b45309; border-color: #fcd34d;">
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
                  Simpan Pengeluaran
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Table Section -->
        <div class="table-section">
          <div class="table-section-header">
            <h3>📋 Riwayat Pengeluaran</h3>
            <span class="entry-count"><?php echo e(count($expenses)); ?> entri</span>
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
                <?php $__empty_1 = true; $__currentLoopData = $expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                  <tr>
                    <td><?php echo e($i + 1); ?></td>
                    <td><?php echo e(\Carbon\Carbon::parse($expense->tanggal)->format('d M Y')); ?></td>
                    <td><?php echo e($expense->kategori); ?></td>
                    <td>
                      <?php if(!empty($expense->uraian ?? '')): ?>
                        <span style="color:#374151;"><?php echo e($expense->uraian); ?></span>
                      <?php else: ?>
                        <span style="color:#94a3b8;font-style:italic;">—</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <span style="font-weight:700;color:#dc2626;">- Rp <?php echo e(number_format($expense->jumlah, 0, ',', '.')); ?></span>
                    </td>
                    <td>
                      <div class="action-cell">
                        <a href="<?php echo e(route('pengeluaran.edit', $expense->id)); ?>" class="btn-edit">Edit</a>
                        <span class="sep">|</span>
                        <form method="POST" action="<?php echo e(route('pengeluaran.delete', $expense->id)); ?>" style="display:inline;"
                              data-confirm="soft">
                          <?php echo csrf_field(); ?>
                          <button type="submit" class="btn-hapus">Hapus</button>
                        </form>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                  <tr>
                    <td colspan="6" style="text-align:center;padding:32px;color:#94a3b8;font-style:italic;">
                      Belum ada data pengeluaran.
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
          <?php if(count($expenses) > 0): ?>
            <div class="laporan-tip">
              Lihat ringkasan lengkap dan ekspor data di halaman <a href="<?php echo e(route('laporan')); ?>">Laporan</a>.
            </div>
          <?php endif; ?>
        </div>

      </div>
    </main>
  </div>

  <!-- Modal Notifikasi -->
  <div id="custom-modal" class="custom-modal">
    <div class="modal-content">
      <div class="modal-icon" id="modal-icon"></div>
      <h2 id="modal-title"></h2>
      <p id="modal-message"></p>
      <button class="modal-btn" onclick="closeModal()">Tutup</button>
    </div>
  </div>



  <style>
    .custom-modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,.5); z-index:9999; justify-content:center; align-items:center; }
    .custom-modal.show { display:flex; }
    .modal-content { background:linear-gradient(135deg,#fff 0%,#fff5f5 100%); border-radius:20px; padding:40px; max-width:420px; width:90%; box-shadow:0 20px 60px rgba(239,68,68,.15); text-align:center; animation:slideUp .4s cubic-bezier(.34,1.56,.64,1); }
    .modal-icon { font-size:64px; margin-bottom:20px; }
    .modal-content h2 { font-size:24px; color:#1f2937; margin-bottom:12px; font-weight:700; }
    .modal-content p { font-size:14px; color:#6b7280; margin-bottom:28px; line-height:1.6; }
    .modal-btn { background:linear-gradient(135deg,#ef4444 0%,#dc2626 100%); color:#fff; border:none; padding:12px 32px; border-radius:10px; font-size:14px; font-weight:600; cursor:pointer; transition:all .3s ease; }
    .modal-btn:hover { transform:translateY(-2px); }
    @keyframes slideUp { from{transform:translateY(30px);opacity:0} to{transform:translateY(0);opacity:1} }
  </style>

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
        document.getElementById('upload-filename').textContent = '✅ ' + name;
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
        document.getElementById('upload-filename').textContent = '📎 Upload Foto Struk Dulu *';
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
      document.getElementById('upload-filename').textContent = '📎 Upload Foto Struk Dulu *';
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

    function showModal(title, message, icon='✓') {
      document.getElementById('modal-icon').textContent = icon;
      document.getElementById('modal-title').textContent = title;
      document.getElementById('modal-message').textContent = message;
      document.getElementById('custom-modal').classList.add('show');
    }
    function closeModal() { document.getElementById('custom-modal').classList.remove('show'); }
    document.getElementById('custom-modal').addEventListener('click', function(e) { if(e.target===this) closeModal(); });

    document.addEventListener('DOMContentLoaded', function() {
      <?php if(session('error')): ?>
        showModal('Gagal', '<?php echo e(session("error")); ?>', '❌');
      <?php elseif(session('success')): ?>
        showModal('Berhasil', '<?php echo e(session("success")); ?>', '✅');
      <?php endif; ?>
    });

    // Daftar kategori dari server (untuk dropdown dinamis)
    const jenisData = <?php echo json_encode($jenis, 15, 512) ?>;

    // Sync hidden kategori inputs dengan main dropdown
    function syncKategoriSync() {
      const val = document.getElementById('kategori_pengeluaran').value;
      document.querySelectorAll('.row-kategori-sync').forEach(inp => inp.value = val);
    }

    document.getElementById('kategori_pengeluaran').addEventListener('change', syncKategoriSync);

    let rowCount = 1;
    function tambahBaris() {
      const container = document.getElementById('items-container');
      const currentVal = document.getElementById('kategori_pengeluaran').value;
      const rowHtml = `
        <div class="form-grid item-row" style="position: relative; padding-bottom: 20px; margin-bottom: 20px; border-bottom: 1px dashed #fecaca; animation: slideUp 0.3s ease;">
          <input type="hidden" name="id_jenis_pengeluaran[]" class="row-kategori-sync" value="${currentVal}">
          <div class="form-group span-2">
            <label class="form-label" for="uraian_${rowCount}">Keterangan / Uraian</label>
            <input type="text" class="form-input uraian-input" id="uraian_${rowCount}" name="uraian[]" placeholder="Contoh: Pembelian reagen kimia, ATK..." maxlength="255" />
          </div>
          <div class="form-group">
            <label class="form-label" for="nominal_${rowCount}">
              <span class="required-dot"></span> Nominal (IDR)
            </label>
            <div class="nominal-wrapper">
              <span class="nominal-prefix">Rp</span>
              <input type="number" class="form-input with-prefix nominal-input" id="nominal_${rowCount}" name="nominal[]" placeholder="0" min="1" required />
            </div>
          </div>
          <button type="button" class="btn-hapus-baris" onclick="hapusBaris(this)" style="position: absolute; top: -5px; right: -5px; background: #fee2e2; border: 1px solid #fecaca; color: #ef4444; width: 28px; height: 28px; border-radius: 50%; cursor: pointer; font-size: 14px; font-weight: bold; display: flex; align-items: center; justify-content: center;" aria-label="Hapus Baris">✖</button>
        </div>
      `;
      container.insertAdjacentHTML('beforeend', rowHtml);
      rowCount++;
      updateHapusButtons();
      attachNominalListeners();
    }

    function tambahKategori() {
      const container = document.getElementById('items-container');
      let optHtml = '<option value="">— Pilih Kategori —</option>';
      jenisData.forEach(j => { optHtml += `<option value="${j.id}">${j.nama}</option>`; });
      const rowHtml = `
        <div class="form-grid item-row" style="position: relative; padding-bottom: 20px; margin-bottom: 20px; border-bottom: 1px dashed #fcd34d; animation: slideUp 0.3s ease;">
          <div class="form-group span-2" style="background:linear-gradient(135deg,rgba(251,191,36,0.08),transparent); border-radius:10px; padding:12px; border:1px dashed #fcd34d;">
            <label class="form-label" style="color:#b45309;">Kategori Pengeluaran <span style="color:#ef4444;">*</span></label>
            <select class="form-input" name="id_jenis_pengeluaran[]" required style="border-color:#fde68a;">
              ${optHtml}
            </select>
          </div>
          <div class="form-group span-2">
            <label class="form-label" for="uraian_${rowCount}">Keterangan / Uraian</label>
            <input type="text" class="form-input uraian-input" id="uraian_${rowCount}" name="uraian[]" placeholder="Contoh: Pembelian reagen kimia, ATK..." maxlength="255" />
          </div>
          <div class="form-group">
            <label class="form-label" for="nominal_${rowCount}">
              <span class="required-dot"></span> Nominal (IDR)
            </label>
            <div class="nominal-wrapper">
              <span class="nominal-prefix">Rp</span>
              <input type="number" class="form-input with-prefix nominal-input" id="nominal_${rowCount}" name="nominal[]" placeholder="0" min="1" required />
            </div>
          </div>
          <button type="button" class="btn-hapus-baris" onclick="hapusBaris(this)" style="position: absolute; top: -5px; right: -5px; background: #fef3c7; border: 1px solid #fcd34d; color: #b45309; width: 28px; height: 28px; border-radius: 50%; cursor: pointer; font-size: 14px; font-weight: bold; display: flex; align-items: center; justify-content: center;" aria-label="Hapus Baris">✖</button>
        </div>
      `;
      container.insertAdjacentHTML('beforeend', rowHtml);
      rowCount++;
      updateHapusButtons();
      attachNominalListeners();
    }

    function hapusBaris(btn) {
      const row = btn.closest('.item-row');
      row.remove();
      updateHapusButtons();
      updateTotal();
    }

    function updateHapusButtons() {
      const rows = document.querySelectorAll('.item-row');
      const btns = document.querySelectorAll('.btn-hapus-baris');
      if (rows.length > 1) {
        btns.forEach(b => b.style.display = 'flex');
      } else {
        btns.forEach(b => b.style.display = 'none');
      }
    }

    function updateTotal() {
      const nominalInputs = document.querySelectorAll('.nominal-input');
      let total = 0;
      nominalInputs.forEach(input => {
        const value = parseInt(input.value) || 0;
        total += value;
      });
      const totalElement = document.getElementById('total-nominal');
      totalElement.textContent = total.toLocaleString('id-ID');
    }

    function attachNominalListeners() {
      const nominalInputs = document.querySelectorAll('.nominal-input');
      nominalInputs.forEach(input => {
        input.removeEventListener('input', updateTotal);
        input.addEventListener('input', updateTotal);
      });
    }

    document.addEventListener('DOMContentLoaded', function() {
      attachNominalListeners();
      // Sync awal: set nilai default kategori ke hidden inputs
      syncKategoriSync();
    });
  </script>
</body>
</html><?php /**PATH D:\Documents\Iclabs\LabML\LabMoneyLens\resources\views/welcome.blade.php ENDPATH**/ ?>