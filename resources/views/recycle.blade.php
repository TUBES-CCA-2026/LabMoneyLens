<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Recycle Bin — Dashboard</title>
  @vite(['resources/css/style.css','resources/css/recyclebin.css','resources/js/script.js'])
  
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
        <a href="{{ route('dashboard') }}" class="nav-item">
          <svg viewBox="0 0 24 24" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
          Dashboard
        </a>
        @unless(session('user_role') == 'Kepala Lab')
          <a href="{{ route('welcome') }}" class="nav-item">
            <svg viewBox="0 0 24 24" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M12 8v4l3 3"/></svg>
            Pengeluaran
          </a>
          <a href="{{ route('pemasukan') }}" class="nav-item">
            <svg viewBox="0 0 24 24" stroke-width="1.8"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            Pemasukan
          </a>
        @endunless

        <a href="{{ route('laporan') }}" class="nav-item">
          <svg viewBox="0 0 24 24" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
          Laporan
        </a>

        <a href="{{ route('recycle') }}" class="nav-item active">
          <svg viewBox="0 0 24 24" stroke-width="1.8"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
          Recycle Bin
        </a>
      </nav>

      <div class="sidebar-logout">
        <form action="{{ route('logout') }}" method="POST">
          @csrf
          <button type="submit" class="logout-btn">
            <svg viewBox="0 0 24 24" stroke-width="1.8"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Log-out
          </button>
        </form>
      </div>
    </aside>

    <main class="main">
      <section class="recycle-panel">
        <div class="recycle-header">
          <div>
            <h2 class="panel-title">Recycle Bin</h2>
            <p class="panel-subtitle">Item pemasukan dan pengeluaran yang sudah dihapus.</p>
          </div>
          <div class="status-cards">
            <article class="status-card trash-card">
              <span class="status-label">TOTAL ITEMS</span>
              <strong class="status-value">{{ $totalItems }} Record</strong>
            </article>
            <article class="status-card value-card">
              <span class="status-label">TOTAL VALUE</span>
              <strong class="status-value">Rp {{ number_format($totalValue, 0, ',', '.') }}</strong>
            </article>
          </div>
        </div>

        @if(session('success'))
          <div class="success-message">{{ session('success') }}</div>
        @endif

        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Kategori</th>
                <th>Jumlah (IDR)</th>
                <th>Tanggal</th>
                <th>Jenis</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($records as $item)
                <tr>
                  <td>{{ $item->kategori }}</td>
                  <td>{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                  <td>{{ \Illuminate\Support\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                  <td>{{ $item->tipe }}</td>
                  <td class="action-cell">
                    <form action="{{ route('recycle.restore', ['type' => strtolower($item->tipe), 'id' => $item->id]) }}" method="POST" class="action-form">
                      @csrf
                      <button type="submit" class="btn-restore">Pulih</button>
                    </form>
                    <form action="{{ route('recycle.forceDelete', ['type' => strtolower($item->tipe), 'id' => $item->id]) }}" method="POST" class="action-form">
                      @csrf
                      <button type="submit" class="btn-delete">Hapus</button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="empty-row">Tidak ada item di recycle bin.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="recycle-actions">
          <form action="{{ route('recycle.restoreAll') }}" method="POST">
            @csrf
            <button type="submit" class="restore-all-btn">Restore All</button>
          </form>
          <form action="{{ route('recycle.emptyTrash') }}" method="POST">
            @csrf
            <button type="submit" class="empty-trash-btn">Empty Trash</button>
          </form>
        </div>
      </section>
    </main>
  </div>
</body>
</html>
