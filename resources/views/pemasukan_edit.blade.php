<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Pemasukan</title>
  @vite(['resources/css/style.css','resources/css/welcome.css','resources/js/script.js'])
  
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
    <aside class="sidebar">
      <div class="sidebar-user">
        <div class="avatar">
          <svg viewBox="0 0 24 24" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
        </div>
        <div>
          <div class="sidebar-username">{{ session('user_name', 'USERNAME') }}</div>
          <div class="sidebar-role">{{ session('user_role', 'Administrator') }}</div>
        </div>
      </div>
      <nav class="sidebar-nav">
        <a href="{{ route('dashboard') }}" class="nav-item">Dashboard</a>
        @unless(session('user_role') == 'Kepala Lab')
          <a href="{{ route('welcome') }}" class="nav-item">Pengeluaran</a>
          <a href="{{ route('pemasukan') }}" class="nav-item active">Pemasukan</a>
        @endunless
        <a href="{{ route('laporan') }}" class="nav-item">Laporan</a>
        @unless(session('user_role') == 'Kepala Lab')
          <a href="{{ route('recycle') }}" class="nav-item">Recycle Bin</a>
        @endunless
      </nav>
      <div class="sidebar-logout">
        <form action="{{ route('logout') }}" method="POST">
          @csrf
          <button type="submit" class="logout-btn">Log-out</button>
        </form>
      </div>
    </aside>
    <main class="main">
      <section class="input-panel">
        <h2 class="panel-title">Edit Pemasukan</h2>
        <p class="panel-subtitle">Perbarui kategori, tanggal, atau jumlah pemasukan.</p>
        <form method="POST" action="{{ route('pemasukan.update', ['id' => $income->id]) }}" class="input-form">
          @csrf
          <div class="form-group">
            <label class="form-label" for="id_jenis_penerimaan">Kategori</label>
            <select class="form-input" id="id_jenis_penerimaan" name="id_jenis_penerimaan" required>
              <option value="">Pilih</option>
              @foreach($jenis as $j)
                <option value="{{ $j->id }}" {{ $income->id_jenis_penerimaan == $j->id ? 'selected' : '' }}>{{ $j->nama }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label class="form-label" for="nominal">Jumlah (IDR)</label>
            <input type="number" class="form-input" id="nominal" name="nominal" placeholder="Rp" min="0" required value="{{ $income->jumlah }}" />
          </div>
          <div class="form-group">
            <label class="form-label" for="tanggal">Tanggal</label>
            <input type="date" class="form-input" id="tanggal" name="tanggal" required value="{{ \Illuminate\Support\Carbon::parse($income->tanggal)->format('Y-m-d') }}" />
          </div>
          <button class="save-btn" type="submit">Simpan Perubahan</button>
        </form>
      </section>
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
      @if(session('error'))
        showModal('⚠️ Gagal', '{{ session("error") }}', '❌');
      @elseif(session('success'))
        showModal('✅ Berhasil', '{{ session("success") }}', '✓');
      @endif
    });
  </script>
</body>
</html>
