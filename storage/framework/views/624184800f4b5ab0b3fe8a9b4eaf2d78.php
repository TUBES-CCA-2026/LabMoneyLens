<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
  <title>Galeri Struk — LabMoneyLens</title>
  <?php echo app('Illuminate\Foundation\Vite')(['resources/css/style.css','resources/js/script.js']); ?>
  
  <style>
    @media (max-width: 1024px) {
      .hamburger-menu { display: flex !important; }
      .sidebar {
        position: fixed !important; left: 0 !important; top: 0 !important;
        width: 220px !important; height: 100vh !important;
        transform: translateX(-100%) !important; transition: transform 0.3s ease !important;
        z-index: 999 !important;
      }
      .sidebar.active { transform: translateX(0) !important; }
      .sidebar-overlay { opacity: 0 !important; transition: opacity 0.3s ease !important; }
      .sidebar-overlay.active { display: block !important; opacity: 1 !important; }
      .filter-section { flex-direction: column; }
      .filter-group { width: 100%; }
      .filter-input, .filter-select { width: 100%; min-width: auto; max-width: 100%; }
      .btn-apply-filter { width: 100%; }
    }

    .galeri-wrapper { padding: 28px 32px; max-width: 1200px; margin: 0 auto; }
    .galeri-header { margin-bottom: 28px; }
    .galeri-header h2 { font-size: 24px; font-weight: 800; color: #1e293b; margin-bottom: 6px; }
    .galeri-header p { color: #64748b; font-size: 14px; }

    .filter-buttons {
      display: flex; gap: 10px; margin-bottom: 28px; flex-wrap: wrap;
    }
    .filter-btn {
      padding: 10px 18px; border: 2px solid #e2e8f0; background: #fff;
      border-radius: 10px; font-size: 13px; font-weight: 600; cursor: pointer;
      color: #64748b; transition: all 0.2s ease; font-family: inherit;
    }
    .filter-btn:hover { border-color: #cbd5e1; }
    .filter-btn.active {
      background: linear-gradient(135deg, #0d9488 0%, #0a6d6a 100%);
      color: #fff; border-color: #0d9488;
    }

    .filter-section {
      display: flex; gap: 16px; margin-bottom: 24px; align-items: flex-end; flex-wrap: wrap;
    }
    .filter-group {
      display: flex; flex-direction: column; gap: 6px;
      flex: 0 1 auto;
    }
    .filter-label {
      font-size: 11px; font-weight: 700; color: #0d9488; text-transform: uppercase; letter-spacing: 0.5px;
    }
    .filter-input, .filter-select {
      padding: 10px 14px; border: 2px solid #ccf0ee; border-radius: 10px;
      font-size: 13px; font-family: inherit; color: #0f766e; background: #fff;
      outline: none; transition: all 0.2s ease;
      box-sizing: border-box;
    }
    .filter-input::placeholder {
      color: #cbd5e1;
    }
    .filter-input[type="month"] {
      color-scheme: light;
    }
    .filter-input[type="month"]::placeholder {
      color: #cbd5e1;
    }
    .filter-input::-webkit-calendar-picker-indicator {
      cursor: pointer; color: #0d9488;
    }
    .filter-input::-webkit-outer-spin-button,
    .filter-input::-webkit-inner-spin-button {
      -webkit-appearance: none; margin: 0;
    }
    .filter-input:hover, .filter-select:hover {
      border-color: #a5e8e3; box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.08);
    }
    .filter-input:focus, .filter-select:focus {
      border-color: #0d9488; box-shadow: 0 0 0 4px rgba(13, 148, 136, 0.15);
    }
    .filter-input {
      min-width: 200px;
      max-width: 300px;
      width: auto;
    }
    .filter-select {
      min-width: 220px;
      max-width: 350px;
      width: auto;
    }
    .btn-apply-filter {
      padding: 10px 24px; background: linear-gradient(135deg, #0d9488 0%, #0a6d6a 100%);
      color: #fff; border: none; border-radius: 10px; font-size: 13px;
      font-weight: 700; cursor: pointer; font-family: inherit; transition: all 0.2s ease;
      box-shadow: 0 4px 10px rgba(13, 148, 136, 0.2);
    }
    .btn-apply-filter:hover {
      background: linear-gradient(135deg, #0a6d6a 0%, #083d39 100%);
      transform: translateY(-2px); box-shadow: 0 6px 16px rgba(13, 148, 136, 0.3);
    }

    .flash-success {
      background: linear-gradient(135deg, #dcfce7, #bbf7d0);
      border: 1.5px solid #86efac; border-radius: 12px;
      padding: 12px 18px; margin-bottom: 20px; font-size: 13px;
      color: #166534; font-weight: 600; display: flex; align-items: center; gap: 8px;
    }

    .struk-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 22px;
      align-items: stretch;
    }

    .struk-card {
      background: #fff; border: 1.5px solid #e2e8f0; border-radius: 16px;
      overflow: hidden; display: flex; flex-direction: column;
      box-shadow: 0 2px 8px rgba(0,0,0,0.06);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
      min-height: 100%;
    }
    .struk-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.1); }

    .struk-img-container {
      width: 100%; min-height: 420px; background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
      display: flex; align-items: center; justify-content: center;
      overflow: hidden; padding: 14px;
      border-bottom: 1px solid #eef2f7;
    }
    .struk-img-container img {
      width: auto;
      height: auto;
      max-width: 100%;
      max-height: 100%;
      object-fit: contain;
      cursor: zoom-in; transition: transform 0.3s ease;
      border-radius: 12px; background: #f8fafc;
      box-shadow: inset 0 0 0 1px #e2e8f0;
      display: block;
    }
    .struk-img-container img:hover { transform: scale(1.03); }

    .struk-info { padding: 14px 16px; flex: 1; }
    .struk-type {
      font-size: 10px; font-weight: 700; text-transform: uppercase;
      padding: 3px 8px; border-radius: 6px; display: inline-block;
      margin-bottom: 8px; letter-spacing: 0.5px;
    }
    .type-Pemasukan { background: #dcfce7; color: #166534; }
    .type-Pengeluaran { background: #fee2e2; color: #991b1b; }
    .struk-date { color: #64748b; font-size: 12px; margin-bottom: 4px; }
    .struk-amount { font-weight: 800; font-size: 15px; margin-bottom: 4px; color: #1e293b; }
    .struk-desc { font-size: 12px; color: #64748b; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

    .struk-actions {
      padding: 10px 16px 14px; display: flex; gap: 8px;
      border-top: 1.5px solid #f1f5f9;
    }

    .btn-edit-foto {
      flex: 1; display: flex; align-items: center; justify-content: center; gap: 5px;
      background: linear-gradient(135deg, #3b82f6, #2563eb); color: #fff;
      border: none; border-radius: 8px; padding: 8px 12px;
      font-size: 12px; font-weight: 600; cursor: pointer; font-family: inherit;
      transition: all 0.2s ease;
    }
    .btn-edit-foto:hover { background: linear-gradient(135deg, #2563eb, #1d4ed8); transform: translateY(-1px); box-shadow: 0 4px 10px rgba(37,99,235,0.3); }

    .btn-hapus-struk {
      flex: 1; display: flex; align-items: center; justify-content: center; gap: 5px;
      background: linear-gradient(135deg, #ef4444, #dc2626); color: #fff;
      border: none; border-radius: 8px; padding: 8px 12px;
      font-size: 12px; font-weight: 600; cursor: pointer; font-family: inherit;
      transition: all 0.2s ease; width: 100%;
    }
    .btn-hapus-struk:hover { background: linear-gradient(135deg, #dc2626, #b91c1c); transform: translateY(-1px); box-shadow: 0 4px 10px rgba(220,38,38,0.3); }

    .empty-state { text-align: center; padding: 60px 20px; background: #fff; border-radius: 16px; border: 1.5px dashed #e2e8f0; }
    .empty-state .empty-icon { font-size: 56px; margin-bottom: 16px; }
    .empty-state h3 { font-size: 18px; color: #1e293b; font-weight: 700; margin-bottom: 8px; }
    .empty-state p { font-size: 13px; color: #64748b; }

    /* Lightbox */
    .lightbox {
      display: none; position: fixed; inset: 0;
      background: rgba(0,0,0,0.92); z-index: 9000;
      align-items: center; justify-content: center;
    }
    .lightbox.show { display: flex; }
    .lightbox img { max-width: 90%; max-height: 88vh; object-fit: contain; border-radius: 8px; }
    .lightbox-close {
      position: absolute; top: 18px; right: 28px;
      color: #fff; font-size: 36px; cursor: pointer; font-weight: 700;
      opacity: 0.8; transition: opacity 0.2s;
    }
    .lightbox-close:hover { opacity: 1; }

    /* Edit Photo Modal */
    .edit-modal-overlay {
      display: none; position: fixed; inset: 0;
      background: rgba(0,0,0,0.5); z-index: 9001;
      align-items: center; justify-content: center;
    }
    .edit-modal-overlay.show { display: flex; }
    .edit-modal {
      background: #fff; border-radius: 20px; padding: 32px;
      max-width: 440px; width: 90%;
      box-shadow: 0 20px 60px rgba(0,0,0,0.2);
      animation: popIn 0.3s cubic-bezier(0.34,1.56,0.64,1);
    }
    @keyframes popIn { from { transform: scale(0.85); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    .edit-modal h3 { font-size: 18px; font-weight: 800; color: #1e293b; margin-bottom: 6px; }
    .edit-modal p { font-size: 13px; color: #64748b; margin-bottom: 20px; }

    .edit-modal-preview {
      width: 100%; height: 160px; background: #f8fafc;
      border: 2px dashed #e2e8f0; border-radius: 12px;
      display: flex; align-items: center; justify-content: center;
      margin-bottom: 16px; overflow: hidden; cursor: pointer;
      transition: border-color 0.2s;
    }
    .edit-modal-preview:hover { border-color: #3b82f6; }
    .edit-modal-preview img { width: 100%; height: 100%; object-fit: contain; }
    .preview-placeholder { text-align: center; color: #94a3b8; }
    .preview-placeholder svg { width: 36px; height: 36px; stroke: #cbd5e1; fill: none; margin-bottom: 8px; display: block; margin: 0 auto 8px; }
    .preview-placeholder span { font-size: 12px; display: block; }

    .edit-modal-actions { display: flex; gap: 10px; margin-top: 20px; }
    .btn-cancel-modal {
      flex: 1; background: #f1f5f9; color: #64748b; border: none;
      border-radius: 10px; padding: 12px; font-size: 13px; font-weight: 600;
      cursor: pointer; font-family: inherit; transition: background 0.2s;
    }
    .btn-cancel-modal:hover { background: #e2e8f0; }
    .btn-save-foto {
      flex: 2; background: linear-gradient(135deg, #3b82f6, #2563eb); color: #fff;
      border: none; border-radius: 10px; padding: 12px; font-size: 13px; font-weight: 700;
      cursor: pointer; font-family: inherit; transition: all 0.2s;
      box-shadow: 0 4px 12px rgba(37,99,235,0.25);
    }
    .btn-save-foto:disabled { opacity: 0.5; cursor: not-allowed; transform: none !important; }
    .btn-save-foto:not(:disabled):hover { background: linear-gradient(135deg, #2563eb, #1d4ed8); transform: translateY(-1px); }
  </style>
</head>
<body>
  <div class="app">
    <button id="hamburger-menu" class="hamburger-menu" aria-label="Toggle Menu">
      <span class="hamburger-line"></span>
      <span class="hamburger-line"></span>
      <span class="hamburger-line"></span>
    </button>

    <?php echo $__env->make('includes.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <main class="main">
      <div class="galeri-wrapper">
        <div class="galeri-header">
          <h2><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15" style="display:inline-block;vertical-align:middle;margin-right:4px;"><path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z"/><path d="M16 8H8M16 12H8M12 16H8"/></svg> Galeri Struk</h2>
          <p>Semua bukti transaksi yang telah diunggah. Klik gambar untuk memperbesar, atau gunakan tombol untuk mengedit / menghapus.</p>
        </div>

        <?php if(session('success')): ?>
          <div class="flash-success"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="15" height="15" style="display:inline-block;vertical-align:middle;margin-right:3px;"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg> <?php echo e(session('success')); ?></div>
        <?php endif; ?>

        <?php if(!$strukList->isEmpty()): ?>
          <div class="filter-buttons">
            <button type="button" id="filter-type-pemasukan" class="filter-btn" onclick="selectFilterType('Pemasukan')">Pemasukan</button>
            <button type="button" id="filter-type-pengeluaran" class="filter-btn" onclick="selectFilterType('Pengeluaran')">Pengeluaran</button>
          </div>

          <div class="filter-section">
            <div class="filter-group">
              <label class="filter-label">Tanggal</label>
              <input type="date" id="filter-tanggal" class="filter-input" placeholder="Pilih tanggal">
            </div>
            <div class="filter-group">
              <label class="filter-label">Kategori</label>
              <select id="filter-kategori" class="filter-select" disabled>
                <option value="">Pilih jenis terlebih dahulu</option>
              </select>
            </div>
            <button class="btn-apply-filter" onclick="applyFilter()">Apply Filter</button>
          </div>
        <?php endif; ?>

        <?php if($strukList->isEmpty()): ?>
          <div class="empty-state">
            <div class="empty-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="56" height="56" style="display:inline-block;vertical-align:middle;opacity:0.3;"><path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z"/><path d="M16 8H8M16 12H8M12 16H8"/></svg></div>
            <h3>Belum Ada Struk</h3>
            <p>Upload struk saat menginput Pengeluaran atau Pemasukan untuk melihatnya di sini.</p>
          </div>
        <?php else: ?>
          <div class="struk-grid">
            <?php $__currentLoopData = $strukList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $struk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <div class="struk-card" data-category="<?php echo e($struk->type); ?>" data-kategori="<?php echo e(strtolower(str_replace(' ', '-', $struk->kategori))); ?>" data-tanggal="<?php echo e($struk->tanggal); ?>">
                <div class="struk-img-container">
                  <img src="<?php echo e(asset('storage/' . $struk->foto)); ?>"
                       alt="Struk <?php echo e($struk->uraian); ?>"
                       onclick="openLightbox('<?php echo e(asset('storage/' . $struk->foto)); ?>')" />
                </div>
                <div class="struk-info">
                  <span class="struk-type type-<?php echo e($struk->type); ?>"><?php echo e($struk->type); ?></span>
                  <div style="font-size: 11px; color: #0d9488; font-weight: 600; margin-bottom: 6px;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="11" height="11" style="display:inline-block;vertical-align:middle;margin-right:3px;"><path d="M12.586 2.586A2 2 0 0 0 11.172 2H4a2 2 0 0 0-2 2v7.172a2 2 0 0 0 .586 1.414l8.704 8.704a2.426 2.426 0 0 0 3.42 0l6.58-6.58a2.426 2.426 0 0 0 0-3.42z"/><circle cx="7.5" cy="7.5" r="1.5"/></svg> <?php echo e($struk->kategori); ?></div>
                  <div class="struk-date"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="12" height="12" style="display:inline-block;vertical-align:middle;margin-right:3px;"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg> <?php echo e(\Illuminate\Support\Carbon::parse($struk->tanggal)->format('d M Y')); ?></div>
                  <div class="struk-amount">Rp <?php echo e(number_format($struk->nominal, 0, ',', '.')); ?></div>
                  <div class="struk-desc"><?php echo e($struk->uraian ?: '—'); ?></div>
                </div>
                <div class="struk-actions">
                  <a href="<?php echo e(route('struk.download', ['type' => strtolower($struk->type), 'id' => $struk->id])); ?>" class="btn-edit-foto" style="text-decoration:none; text-align:center; <?php if(session('user_role') == 'Kepala Lab'): ?> flex:1; <?php endif; ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13" style="display:inline-block;vertical-align:middle;margin-right:3px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg> Download
                  </a>
                  <?php if (! (session('user_role') == 'Kepala Lab')): ?>
                    <button class="btn-edit-foto"
                            onclick="openEditModal('<?php echo e(strtolower($struk->type)); ?>', <?php echo e($struk->id); ?>, '<?php echo e(asset('storage/' . $struk->foto)); ?>')">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15" style="display:inline-block;vertical-align:middle;margin-right:3px;"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg> Ganti Foto
                    </button>
                    <form method="POST"
                          action="<?php echo e(route('struk.delete', ['type' => strtolower($struk->type), 'id' => $struk->id])); ?>"
                          style="flex:1;"
                          data-confirm="soft">
                      <?php echo csrf_field(); ?>
                      <button type="submit" class="btn-hapus-struk"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13" style="display:inline-block;vertical-align:middle;margin-right:3px;"><polyline points="3 6 5 6 21 6"/><path d="m19 6-.867 12.142A2 2 0 0 1 16.138 20H7.862a2 2 0 0 1-1.995-1.858L5 6"/><path d="M10 11v6M14 11v6M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg> Hapus</button>
                    </form>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </div>
        <?php endif; ?>
      </div>
    </main>
  </div>

  <!-- Lightbox -->
  <div id="lightbox" class="lightbox" onclick="closeLightbox()">
    <span class="lightbox-close">&times;</span>
    <img id="lightbox-img" src="" alt="Preview Struk">
  </div>

  <!-- Edit Foto Modal -->
  <div id="edit-modal-overlay" class="edit-modal-overlay" onclick="closeEditModal(event)">
    <div class="edit-modal" onclick="event.stopPropagation()">
      <h3><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15" style="display:inline-block;vertical-align:middle;margin-right:3px;"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg> Ganti Foto Struk</h3>
      <p>Pilih foto baru untuk menggantikan foto struk saat ini. Foto lama akan dihapus secara otomatis.</p>

      <div class="edit-modal-preview" onclick="document.getElementById('foto_baru').click()">
        <img id="edit-preview-img" src="" alt="" style="display:none; width:100%; height:100%; object-fit:contain;">
        <div class="preview-placeholder" id="edit-preview-placeholder">
          <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
          <span>Klik untuk pilih foto baru</span>
          <span style="font-size:11px;margin-top:4px;color:#cbd5e1;">JPG, PNG, WEBP — Maks. 5MB</span>
        </div>
      </div>

      <form id="edit-foto-form" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <input type="file" id="foto_baru" name="foto_baru" accept="image/*" hidden>
        <div class="edit-modal-actions">
          <button type="button" class="btn-cancel-modal" onclick="closeEditModal(null, true)">Batal</button>
          <button type="submit" class="btn-save-foto" id="btn-save-foto" disabled><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15" style="display:inline-block;vertical-align:middle;margin-right:3px;"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg> Simpan Foto</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function closeLightbox() {
      document.getElementById('lightbox').classList.remove('show');
    }

    function openEditModal(type, id, currentSrc) {
      document.getElementById('edit-foto-form').action = '/struk/update-foto/' + type + '/' + id;
      document.getElementById('edit-preview-img').src = currentSrc;
      document.getElementById('edit-preview-img').style.display = 'block';
      document.getElementById('edit-preview-placeholder').style.display = 'none';
      document.getElementById('foto_baru').value = '';
      document.getElementById('btn-save-foto').disabled = true;
      document.getElementById('edit-modal-overlay').classList.add('show');
    }

    function closeEditModal(event, force) {
      if (force || (event && event.target === document.getElementById('edit-modal-overlay'))) {
        document.getElementById('edit-modal-overlay').classList.remove('show');
      }
    }

    document.getElementById('foto_baru').addEventListener('change', function() {
      if (this.files[0]) {
        const reader = new FileReader();
        reader.onload = (e) => {
          document.getElementById('edit-preview-img').src = e.target.result;
          document.getElementById('edit-preview-img').style.display = 'block';
          document.getElementById('edit-preview-placeholder').style.display = 'none';
        };
        reader.readAsDataURL(this.files[0]);
        document.getElementById('btn-save-foto').disabled = false;
      }
    });

    const kategoriData = {
      Pemasukan: <?php echo json_encode($kategoriPemasukan, 15, 512) ?>,
      Pengeluaran: <?php echo json_encode($kategoriPengeluaran, 15, 512) ?>
    };
    let activeFilterType = '';

    function openLightbox(src) {
      document.getElementById('lightbox-img').src = src;
      document.getElementById('lightbox').classList.add('show');
    }

    function selectFilterType(type) {
      activeFilterType = type;
      document.getElementById('filter-type-pemasukan').classList.toggle('active', type === 'Pemasukan');
      document.getElementById('filter-type-pengeluaran').classList.toggle('active', type === 'Pengeluaran');
      updateCategoryOptions();
    }

    function updateCategoryOptions() {
      const kategoriSelect = document.getElementById('filter-kategori');
      kategoriSelect.innerHTML = '';

      if (!activeFilterType) {
        kategoriSelect.disabled = true;
        kategoriSelect.innerHTML = '<option value="">Pilih jenis terlebih dahulu</option>';
        return;
      }

      const categories = kategoriData[activeFilterType] || [];
      kategoriSelect.disabled = false;
      kategoriSelect.innerHTML = '<option value="">Semua</option>';

      if (categories.length === 0) {
        kategoriSelect.innerHTML = '<option value="">Tidak ada kategori aktif</option>';
        kategoriSelect.disabled = true;
        return;
      }

      categories.forEach(item => {
        const option = document.createElement('option');
        option.value = `kategori-${item.toLowerCase().replace(/\s+/g, '-')}`;
        option.textContent = item;
        kategoriSelect.appendChild(option);
      });
    }

    function applyFilter() {
      const tanggalInput = document.getElementById('filter-tanggal').value;
      const kategoriInput = document.getElementById('filter-kategori').value;
      const cards = document.querySelectorAll('.struk-card');

      cards.forEach(card => {
        const cardTanggal = card.getAttribute('data-tanggal');
        const cardType = card.getAttribute('data-category');
        const cardKategori = card.getAttribute('data-kategori');

        let tanggalMatch = true;
        let jenisMatch = true;
        let kategoriMatch = true;

        if (tanggalInput) {
          tanggalMatch = cardTanggal === tanggalInput;
        }

        if (activeFilterType) {
          jenisMatch = cardType === activeFilterType;
        }

        if (kategoriInput && kategoriInput.startsWith('kategori-')) {
          const filterKategori = kategoriInput.replace('kategori-', '');
          kategoriMatch = cardKategori === filterKategori;
        }

        if (tanggalMatch && jenisMatch && kategoriMatch) {
          card.style.display = '';
          card.style.animation = 'fadeIn 0.3s ease';
        } else {
          card.style.display = 'none';
        }
      });
    }

    document.addEventListener('DOMContentLoaded', () => {
      updateCategoryOptions();
    });

    // Add fadeIn animation
    const style = document.createElement('style');
    style.textContent = `
      @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
      }
    `;
    document.head.appendChild(style);
  </script>
</body>
</html>
<?php /**PATH C:\Users\LOQ\OneDrive\Documents\codingan\TUBES\LabMoneyLens\resources\views/struk.blade.php ENDPATH**/ ?>