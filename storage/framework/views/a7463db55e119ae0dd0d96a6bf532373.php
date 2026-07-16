<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Laporan — Dashboard</title>
  <?php echo app('Illuminate\Foundation\Vite')(['resources/css/style.css','resources/css/laporan.css','resources/js/script.js']); ?>
  
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
      <a href="<?php echo e(route('profile')); ?>" class="sidebar-user" style="display:flex; align-items:center; gap:14px; text-decoration:none; color:inherit;">
        <div class="avatar">
          <?php if(!empty(session('user_photo'))): ?>
            <img src="<?php echo e(asset('storage/' . session('user_photo'))); ?>" alt="Foto Profil" style="width:48px;height:48px;border-radius:50%;object-fit:cover;" />
          <?php else: ?>
            <svg viewBox="0 0 24 24" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
          <?php endif; ?>
        </div>
        <div>
          <div class="sidebar-username"><?php echo e(session('user_name', 'USERNAME')); ?></div>
          <div class="sidebar-role"><?php echo e(session('user_role', 'Administrator')); ?></div>
        </div>
      </a>

      <nav class="sidebar-nav">
        <a href="<?php echo e(route('dashboard')); ?>" class="nav-item">
          <svg viewBox="0 0 24 24" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
          Dashboard
        </a>

        <?php if (! (session('user_role') == 'Kepala Lab')): ?>
          <a href="<?php echo e(route('welcome')); ?>" class="nav-item">
            <svg viewBox="0 0 24 24" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M12 8v4l3 3"/></svg>
            Pengeluaran
          </a>

          <a href="<?php echo e(route('pemasukan')); ?>" class="nav-item">
            <svg viewBox="0 0 24 24" stroke-width="1.8"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            Pemasukan
          </a>
        <?php endif; ?>

        <a href="<?php echo e(route('struk')); ?>" class="nav-item">
          <svg viewBox="0 0 24 24" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
          Galeri Struk
        </a>

        <a href="<?php echo e(route('laporan')); ?>" class="nav-item active">
          <svg viewBox="0 0 24 24" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
          Laporan
        </a>

        <?php if (! (session('user_role') == 'Kepala Lab')): ?>
          <a href="<?php echo e(route('recycle')); ?>" class="nav-item">
            <svg viewBox="0 0 24 24" stroke-width="1.8"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
            Back Up
          </a>
        <?php endif; ?>
      </nav>

      <div class="sidebar-logout">
        <form action="<?php echo e(route('logout')); ?>" method="POST">
          <?php echo csrf_field(); ?>
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

          <?php if($records->isEmpty()): ?>
            <span class="download-btn" style="pointer-events:none; opacity:0.6;">Unduh</span>
          <?php else: ?>
            <a href="<?php echo e(route('laporan', array_merge(request()->query(), ['export' => 'csv']))); ?>" class="download-btn">Unduh</a>
          <?php endif; ?>
        </div>

        <div class="report-cards">
          <article class="report-card expense-card">
            <span class="report-card-label">Pengeluaran</span>
            <span class="report-card-value" id="live-report-expense"><?php echo e(rupiah($totalExpense)); ?></span>
            <span class="report-card-note">Total semua pengeluaran</span>
          </article>
          <article class="report-card income-card">
            <span class="report-card-label">Pemasukan</span>
            <span class="report-card-value" id="live-report-income"><?php echo e(rupiah($totalIncome)); ?></span>
            <span class="report-card-note">Total semua pemasukan</span>
          </article>
          <article class="report-card balance-card">
            <span class="report-card-label">Saldo Bersih</span>
            <span class="report-card-value" id="live-report-balance"><?php echo e(rupiah(abs($balance))); ?></span>
            <span class="report-card-note">Selisih pemasukan dan pengeluaran</span>
          </article>
        </div>

        <!-- Preview removed; actions merged into main records table below -->

        <form method="get" action="<?php echo e(route('laporan')); ?>" class="filter-form">
          <div class="form-group">
            <label class="form-label" for="month">Bulan</label>
            <input type="month" class="form-input" id="month" name="month" value="<?php echo e($month); ?>" />
          </div>

          <div class="form-group">
            <label class="form-label" for="category">Kategori</label>
            <select class="form-input" id="category" name="category">
              <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e(strtolower($item)); ?>" <?php echo e(($category ?? 'semua') === strtolower($item) ? 'selected' : ''); ?>><?php echo e($item); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody id="live-report-body">
              <?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                  <td><?php echo e($row->kategori); ?></td>
                  <td><?php echo e($row->uraian ?? '-'); ?></td>
                  <td><?php echo e(rupiah($row->jumlah)); ?></td>
                  <td><?php echo e(\Illuminate\Support\Carbon::parse($row->created_at)->format('d/m/Y H:i')); ?></td>
                  <td><?php echo e($row->tipe); ?></td>
                  <td class="action-cell">
                    <?php if($row->tipe === 'Pemasukan'): ?>
                      <a href="<?php echo e(route('pemasukan.edit', ['id' => $row->id])); ?>" class="btn-edit">Edit</a>
                      <span class="sep">/</span>
                      <form action="<?php echo e(route('pemasukan.delete', ['id' => $row->id])); ?>" method="POST" style="display:inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn-hapus">Hapus</button>
                      </form>
                    <?php else: ?>
                      <a href="<?php echo e(route('pengeluaran.edit', ['id' => $row->id])); ?>" class="btn-edit">Edit</a>
                      <span class="sep">/</span>
                      <form action="<?php echo e(route('pengeluaran.delete', ['id' => $row->id])); ?>" method="POST" style="display:inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn-hapus">Hapus</button>
                      </form>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                  <td colspan="6">Tidak ada data laporan untuk filter ini.</td>
                </tr>
              <?php endif; ?>
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

    function renderReportRows(records) {
      if (!body) return;

      if (!records.length) {
        body.innerHTML = '<tr><td colspan="6">Tidak ada data laporan untuk filter ini.</td></tr>';
        return;
      }

      body.innerHTML = records.map(function (row) {
        return `<tr>
          <td>${row.kategori}</td>
          <td>${row.uraian || '-'}</td>
          <td>${formatCurrency(row.jumlah)}</td>
          <td>${new Date(row.created_at).toLocaleString('id-ID')}</td>
          <td>${row.tipe}</td>
          <td class="action-cell">
            ${row.tipe === 'Pemasukan'
              ? `<a href="/pemasukan/edit/${row.id}" class="btn-edit">Edit</a><span class="sep">/</span><form action="/pemasukan/delete/${row.id}" method="POST" style="display:inline"><input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>"><button type="submit" class="btn-hapus">Hapus</button></form>`
              : `<a href="/pengeluaran/edit/${row.id}" class="btn-edit">Edit</a><span class="sep">/</span><form action="/pengeluaran/delete/${row.id}" method="POST" style="display:inline"><input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>"><button type="submit" class="btn-hapus">Hapus</button></form>`}
          </td>
        </tr>`;
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

      fetch('<?php echo e(route('laporan.liveData')); ?>?' + params.toString(), {
        headers: { 'Accept': 'application/json' }
      })
        .then(function (res) { return res.ok ? res.json() : null; })
        .then(function (data) {
          if (!data) return;
          renderSummary(data);
          renderReportRows(data.records || []);
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
<?php /**PATH E:\lml\LabMoneyLens\resources\views/laporan.blade.php ENDPATH**/ ?>