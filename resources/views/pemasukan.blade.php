<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Pengeluaran — Dashboard</title>
  <link rel="stylesheet" href="style.css" />
  @vite(['resources/css/style1.css', 'resources/js/script1.js'])
</head>
<body>

  <div class="app">

    <!-- ── Sidebar ── -->
    <aside class="sidebar">
      <div class="sidebar-user">
        <div class="avatar">
          <svg viewBox="0 0 24 24" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
        </div>
        <div>
          <div class="sidebar-username">USERNAME</div>
          <div class="sidebar-role">Administrator</div>
        </div>
      </div>

      <nav class="sidebar-nav">
        <div class="nav-item">
          <svg viewBox="0 0 24 24" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
          Dashboard
        </div>

        <a href="{{ route('welcome') }}" class="nav-item">
          <svg viewBox="0 0 24 24" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M12 8v4l3 3"/></svg>
          Pengeluaran
        </a>

        <a href="{{ route('pemasukan') }}" class="nav-item active">
          <svg viewBox="0 0 24 24" stroke-width="1.8"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
          Pemasukan
        </a>

        <div class="nav-item">
          <svg viewBox="0 0 24 24" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
          Laporan
        </div>

        <div class="nav-item">
          <svg viewBox="0 0 24 24" stroke-width="1.8"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
          Recycle Bin
        </div>
      </nav>

      <div class="sidebar-logout">
        <button class="logout-btn">
          <svg viewBox="0 0 24 24" stroke-width="1.8"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
          Log-out
        </button>
      </div>
    </aside>

    <!-- ── Konten ── -->
    <main class="main">

      <!-- Panel Input -->
      <section class="input-panel">
        <h2 class="panel-title">Input Pemasukan</h2>
        <p class="panel-subtitle">Tambahkan catatan pemasukan baru secara manual atau foto struk fisik Anda.</p>

        <div class="upload-zone" role="button" tabindex="0" aria-label="Unggah foto struk">
          <svg viewBox="0 0 24 24" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
          <span class="upload-label">Unggah foto di sini</span>
        </div>

        <p class="divider-text">Atau secara manual</p>

        <div class="form-group">
          <label class="form-label" for="kategori">Kategori</label>
          <select class="form-input" id="kategori">
            <option value="">Pilih</option>
            <option>Dana Hibah</option>
            <option>Dana Fakultas</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label" for="jumlah">Jumlah (IDR)</label>
          <input type="number" class="form-input" id="jumlah" placeholder="Rp" min="0" />
        </div>

        <div class="form-group">
          <label class="form-label" for="tanggal">Tanggal</label>
          <input type="date" class="form-input" id="tanggal" />
        </div>

        <button class="save-btn" id="save-btn">
          <svg viewBox="0 0 24 24" stroke-width="1.8"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
          Simpan
        </button>
      </section>

      <!-- Panel Pratinjau -->
      <section class="preview-panel">
        <div class="preview-header">
          <div>
            <h1 class="preview-title">Pratinjau Entri</h1>
            <p class="preview-subtitle">Transaksi yang baru ditambahkan namun belum dikonfirmasi</p>
          </div>
          <button class="confirm-btn" id="confirm-btn">Konfirmasi</button>
        </div>

        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th style="width:22%">ID_Pengeluaran</th>
                <th style="width:20%">Kategori</th>
                <th style="width:20%">Jumlah (IDR)</th>
                <th style="width:20%">Tanggal</th>
                <th style="width:18%">Aksi</th>
              </tr>
            </thead>
            <tbody id="table-body"></tbody>
          </table>
        </div>
      </section>

    </main>
  </div>

  <script src="script.js"></script>
</body>
</html>