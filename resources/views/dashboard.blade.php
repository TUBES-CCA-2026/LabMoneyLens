<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard — LabMoneyLens</title>
  @vite(['resources/css/style.css','resources/css/dashboard.css','resources/js/script.js'])
  
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
        <a href="{{ route('dashboard') }}" class="nav-item active">
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

        @unless(session('user_role') == 'Kepala Lab')
          <a href="{{ route('recycle') }}" class="nav-item">
            <svg viewBox="0 0 24 24" stroke-width="1.8"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
            Recycle Bin
          </a>
        @endunless
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

    <main class="main dashboard-main">
      <section class="dashboard-cards">
        <article class="dashboard-card income-card">
          <span class="card-icon up">▲</span>
          <span class="card-title">INCOME</span>
          <strong class="card-value">+ {{ number_format($totalIncome, 0, ',', '.') }}</strong>
        </article>

        <article class="dashboard-card expense-card">
          <span class="card-icon down">▼</span>
          <span class="card-title">EXPENSES</span>
          <strong class="card-value">- {{ number_format($totalExpense, 0, ',', '.') }}</strong>
        </article>

        <article class="dashboard-card balance-card">
          <span class="card-icon wallet">₿</span>
          <span class="card-title">SALDO</span>
          <strong class="card-value">Rp {{ number_format($balance, 0, ',', '.') }}</strong>
        </article>
      </section>

      <section class="dashboard-grid">
        <article class="chart-card">
          <div class="card-header">
            <h3>EXPENSES CHART</h3>
            <span class="chart-period">Each Semester</span>
          </div>
          @php
            $maxExpenseTotal = max($expenseCategories->max('total'), 1);
            $labelStep = ceil($maxExpenseTotal / 8 / 100000) * 100000;
            $maxAxis = max($labelStep * 8, $maxExpenseTotal);
            $yLabels = [];
            for ($i = 8; $i >= 0; $i--) {
                $yLabels[] = $i * $labelStep;
            }
          @endphp
          <div class="chart-placeholder">
            <div class="chart-y-labels">
              @foreach($yLabels as $value)
                <span>Rp {{ number_format($value, 0, ',', '.') }}</span>
              @endforeach
            </div>
            <div class="chart-scrollable">
              <div class="chart-bars">
                @foreach($expenseCategories as $category)
                  <div class="chart-bar">
                    <div class="bar-fill" style="height: {{ ($category->total / $maxAxis) * 100 }}%;"></div>
                  </div>
                @endforeach
              </div>
              <div class="chart-base-line"></div>
              <div class="chart-x-labels">
                @foreach($expenseCategories as $category)
                  <span>{{ $category->category }}</span>
                @endforeach
              </div>
            </div>
          </div>
        </article>

        <article class="recent-card">
          <div class="card-header">
            <h3>RECENT</h3>
          </div>
          <div class="recent-list">
            @forelse($recentTransactions as $item)
              <div class="recent-item {{ $item->type === 'Pemasukan' ? 'income-row' : 'expense-row' }}">
                <div>
                  <p class="recent-label">{{ $item->category }}</p>
                  <p class="recent-date">{{ \Illuminate\Support\Carbon::parse($item->tanggal)->format('d/m/Y') }}</p>
                </div>
                <strong>{{ $item->type === 'Pemasukan' ? '+' : '-' }}{{ number_format($item->amount, 0, ',', '.') }}</strong>
              </div>
            @empty
              <div class="recent-item empty">Belum ada transaksi.</div>
            @endforelse
          </div>
        </article>
      </section>
    </main>
  </div>
</body>
</html>
