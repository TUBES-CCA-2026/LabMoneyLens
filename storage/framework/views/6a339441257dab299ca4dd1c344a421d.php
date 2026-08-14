<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
  <title>Profil — LabMoneyLens</title>
  <?php echo app('Illuminate\Foundation\Vite')(['resources/css/style.css','resources/css/dashboard.css','resources/js/script.js']); ?>
  <!-- Cropper.js -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
  <style>
    /* ===== Crop Modal ===== */
    #crop-modal-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.72);
      z-index: 9999;
      align-items: center;
      justify-content: center;
      backdrop-filter: blur(6px);
      animation: fadeInOverlay 0.2s ease;
    }
    #crop-modal-overlay.active { display: flex; }
    @keyframes fadeInOverlay {
      from { opacity: 0; } to { opacity: 1; }
    }
    #crop-modal {
      background: #fff;
      border-radius: 22px;
      padding: 28px 28px 24px;
      max-width: 520px;
      width: 92vw;
      box-shadow: 0 24px 80px rgba(13,148,136,0.22), 0 2px 16px rgba(0,0,0,0.15);
      display: flex;
      flex-direction: column;
      gap: 18px;
      animation: slideUpModal 0.28s cubic-bezier(.22,.68,0,1.2);
    }
    @keyframes slideUpModal {
      from { transform: translateY(40px) scale(0.96); opacity: 0; }
      to   { transform: translateY(0)     scale(1);    opacity: 1; }
    }
    #crop-modal-title {
      font-size: 1.08rem;
      font-weight: 700;
      color: #0f766e;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    #crop-modal-title svg { flex-shrink: 0; }
    #crop-canvas-wrap {
      width: 100%;
      height: 340px;
      background: #f0faf9;
      border-radius: 14px;
      overflow: hidden;
      border: 2px solid #ccede9;
    }
    #crop-canvas-wrap img {
      display: block;
      max-width: 100%;
    }
    #crop-hint {
      font-size: 12px;
      color: #6b7280;
      text-align: center;
      margin: -6px 0;
    }
    #crop-modal-actions {
      display: flex;
      gap: 12px;
      justify-content: flex-end;
    }
    #btn-crop-cancel {
      padding: 10px 22px;
      border-radius: 10px;
      border: 2px solid #e5e7eb;
      background: #fff;
      color: #374151;
      font-weight: 600;
      font-size: 14px;
      cursor: pointer;
      transition: all 0.18s ease;
    }
    #btn-crop-cancel:hover { border-color: #d1fae5; background: #f9fafb; }
    #btn-crop-confirm {
      padding: 10px 26px;
      border-radius: 10px;
      border: none;
      background: linear-gradient(135deg, #0d9488, #059669);
      color: #fff;
      font-weight: 700;
      font-size: 14px;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 8px;
      box-shadow: 0 4px 14px rgba(13,148,136,0.3);
      transition: all 0.18s ease;
    }
    #btn-crop-confirm:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(13,148,136,0.4); }
    /* Preview lingkaran di thumbnail */
    #foto_preview_img { border-radius: 50% !important; }
  </style>
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
    <button id="hamburger-menu" class="hamburger-menu" aria-label="Toggle Menu">
      <span class="hamburger-line"></span>
      <span class="hamburger-line"></span>
      <span class="hamburger-line"></span>
    </button>

    <?php echo $__env->make('includes.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <main class="main">
      <section class="profile-card">
        <div class="profile-card-header">
          <div>
            <div class="profile-card-badge">Profil akun</div>
            <h2 class="panel-title">Edit Profil</h2>
            <p class="panel-subtitle">Perbarui nama, password, dan foto profil Anda dengan tampilan yang lebih rapi dan sesuai tema web.</p>
          </div>
        </div>

        <div class="profile-hero">
          <div class="profile-avatar-preview">
            <?php if(!empty(session('user_photo'))): ?>
              <img src="<?php echo e(asset('storage/' . session('user_photo'))); ?>" alt="Foto Profil" />
            <?php else: ?>
              <svg viewBox="0 0 24 24" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
            <?php endif; ?>
          </div>
          <div>
            <h3><?php echo e($user->nama); ?></h3>
            <p>Perubahan profil akan diterapkan setelah Anda menekan tombol simpan.</p>
          </div>
        </div>

        <?php if(session('success')): ?>
          <div class="alert-success">
            <?php echo e(session('success')); ?>

          </div>
        <?php endif; ?>

        <form action="<?php echo e(route('profile.update')); ?>" method="POST" enctype="multipart/form-data" class="profile-form">
          <?php echo csrf_field(); ?>

          <div class="form-group">
            <label class="form-label" for="nama">Nama</label>
            <input type="text" class="form-input" id="nama" name="nama" value="<?php echo e(old('nama', $user->nama)); ?>" required>
            <?php $__errorArgs = ['nama'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small style="color:#dc2626;"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>

          <div class="form-group">
            <label class="form-label" for="password">Password Baru</label>
            <input type="password" class="form-input" id="password" name="password" placeholder="Kosongkan jika tidak ingin ubah">
            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small style="color:#dc2626;"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>

          <div class="form-group">
            <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
            <input type="password" class="form-input" id="password_confirmation" name="password_confirmation" placeholder="Ulangi password baru">
          </div>

          <div class="form-group span-2">
            <label class="form-label">Foto Profil</label>

            <!-- Hidden input untuk base64 hasil crop -->
            <input type="hidden" id="foto_profil_cropped" name="foto_profil_cropped">

            <!-- Preview Container (tampil setelah crop) -->
            <div id="foto_preview_container" style="display: none; align-items: center; gap: 16px; padding: 12px 14px; border: 2px dashed #0d9488; border-radius: 14px; background: linear-gradient(135deg, rgba(224, 247, 245, 0.7) 0%, rgba(255, 255, 255, 0.95) 100%); margin-bottom: 12px;">
              <img id="foto_preview_img" src="" alt="Preview Foto" style="width: 64px; height: 64px; object-fit: cover; border-radius: 50%; border: 3px solid #0d9488; box-shadow: 0 4px 16px rgba(13, 148, 136, 0.25);">
              <div style="display: flex; flex-direction: column; gap: 2px; flex-grow: 1;">
                <strong id="foto_preview_name" style="font-size: 13px; color: #0f766e; word-break: break-all;">Foto siap diunggah</strong>
                <small style="font-size: 11px; color: #0d9488;">Telah dipotong berbentuk lingkaran</small>
                <button type="button" id="btn_cancel_foto" style="align-self: flex-start; background: none; border: none; color: #dc2626; font-size: 11px; cursor: pointer; padding: 0; margin-top: 4px; font-weight: 600; display: flex; align-items: center; gap: 3px;">
                  <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="12" height="12" style="display:inline-block;vertical-align:middle;"><path d="M18 6 6 18M6 6l12 12"/></svg></span> Hapus Pilihan
                </button>
              </div>
            </div>

            <label class="file-picker" id="file-picker-label">
              <span class="file-picker-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="26" height="26" style="display:inline-block;vertical-align:middle;"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3z"/><circle cx="12" cy="13" r="3"/></svg></span>
              <span class="file-picker-text">
                <strong>Pilih foto profil baru</strong>
                <small>PNG, JPG, atau JPEG &mdash; akan dipotong lingkaran</small>
              </span>
              <input type="file" id="foto_profil_raw" accept="image/*" style="display:none;">
            </label>
            <?php $__errorArgs = ['foto_profil_cropped'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small style="color:#dc2626;"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>

          <div class="form-actions span-2">
            <button type="submit" class="save-btn">Simpan Profil</button>
          </div>
        </form>
      </section>
    </main>
  </div>

  <!-- ===== Crop Modal ===== -->
  <div id="crop-modal-overlay">
    <div id="crop-modal">
      <div id="crop-modal-title">
        <svg viewBox="0 0 24 24" fill="none" stroke="#0d9488" stroke-width="2" width="20" height="20"><circle cx="12" cy="12" r="10"/><path d="M8 12a4 4 0 1 0 8 0 4 4 0 0 0-8 0"/></svg>
        Crop Foto — Sesuaikan Area Lingkaran
      </div>
      <div id="crop-canvas-wrap">
        <img id="crop-image-el" src="" alt="Crop">
      </div>
      <p id="crop-hint">Seret & zoom untuk mengatur posisi foto di dalam lingkaran</p>
      <div id="crop-modal-actions">
        <button type="button" id="btn-crop-cancel">
          Batal
        </button>
        <button type="button" id="btn-crop-confirm">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="16" height="16"><polyline points="20 6 9 17 4 12"/></svg>
          Gunakan Foto Ini
        </button>
      </div>
    </div>
  </div>

  <!-- Cropper.js Script -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
  <script>
    (function() {
      let cropperInstance = null;

      function initProfileCrop() {
        const rawInput       = document.getElementById('foto_profil_raw');
        const croppedInput   = document.getElementById('foto_profil_cropped');
        const avatarPreview  = document.querySelector('.profile-avatar-preview');
        const previewContainer = document.getElementById('foto_preview_container');
        const previewImg     = document.getElementById('foto_preview_img');
        const previewName    = document.getElementById('foto_preview_name');
        const btnCancel      = document.getElementById('btn_cancel_foto');
        const filePicker     = document.getElementById('file-picker-label');
        const modalOverlay   = document.getElementById('crop-modal-overlay');
        const cropImageEl    = document.getElementById('crop-image-el');
        const btnCropCancel  = document.getElementById('btn-crop-cancel');
        const btnCropConfirm = document.getElementById('btn-crop-confirm');

        if (!rawInput || rawInput.dataset.bound) return;
        rawInput.dataset.bound = 'true';

        const originalAvatarHTML = avatarPreview ? avatarPreview.innerHTML : '';

        // Buka file dialog saat label diklik
        filePicker.addEventListener('click', () => rawInput.click());

        rawInput.addEventListener('change', function(e) {
          const file = e.target.files[0];
          if (!file || !file.type.startsWith('image/')) return;

          const reader = new FileReader();
          reader.onload = function(ev) {
            openCropModal(ev.target.result, file.name);
          };
          reader.readAsDataURL(file);
          // reset input supaya event change tetap trigger kalau file sama
          rawInput.value = '';
        });

        // === Open modal ===
        function openCropModal(src, filename) {
          cropImageEl.src = src;
          modalOverlay.classList.add('active');
          document.body.style.overflow = 'hidden';

          // Inisialisasi Cropper setelah img load
          cropImageEl.onload = function() {
            if (cropperInstance) {
              cropperInstance.destroy();
              cropperInstance = null;
            }
            cropperInstance = new Cropper(cropImageEl, {
              aspectRatio: 1,
              viewMode: 1,
              dragMode: 'move',
              autoCropArea: 0.85,
              cropBoxResizable: false,
              cropBoxMovable: false,
              guides: false,
              center: false,
              highlight: false,
              background: false,
              movable: true,
              rotatable: false,
              scalable: false,
              zoomable: true,
              zoomOnTouch: true,
              zoomOnWheel: true,
            });
          };

          // Simpan nama file untuk dipakai nanti
          modalOverlay._filename = filename;
        }

        // === Batal crop ===
        btnCropCancel.addEventListener('click', closeCropModal);
        modalOverlay.addEventListener('click', function(e) {
          if (e.target === modalOverlay) closeCropModal();
        });

        function closeCropModal() {
          if (cropperInstance) { cropperInstance.destroy(); cropperInstance = null; }
          cropImageEl.src = '';
          modalOverlay.classList.remove('active');
          document.body.style.overflow = '';
        }

        // === Konfirmasi crop ===
        btnCropConfirm.addEventListener('click', function() {
          if (!cropperInstance) return;

          // Gambar hasil crop ke canvas bulat
          const canvas = cropperInstance.getCroppedCanvas({
            width: 400,
            height: 400,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
          });

          // Buat canvas lingkaran
          const circleCanvas = document.createElement('canvas');
          circleCanvas.width = 400;
          circleCanvas.height = 400;
          const ctx = circleCanvas.getContext('2d');
          ctx.beginPath();
          ctx.arc(200, 200, 200, 0, Math.PI * 2);
          ctx.closePath();
          ctx.clip();
          ctx.drawImage(canvas, 0, 0, 400, 400);

          const base64 = circleCanvas.toDataURL('image/png', 0.92);

          // Simpan ke hidden input
          croppedInput.value = base64;

          // Preview di form
          if (avatarPreview) {
            avatarPreview.innerHTML = `<img src="${base64}" alt="Preview Foto Profil" />`;
          }
          previewImg.src = base64;
          previewName.textContent = modalOverlay._filename || 'Foto dipotong';
          previewContainer.style.display = 'flex';
          filePicker.style.display = 'none';

          closeCropModal();
        });

        // === Hapus pilihan ===
        if (btnCancel) {
          btnCancel.addEventListener('click', function(e) {
            e.preventDefault();
            croppedInput.value = '';
            if (avatarPreview) avatarPreview.innerHTML = originalAvatarHTML;
            previewContainer.style.display = 'none';
            filePicker.style.display = 'flex';
          });
        }
      }

      // Jalankan saat DOM siap
      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initProfileCrop);
      } else {
        initProfileCrop();
      }
      document.addEventListener('turbo:load', initProfileCrop);
    })();
  </script>
</body>
</html>
<?php /**PATH D:\TubesWeb\LabMoneyLens\resources\views/profile.blade.php ENDPATH**/ ?>