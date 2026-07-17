<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
  <title>Back Up — Dashboard</title>
  <?php echo app('Illuminate\Foundation\Vite')(['resources/css/style.css','resources/css/recyclebin.css','resources/js/script.js']); ?>
  
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
      <section class="recycle-panel">
        <div class="recycle-header">
          <div>
            <h2 class="panel-title">Back Up</h2>
            <p class="panel-subtitle">Item pemasukan dan pengeluaran yang sudah dihapus.</p>
          </div>
          <div class="status-cards">
            <article class="status-card trash-card">
              <span class="status-label">TOTAL ITEMS</span>
              <strong class="status-value"><?php echo e($totalItems); ?> Record</strong>
            </article>
            <article class="status-card value-card">
              <span class="status-label">TOTAL VALUE</span>
              <strong class="status-value">Rp <?php echo e(number_format($totalValue, 0, ',', '.')); ?></strong>
            </article>
          </div>
        </div>

        <?php if(session('success')): ?>
          <div class="success-message"><?php echo e(session('success')); ?></div>
        <?php endif; ?>

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
              <?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                  <td><?php echo e($item->kategori); ?></td>
                  <td><?php echo e(number_format($item->jumlah, 0, ',', '.')); ?></td>
                  <td><?php echo e(\Illuminate\Support\Carbon::parse($item->tanggal)->format('d/m/Y')); ?></td>
                  <td><?php echo e($item->tipe); ?></td>
                  <td class="action-cell">
                    <form action="<?php echo e(route('recycle.restore', ['type' => strtolower($item->tipe), 'id' => $item->id])); ?>" method="POST" class="action-form">
                      <?php echo csrf_field(); ?>
                      <button type="submit" class="btn-restore">Pulih</button>
                    </form>
                    <form action="<?php echo e(route('recycle.forceDelete', ['type' => strtolower($item->tipe), 'id' => $item->id])); ?>" method="POST" class="action-form" data-confirm="permanent">
                      <?php echo csrf_field(); ?>
                      <button type="submit" class="btn-delete">Hapus</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                  <td colspan="5" class="empty-row">Tidak ada item di back up.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <div class="recycle-actions">
          <form action="<?php echo e(route('recycle.restoreAll')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <button type="submit" class="restore-all-btn">Restore All</button>
          </form>
          <form action="<?php echo e(route('recycle.emptyTrash')); ?>" method="POST" data-confirm="permanent">
            <?php echo csrf_field(); ?>
            <button type="submit" class="empty-trash-btn">Empty Trash</button>
          </form>
        </div>
      </section>
    </main>
  </div>
</body>
</html>
<?php /**PATH C:\Users\LOQ\OneDrive\Documents\codingan\New folder\LabMoneyLens\resources\views/recycle.blade.php ENDPATH**/ ?>