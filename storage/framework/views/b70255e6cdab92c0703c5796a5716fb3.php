<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
  <title>Pilih Cara Input Pengeluaran — LabMoneyLens</title>
  <?php echo app('Illuminate\Foundation\Vite')(['resources/css/style.css','resources/css/welcome.css','resources/js/script.js']); ?>

  <style>
    .main {
      flex-direction: column;
      align-items: center;
      padding: 32px 24px;
      gap: 28px;
      overflow-y: auto;
    }

    .page-wrapper {
      width: 100%;
      max-width: 800px;
      display: flex;
      flex-direction: column;
      gap: 28px;
    }

    .page-hero {
      background: linear-gradient(135deg, #7c2d12 0%, #b91c1c 60%, #dc2626 100%);
      border-radius: 20px;
      padding: 28px 32px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 20px;
      box-shadow: 0 8px 24px rgba(220, 38, 38, 0.25);
    }

    .page-hero-info h1 {
      font-size: 28px;
      font-weight: 800;
      color: #fff;
      letter-spacing: -0.5px;
    }

    .page-hero-info p {
      font-size: 13px;
      color: rgba(255,255,255,0.85);
      margin-top: 8px;
      line-height: 1.6;
    }

    .choice-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
    }

    .choice-card {
      background: linear-gradient(135deg, #ffffff 0%, #fff9f9 100%);
      border: 2px solid #fecaca;
      border-radius: 16px;
      padding: 24px;
      cursor: pointer;
      transition: all 0.3s ease;
      text-decoration: none;
      display: flex;
      flex-direction: column;
      gap: 16px;
      color: inherit;
    }

    .choice-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 32px rgba(220, 38, 38, 0.15);
      border-color: #ef4444;
    }

    .choice-icon {
      width: 48px;
      height: 48px;
      background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
    }

    .choice-card:hover .choice-icon {
      background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
      transform: scale(1.1);
    }

    .choice-header {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .choice-title {
      font-size: 16px;
      font-weight: 800;
      color: #991b1b;
    }

    .choice-subtitle {
      font-size: 12px;
      color: #7f1d1d;
      line-height: 1.5;
    }

    .choice-features {
      font-size: 11px;
      color: #64748b;
      line-height: 1.8;
      border-top: 1px solid #fecaca;
      padding-top: 12px;
    }

    .feature-item {
      display: flex;
      align-items: center;
      gap: 6px;
      margin-bottom: 4px;
    }

    .feature-item:last-child {
      margin-bottom: 0;
    }

    .feature-icon {
      width: 16px;
      height: 16px;
      border-radius: 50%;
      background: #fecaca;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 10px;
      color: #ef4444;
      flex-shrink: 0;
    }

    .choice-cta {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px;
      margin-top: auto;
      font-weight: 600;
      color: #dc2626;
      font-size: 13px;
    }

    .back-btn {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      color: #dc2626;
      text-decoration: none;
      font-weight: 600;
      font-size: 13px;
      margin-bottom: 10px;
    }

    .back-btn:hover {
      text-decoration: underline;
    }

    @media (max-width: 600px) {
      .choice-grid {
        grid-template-columns: 1fr;
      }

      .page-hero {
        flex-direction: column;
        align-items: flex-start;
      }
    }
  </style>
</head>
<body>
  <div class="app">
    <!-- Hamburger -->
    <button id="hamburger-menu" class="hamburger-menu" aria-label="Toggle Menu">
      <span class="hamburger-line"></span>
      <span class="hamburger-line"></span>
      <span class="hamburger-line"></span>
    </button>

    <?php echo $__env->make('includes.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <main class="main">
      <div class="page-wrapper">

        <!-- Page Hero -->
        <div class="page-hero">
          <div class="page-hero-info">
            <h1>Input Pengeluaran</h1>
            <p>Pilih cara input pengeluaran yang paling sesuai untuk Anda</p>
          </div>
        </div>

        <!-- Choice Grid -->
        <div class="choice-grid">
          <!-- Manual Option -->
          <a href="<?php echo e(route('pengeluaran.manual')); ?>" class="choice-card">
            <div class="choice-icon">✏️</div>
            <div class="choice-header">
              <div class="choice-title">Input Manual</div>
              <div class="choice-subtitle">Catat tanpa perlu foto struk</div>
            </div>
            <div class="choice-features">
              <div class="feature-item">
                <span class="feature-icon">✓</span>
                <span>Langsung menyimpan</span>
              </div>
              <div class="feature-item">
                <span class="feature-icon">✓</span>
                <span>Cocok untuk transfer bank</span>
              </div>
              <div class="feature-item">
                <span class="feature-icon">✓</span>
                <span>Cepat dan mudah</span>
              </div>
            </div>
            <div class="choice-cta">
              <span>Mulai Input</span>
              <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" width="16" height="16"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </div>
          </a>

          <!-- Otomatis Option -->
          <a href="<?php echo e(route('pengeluaran.otomatis')); ?>" class="choice-card">
            <div class="choice-icon">📸</div>
            <div class="choice-header">
              <div class="choice-title">Input Otomatis</div>
              <div class="choice-subtitle">Scan foto struk & parsing otomatis</div>
            </div>
            <div class="choice-features">
              <div class="feature-item">
                <span class="feature-icon">✓</span>
                <span>Upload foto struk</span>
              </div>
              <div class="feature-item">
                <span class="feature-icon">✓</span>
                <span>AI parsing otomatis</span>
              </div>
              <div class="feature-item">
                <span class="feature-icon">✓</span>
                <span>Perlu validasi manual</span>
              </div>
            </div>
            <div class="choice-cta">
              <span>Mulai Input</span>
              <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" width="16" height="16"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </div>
          </a>
        </div>

      </div>
    </main>
  </div>
</body>
</html>
<?php /**PATH C:\Users\LOQ\OneDrive\Documents\codingan\TUBES\LabMoneyLens\resources\views/pengeluaran_pilih.blade.php ENDPATH**/ ?>