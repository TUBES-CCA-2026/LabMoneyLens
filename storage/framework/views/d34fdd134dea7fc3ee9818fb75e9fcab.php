<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Pengeluaran</title>
  <?php echo app('Illuminate\Foundation\Vite')(['resources/css/style.css','resources/css/welcome.css']); ?>
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
        <a href="<?php echo e(route('dashboard')); ?>" class="nav-item">Dashboard</a>
        <a href="<?php echo e(route('welcome')); ?>" class="nav-item active">Pengeluaran</a>
        <a href="<?php echo e(route('pemasukan')); ?>" class="nav-item">Pemasukan</a>
        <a href="<?php echo e(route('laporan')); ?>" class="nav-item">Laporan</a>
        <a href="<?php echo e(route('recycle')); ?>" class="nav-item">Recycle Bin</a>
      </nav>
      <div class="sidebar-logout">
        <form action="<?php echo e(route('logout')); ?>" method="POST">
          <?php echo csrf_field(); ?>
          <button type="submit" class="logout-btn">Log-out</button>
        </form>
      </div>
    </aside>
    <main class="main">
      <section class="input-panel">
        <h2 class="panel-title">Edit Pengeluaran</h2>
        <p class="panel-subtitle">Perbarui kategori, tanggal, atau jumlah sebelum dikonfirmasi.</p>
        <form method="POST" action="<?php echo e(route('pengeluaran.update', ['id' => $expense->id])); ?>" class="input-form">
          <?php echo csrf_field(); ?>
          <div class="form-group">
            <label class="form-label" for="id_jenis_pengeluaran">Kategori</label>
            <select class="form-input" id="id_jenis_pengeluaran" name="id_jenis_pengeluaran" required>
              <option value="">Pilih</option>
              <?php $__currentLoopData = $jenis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($j->id); ?>" <?php echo e($expense->id_jenis_pengeluaran == $j->id ? 'selected' : ''); ?>><?php echo e($j->nama); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label" for="nominal">Jumlah (IDR)</label>
            <input type="number" class="form-input" id="nominal" name="nominal" placeholder="Rp" min="0" required value="<?php echo e($expense->jumlah); ?>" />
          </div>
          <div class="form-group">
            <label class="form-label" for="tanggal">Tanggal</label>
            <input type="date" class="form-input" id="tanggal" name="tanggal" required value="<?php echo e($expense->tanggal); ?>" />
          </div>
          <button class="save-btn" type="submit">Simpan Perubahan</button>
        </form>
      </section>
    </main>
  </div>
</body>
</html>
<?php /**PATH C:\Users\ASUS\Documents\yaya\LabMoneyLens\resources\views/pengeluaran_edit.blade.php ENDPATH**/ ?>