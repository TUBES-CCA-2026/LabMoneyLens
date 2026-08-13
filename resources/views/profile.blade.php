<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Profil — LabMoneyLens</title>
  @vite(['resources/css/style.css','resources/css/dashboard.css','resources/js/script.js'])
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

    @include('includes.sidebar')

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
            @if(!empty(session('user_photo')))
              <img src="{{ asset('storage/' . session('user_photo')) }}" alt="Foto Profil" />
            @else
              <svg viewBox="0 0 24 24" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
            @endif
          </div>
          <div>
            <h3>{{ $user->nama }}</h3>
            <p>Perubahan profil akan diterapkan setelah Anda menekan tombol simpan.</p>
          </div>
        </div>

        @if(session('success'))
          <div class="alert-success">
            {{ session('success') }}
          </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="profile-form">
          @csrf

          <div class="form-group">
            <label class="form-label" for="nama">Nama</label>
            <input type="text" class="form-input" id="nama" name="nama" value="{{ old('nama', $user->nama) }}" required>
            @error('nama')<small style="color:#dc2626;">{{ $message }}</small>@enderror
          </div>

          <div class="form-group">
            <label class="form-label" for="password">Password Baru</label>
            <input type="password" class="form-input" id="password" name="password" placeholder="Kosongkan jika tidak ingin ubah">
            @error('password')<small style="color:#dc2626;">{{ $message }}</small>@enderror
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
                  <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="12" height="12" style="display:inline-block;vertical-align:middle;"><path d="M18 6 6 18M6 6l12 12"/></svg></span> Hapus Pilihan
                </button>
              </div>
            </div>

            <label class="file-picker" for="foto_profil">
              <span class="file-picker-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="26" height="26" style="display:inline-block;vertical-align:middle;"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3z"/><circle cx="12" cy="13" r="3"/></svg></span>
              <span class="file-picker-text">
                <strong>Pilih foto profil baru</strong>
                <small>PNG, JPG, atau JPEG</small>
              </span>
              <input type="file" id="foto_profil" name="foto_profil" accept="image/*">
            </label>
            @error('foto_profil')<small style="color:#dc2626;">{{ $message }}</small>@enderror
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
