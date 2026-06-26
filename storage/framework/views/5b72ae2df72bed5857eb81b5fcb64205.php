<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Laporan — Dashboard</title>
  <?php echo app('Illuminate\Foundation\Vite')(['resources/css/style.css','resources/css/laporan.css']); ?>
</head>
<body>

  <div class="app">

    <aside class="sidebar">
      <div class="sidebar-user">
        <div class="avatar">
          <svg viewBox="0 0 24 24" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
        </div>
        <div>
          <div class="sidebar-username"><?php echo e(session('user_name', 'USERNAME')); ?></div>
          <div class="sidebar-role"><?php echo e(session('user_role', 'Administrator')); ?></div>
        </div>
      </div>

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

        <a href="<?php echo e(route('laporan')); ?>" class="nav-item active">
          <svg viewBox="0 0 24 24" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
          Laporan
        </a>

        <?php if (! (session('user_role') == 'Kepala Lab')): ?>
          <a href="<?php echo e(route('recycle')); ?>" class="nav-item">
            <svg viewBox="0 0 24 24" stroke-width="1.8"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
            Recycle Bin
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
          <a href="<?php echo e(route('laporan', array_merge(request()->query(), ['export' => 'csv']))); ?>" class="download-btn">Unduh</a>
        </div>

        <div class="report-cards">
          <article class="report-card expense-card">
            <span class="report-card-label">Pengeluaran</span>
            <span class="report-card-value">- <?php echo e(number_format($totalExpense, 0, ',', '.')); ?></span>
            <span class="report-card-note">Total semua pengeluaran</span>
          </article>
          <article class="report-card income-card">
            <span class="report-card-label">Pemasukan</span>
            <span class="report-card-value">+ <?php echo e(number_format($totalIncome, 0, ',', '.')); ?></span>
            <span class="report-card-note">Total semua pemasukan</span>
          </article>
          <article class="report-card balance-card">
            <span class="report-card-label">Saldo Bersih</span>
            <span class="report-card-value"><?php echo e($balance >= 0 ? '+' : '-'); ?> <?php echo e(number_format(abs($balance), 0, ',', '.')); ?></span>
            <span class="report-card-note">Selisih pemasukan dan pengeluaran</span>
          </article>
        </div>

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
                <th>ID Laporan</th>
                <th>Kategori</th>
                <th>Jumlah (IDR)</th>
                <th>Tanggal</th>
                <th>Jenis</th>
              </tr>
            </thead>
            <tbody>
              <?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                  <td><?php echo e($row->id); ?></td>
                  <td><?php echo e($row->kategori); ?></td>
                  <td><?php echo e(number_format($row->jumlah, 0, ',', '.')); ?></td>
                  <td><?php echo e(\Illuminate\Support\Carbon::parse($row->tanggal)->format('d/m/Y')); ?></td>
                  <td><?php echo e($row->tipe); ?></td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                  <td colspan="5">Tidak ada data laporan untuk filter ini.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>
    </main>
  </div>
</body>
</html>
<?php /**PATH E:\LML\LabMoneyLens\resources\views/laporan.blade.php ENDPATH**/ ?>