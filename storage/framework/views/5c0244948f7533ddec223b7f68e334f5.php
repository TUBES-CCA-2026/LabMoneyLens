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

    <?php echo $__env->make('includes.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

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
                      <form action="<?php echo e(route('pemasukan.delete', ['id' => $row->id])); ?>" method="POST" style="display:inline" data-confirm="soft">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn-hapus">Hapus</button>
                      </form>
                    <?php else: ?>
                      <a href="<?php echo e(route('pengeluaran.edit', ['id' => $row->id])); ?>" class="btn-edit">Edit</a>
                      <span class="sep">/</span>
                      <form action="<?php echo e(route('pengeluaran.delete', ['id' => $row->id])); ?>" method="POST" style="display:inline" data-confirm="soft">
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
              ? `<a href="/pemasukan/edit/${row.id}" class="btn-edit">Edit</a><span class="sep">/</span><form action="/pemasukan/delete/${row.id}" method="POST" style="display:inline" data-confirm="soft"><input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>"><button type="submit" class="btn-hapus">Hapus</button></form>`
              : `<a href="/pengeluaran/edit/${row.id}" class="btn-edit">Edit</a><span class="sep">/</span><form action="/pengeluaran/delete/${row.id}" method="POST" style="display:inline" data-confirm="soft"><input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>"><button type="submit" class="btn-hapus">Hapus</button></form>`}
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
<?php /**PATH D:\TubesWeb\LabMoneyLens\resources\views/laporan.blade.php ENDPATH**/ ?>