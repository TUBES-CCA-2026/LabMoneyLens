<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Galeri Struk — Dashboard</title>
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
    }

    .galeri-wrapper { padding: 28px 32px; max-width: 1200px; margin: 0 auto; }
    .galeri-header { margin-bottom: 28px; }
    .galeri-header h2 { font-size: 24px; font-weight: 800; color: #1e293b; margin-bottom: 6px; }
    .galeri-header p { color: #64748b; font-size: 14px; }

    .flash-success {
      background: linear-gradient(135deg, #dcfce7, #bbf7d0);
      border: 1.5px solid #86efac; border-radius: 12px;
      padding: 12px 18px; margin-bottom: 20px; font-size: 13px;
      color: #166534; font-weight: 600; display: flex; align-items: center; gap: 8px;
    }

    .struk-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
      gap: 20px;
    }

    .struk-card {
      background: #fff; border: 1.5px solid #e2e8f0; border-radius: 16px;
      overflow: hidden; display: flex; flex-direction: column;
      box-shadow: 0 2px 8px rgba(0,0,0,0.06);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .struk-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.1); }

    .struk-img-container {
      width: 100%; height: 200px; background: #f8fafc;
      display: flex; align-items: center; justify-content: center;
      overflow: hidden;
    }
    .struk-img-container img {
      width: 100%; height: 100%; object-fit: contain;
      cursor: zoom-in; transition: transform 0.3s ease;
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

    <div id="sidebar-overlay" class="sidebar-overlay"></div>

    <aside class="sidebar">
      <div class="sidebar-user">
        <div class="avatar">
          <svg viewBox="0 0 24 24" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
        </div>
        <div>
          <div class="sidebar-username"><?php echo e(session('user_name', 'USERNAME')); ?></div>
          <div class="sidebar-role"><?php echo e(session('user_role', 'Administrator')); ?></div>
        </div>
      </div>

      <nav class="sidebar-nav">
        <a href="<?php echo e(route('dashboard')); ?>" class="nav-item">
          <svg viewBox="0 0 24 24" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
          Dashboard
        </a>
        <?php if (! (session('user_role') == 'Kepala Lab')): ?>
          <a href="<?php echo e(route('welcome')); ?>" class="nav-item">
            <svg viewBox="0 0 24 24" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M12 8v4l3 3"/></svg>
            Pengeluaran
          </a>
          <a href="<?php echo e(route('pemasukan')); ?>" class="nav-item">
            <svg viewBox="0 0 24 24" stroke-width="1.8"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            Pemasukan
          </a>
        <?php endif; ?>
        <a href="<?php echo e(route('struk')); ?>" class="nav-item active">
          <svg viewBox="0 0 24 24" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
          Galeri Struk
        </a>
        <a href="<?php echo e(route('laporan')); ?>" class="nav-item">
          <svg viewBox="0 0 24 24" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
          Laporan
        </a>
        <?php if (! (session('user_role') == 'Kepala Lab')): ?>
          <a href="<?php echo e(route('recycle')); ?>" class="nav-item">
            <svg viewBox="0 0 24 24" stroke-width="1.8"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
            Recycle Bin
          </a>
        <?php endif; ?>
      </nav>

      <div class="sidebar-logout">
        <form action="<?php echo e(route('logout')); ?>" method="POST">
          <?php echo csrf_field(); ?>
          <button type="submit" class="logout-btn">
            <svg viewBox="0 0 24 24" stroke-width="1.8"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Log-out
          </button>
        </form>
      </div>
    </aside>

    <main class="main">
      <div class="galeri-wrapper">
        <div class="galeri-header">
          <h2>🧾 Galeri Struk</h2>
          <p>Semua bukti transaksi yang telah diunggah. Klik gambar untuk memperbesar, atau gunakan tombol untuk mengedit / menghapus.</p>
        </div>

        <?php if(session('success')): ?>
          <div class="flash-success">✅ <?php echo e(session('success')); ?></div>
        <?php endif; ?>

        <?php if($strukList->isEmpty()): ?>
          <div class="empty-state">
            <div class="empty-icon">🧾</div>
            <h3>Belum Ada Struk</h3>
            <p>Upload struk saat menginput Pengeluaran atau Pemasukan untuk melihatnya di sini.</p>
          </div>
        <?php else: ?>
          <div class="struk-grid">
            <?php $__currentLoopData = $strukList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $struk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <div class="struk-card">
                <div class="struk-img-container">
                  <img src="<?php echo e(asset('storage/' . $struk->foto)); ?>"
                       alt="Struk <?php echo e($struk->uraian); ?>"
                       onclick="openLightbox('<?php echo e(asset('storage/' . $struk->foto)); ?>')" />
                </div>
                <div class="struk-info">
                  <span class="struk-type type-<?php echo e($struk->type); ?>"><?php echo e($struk->type); ?></span>
                  <div class="struk-date">📅 <?php echo e(\Illuminate\Support\Carbon::parse($struk->tanggal)->format('d M Y')); ?></div>
                  <div class="struk-amount">Rp <?php echo e(number_format($struk->nominal, 0, ',', '.')); ?></div>
                  <div class="struk-desc"><?php echo e($struk->uraian ?: '—'); ?></div>
                </div>
                <div class="struk-actions">
                  <button class="btn-edit-foto"
                          onclick="openEditModal('<?php echo e(strtolower($struk->type)); ?>', <?php echo e($struk->id); ?>, '<?php echo e(asset('storage/' . $struk->foto)); ?>')">
                    ✏️ Ganti Foto
                  </button>
                  <form method="POST"
                        action="<?php echo e(route('struk.delete', ['type' => strtolower($struk->type), 'id' => $struk->id])); ?>"
                        style="flex:1;"
                        onsubmit="return confirm('Yakin hapus struk ini? Data akan masuk ke Recycle Bin.')">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn-hapus-struk">🗑 Hapus</button>
                  </form>
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
      <h3>✏️ Ganti Foto Struk</h3>
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
          <button type="submit" class="btn-save-foto" id="btn-save-foto" disabled>💾 Simpan Foto</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function openLightbox(src) {
      document.getElementById('lightbox-img').src = src;
      document.getElementById('lightbox').classList.add('show');
    }
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
  </script>
</body>
</html>
<?php /**PATH C:\Users\LOQ\OneDrive\Documents\codingan\LBL\LabMoneyLens\resources\views/struk.blade.php ENDPATH**/ ?>