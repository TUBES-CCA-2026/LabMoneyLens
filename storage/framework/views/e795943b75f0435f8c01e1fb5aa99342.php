<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
  <title>Edit Pemasukan — LabMoneyLens</title>
  <?php echo app('Illuminate\Foundation\Vite')(['resources/css/style.css','resources/css/welcome.css','resources/js/script.js']); ?>
  
  <!-- Inline mobile hamburger styling as backup -->
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

    /* ── Form Card ── */
    .form-card {
      background: linear-gradient(135deg, #ffffff 0%, #f9fdfb 100%);
      border-radius: 20px;
      border: 2px solid #e0f7f5;
      box-shadow: 0 4px 16px rgba(13, 148, 136, 0.1);
      overflow: hidden;
    }

    .form-card-header {
      background: linear-gradient(90deg, #f0fdf9 0%, #ecfdf5 100%);
      border-bottom: 2px solid #e0f7f5;
      padding: 18px 28px;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .form-card-header .header-icon {
      width: 40px;
      height: 40px;
      background: linear-gradient(135deg, #0d9488 0%, #059669 100%);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      box-shadow: 0 4px 10px rgba(13, 148, 136, 0.3);
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
      color: #0f766e;
    }

    .form-card-header p {
      font-size: 12px;
      color: #0d9488;
      margin-top: 2px;
    }

    .form-card-body {
      padding: 28px;
    }

    /* ── Upload zone (compact horizontal) ── */
    .upload-row {
      display: flex;
      align-items: center;
      gap: 16px;
      background: linear-gradient(135deg, rgba(224,247,245,0.4) 0%, transparent 100%);
      border: 2px dashed rgba(13, 148, 136, 0.35);
      border-radius: 14px;
      padding: 16px 20px;
      cursor: pointer;
      transition: all 0.25s ease;
      margin-bottom: 24px;
    }

    .upload-row:hover {
      border-color: #0d9488;
      background: rgba(224,247,245,0.6);
    }

    .upload-row-icon {
      width: 44px;
      height: 44px;
      background: linear-gradient(135deg, #e0f7f5 0%, #ccfbf1 100%);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .upload-row-icon svg {
      width: 22px;
      height: 22px;
      stroke: #0d9488;
      fill: none;
    }

    .upload-row-text {
      flex: 1;
    }

    .upload-row-text strong {
      font-size: 13px;
      font-weight: 700;
      color: #0f766e;
      display: block;
    }

    .upload-row-text span {
      font-size: 11px;
      color: #64748b;
      margin-top: 2px;
      display: block;
    }

    .upload-row-btn {
      background: linear-gradient(135deg, #0d9488 0%, #059669 100%);
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
      background: linear-gradient(135deg, #0a7065 0%, #047857 100%);
      box-shadow: 0 4px 12px rgba(13, 148, 136, 0.3);
      transform: translateY(-1px);
    }

    /* ── Form Grid ── */
    .form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 18px;
    }

    .form-grid .span-2 {
      grid-column: span 2;
    }

    .form-group {
      display: flex;
      flex-direction: column;
      gap: 7px;
    }

    .form-label {
      font-size: 11px;
      font-weight: 700;
      color: #0f766e;
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
      border: 2px solid #ccf0ee;
      border-radius: 10px;
      font-size: 13px;
      font-family: inherit;
      color: #0f766e;
      background: #ffffff;
      outline: none;
      width: 100%;
      transition: all 0.2s ease;
    }

    .form-input:hover { border-color: #a5e8e3; }
    .form-input:focus {
      border-color: #0d9488;
      box-shadow: 0 0 0 4px rgba(13, 148, 136, 0.12);
    }

    select.form-input {
      appearance: none;
      -webkit-appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%230d9488' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 12px center;
      padding-right: 34px;
    }

    textarea.form-input {
      resize: vertical;
      min-height: 76px;
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
      color: #0d9488;
      pointer-events: none;
    }

    .form-input.with-prefix {
      padding-left: 38px;
    }

    /* ── Form Actions ── */
    .form-actions {
      display: flex;
      gap: 12px;
      margin-top: 24px;
      padding-top: 20px;
      border-top: 2px solid #f0fdf9;
    }

    .save-btn {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      background: linear-gradient(135deg, #0d9488 0%, #059669 100%);
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
      box-shadow: 0 4px 14px rgba(13, 148, 136, 0.3);
    }

    .save-btn:hover {
      background: linear-gradient(135deg, #0a7065 0%, #047857 100%);
      box-shadow: 0 6px 20px rgba(13, 148, 136, 0.4);
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
      background: linear-gradient(135deg, #ffffff 0%, #f9fdfb 100%);
      border-radius: 20px;
      border: 2px solid #e0f7f5;
      box-shadow: 0 4px 16px rgba(13, 148, 136, 0.08);
      overflow: hidden;
    }

    .table-section-header {
      background: linear-gradient(90deg, #f0fdf9 0%, #ecfdf5 100%);
      border-bottom: 2px solid #e0f7f5;
      padding: 16px 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
    }

    .table-section-header h3 {
      font-size: 14px;
      font-weight: 800;
      color: #0f766e;
      text-transform: uppercase;
      letter-spacing: 0.6px;
    }

    .table-section-header .entry-count {
      font-size: 12px;
      font-weight: 600;
      color: #0d9488;
      background: rgba(13, 148, 136, 0.1);
      padding: 4px 12px;
      border-radius: 20px;
    }

    .table-wrap {
      overflow-x: auto;
    }

    /* ── Helper tip ── */
    .laporan-tip {
      text-align: center;
      font-size: 12px;
      color: #64748b;
      padding: 16px;
    }

    .laporan-tip a {
      color: #0d9488;
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
    <!-- ── Hamburger Menu Button ── -->
    <button id="hamburger-menu" class="hamburger-menu" aria-label="Toggle Menu">
      <span class="hamburger-line"></span>
      <span class="hamburger-line"></span>
      <span class="hamburger-line"></span>
    </button>

    <?php echo $__env->make('includes.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <main class="main">
      <div class="page-wrapper">
        <div class="form-card" style="max-width: 600px; margin: 0 auto; margin-top: 4vh;">
          <div class="form-card-header">
            <div class="header-icon">
              <svg viewBox="0 0 24 24" stroke-width="1.8"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            </div>
            <div>
              <h2>Edit Data Pemasukan</h2>
              <p>Semua field bertanda <span style="color:#ef4444;font-weight:700;">●</span> wajib diisi</p>
            </div>
          </div>

          <div class="form-card-body">
            <?php if($income->foto_struk): ?>
            <div id="preview-container" style="margin-bottom:24px; background: linear-gradient(135deg, #f0fdf9 0%, #ecfdf5 100%); border: 2px solid #a5e8e3; border-radius: 14px; padding: 16px; text-align: center;">
              <div style="font-size: 12px; font-weight: 700; color: #0f766e; margin-bottom: 12px;">✅ Foto Struk / Kwitansi (Tersimpan)</div>
              <img src="<?php echo e(asset('storage/' . $income->foto_struk)); ?>" alt="Preview Struk" style="max-width: 100%; max-height: 250px; border-radius: 10px; box-shadow: 0 4px 12px rgba(13, 148, 136, 0.15);">
            </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('pemasukan.update', ['id' => $income->id])); ?>" class="input-form">
              <?php echo csrf_field(); ?>

              <!-- Tanggal & Kategori -->
              <div class="form-grid">
                <div class="form-group">
                  <label class="form-label" for="tanggal">
                    <span class="required-dot"></span> Tanggal Transaksi
                  </label>
                  <input type="date" class="form-input" id="tanggal" name="tanggal" required value="<?php echo e(\Illuminate\Support\Carbon::parse($income->tanggal)->format('Y-m-d')); ?>" />
                </div>

                <div class="form-group">
                  <label class="form-label" for="id_jenis_penerimaan">
                    <span class="required-dot"></span> Kategori Penerimaan
                  </label>
                  <select class="form-input" id="id_jenis_penerimaan" name="id_jenis_penerimaan" required>
                    <option value="">— Pilih Kategori —</option>
                    <?php $__currentLoopData = $jenis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <option value="<?php echo e($j->id); ?>" <?php echo e($income->id_jenis_penerimaan == $j->id ? 'selected' : ''); ?>><?php echo e($j->nama); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </select>
                </div>
              </div>

              <!-- Item rows: Keterangan + Nominal -->
              <div id="items-container" style="margin-top: 18px;">
                <?php $__currentLoopData = $incomes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="form-grid item-row" style="position: relative; padding-bottom: 20px; margin-bottom: 20px; border-bottom: 1px dashed #e0f7f5;">
                  <input type="hidden" name="id_pemasukan[]" value="<?php echo e($item->id); ?>">
                  
                  <!-- Keterangan -->
                  <div class="form-group span-2">
                    <label class="form-label" for="uraian_<?php echo e($index); ?>">Keterangan / Uraian</label>
                    <input type="text" class="form-input uraian-input" id="uraian_<?php echo e($index); ?>" name="uraian[]"
                           placeholder="Contoh: Transfer dari bendahara, dana hibah..." maxlength="255" value="<?php echo e($item->uraian); ?>" />
                  </div>

                  <!-- Nominal -->
                  <div class="form-group span-2">
                    <label class="form-label" for="nominal_<?php echo e($index); ?>">
                      <span class="required-dot"></span> Nominal (IDR)
                    </label>
                    <div class="nominal-wrapper">
                      <span class="nominal-prefix">Rp</span>
                      <input type="number" class="form-input with-prefix nominal-input" id="nominal_<?php echo e($index); ?>" name="nominal[]" placeholder="0" min="0" required value="<?php echo e($item->jumlah); ?>" />
                    </div>
                  </div>
                  
                  <?php if($index > 0): ?>
                  <button type="button" class="btn-hapus-baris" onclick="hapusBaris(this)" style="position: absolute; top: 0; right: 0; background: none; border: none; color: #0d9488; cursor: pointer; font-size: 18px;" aria-label="Hapus Baris">✖</button>
                  <?php else: ?>
                  <button type="button" class="btn-hapus-baris" onclick="hapusBaris(this)" style="display: none; position: absolute; top: 0; right: 0; background: none; border: none; color: #0d9488; cursor: pointer; font-size: 18px;" aria-label="Hapus Baris">✖</button>
                  <?php endif; ?>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </div>

              <!-- Total Section -->
              <div class="form-group span-2" style="background: linear-gradient(135deg, rgba(13, 148, 136, 0.08) 0%, rgba(16, 185, 129, 0.04) 100%); border: 2px solid #a5e8e3; border-radius: 12px; padding: 16px 18px; margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px;">
                  <span style="font-size: 13px; font-weight: 700; color: #0f766e; text-transform: uppercase; letter-spacing: 0.5px;">Total Pemasukan</span>
                  <div style="display: flex; align-items: center; gap: 6px;">
                    <span style="font-size: 12px; font-weight: 600; color: #059669;">Rp</span>
                    <span id="total-nominal" style="font-size: 18px; font-weight: 800; color: #059669;">0</span>
                  </div>
                </div>
              </div>
              
              <button type="button" class="reset-btn" onclick="tambahBaris()" style="width: 100%; margin-bottom: 20px; border-style: dashed; color: #059669; border-color: #a7f3d0;">
                <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" width="16" height="16"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Tambah Baris Item
              </button>

              <!-- Actions -->
              <div class="form-actions">
                <a href="<?php echo e(route('laporan')); ?>" class="reset-btn" style="text-decoration: none; flex: 1; display: flex; text-align: center;">Batal</a>
                <button id="save-btn" class="save-btn" type="submit" style="flex: 1;">
                  <svg viewBox="0 0 24 24" stroke-width="1.8"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                  Simpan Perubahan
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </main>
  </div>

  <script>
    let rowCount = <?php echo e(count($incomes)); ?>;

    function tambahBaris() {
      const container = document.getElementById('items-container');
      const newRow = document.createElement('div');
      newRow.className = 'form-grid item-row';
      newRow.style = 'position: relative; padding-bottom: 20px; margin-bottom: 20px; border-bottom: 1px dashed #e0f7f5;';
      
      newRow.innerHTML = `
        <div class="form-group span-2">
          <label class="form-label" for="uraian_${rowCount}">Keterangan / Uraian</label>
          <input type="text" class="form-input uraian-input" id="uraian_${rowCount}" name="uraian[]"
                 placeholder="Contoh: Transfer dari bendahara, dana hibah..." maxlength="255" />
        </div>
        <div class="form-group span-2">
          <label class="form-label" for="nominal_${rowCount}">
            <span class="required-dot"></span> Nominal (IDR)
          </label>
          <div class="nominal-wrapper">
            <span class="nominal-prefix">Rp</span>
            <input type="number" class="form-input with-prefix nominal-input" id="nominal_${rowCount}" name="nominal[]" placeholder="0" min="1" required />
          </div>
        </div>
        <button type="button" class="btn-hapus-baris" onclick="hapusBaris(this)" style="position: absolute; top: 0; right: 0; background: none; border: none; color: #0d9488; cursor: pointer; font-size: 18px;" aria-label="Hapus Baris">✖</button>
      `;
      
      container.appendChild(newRow);
      
      // Re-bind calculation to new input
      const newNominalInput = newRow.querySelector('.nominal-input');
      newNominalInput.addEventListener('input', calculateTotal);
      
      updateHapusButtons();
      rowCount++;
    }

    function hapusBaris(button) {
      const row = button.closest('.item-row');
      row.remove();
      updateHapusButtons();
      calculateTotal();
    }

    function updateHapusButtons() {
      const rows = document.querySelectorAll('.item-row');
      rows.forEach((row, index) => {
        const btn = row.querySelector('.btn-hapus-baris');
        if (btn) {
          btn.style.display = rows.length > 1 ? 'block' : 'none';
        }
      });
    }

    function calculateTotal() {
      const nominalInputs = document.querySelectorAll('.nominal-input');
      let total = 0;
      nominalInputs.forEach(input => {
        const val = parseFloat(input.value);
        if (!isNaN(val)) total += val;
      });
      document.getElementById('total-nominal').textContent = total.toLocaleString('id-ID');
    }

    document.addEventListener('DOMContentLoaded', function() {
      const nominalInputs = document.querySelectorAll('.nominal-input');
      nominalInputs.forEach(input => {
        input.addEventListener('input', calculateTotal);
      });
      calculateTotal();
      updateHapusButtons();
    });
  </script>

  <!-- Custom Modal Pop-up -->
  <div id="custom-modal" class="custom-modal">
    <div class="modal-content">
      <div class="modal-icon" id="modal-icon"></div>
      <h2 id="modal-title"></h2>
      <p id="modal-message"></p>
      <button class="modal-btn" onclick="closeModal()">Tutup</button>
    </div>
  </div>

  <style>
    .custom-modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      z-index: 9999;
      justify-content: center;
      align-items: center;
      animation: fadeIn 0.3s ease;
    }

    .custom-modal.show {
      display: flex;
    }

    .modal-content {
      background: linear-gradient(135deg, #ffffff 0%, #f0fbff 100%);
      border-radius: 20px;
      padding: 40px;
      max-width: 420px;
      width: 90%;
      box-shadow: 0 20px 60px rgba(13, 148, 136, 0.2);
      text-align: center;
      animation: slideUp 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .modal-icon {
      font-size: 64px;
      margin-bottom: 20px;
      animation: popIn 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .modal-content h2 {
      font-size: 24px;
      color: #1f2937;
      margin-bottom: 12px;
      font-weight: 700;
    }

    .modal-content p {
      font-size: 14px;
      color: #6b7280;
      margin-bottom: 28px;
      line-height: 1.6;
    }

    .modal-btn {
      background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%);
      color: white;
      border: none;
      padding: 12px 32px;
      border-radius: 10px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(13, 148, 136, 0.3);
    }

    .modal-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(13, 148, 136, 0.4);
    }

    .modal-btn:active {
      transform: translateY(0);
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
      }
      to {
        opacity: 1;
      }
    }

    @keyframes slideUp {
      from {
        transform: translateY(30px);
        opacity: 0;
      }
      to {
        transform: translateY(0);
        opacity: 1;
      }
    }

    @keyframes popIn {
      0% {
        transform: scale(0);
      }
      50% {
        transform: scale(1.1);
      }
      100% {
        transform: scale(1);
      }
    }
  </style>

  <script>
    function showModal(title, message, icon = '✓') {
      document.getElementById('modal-icon').textContent = icon;
      document.getElementById('modal-title').textContent = title;
      document.getElementById('modal-message').textContent = message;
      document.getElementById('custom-modal').classList.add('show');
    }

    function closeModal() {
      document.getElementById('custom-modal').classList.remove('show');
    }

    // Close modal when clicking outside
    document.getElementById('custom-modal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeModal();
      }
    });

    // Tampilkan pop-up untuk error/success messages
    document.addEventListener('DOMContentLoaded', function() {
      <?php if(session('error')): ?>
        showModal('⚠️ Gagal', '<?php echo e(session("error")); ?>', '❌');
      <?php elseif(session('success')): ?>
        showModal('✅ Berhasil', '<?php echo e(session("success")); ?>', '✓');
      <?php endif; ?>
    });
  </script>
</body>
</html>
<?php /**PATH C:\Users\LOQ\OneDrive\Documents\codingan\New folder\LabMoneyLens\resources\views/pemasukan_edit.blade.php ENDPATH**/ ?>