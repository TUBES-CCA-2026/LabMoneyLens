<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Laporan — Dashboard</title>
  @vite(['resources/css/style.css','resources/css/laporan.css'])
</head>
<body>

  <div class="app">

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

        <a href="{{ route('welcome') }}" class="nav-item">
          <svg viewBox="0 0 24 24" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M12 8v4l3 3"/></svg>
          Pengeluaran
        </a>

        <a href="{{ route('pemasukan') }}" class="nav-item">
          <svg viewBox="0 0 24 24" stroke-width="1.8"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
          Pemasukan
        </a>

        <a href="{{ route('laporan') }}" class="nav-item active">
          <svg viewBox="0 0 24 24" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
          Laporan
        </a>

        <a href="{{ route('recycle') }}" class="nav-item">
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
      <section class="report-panel">
        <div class="report-header">
          <div>
            <h2 class="panel-title">Laporan Keuangan</h2>
            <p class="panel-subtitle">Laporan berdasarkan pemasukan dan pengeluaran yang sudah diinput.</p>
          </div>
          <a href="{{ route('laporan') }}" class="download-btn">Unduh</a>
        </div>

        <div class="report-cards">
          <article class="report-card expense-card">
            <span class="report-card-label">Pengeluaran</span>
            <span class="report-card-value">- {{ number_format($totalExpense, 0, ',', '.') }}</span>
            <span class="report-card-note">Total semua pengeluaran</span>
          </article>
          <article class="report-card income-card">
            <span class="report-card-label">Pemasukan</span>
            <span class="report-card-value">+ {{ number_format($totalIncome, 0, ',', '.') }}</span>
            <span class="report-card-note">Total semua pemasukan</span>
          </article>
          <article class="report-card balance-card">
            <span class="report-card-label">Saldo Bersih</span>
            <span class="report-card-value">{{ $balance >= 0 ? '+' : '-' }} {{ number_format(abs($balance), 0, ',', '.') }}</span>
            <span class="report-card-note">Selisih pemasukan dan pengeluaran</span>
          </article>
        </div>

        <form method="get" action="{{ route('laporan') }}" class="filter-form">
          <div class="form-group">
            <label class="form-label" for="month">Bulan</label>
            <input type="month" class="form-input" id="month" name="month" value="{{ $month }}" />
          </div>

          <div class="form-group">
            <label class="form-label" for="category">Kategori</label>
            <select class="form-input" id="category" name="category">
              @foreach($categories as $item)
                <option value="{{ strtolower($item) }}" {{ ($category ?? 'semua') === strtolower($item) ? 'selected' : '' }}>{{ $item }}</option>
              @endforeach
            </select>
          </div>

          <button type="submit" class="apply-filter-btn">Apply Filter</button>
        </form>

        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>ID Laporan</th>
                <th>Kategori</th>
                <th>Jumlah (IDR)</th>
                <th>Tanggal</th>
                <th>Jenis</th>
              </tr>
            </thead>
            <tbody>
              @forelse($records as $row)
                <tr>
                  <td>{{ $row->id }}</td>
                  <td>{{ $row->kategori }}</td>
                  <td>{{ number_format($row->jumlah, 0, ',', '.') }}</td>
                  <td>{{ \Illuminate\Support\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                  <td>{{ $row->tipe }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="5">Tidak ada data laporan untuk filter ini.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </section>
    </main>
  </div>
</body>
</html>
