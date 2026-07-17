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
            <label class="file-picker" for="foto_profil">
              <span class="file-picker-icon">📷</span>
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
        
        if (fotoInput && avatarPreview && !fotoInput.dataset.previewBound) {
          fotoInput.dataset.previewBound = "true";
          fotoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
              const reader = new FileReader();
              reader.onload = function(e) {
                // Update top circle preview
                avatarPreview.innerHTML = `<img src="${e.target.result}" alt="Preview Foto Profil" />`;
                
                // Update file picker icon
                const pickerIcon = document.querySelector('.file-picker-icon');
                if(pickerIcon) {
                  pickerIcon.innerHTML = `<img src="${e.target.result}" style="width:100%; height:100%; object-fit:cover; border-radius:10px;" />`;
                }
                
                // Update file picker text
                const pickerText = document.querySelector('.file-picker-text strong');
                if(pickerText) {
                  pickerText.textContent = file.name;
                }
              };
              reader.readAsDataURL(file);
            }
          });
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
