<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
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

    @include('includes.sidebar')

    <main class="main dashboard-main">
      <section class="dashboard-cards">
        <article class="dashboard-card income-card">
          <span class="card-icon up">▲</span>
          <span class="card-title">INCOME</span>
          <strong class="card-value" id="live-total-income">{{ rupiah($totalIncome) }}</strong>
        </article>

        <article class="dashboard-card expense-card">
          <span class="card-icon down">▼</span>
          <span class="card-title">EXPENSES</span>
          <strong class="card-value" id="live-total-expense">{{ rupiah($totalExpense) }}</strong>
        </article>

        <article class="dashboard-card balance-card">
          <span class="card-icon wallet">₿</span>
          <span class="card-title">SALDO</span>
          <strong class="card-value" id="live-balance">{{ rupiah($balance) }}</strong>
        </article>
      </section>

      <section class="dashboard-grid">
        <article class="chart-card" style="position:relative;">
          <div class="card-header">
            <h3>EXPENSES CHART</h3>
            <!-- ── Semester Filter Dropdown ── -->
            <div class="semester-filter" id="semester-filter">
              <button class="semester-btn" id="semester-btn" type="button" aria-haspopup="listbox" aria-expanded="false">
                <span id="semester-label">Sem {{ $selectedSemester }} {{ $selectedYear }}</span>
                <svg class="semester-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="14" height="14"><polyline points="6 9 12 15 18 9"/></svg>
              </button>
              <div class="semester-dropdown" id="semester-dropdown" role="listbox" aria-label="Pilih Semester">
                @foreach($availableYears as $yr)
                  <div class="semester-group-label">{{ $yr }}</div>
                  <div class="semester-option {{ $selectedYear == $yr && $selectedSemester == 1 ? 'active' : '' }}"
                       data-year="{{ $yr }}" data-sem="1" role="option"
                       aria-selected="{{ $selectedYear == $yr && $selectedSemester == 1 ? 'true' : 'false' }}">
                    Sem 1 &mdash; Jan s/d Jun {{ $yr }}
                  </div>
                  <div class="semester-option {{ $selectedYear == $yr && $selectedSemester == 2 ? 'active' : '' }}"
                       data-year="{{ $yr }}" data-sem="2" role="option"
                       aria-selected="{{ $selectedYear == $yr && $selectedSemester == 2 ? 'true' : 'false' }}">
                    Sem 2 &mdash; Jul s/d Des {{ $yr }}
                  </div>
                @endforeach
              </div>
            </div>
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

          <div class="chart-placeholder" id="chart-placeholder">
            <div class="chart-y-labels" id="chart-y-labels">
              @foreach($yLabels as $value)
                <span>{{ rupiah($value) }}</span>
              @endforeach
            </div>
            <div class="chart-scrollable">
              <div class="chart-bars" id="chart-bars">
                @foreach($expenseCategories as $category)
                  <div class="chart-bar">
                    <div class="bar-fill" style="height: {{ ($category->total / $maxAxis) * 100 }}%;"></div>
                  </div>
                @endforeach
              </div>
              <div class="chart-base-line"></div>
              <div class="chart-x-labels" id="chart-x-labels">
                @foreach($expenseCategories as $category)
                  <span>{{ $category->category }}</span>
                @endforeach
              </div>
            </div>
          </div>

          <!-- Loading overlay -->
          <div class="chart-loading" id="chart-loading" style="display:none;">
            <div class="chart-spinner"></div>
            <span>Memuat data...</span>
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
                <strong>{{ rupiah($item->amount) }}</strong>
              </div>
            @empty
              <div class="recent-item empty">Belum ada transaksi.</div>
            @endforelse
          </div>
        </article>
      </section>
    </main>
  </div>

  <script>
  (function () {
    'use strict';

    const btn       = document.getElementById('semester-btn');
    const dropdown  = document.getElementById('semester-dropdown');
    const label     = document.getElementById('semester-label');
    const loading   = document.getElementById('chart-loading');
    const chartBars = document.getElementById('chart-bars');
    const chartXLab = document.getElementById('chart-x-labels');
    const chartYLab = document.getElementById('chart-y-labels');
    const totalIncomeEl = document.getElementById('live-total-income');
    const totalExpenseEl = document.getElementById('live-total-expense');
    const balanceEl = document.getElementById('live-balance');
    const recentListEl = document.getElementById('live-recent-list');

    // ── Toggle dropdown ──
    btn.addEventListener('click', function (e) {
      e.stopPropagation();
      const isOpen = dropdown.classList.toggle('open');
      btn.setAttribute('aria-expanded', isOpen);
    });

    // ── Tutup dropdown kalau klik di luar ──
    document.addEventListener('click', function () {
      dropdown.classList.remove('open');
      btn.setAttribute('aria-expanded', 'false');
    });
    dropdown.addEventListener('click', function (e) { e.stopPropagation(); });

    // ── Format angka ke "Rp X.XXX" ──
    function formatRp(val) {
      return 'Rp ' + Number(val).toLocaleString('id-ID');
    }

    function renderLiveData(data) {
      if (!data) return;

      if (totalIncomeEl) {
        totalIncomeEl.textContent = formatRp(data.totalIncome);
      }
      if (totalExpenseEl) {
        totalExpenseEl.textContent = formatRp(data.totalExpense);
      }
      if (balanceEl) {
        balanceEl.textContent = formatRp(data.balance);
      }

      if (recentListEl && Array.isArray(data.recentTransactions)) {
        if (!data.recentTransactions.length) {
          recentListEl.innerHTML = '<div class="recent-item empty">Belum ada transaksi.</div>';
          return;
        }

        recentListEl.innerHTML = data.recentTransactions.map(function (item) {
          const amount = formatRp(item.amount);
          return `<div class="recent-item ${item.type === 'Pemasukan' ? 'income-row' : 'expense-row'}">
            <div>
              <p class="recent-label">${item.category}</p>
              <p class="recent-date">${new Date(item.tanggal).toLocaleDateString('id-ID')}</p>
            </div>
            <strong>${amount}</strong>
          </div>`;
        }).join('');
      }
    }

    function refreshLiveData() {
      fetch('/dashboard/live-data', {
        headers: { 'Accept': 'application/json' }
      })
        .then(function (res) { return res.ok ? res.json() : null; })
        .then(function (data) {
          if (data) {
            renderLiveData(data);
          }
        })
        .catch(function (err) {
          console.warn('Polling live data gagal:', err);
        });
    }

    // ── Render ulang chart ──
    function renderChart(categories) {
      const max    = Math.max(...categories.map(c => c.total), 1);
      const step   = Math.ceil(max / 8 / 100000) * 100000 || 100000;
      const maxAx  = Math.max(step * 8, max);

      // Y labels
      let yHtml = '';
      for (let i = 8; i >= 0; i--) {
        yHtml += `<span>${formatRp(i * step)}</span>`;
      }
      chartYLab.innerHTML = yHtml;

      // Bars
      let barsHtml = '';
      categories.forEach(cat => {
        const pct = maxAx > 0 ? (cat.total / maxAx * 100) : 0;
        barsHtml += `<div class="chart-bar">
          <div class="bar-fill" style="height:${pct}%;"></div>
        </div>`;
      });
      chartBars.innerHTML = barsHtml;

      // X labels
      let xHtml = '';
      categories.forEach(cat => {
        xHtml += `<span>${cat.category}</span>`;
      });
      chartXLab.innerHTML = xHtml;
    }

    // ── Pilih semester ──
    document.querySelectorAll('.semester-option').forEach(function (opt) {
      opt.addEventListener('click', function () {
        const year = this.dataset.year;
        const sem  = this.dataset.sem;

        // Update label tombol
        label.textContent = `Sem ${sem} ${year}`;

        // Update active state
        document.querySelectorAll('.semester-option').forEach(o => {
          o.classList.remove('active');
          o.setAttribute('aria-selected', 'false');
        });
        this.classList.add('active');
        this.setAttribute('aria-selected', 'true');

        // Tutup dropdown
        dropdown.classList.remove('open');
        btn.setAttribute('aria-expanded', 'false');

        // Tampilkan loading
        loading.style.display = 'flex';

        // AJAX fetch chart data
        fetch(`/dashboard/chart-data?year=${year}&semester=${sem}`)
          .then(res => res.json())
          .then(data => {
            renderChart(data.categories);
          })
          .catch(err => {
            console.error('Gagal memuat data semester:', err);
          })
          .finally(() => {
            loading.style.display = 'none';
          });
      });
    });

    refreshLiveData();
    setInterval(refreshLiveData, 4000);
  })();
  </script>
</body>
</html>
