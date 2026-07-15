<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
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

    <div id="sidebar-overlay" class="sidebar-overlay"></div>

    <aside class="sidebar">
      <a href="{{ route('profile') }}" class="sidebar-user" style="display:flex; align-items:center; gap:14px; text-decoration:none; color:inherit;">
        <div class="avatar">
          @if(!empty(session('user_photo')))
            <img src="{{ asset('storage/' . session('user_photo')) }}" alt="Foto Profil" style="width:48px;height:48px;border-radius:50%;object-fit:cover;" />
          @else
            <svg viewBox="0 0 24 24" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
          @endif
        </div>
        <div>
          <div class="sidebar-username">{{ session('user_name', 'USERNAME') }}</div>
          <div class="sidebar-role">{{ session('user_role', 'Administrator') }}</div>
        </div>
      </a>

      <nav class="sidebar-nav">
        <a href="{{ route('dashboard') }}" class="nav-item">
          Dashboard
        </a>
      </nav>
    </aside>

    <main class="main">
      <section class="panel" style="max-width: 760px; margin: 40px auto;">
        <div class="panel-header">
          <div>
            <h2 class="panel-title">Edit Profil</h2>
            <p class="panel-subtitle">Perbarui nama, password, dan foto profil Anda.</p>
          </div>
        </div>

        @if(session('success'))
          <div class="alert-success" style="margin-bottom: 18px; padding: 12px 14px; border-radius: 10px; background: #ecfdf5; color: #047857; font-weight: 600;">
            {{ session('success') }}
          </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="form-grid" style="gap: 18px;">
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
            <input type="file" class="form-input" id="foto_profil" name="foto_profil" accept="image/*">
            @error('foto_profil')<small style="color:#dc2626;">{{ $message }}</small>@enderror
          </div>

          <div class="form-actions span-2">
            <button type="submit" class="save-btn">Simpan Profil</button>
          </div>
        </form>
      </section>
    </main>
  </div>
</body>
</html>
