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
<<<<<<< HEAD
=======
    .filter-auto-note {
      align-self: center;
      color: #64748b;
      font-size: 13px;
      white-space: nowrap;
    }
>>>>>>> 0026227 (Baru)
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

<<<<<<< HEAD
          <button type="submit" class="apply-filter-btn">Apply Filter</button>
=======
          <span class="filter-auto-note">Filter diterapkan otomatis</span>
>>>>>>> 0026227 (Baru)
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
                @unless(session('user_role') == 'Kepala Lab')
                  <th>Aksi</th>
                @endunless
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
                  $rawTanggal = $first->tanggal;
                  $carbonTanggal = \Illuminate\Support\Carbon::parse($rawTanggal);
                  // If `tanggal` only contains a date (time 00:00), combine date from `tanggal` with time from `created_at`.
                  if ($carbonTanggal->hour === 0 && $carbonTanggal->minute === 0 && strpos($rawTanggal, ' ') === false) {
                    $timePart = \Illuminate\Support\Carbon::parse($first->created_at)->format('H:i');
                    $tanggal = $carbonTanggal->format('d/m/Y') . ' ' . $timePart;
                  } else {
                    $tanggal = $carbonTanggal->format('d/m/Y H:i');
                  }
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
                      @unless(session('user_role') == 'Kepala Lab')
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
                      @endunless
                    @endif
                  </tr>
                @endforeach
              @empty
                <tr>
                  <td colspan="{{ session('user_role') == 'Kepala Lab' ? 6 : 7 }}">Tidak ada data laporan untuk filter ini.</td>
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

    const isKepalaLab = {{ session('user_role') === 'Kepala Lab' ? 'true' : 'false' }};

    let allGroupedRecords = [];
    let currentPage = 1;
    const itemsPerPage = 10;

    function formatCurrency(value) {
      return 'Rp ' + Number(value || 0).toLocaleString('id-ID');
    }

    function renderReportRows(groupedRecords) {
      if (!body) return;

      if (!groupedRecords || !groupedRecords.length) {
        body.innerHTML = `<tr><td colspan="${isKepalaLab ? 6 : 7}" style="text-align:center;padding:20px;">Tidak ada data laporan untuk filter ini.</td></tr>`;
        return;
      }

      const startIndex = (currentPage - 1) * itemsPerPage;
      const endIndex = startIndex + itemsPerPage;
      const paginatedRecords = groupedRecords.slice(startIndex, endIndex);

      body.innerHTML = paginatedRecords.map(function (group) {
        const count = group.items.length;
        const total = formatCurrency(group.total);
        const kategori = group.kategori;
        const tipe = group.tipe;
        
        // Format date to match php's format d/m/Y H:i
        const dateObj = new Date(group.display_datetime || group.created_at);
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
            if (!isKepalaLab) {
              html += `<td class="action-cell" rowspan="${count}">`;
              if (tipe === 'Pemasukan') {
                html += `<a href="/pemasukan/edit/${firstId}" class="btn-edit">Edit</a><span class="sep">/</span><form action="/pemasukan/delete/${firstId}" method="POST" style="display:inline" data-confirm="soft"><input type="hidden" name="_token" value="{{ csrf_token() }}"><button type="submit" class="btn-hapus">Hapus</button></form>`;
              } else {
                html += `<a href="/pengeluaran/edit/${firstId}" class="btn-edit">Edit</a><span class="sep">/</span><form action="/pengeluaran/delete/${firstId}" method="POST" style="display:inline" data-confirm="soft"><input type="hidden" name="_token" value="{{ csrf_token() }}"><button type="submit" class="btn-hapus">Hapus</button></form>`;
              }
              html += `</td>`;
            }
          }
          html += '</tr>';
        });
        return html;
      }).join('');
    }

    function renderPagination(totalItems) {
      const totalPages = Math.ceil(totalItems / itemsPerPage);
      let paginationContainer = document.getElementById('client-pagination');
      
      if (!paginationContainer) {
        paginationContainer = document.createElement('div');
        paginationContainer.id = 'client-pagination';
        paginationContainer.style.display = 'flex';
        paginationContainer.style.justifyContent = 'center';
        paginationContainer.style.gap = '10px';
        paginationContainer.style.marginTop = '20px';
        paginationContainer.style.padding = '10px 20px';
        // Insert after table-wrap
        const tableWrap = document.querySelector('.table-wrap');
        if (tableWrap) {
          tableWrap.parentNode.insertBefore(paginationContainer, tableWrap.nextSibling);
        }
      }

      if (totalItems < itemsPerPage) {
        paginationContainer.innerHTML = '';
        return;
      }

      let html = '';
      
      // Prev Button
      if (currentPage > 1) {
        html += `<button onclick="goToPage(${currentPage - 1})" style="padding:8px 12px; border:1px solid #cbd5e1; background:#fff; color:#0f766e; border-radius:6px; cursor:pointer;">&laquo; Prev</button>`;
      } else {
        html += `<button disabled style="padding:8px 12px; border:1px solid #e2e8f0; background:#f8fafc; color:#94a3b8; border-radius:6px; cursor:not-allowed;">&laquo; Prev</button>`;
      }

      // Page numbers
      for (let i = 1; i <= totalPages; i++) {
        if (i === currentPage) {
          html += `<button style="padding:8px 14px; border:1px solid #0d9488; background:#0d9488; color:#fff; border-radius:6px; font-weight:bold;">${i}</button>`;
        } else {
          html += `<button onclick="goToPage(${i})" style="padding:8px 14px; border:1px solid #cbd5e1; background:#fff; color:#0f766e; border-radius:6px; cursor:pointer;">${i}</button>`;
        }
      }

      // Next Button
      if (currentPage < totalPages) {
        html += `<button onclick="goToPage(${currentPage + 1})" style="padding:8px 12px; border:1px solid #cbd5e1; background:#fff; color:#0f766e; border-radius:6px; cursor:pointer;">Next &raquo;</button>`;
      } else {
        html += `<button disabled style="padding:8px 12px; border:1px solid #e2e8f0; background:#f8fafc; color:#94a3b8; border-radius:6px; cursor:not-allowed;">Next &raquo;</button>`;
      }

      paginationContainer.innerHTML = html;
    }

    window.goToPage = function(page) {
      currentPage = page;
      renderReportRows(allGroupedRecords);
      renderPagination(allGroupedRecords.length);
    };

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

    function refreshReport(resetPage = false) {
      if (resetPage) currentPage = 1;
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
          allGroupedRecords = data.groupedRecords || [];
          
          // Re-calculate page if somehow total pages is less than current page
          const maxPage = Math.ceil(allGroupedRecords.length / itemsPerPage);
          if (currentPage > maxPage && maxPage > 0) currentPage = maxPage;
          
          renderReportRows(allGroupedRecords);
          renderPagination(allGroupedRecords.length);
        })
        .catch(function (err) {
          console.warn('Polling laporan gagal:', err);
        });
    }

<<<<<<< HEAD
    if (monthInput) monthInput.addEventListener('change', () => refreshReport(true));
    if (categorySelect) categorySelect.addEventListener('change', () => refreshReport(true));
=======
    // Filter diterapkan otomatis saat bulan atau kategori berubah.
    // Tidak lagi bergantung pada tombol Apply Filter yang sebelumnya tidak
    // memberikan feedback yang jelas karena tabel juga diperbarui via AJAX.
    function applyAutoFilter() {
      const params = new URLSearchParams();
      if (monthInput && monthInput.value) params.set('month', monthInput.value);
      if (categorySelect && categorySelect.value) params.set('category', categorySelect.value);
      const query = params.toString();
      const target = query ? ('{{ route('laporan') }}?' + query) : '{{ route('laporan') }}';
      window.history.replaceState({}, '', target);
      refreshReport(true);
    }

    if (monthInput) monthInput.addEventListener('change', applyAutoFilter);
    if (categorySelect) categorySelect.addEventListener('change', applyAutoFilter);
>>>>>>> 0026227 (Baru)

    refreshReport();
    setInterval(() => refreshReport(false), 4000);
  })();
  </script>
</body>
</html>
