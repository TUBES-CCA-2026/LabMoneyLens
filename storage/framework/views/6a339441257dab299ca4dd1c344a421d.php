<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
  <title>Profil — LabMoneyLens</title>
  <?php echo app('Illuminate\Foundation\Vite')(['resources/css/style.css','resources/css/dashboard.css','resources/js/script.js']); ?>
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
            <label class="form-label" for="foto_profil">Foto Profil</label>
            
            <!-- Preview Container -->
            <div id="foto_preview_container" style="display: none; align-items: center; gap: 16px; padding: 12px 14px; border: 2px dashed #0d9488; border-radius: 14px; background: linear-gradient(135deg, rgba(224, 247, 245, 0.7) 0%, rgba(255, 255, 255, 0.95) 100%); margin-bottom: 12px;">
              <img id="foto_preview_img" src="" alt="Preview Foto" style="width: 60px; height: 60px; object-fit: cover; border-radius: 10px; border: 1px solid #dff5f2; box-shadow: 0 4px 10px rgba(13, 148, 136, 0.15);">
              <div style="display: flex; flex-direction: column; gap: 2px; flex-grow: 1;">
                <strong id="foto_preview_name" style="font-size: 13px; color: #0f766e; word-break: break-all;">Nama file</strong>
                <small id="foto_preview_size" style="font-size: 11px; color: #0d9488;">Ukuran file</small>
                <button type="button" id="btn_cancel_foto" style="align-self: flex-start; background: none; border: none; color: #dc2626; font-size: 11px; cursor: pointer; padding: 0; margin-top: 4px; font-weight: 600; display: flex; align-items: center; gap: 3px;">
                  <span>✕</span> Hapus Pilihan
                </button>
              </div>
            </div>

            <label class="file-picker" for="foto_profil">
              <span class="file-picker-icon">📷</span>
              <span class="file-picker-text">
                <strong>Pilih foto profil baru</strong>
                <small>PNG, JPG, atau JPEG</small>
              </span>
              <input type="file" id="foto_profil" name="foto_profil" accept="image/*">
            </label>
            <?php $__errorArgs = ['foto_profil'];
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

  <script>
    (function() {
      function initProfilePreview() {
        const fotoInput = document.getElementById('foto_profil');
        const avatarPreview = document.querySelector('.profile-avatar-preview');
        const previewContainer = document.getElementById('foto_preview_container');
        const previewImg = document.getElementById('foto_preview_img');
        const previewName = document.getElementById('foto_preview_name');
        const previewSize = document.getElementById('foto_preview_size');
        const btnCancel = document.getElementById('btn_cancel_foto');
        const filePicker = document.querySelector('.file-picker');
        
        if (fotoInput && avatarPreview && !fotoInput.dataset.previewBound) {
          fotoInput.dataset.previewBound = "true";
          
          const originalAvatarHTML = avatarPreview.innerHTML;
          
          fotoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
              const reader = new FileReader();
              reader.onload = function(event) {
                // Update top circle preview
                avatarPreview.innerHTML = `<img src="${event.target.result}" alt="Preview Foto Profil" />`;
                
                // Update preview container
                if (previewContainer && previewImg && previewName && previewSize && filePicker) {
                  previewImg.src = event.target.result;
                  previewName.textContent = file.name;
                  
                  const sizeInKB = (file.size / 1024).toFixed(1);
                  previewSize.textContent = `${sizeInKB} KB`;
                  
                  previewContainer.style.display = 'flex';
                  filePicker.style.display = 'none';
                }
              };
              reader.readAsDataURL(file);
            } else {
              resetPreview();
            }
          });
          
          if (btnCancel) {
            btnCancel.addEventListener('click', function(e) {
              e.preventDefault();
              resetPreview();
            });
          }
          
          function resetPreview() {
            fotoInput.value = '';
            avatarPreview.innerHTML = originalAvatarHTML;
            if (previewContainer && filePicker) {
              previewContainer.style.display = 'none';
              filePicker.style.display = 'flex';
            }
          }
        }
      }

      // Eksekusi langsung jika halaman diload normal
      initProfilePreview();
      
      // Jika menggunakan Turbo
      document.addEventListener("turbo:load", initProfilePreview);
    })();
  </script>
</body>
</html>
<?php /**PATH D:\TubesWeb\LabMoneyLens\resources\views/profile.blade.php ENDPATH**/ ?>