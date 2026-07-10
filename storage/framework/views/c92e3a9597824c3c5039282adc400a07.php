<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
  <title>Pengeluaran — Dashboard</title>
  <?php echo app('Illuminate\Foundation\Vite')(['resources/css/style.css','resources/css/welcome.css','resources/js/script.js']); ?>
  <script>window.receiptParseUrl = "<?php echo e(route('receipt.parse')); ?>";</script>
  
  <!-- Inline mobile hamburger styling as backup -->
  <style>
    @media (max-width: 1024px) {
      .hamburger-menu {
        display: flex !important;
      }
      .sidebar {
        position: fixed !important;
        left: 0 !important;
        top: 0 !important;
        width: 220px !important;
        height: 100vh !important;
        transform: translateX(-100%) !important;
        transition: transform 0.3s ease !important;
        z-index: 999 !important;
      }
      .sidebar.active {
        transform: translateX(0) !important;
      }
      .sidebar-overlay {
        opacity: 0 !important;
        transition: opacity 0.3s ease !important;
      }
      .sidebar-overlay.active {
        display: block !important;
        opacity: 1 !important;
      }
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

    <!-- ── Sidebar Overlay ── -->
    <div id="sidebar-overlay" class="sidebar-overlay"></div>
 
    <!-- ── Sidebar ── -->
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
          <a href="<?php echo e(route('welcome')); ?>" class="nav-item active">
            <svg viewBox="0 0 24 24" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M12 8v4l3 3"/></svg>
            Pengeluaran
          </a>
          <a href="<?php echo e(route('pemasukan')); ?>" class="nav-item">
            <svg viewBox="0 0 24 24" stroke-width="1.8"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            Pemasukan
          </a>
        <?php endif; ?>
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
 
    <!-- ── Konten ── -->
    <main class="main">
 
      <!-- Panel Input -->
      <section class="input-panel">
        <h2 class="panel-title">Input Pengeluaran</h2>
        <p class="panel-subtitle">Tambahkan catatan pengeluaran baru secara manual atau foto struk fisik Anda.</p>
 
        <div class="upload-zone" role="button" tabindex="0" aria-label="Unggah foto struk">
          <span id="upload-preview">
            <svg viewBox="0 0 24 24" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
            <span class="upload-label">Unggah foto di sini</span>
          </span>
        </div>

        <p class="divider-text">Atau secara manual</p>

        <form id="receipt_form" method="POST" action="<?php echo e(route('pengeluaran.store')); ?>" enctype="multipart/form-data" class="input-form">
          <?php echo csrf_field(); ?>
          <input type="file" id="receipt_image" name="receipt_image" accept="image/*" hidden>
          <input type="hidden" id="receipt_type" name="type" value="pengeluaran">
          <?php echo csrf_field(); ?>
          <div class="form-group">
            <label class="form-label" for="id_jenis_pengeluaran">Kategori</label>
            <select class="form-input" id="id_jenis_pengeluaran" name="id_jenis_pengeluaran" required>
              <option value="">Pilih</option>
              <?php $__currentLoopData = $jenis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($j->id); ?>"><?php echo e($j->nama); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label" for="nominal">Jumlah (IDR)</label>
            <input type="number" class="form-input" id="nominal" name="nominal" placeholder="Rp" min="0" required />
          </div>

          <div class="form-group">
            <label class="form-label" for="tanggal">Tanggal</label>
            <input type="date" class="form-input" id="tanggal" name="tanggal" required />
          </div>

          <button id="save-btn" class="save-btn" type="submit">
            <svg viewBox="0 0 24 24" stroke-width="1.8"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Simpan
          </button>
        </form>
      </section>
 
      <p class="divider-text">Pratinjau entri sekarang tersedia di halaman <a href="<?php echo e(route('laporan')); ?>">Laporan</a>.</p>
 
    </main>
  </div>

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
</html><?php /**PATH D:\lbl\LabMoneyLens\resources\views/welcome.blade.php ENDPATH**/ ?>