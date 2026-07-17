<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Laporan — Dashboard</title>
  @vite(['resources/css/style.css','resources/css/laporan.css','resources/js/script.js'])
  
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
      <section class="report-panel">
        <div class="report-header">
          <div>
            <h2 class="panel-title">Laporan Keuangan</h2>
            <p class="panel-subtitle">Laporan berdasarkan pemasukan dan pengeluaran yang sudah diinput.</p>
          </div>

          @if($records->isEmpty())
            <span class="download-btn" style="pointer-events:none; opacity:0.6;">Unduh</span>
          @else
            <a href="{{ route('laporan', array_merge(request()->query(), ['export' => 'csv'])) }}" class="download-btn">Unduh</a>
          @endif
        </div>

        <div class="report-cards">
          <article class="report-card expense-card">
            <span class="report-card-label">Pengeluaran</span>
            <span class="report-card-value" id="live-report-expense">{{ rupiah($totalExpense) }}</span>
            <span class="report-card-note">Total semua pengeluaran</span>
          </article>
          <article class="report-card income-card">
            <span class="report-card-label">Pemasukan</span>
            <span class="report-card-value" id="live-report-income">{{ rupiah($totalIncome) }}</span>
            <span class="report-card-note">Total semua pemasukan</span>
          </article>
          <article class="report-card balance-card">
            <span class="report-card-label">Saldo Bersih</span>
            <span class="report-card-value" id="live-report-balance">{{ rupiah(abs($balance)) }}</span>
            <span class="report-card-note">Selisih pemasukan dan pengeluaran</span>
          </article>
        </div>

        <!-- Preview removed; actions merged into main records table below -->

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
                <th>Kategori</th>
                <th>Uraian</th>
                <th>Nominal (IDR)</th>
                <th>Tanggal & Waktu</th>
                <th>Jenis</th>
                <th>Total (IDR)</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody id="live-report-body">
              @forelse($groupedRecords as $group)
                @php
                  $first = $group->first();
                  $count = $group->count();
                  $total = $group->sum('jumlah');
                  $tipe = $first->tipe;
                  $kategori = $first->kategori;
                  $tanggal = \Illuminate\Support\Carbon::parse($first->created_at)->format('d/m/Y H:i');
                @endphp
                @foreach($group as $index => $row)
                  <tr>
                    @if($index === 0)
                      <td rowspan="{{ $count }}">{{ $kategori }}</td>
                    @endif
                    <td>{{ $row->uraian ?? '-' }}</td>
                    <td>{{ rupiah($row->jumlah) }}</td>
                    @if($index === 0)
                      <td rowspan="{{ $count }}">{{ $tanggal }}</td>
                      <td rowspan="{{ $count }}">{{ $tipe }}</td>
                      <td rowspan="{{ $count }}">{{ rupiah($total) }}</td>
                      <td class="action-cell" rowspan="{{ $count }}">
                        @if($tipe === 'Pemasukan')
                          <a href="{{ route('pemasukan.edit', ['id' => $first->id]) }}" class="btn-edit">Edit</a>
                          <span class="sep">/</span>
                          <form action="{{ route('pemasukan.delete', ['id' => $first->id]) }}" method="POST" style="display:inline" data-confirm="soft">
                            @csrf
                            <button type="submit" class="btn-hapus">Hapus</button>
                          </form>
                        @else
                          <a href="{{ route('pengeluaran.edit', ['id' => $first->id]) }}" class="btn-edit">Edit</a>
                          <span class="sep">/</span>
                          <form action="{{ route('pengeluaran.delete', ['id' => $first->id]) }}" method="POST" style="display:inline" data-confirm="soft">
                            @csrf
                            <button type="submit" class="btn-hapus">Hapus</button>
                          </form>
                        @endif
                      </td>
                    @endif
                  </tr>
                @endforeach
              @empty
                <tr>
                  <td colspan="6">Tidak ada data laporan untuk filter ini.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </section>
    </main>
  </div>
  <script>
  (function () {
    'use strict';

    const body = document.getElementById('live-report-body');
    const expenseEl = document.getElementById('live-report-expense');
    const incomeEl = document.getElementById('live-report-income');
    const balanceEl = document.getElementById('live-report-balance');
    const monthInput = document.getElementById('month');
    const categorySelect = document.getElementById('category');

    function formatCurrency(value) {
      return 'Rp ' + Number(value || 0).toLocaleString('id-ID');
    }

    function renderReportRows(groupedRecords) {
      if (!body) return;

      if (!groupedRecords || !groupedRecords.length) {
        body.innerHTML = '<tr><td colspan="7">Tidak ada data laporan untuk filter ini.</td></tr>';
        return;
      }

      body.innerHTML = groupedRecords.map(function (group) {
        const count = group.items.length;
        const total = formatCurrency(group.total);
        const kategori = group.kategori;
        const tipe = group.tipe;
        
        // Format date to match php's format d/m/Y H:i
        const dateObj = new Date(group.created_at);
        const day = String(dateObj.getDate()).padStart(2, '0');
        const month = String(dateObj.getMonth() + 1).padStart(2, '0');
        const year = dateObj.getFullYear();
        const hours = String(dateObj.getHours()).padStart(2, '0');
        const minutes = String(dateObj.getMinutes()).padStart(2, '0');
        const tanggal = `${day}/${month}/${year} ${hours}:${minutes}`;

        const firstId = group.items[0].id;
        
        let html = '';
        group.items.forEach(function(row, index) {
          html += '<tr>';
          if (index === 0) {
            html += `<td rowspan="${count}">${kategori}</td>`;
          }
          html += `<td>${row.uraian || '-'}</td>`;
          html += `<td>${formatCurrency(row.jumlah)}</td>`;
          if (index === 0) {
            html += `<td rowspan="${count}">${tanggal}</td>`;
            html += `<td rowspan="${count}">${tipe}</td>`;
            html += `<td rowspan="${count}">${total}</td>`;
            html += `<td class="action-cell" rowspan="${count}">`;
            if (tipe === 'Pemasukan') {
              html += `<a href="/pemasukan/edit/${firstId}" class="btn-edit">Edit</a><span class="sep">/</span><form action="/pemasukan/delete/${firstId}" method="POST" style="display:inline" data-confirm="soft"><input type="hidden" name="_token" value="{{ csrf_token() }}"><button type="submit" class="btn-hapus">Hapus</button></form>`;
            } else {
              html += `<a href="/pengeluaran/edit/${firstId}" class="btn-edit">Edit</a><span class="sep">/</span><form action="/pengeluaran/delete/${firstId}" method="POST" style="display:inline" data-confirm="soft"><input type="hidden" name="_token" value="{{ csrf_token() }}"><button type="submit" class="btn-hapus">Hapus</button></form>`;
            }
            html += `</td>`;
          }
          html += '</tr>';
        });
        return html;
      }).join('');
    }

    function renderSummary(data) {
      if (expenseEl) {
        expenseEl.textContent = formatCurrency(data.totalExpense);
      }
      if (incomeEl) {
        incomeEl.textContent = formatCurrency(data.totalIncome);
      }
      if (balanceEl) {
        balanceEl.textContent = formatCurrency(Math.abs(data.balance));
      }
    }

    function refreshReport() {
      const params = new URLSearchParams();
      if (monthInput && monthInput.value) params.set('month', monthInput.value);
      if (categorySelect && categorySelect.value) params.set('category', categorySelect.value);

      fetch('{{ route('laporan.liveData') }}?' + params.toString(), {
        headers: { 'Accept': 'application/json' }
      })
        .then(function (res) { return res.ok ? res.json() : null; })
        .then(function (data) {
          if (!data) return;
          renderSummary(data);
          renderReportRows(data.groupedRecords || []);
        })
        .catch(function (err) {
          console.warn('Polling laporan gagal:', err);
        });
    }

    refreshReport();
    setInterval(refreshReport, 4000);
  })();
  </script>
</body>
</html>
