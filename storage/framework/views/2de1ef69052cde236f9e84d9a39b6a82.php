<div id="sidebar-overlay" class="sidebar-overlay" data-turbo-permanent></div>

<!-- Sidebar -->
<aside id="main-sidebar" class="sidebar" data-turbo-permanent>
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
      <a href="<?php echo e(route('pengeluaran.pilih')); ?>" class="nav-item <?php echo e(request()->routeIs('pengeluaran.*') ? 'active' : ''); ?>">
        <svg viewBox="0 0 24 24" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M12 8v4l3 3"/></svg>
        Pengeluaran
      </a>
      <a href="<?php echo e(route('pemasukan.pilih')); ?>" class="nav-item <?php echo e(request()->routeIs('pemasukan.*') ? 'active' : ''); ?>">
        <svg viewBox="0 0 24 24" stroke-width="1.8"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        Pemasukan
      </a>
    <?php endif; ?>
    <a href="<?php echo e(route('struk')); ?>" class="nav-item <?php echo e(request()->routeIs('struk') ? 'active' : ''); ?>">
      <svg viewBox="0 0 24 24" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
      Galeri Struk
    </a>

    <a href="<?php echo e(route('laporan')); ?>" class="nav-item <?php echo e(request()->routeIs('laporan') ? 'active' : ''); ?>">
      <svg viewBox="0 0 24 24" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
      Laporan
    </a>
    <?php if (! (session('user_role') == 'Kepala Lab')): ?>
      <a href="<?php echo e(route('recycle')); ?>" class="nav-item <?php echo e(request()->routeIs('recycle') ? 'active' : ''); ?>">
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
<?php /**PATH C:\Users\LOQ\OneDrive\Documents\codingan\TUBES\LabMoneyLens\resources\views/includes/sidebar.blade.php ENDPATH**/ ?>