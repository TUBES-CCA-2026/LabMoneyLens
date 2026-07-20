<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Back Up — Dashboard</title>
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

    @include('includes.sidebar')

    <main class="main">
      <section class="recycle-panel">
        <div class="recycle-header">
          <div>
            <h2 class="panel-title">Back Up</h2>
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
                <th>Nominal (IDR)</th>
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
                    <form action="{{ route('recycle.forceDelete', ['type' => strtolower($item->tipe), 'id' => $item->id]) }}" method="POST" class="action-form" data-confirm="permanent">
                      @csrf
                      <button type="submit" class="btn-delete">Hapus</button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="empty-row">Tidak ada item di back up.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        @if($records->hasPages())
          <div style="margin-top: 20px; padding: 0 20px;">
            <style>
              .pagination { display: flex; list-style: none; padding: 0; margin: 0; justify-content: flex-end; gap: 4px; }
              .page-item .page-link { display: block; padding: 6px 12px; border: 1px solid #cbd5e1; border-radius: 6px; color: #0f766e; text-decoration: none; font-size: 13px; background: #fff; transition: all 0.2s ease; }
              .page-item .page-link:hover { background: #f1f5f9; }
              .page-item.active .page-link { background: #0d9488; color: #fff; border-color: #0d9488; }
              .page-item.disabled .page-link { color: #94a3b8; background: #f8fafc; cursor: not-allowed; border-color: #e2e8f0; }
            </style>
            {{ $records->links('pagination::bootstrap-4') }}
          </div>
        @endif

        <div class="recycle-actions">
          <form action="{{ route('recycle.restoreAll') }}" method="POST">
            @csrf
            <button type="submit" class="restore-all-btn">Restore All</button>
          </form>
          <form action="{{ route('recycle.emptyTrash') }}" method="POST" data-confirm="permanent">
            @csrf
            <button type="submit" class="empty-trash-btn">Empty Trash</button>
          </form>
        </div>
      </section>
    </main>
  </div>
</body>
</html>
