<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
  <title>Input Pemasukan Manual — LabMoneyLens</title>
  <?php echo app('Illuminate\Foundation\Vite')(['resources/css/style.css','resources/css/welcome.css','resources/js/script.js']); ?>

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
    }

    .form-label {
      font-size: 13px;
      font-weight: 700;
      color: #0f766e;
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

    <?php echo $__env->make('includes.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <main class="main">
      <div class="page-wrapper">

        <!-- Back Button -->
        <a href="<?php echo e(route('pemasukan.pilih')); ?>" class="back-btn">
          <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
          Kembali ke Pilihan Input
        </a>

        <!-- Page Hero -->
        <?php
          $totalIncome  = \Illuminate\Support\Facades\DB::table('pemasukan')->where('is_confirmed',1)->whereNull('deleted_at')->sum('nominal');
          $totalExpense = \Illuminate\Support\Facades\DB::table('pengeluaran')->where('is_confirmed',1)->whereNull('deleted_at')->sum('nominal');
          $saldo = $totalIncome - $totalExpense;
        ?>
        <div class="page-hero">
          <div class="page-hero-info">
            <h1>Input Pemasukan Manual</h1>
            <p>Catat pemasukan baru secara manual tanpa perlu foto bukti.</p>
          </div>
          <div class="hero-balance-badge">
            <div class="badge-label">Saldo Tersedia</div>
            <div class="badge-value">Rp <?php echo e(number_format($saldo, 0, ',', '.')); ?></div>
            <div class="badge-warning">Total: Pemasukan - Pengeluaran</div>
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
            <?php if($errors->any()): ?>
              <div style="background-color: #d1fae5; border: 1.5px solid #a7f3d0; border-radius: 12px; padding: 16px; margin-bottom: 20px; color: #0f766e; font-size: 13px;">
                <strong style="display: block; margin-bottom: 8px;">⚠️ Gagal Menyimpan:</strong>
                <ul style="margin: 0; padding-left: 20px;">
                  <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
              </div>
            <?php endif; ?>

            <!-- Saldo info bar -->
            <div class="saldo-info">
              <div class="saldo-info-text">
                Saldo saat ini: <strong>Rp <?php echo e(number_format($saldo, 0, ',', '.')); ?></strong>.
              </div>
            </div>

            <form id="manual_form" method="POST" action="<?php echo e(route('pemasukan.store-manual')); ?>" enctype="multipart/form-data" class="input-form">
              <?php echo csrf_field(); ?>
              <input type="file" id="receipt_image" name="receipt_image" accept="image/*" required hidden>
              <input type="hidden" name="input_type" value="manual">

              <!-- Upload Zone (horizontal compact) - WAJIB PERTAMA -->
              <div class="upload-row" id="upload-row" role="button" tabindex="0" aria-label="Unggah foto struk" style="background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 12px; padding: 16px; display: flex; align-items: center; justify-content: space-between; cursor: pointer; margin-bottom: 20px; transition: all 0.2s;">
                <div style="display: flex; align-items: center; gap: 16px;">
                  <div style="width: 48px; height: 48px; background: #e2e8f0; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                    <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" fill="none" width="24" height="24" style="color: #64748b;"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                  </div>
                  <div style="display: flex; flex-direction: column; gap: 4px;">
                    <strong id="upload-filename" style="font-size: 14px; color: #334155;">📎 Upload Foto Struk Dulu <span style="color:#ef4444">*</span></strong>
                    <span style="font-size: 12px; color: #64748b;">Maksimal 5MB. Format JPG, PNG, WEBP.</span>
                  </div>
                </div>
                <button type="button" class="upload-row-btn" onclick="document.getElementById('receipt_image').click()" style="background: #0f766e; color: #fff; border: none; padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 12px; cursor: pointer;">Pilih File</button>
              </div>

              <!-- Preview Foto Struk -->
              <div id="preview-container" style="display:none; margin-bottom:20px; background: linear-gradient(135deg, #f0fdf9 0%, #ecfdf5 100%); border: 2px solid #a5e8e3; border-radius: 14px; padding: 16px; text-align: center;">
                <div style="font-size: 12px; font-weight: 700; color: #0f766e; margin-bottom: 12px;">✅ Foto Struk Tersimpan</div>
                <img id="preview-image" src="" alt="Preview Struk" style="max-width: 100%; max-height: 300px; border-radius: 10px; box-shadow: 0 4px 12px rgba(13, 148, 136, 0.15);">
                <div style="margin-top: 12px; display: flex; gap: 10px;">
                  <button type="button" class="reset-btn" onclick="clearPreview()" style="flex: 1; border-color: #0d9488; color: #0d9488;">🗑 Hapus Foto</button>
                </div>
              </div>

              <!-- Form overlay blocker -->
              <div id="form-blocker" style="position:relative;">
                <div id="form-overlay" style="position:absolute;inset:0;background:rgba(255,255,255,0.85);z-index:10;border-radius:12px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:12px;backdrop-filter:blur(2px);">
                  <div style="font-size:48px;">🧾</div>
                  <div style="font-size:14px;font-weight:700;color:#0f766e;text-align:center;">Upload Foto Struk / Kwitansi Terlebih Dahulu</div>
                  <div style="font-size:12px;color:#64748b;text-align:center;">Form akan terbuka setelah foto struk berhasil diunggah.</div>
                  <button type="button" onclick="document.getElementById('receipt_image').click()" style="background:linear-gradient(135deg,#0d9488,#059669);color:#fff;border:none;border-radius:10px;padding:10px 20px;font-size:13px;font-weight:700;cursor:pointer;">📎 Pilih File Struk</button>
                </div>

              <!-- Tanggal & Kategori (Fixed) -->
              <div class="form-grid">
                <div class="form-group">
                  <label class="form-label" for="tanggal">
                    <span class="required-dot">●</span> Tanggal Transaksi (berlaku untuk semua baris)
                  </label>
                  <input type="date" class="form-input" id="tanggal" name="tanggal" value="<?php echo e(date('Y-m-d')); ?>" required />
                </div>

                <div class="form-group">
                  <label class="form-label" for="kategori_pemasukan">
                    <span class="required-dot">●</span> Kategori Default Pemasukan
                  </label>
                  <select class="form-input" id="kategori_pemasukan" name="kategori_pemasukan" required>
                    <option value="">— Pilih Kategori —</option>
                    <?php $__currentLoopData = $jenis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <option value="<?php echo e($j->id); ?>"><?php echo e($j->nama); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                  
                  <button type="button" class="btn-hapus-baris" onclick="hapusBaris(this)" style="display: none; position: absolute; top: 0; right: 0;" aria-label="Hapus Baris">✖</button>
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
            <h3>📋 Riwayat Pemasukan</h3>
            <span class="entry-count"><?php echo e(count($incomes)); ?> entri</span>
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
                <?php $__empty_1 = true; $__currentLoopData = $incomes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $income): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                  <tr>
                    <td><?php echo e($i + 1); ?></td>
                    <td><?php echo e(\Carbon\Carbon::parse($income->tanggal)->format('d M Y')); ?></td>
                    <td><?php echo e($income->kategori); ?></td>
                    <td>
                      <?php if(!empty($income->uraian ?? '')): ?>
                        <span style="color:#374151;"><?php echo e($income->uraian); ?></span>
                      <?php else: ?>
                        <span style="color:#94a3b8;font-style:italic;">—</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <span style="font-weight:700;color:#10b981;">+ Rp <?php echo e(number_format($income->jumlah, 0, ',', '.')); ?></span>
                    </td>
                    <td>
                      <div class="action-cell">
                        <a href="<?php echo e(route('pemasukan.edit', $income->id)); ?>" class="btn-edit">Edit</a>
                        <span class="sep">|</span>
                        <form method="POST" action="<?php echo e(route('pemasukan.delete', $income->id)); ?>" style="display:inline;"
                              data-confirm="soft">
                          <?php echo csrf_field(); ?>
                          <button type="submit" class="btn-hapus">Hapus</button>
                        </form>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                  <tr>
                    <td colspan="6" style="text-align:center;padding:20px;color:#94a3b8;font-style:italic;">
                      Belum ada data pemasukan.
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </main>
  </div>

  <script>
    // Data kategori
    const jenisDataManual = <?php echo json_encode($jenis); ?>;

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
        <button type="button" class="btn-hapus-baris" onclick="hapusBaris(this)" style="position: absolute; top: 0; right: 0;" aria-label="Hapus Baris">✖</button>
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
        <button type="button" class="btn-hapus-baris" onclick="hapusBaris(this)" style="position: absolute; top: 0; right: 0;" aria-label="Hapus Baris">✖</button>
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
<?php /**PATH C:\Users\LOQ\OneDrive\Documents\codingan\TUBES\LabMoneyLens\resources\views/pemasukan_manual.blade.php ENDPATH**/ ?>