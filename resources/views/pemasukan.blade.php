<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Pemasukan — LabMoneyLens</title>
  @vite(['resources/css/style.css','resources/css/welcome.css','resources/js/script.js'])
  <script>window.receiptParseUrl = "{{ route('receipt.parse') }}";</script>

  <style>
    /* ── Override layout: centered main ── */
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

    /* ── Page Header ── */
    .page-hero {
      background: linear-gradient(135deg, #0f766e 0%, #0d9488 60%, #10b981 100%);
      border-radius: 20px;
      padding: 28px 32px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 20px;
      box-shadow: 0 8px 24px rgba(13, 148, 136, 0.25);
    }

    .page-hero-info h1 {
      font-size: 24px;
      font-weight: 800;
      color: #fff;
      letter-spacing: -0.3px;
    }

    .page-hero-info p {
      font-size: 13px;
      color: rgba(255,255,255,0.8);
      margin-top: 6px;
      line-height: 1.5;
    }

    .hero-balance-badge {
      background: rgba(255,255,255,0.18);
      border: 1.5px solid rgba(255,255,255,0.3);
      border-radius: 16px;
      padding: 14px 22px;
      text-align: center;
      backdrop-filter: blur(8px);
      white-space: nowrap;
      flex-shrink: 0;
    }

    .hero-balance-badge .badge-label {
      font-size: 10px;
      font-weight: 700;
      color: rgba(255,255,255,0.75);
      text-transform: uppercase;
      letter-spacing: 0.8px;
    }

    .hero-balance-badge .badge-value {
      font-size: 20px;
      font-weight: 800;
      color: #fff;
      margin-top: 4px;
    }

    /* ── Form Card ── */
    .form-card {
      background: linear-gradient(135deg, #ffffff 0%, #f9fdfb 100%);
      border-radius: 20px;
      border: 2px solid #e0f7f5;
      box-shadow: 0 4px 16px rgba(13, 148, 136, 0.1);
      overflow: hidden;
    }

    .form-card-header {
      background: linear-gradient(90deg, #f0fdf9 0%, #ecfdf5 100%);
      border-bottom: 2px solid #e0f7f5;
      padding: 18px 28px;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .form-card-header .header-icon {
      width: 40px;
      height: 40px;
      background: linear-gradient(135deg, #0d9488 0%, #059669 100%);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      box-shadow: 0 4px 10px rgba(13, 148, 136, 0.3);
    }

    .form-card-header .header-icon svg {
      width: 20px;
      height: 20px;
      stroke: #fff;
      fill: none;
    }

    .form-card-header h2 {
      font-size: 16px;
      font-weight: 800;
      color: #0f766e;
    }

    .form-card-header p {
      font-size: 12px;
      color: #0d9488;
      margin-top: 2px;
    }

    .form-card-body {
      padding: 28px;
    }

    /* ── Upload zone (compact horizontal) ── */
    .upload-row {
      display: flex;
      align-items: center;
      gap: 16px;
      background: linear-gradient(135deg, rgba(224,247,245,0.4) 0%, transparent 100%);
      border: 2px dashed rgba(13, 148, 136, 0.35);
      border-radius: 14px;
      padding: 16px 20px;
      cursor: pointer;
      transition: all 0.25s ease;
      margin-bottom: 24px;
    }

    .upload-row:hover {
      border-color: #0d9488;
      background: rgba(224,247,245,0.6);
    }

    .upload-row-icon {
      width: 44px;
      height: 44px;
      background: linear-gradient(135deg, #e0f7f5 0%, #ccfbf1 100%);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .upload-row-icon svg {
      width: 22px;
      height: 22px;
      stroke: #0d9488;
      fill: none;
    }

    .upload-row-text {
      flex: 1;
    }

    .upload-row-text strong {
      font-size: 13px;
      font-weight: 700;
      color: #0f766e;
      display: block;
    }

    .upload-row-text span {
      font-size: 11px;
      color: #64748b;
      margin-top: 2px;
      display: block;
    }

    .upload-row-btn {
      background: linear-gradient(135deg, #0d9488 0%, #059669 100%);
      color: #fff;
      border: none;
      border-radius: 10px;
      padding: 8px 18px;
      font-size: 12px;
      font-weight: 700;
      cursor: pointer;
      font-family: inherit;
      transition: all 0.2s ease;
      white-space: nowrap;
    }

    .upload-row-btn:hover {
      background: linear-gradient(135deg, #0a7065 0%, #047857 100%);
      box-shadow: 0 4px 12px rgba(13, 148, 136, 0.3);
      transform: translateY(-1px);
    }

    /* ── Form Grid ── */
    .form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 18px;
    }

    .form-grid .span-2 {
      grid-column: span 2;
    }

    .form-group {
      display: flex;
      flex-direction: column;
      gap: 7px;
    }

    .form-label {
      font-size: 11px;
      font-weight: 700;
      color: #0f766e;
      text-transform: uppercase;
      letter-spacing: 0.6px;
      display: flex;
      align-items: center;
      gap: 5px;
    }

    .form-label .required-dot {
      width: 5px;
      height: 5px;
      background: #ef4444;
      border-radius: 50%;
    }

    .form-input {
      padding: 11px 14px;
      border: 2px solid #ccf0ee;
      border-radius: 10px;
      font-size: 13px;
      font-family: inherit;
      color: #0f766e;
      background: #ffffff;
      outline: none;
      width: 100%;
      transition: all 0.2s ease;
    }

    .form-input:hover { border-color: #a5e8e3; }
    .form-input:focus {
      border-color: #0d9488;
      box-shadow: 0 0 0 4px rgba(13, 148, 136, 0.12);
    }

    select.form-input {
      appearance: none;
      -webkit-appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%230d9488' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 12px center;
      padding-right: 34px;
    }

    textarea.form-input {
      resize: vertical;
      min-height: 76px;
    }

    .nominal-wrapper {
      position: relative;
    }

    .nominal-prefix {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 13px;
      font-weight: 700;
      color: #0d9488;
      pointer-events: none;
    }

    .form-input.with-prefix {
      padding-left: 38px;
    }

    /* ── Form Actions ── */
    .form-actions {
      display: flex;
      gap: 12px;
      margin-top: 24px;
      padding-top: 20px;
      border-top: 2px solid #f0fdf9;
    }

    .save-btn {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      background: linear-gradient(135deg, #0d9488 0%, #059669 100%);
      color: #ffffff;
      border: none;
      border-radius: 12px;
      padding: 14px 24px;
      font-size: 14px;
      font-weight: 700;
      cursor: pointer;
      font-family: inherit;
      letter-spacing: 0.3px;
      transition: all 0.2s ease;
      box-shadow: 0 4px 14px rgba(13, 148, 136, 0.3);
    }

    .save-btn:hover {
      background: linear-gradient(135deg, #0a7065 0%, #047857 100%);
      box-shadow: 0 6px 20px rgba(13, 148, 136, 0.4);
      transform: translateY(-2px);
    }

    .save-btn:active { transform: translateY(0); }

    .save-btn svg {
      width: 18px;
      height: 18px;
      stroke: currentColor;
      fill: none;
    }

    .reset-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      background: transparent;
      color: #64748b;
      border: 2px solid #e2e8f0;
      border-radius: 12px;
      padding: 14px 20px;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      font-family: inherit;
      transition: all 0.2s ease;
    }

    .reset-btn:hover {
      border-color: #cbd5e1;
      background: #f8fafc;
      color: #475569;
    }

    /* ── Table Section ── */
    .table-section {
      background: linear-gradient(135deg, #ffffff 0%, #f9fdfb 100%);
      border-radius: 20px;
      border: 2px solid #e0f7f5;
      box-shadow: 0 4px 16px rgba(13, 148, 136, 0.08);
      overflow: hidden;
    }

    .table-section-header {
      background: linear-gradient(90deg, #f0fdf9 0%, #ecfdf5 100%);
      border-bottom: 2px solid #e0f7f5;
      padding: 16px 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
    }

    .table-section-header h3 {
      font-size: 14px;
      font-weight: 800;
      color: #0f766e;
      text-transform: uppercase;
      letter-spacing: 0.6px;
    }

    .table-section-header .entry-count {
      font-size: 12px;
      font-weight: 600;
      color: #0d9488;
      background: rgba(13, 148, 136, 0.1);
      padding: 4px 12px;
      border-radius: 20px;
    }

    .table-wrap {
      overflow-x: auto;
    }

    /* ── Helper tip ── */
    .laporan-tip {
      text-align: center;
      font-size: 12px;
      color: #64748b;
      padding: 16px;
    }

    .laporan-tip a {
      color: #0d9488;
      font-weight: 700;
      text-decoration: none;
    }

    .laporan-tip a:hover { text-decoration: underline; }

    /* ── Responsive ── */
    @media (max-width: 1024px) {
      .hamburger-menu { display: flex !important; }
      .sidebar {
        position: fixed !important; left: 0 !important; top: 0 !important;
        width: 220px !important; height: 100vh !important;
        transform: translateX(-100%) !important;
        transition: transform 0.3s ease !important; z-index: 999 !important;
      }
      .sidebar.active { transform: translateX(0) !important; }
    }

    @media (max-width: 700px) {
      .form-grid { grid-template-columns: 1fr; }
      .form-grid .span-2 { grid-column: span 1; }
      .page-hero { flex-direction: column; align-items: flex-start; }
      .hero-balance-badge { align-self: stretch; }
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

    @include('includes.sidebar')

    <!-- Main Content -->
    <main class="main">
      <div class="page-wrapper">

        <!-- Page Hero -->
        @php
          $totalIncome  = \Illuminate\Support\Facades\DB::table('pemasukan')->where('is_confirmed',1)->whereNull('deleted_at')->sum('nominal');
          $totalExpense = \Illuminate\Support\Facades\DB::table('pengeluaran')->where('is_confirmed',1)->whereNull('deleted_at')->sum('nominal');
          $saldo = $totalIncome - $totalExpense;
        @endphp
        <div class="page-hero">
          <div class="page-hero-info">
            <h1>💰 Input Pemasukan</h1>
            <p>Catat pemasukan baru secara manual atau scan foto struk/kwitansi Anda.<br>Semua data tersimpan aman dan dapat diakses di halaman Laporan.</p>
          </div>
          <div class="hero-balance-badge">
            <div class="badge-label">Saldo Saat Ini</div>
            <div class="badge-value">Rp {{ number_format($saldo, 0, ',', '.') }}</div>
          </div>
        </div>

        <!-- Form Card -->
        <div class="form-card">
          <div class="form-card-header">
            <div class="header-icon">
              <svg viewBox="0 0 24 24" stroke-width="1.8"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <div>
              <h2>Form Pemasukan Baru</h2>
              <p>Semua field bertanda <span style="color:#ef4444;font-weight:700;">●</span> wajib diisi</p>
            </div>
          </div>

          <div class="form-card-body">
            @if ($errors->any())
              <div style="background-color: #fee2e2; border: 1.5px solid #fecaca; border-radius: 12px; padding: 16px; margin-bottom: 20px; color: #991b1b; font-size: 13px;">
                <strong style="display: block; margin-bottom: 8px;">⚠️ Gagal Menyimpan:</strong>
                <ul style="margin: 0; padding-left: 20px;">
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            <!-- Upload Zone (horizontal compact) - WAJIB PERTAMA -->
            <div class="upload-row" id="upload-row" role="button" tabindex="0" aria-label="Unggah foto struk">
              <div class="upload-row-icon">
                <svg viewBox="0 0 24 24" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
              </div>
              <div class="upload-row-text">
                <strong id="upload-filename">📎 Upload Foto Struk / Kwitansi Dulu <span style="color:#ef4444">*</span></strong>
                <span>Struk wajib diunggah sebelum mengisi form. Format: JPG, PNG, WEBP — Maks. 5MB.</span>
              </div>
              <button type="button" class="upload-row-btn" onclick="document.getElementById('receipt_image').click()">Pilih File</button>
            </div>

            <!-- Preview Foto Struk -->
            <div id="preview-container" style="display:none; margin-bottom:20px; background: linear-gradient(135deg, #f0fdf9 0%, #ecfdf5 100%); border: 2px solid #a5e8e3; border-radius: 14px; padding: 16px; text-align: center;">
              <div style="font-size: 12px; font-weight: 700; color: #0f766e; margin-bottom: 12px;">✅ Foto Struk Tersimpan</div>
              <img id="preview-image" src="" alt="Preview Struk" style="max-width: 100%; max-height: 300px; border-radius: 10px; box-shadow: 0 4px 12px rgba(13, 148, 136, 0.15);">
              <div style="margin-top: 12px; display: flex; gap: 10px;">
                <button type="button" class="reset-btn" onclick="clearPreview()" style="flex: 1; border-color: #0d9488; color: #0d9488;">🗑 Hapus Foto</button>
              </div>
            </div>

            <form id="receipt_form" method="POST" action="{{ route('pemasukan.store') }}" enctype="multipart/form-data" class="input-form">
              @csrf
              <input type="file" id="receipt_image" name="receipt_image" accept="image/*" hidden>
              <input type="hidden" id="receipt_type" name="type" value="pemasukan">

              <!-- Form overlay blocker -->
              <div id="form-blocker" style="position:relative;">
                <div id="form-overlay" style="position:absolute;inset:0;background:rgba(255,255,255,0.85);z-index:10;border-radius:12px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:12px;backdrop-filter:blur(2px);">
                  <div style="font-size:48px;">🧾</div>
                  <div style="font-size:14px;font-weight:700;color:#0f766e;text-align:center;">Upload Foto Struk / Kwitansi Terlebih Dahulu</div>
                  <div style="font-size:12px;color:#64748b;text-align:center;">Form akan terbuka setelah foto struk berhasil diunggah.</div>
                  <button type="button" onclick="document.getElementById('receipt_image').click()" style="background:linear-gradient(135deg,#0d9488,#059669);color:#fff;border:none;border-radius:10px;padding:10px 20px;font-size:13px;font-weight:700;cursor:pointer;">📎 Pilih File Struk</button>
                </div>

              <!-- Tanggal & Kategori (Fixed) -->
              <div class="form-grid">
                <div class="form-group">
                  <label class="form-label" for="tanggal">
                    <span class="required-dot"></span> Tanggal Transaksi (berlaku untuk semua baris)
                  </label>
                  <input type="date" class="form-input" id="tanggal" name="tanggal" value="{{ date('Y-m-d') }}" required />
                </div>

                <div class="form-group">
                  <label class="form-label" for="kategori_penerimaan">
                    <span class="required-dot"></span> Kategori Default Penerimaan
                  </label>
                  <select class="form-input" id="kategori_penerimaan" required>
                    <option value="">— Pilih Kategori —</option>
                    @foreach($jenis as $j)
                      <option value="{{ $j->id }}">{{ $j->nama }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <!-- Item rows: Keterangan + Nominal -->
              <div id="items-container">
                <div class="form-grid item-row" style="position: relative; padding-bottom: 20px; margin-bottom: 20px; border-bottom: 1px dashed #e0f7f5;">
                  <!-- Hidden kategori sync -->
                  <input type="hidden" name="id_jenis_penerimaan[]" class="row-kategori-sync">
                  <!-- Keterangan -->
                  <div class="form-group span-2">
                    <label class="form-label" for="uraian_0"><span class="required-dot"></span> Keterangan / Uraian</label>
                    <input type="text" class="form-input uraian-input" id="uraian_0" name="uraian[]"
                           placeholder="Contoh: Transfer dari bendahara, dana hibah..." maxlength="255" required />
                  </div>

                  <!-- Nominal -->
                  <div class="form-group">
                    <label class="form-label" for="nominal_0">
                      <span class="required-dot"></span> Nominal (IDR)
                    </label>
                    <div class="nominal-wrapper">
                      <span class="nominal-prefix">Rp</span>
                      <input type="number" class="form-input with-prefix nominal-input" id="nominal_0"
                             name="nominal[]" placeholder="0" min="1" required />
                    </div>
                  </div>
                  
                  <button type="button" class="btn-hapus-baris" onclick="hapusBaris(this)" style="display: none; position: absolute; top: 0; right: 0; background: none; border: none; color: #0d9488; cursor: pointer; font-size: 18px;" aria-label="Hapus Baris">✖</button>
                </div>
              </div>

              <!-- Total Section -->
              <div class="form-group span-2" style="background: linear-gradient(135deg, rgba(13, 148, 136, 0.08) 0%, rgba(16, 185, 129, 0.04) 100%); border: 2px solid #a5e8e3; border-radius: 12px; padding: 16px 18px; margin-top: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px;">
                  <span style="font-size: 13px; font-weight: 700; color: #0f766e; text-transform: uppercase; letter-spacing: 0.5px;">Total Pemasukan</span>
                  <div style="display: flex; align-items: center; gap: 6px;">
                    <span style="font-size: 12px; font-weight: 600; color: #059669;">Rp</span>
                    <span id="total-nominal" style="font-size: 18px; font-weight: 800; color: #059669;">0</span>
                  </div>
                </div>
              </div>
              
              <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                <button type="button" class="reset-btn" onclick="tambahBaris()" style="flex: 1; border-style: dashed; color: #059669; border-color: #a7f3d0;">
                  <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" width="16" height="16"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                  Tambah Baris Item
                </button>
                <button type="button" class="reset-btn" onclick="tambahKategori()" style="flex: 1; border-style: dashed; color: #b45309; border-color: #fcd34d;">
                  <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" width="16" height="16"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><line x1="14" y1="17" x2="21" y2="17"/><line x1="17" y1="14" x2="17" y2="21"/></svg>
                  Tambah Kategori Lain
                </button>
              </div>

              </div><!-- /form-blocker -->

              <!-- Actions -->
              <div class="form-actions">
                <button type="reset" class="reset-btn">
                  <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" width="16" height="16"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                  Reset
                </button>
                <button id="save-btn" class="save-btn" type="submit">
                  <svg viewBox="0 0 24 24" stroke-width="1.8"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                  Simpan Pemasukan
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Table Section -->
        <div class="table-section">
          <div class="table-section-header">
            <h3>📋 Riwayat Pemasukan</h3>
            <span class="entry-count">{{ count($incomes) }} entri</span>
          </div>
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th style="width:5%">#</th>
                  <th style="width:18%">Tanggal</th>
                  <th style="width:22%">Kategori</th>
                  <th style="width:22%">Keterangan</th>
                  <th style="width:18%">Jumlah</th>
                  <th style="width:15%">Aksi</th>
                </tr>
              </thead>
              <tbody>
                @forelse($incomes as $i => $income)
                  <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($income->tanggal)->format('d M Y') }}</td>
                    <td>{{ $income->kategori }}</td>
                    <td>
                      @if(!empty($income->uraian ?? ''))
                        <span style="color:#374151;">{{ $income->uraian }}</span>
                      @else
                        <span style="color:#94a3b8;font-style:italic;">—</span>
                      @endif
                    </td>
                    <td>
                      <span style="font-weight:700;color:#059669;">+ Rp {{ number_format($income->jumlah, 0, ',', '.') }}</span>
                    </td>
                    <td>
                      <div class="action-cell">
                        <a href="{{ route('pemasukan.edit', $income->id) }}" class="btn-edit">Edit</a>
                        <span class="sep">|</span>
                        <form method="POST" action="{{ route('pemasukan.delete', $income->id) }}" style="display:inline;"
                              data-confirm="soft">
                          @csrf
                          <button type="submit" class="btn-hapus">Hapus</button>
                        </form>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" style="text-align:center;padding:32px;color:#94a3b8;font-style:italic;">
                      Belum ada data pemasukan.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div style="margin-top: 20px; padding: 0 20px;">
            <style>
              .pagination { display: flex; list-style: none; padding: 0; margin: 0; justify-content: flex-end; gap: 4px; }
              .page-item .page-link { display: block; padding: 6px 12px; border: 1px solid #cbd5e1; border-radius: 6px; color: #0f766e; text-decoration: none; font-size: 13px; background: #fff; transition: all 0.2s ease; }
              .page-item .page-link:hover { background: #f1f5f9; }
              .page-item.active .page-link { background: #0d9488; color: #fff; border-color: #0d9488; }
              .page-item.disabled .page-link { color: #94a3b8; background: #f8fafc; cursor: not-allowed; border-color: #e2e8f0; }
            </style>
            {{ $incomes->links('pagination::bootstrap-4') }}
            @if($incomes->hasMorePages())
              <div style="margin-top: 8px; display:flex; justify-content:flex-end; gap:8px;">
                <a href="{{ $incomes->nextPageUrl() }}" class="btn-page-next" style="padding:8px 14px; background:#0d9488; color:#fff; border-radius:8px; text-decoration:none;">Next</a>
              </div>
            @endif
          </div>
          @if(count($incomes) > 0)
            <div class="laporan-tip">
              Lihat ringkasan lengkap dan ekspor data di halaman <a href="{{ route('laporan') }}">Laporan</a>.
            </div>
          @endif
        </div>

      </div>
    </main>
  </div>

  <!-- Modal Notifikasi -->
  <div id="custom-modal" class="custom-modal">
    <div class="modal-content">
      <div class="modal-icon" id="modal-icon"></div>
      <h2 id="modal-title"></h2>
      <p id="modal-message"></p>
      <button class="modal-btn" onclick="closeModal()">Tutup</button>
    </div>
  </div>



  <style>
    .custom-modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,.5); z-index:9999; justify-content:center; align-items:center; }
    .custom-modal.show { display:flex; }
    .modal-content { background:linear-gradient(135deg,#fff 0%,#f0fbff 100%); border-radius:20px; padding:40px; max-width:420px; width:90%; box-shadow:0 20px 60px rgba(13,148,136,.2); text-align:center; animation:slideUp .4s cubic-bezier(.34,1.56,.64,1); }
    .modal-icon { font-size:64px; margin-bottom:20px; }
    .modal-content h2 { font-size:24px; color:#1f2937; margin-bottom:12px; font-weight:700; }
    .modal-content p { font-size:14px; color:#6b7280; margin-bottom:28px; line-height:1.6; }
    .modal-btn { background:linear-gradient(135deg,#0f766e 0%,#0d9488 100%); color:#fff; border:none; padding:12px 32px; border-radius:10px; font-size:14px; font-weight:600; cursor:pointer; transition:all .3s ease; }
    .modal-btn:hover { transform:translateY(-2px); }
    @keyframes slideUp { from{transform:translateY(30px);opacity:0} to{transform:translateY(0);opacity:1} }
  </style>

  <script>
    // Upload filename preview
    const receiptInput = document.getElementById('receipt_image');
    const formOverlay = document.getElementById('form-overlay');
    const previewContainer = document.getElementById('preview-container');
    const previewImage = document.getElementById('preview-image');

    receiptInput.addEventListener('change', function() {
      if (this.files[0]) {
        const file = this.files[0];
        const name = file.name;
        
        // Validate file size (5MB = 5242880 bytes)
        if (file.size > 5242880) {
          alert('Ukuran file tidak boleh lebih dari 5MB');
          this.value = '';
          previewContainer.style.display = 'none';
          formOverlay.style.display = 'flex';
          return;
        }
        
        // Update upload row status
        document.getElementById('upload-filename').textContent = '✅ ' + name;
        document.getElementById('upload-filename').style.color = '#16a34a';
        document.getElementById('upload-row').style.borderColor = '#16a34a';
        document.getElementById('upload-row').style.background = 'rgba(220,252,231,0.5)';
        
        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
          previewImage.src = e.target.result;
          previewContainer.style.display = 'block';
        };
        reader.readAsDataURL(file);
        
        // Hide overlay to unlock form
        formOverlay.style.display = 'none';
      } else {
        document.getElementById('upload-filename').textContent = '📎 Upload Foto Struk / Kwitansi Dulu *';
        document.getElementById('upload-filename').style.color = '';
        document.getElementById('upload-row').style.borderColor = '';
        document.getElementById('upload-row').style.background = '';
        previewContainer.style.display = 'none';
        formOverlay.style.display = 'flex';
      }
    });

    function clearPreview() {
      receiptInput.value = '';
      previewContainer.style.display = 'none';
      document.getElementById('upload-filename').textContent = '📎 Upload Foto Struk / Kwitansi Dulu *';
      document.getElementById('upload-filename').style.color = '';
      document.getElementById('upload-row').style.borderColor = '';
      document.getElementById('upload-row').style.background = '';
      formOverlay.style.display = 'flex';
    }

    // Click upload row
    document.getElementById('upload-row').addEventListener('click', function(e) {
      if (!e.target.classList.contains('upload-row-btn')) {
        document.getElementById('receipt_image').click();
      }
    });

    function showModal(title, message, icon='✓') {
      document.getElementById('modal-icon').textContent = icon;
      document.getElementById('modal-title').textContent = title;
      document.getElementById('modal-message').textContent = message;
      document.getElementById('custom-modal').classList.add('show');
    }
    function closeModal() { document.getElementById('custom-modal').classList.remove('show'); }
    document.getElementById('custom-modal').addEventListener('click', function(e) { if(e.target===this) closeModal(); });

    document.addEventListener('DOMContentLoaded', function() {
      @if(session('error'))
        showModal('Gagal', '{{ session("error") }}', '❌');
      @elseif(session('success'))
        showModal('Berhasil', '{{ session("success") }}', '✅');
      @endif
      attachNominalListeners();
      // Sync awal: set nilai default kategori ke hidden inputs
      syncKategoriSync();
    });

    // Daftar kategori dari server (untuk dropdown dinamis)
    const jenisData = @json($jenis);

    // Sync hidden kategori inputs dengan main dropdown
    function syncKategoriSync() {
      const val = document.getElementById('kategori_penerimaan').value;
      document.querySelectorAll('.row-kategori-sync').forEach(inp => inp.value = val);
    }

    document.getElementById('kategori_penerimaan').addEventListener('change', syncKategoriSync);

    let rowCount = 1;
    function tambahBaris() {
      const container = document.getElementById('items-container');
      const currentVal = document.getElementById('kategori_penerimaan').value;
      const rowHtml = `
        <div class="form-grid item-row" style="position: relative; padding-bottom: 20px; margin-bottom: 20px; border-bottom: 1px dashed #e0f7f5; animation: slideUp 0.3s ease;">
          <input type="hidden" name="id_jenis_penerimaan[]" class="row-kategori-sync" value="${currentVal}">
          <div class="form-group span-2">
            <label class="form-label" for="uraian_${rowCount}">Keterangan / Uraian</label>
            <input type="text" class="form-input uraian-input" id="uraian_${rowCount}" name="uraian[]" placeholder="Contoh: Transfer dari bendahara, dana hibah..." maxlength="255" />
          </div>
          <div class="form-group">
            <label class="form-label" for="nominal_${rowCount}">
              <span class="required-dot"></span> Nominal (IDR)
            </label>
            <div class="nominal-wrapper">
              <span class="nominal-prefix">Rp</span>
              <input type="number" class="form-input with-prefix nominal-input" id="nominal_${rowCount}" name="nominal[]" placeholder="0" min="1" required />
            </div>
          </div>
          <button type="button" class="btn-hapus-baris" onclick="hapusBaris(this)" style="position: absolute; top: -5px; right: -5px; background: #ecfdf5; border: 1px solid #a7f3d0; color: #0d9488; width: 28px; height: 28px; border-radius: 50%; cursor: pointer; font-size: 14px; font-weight: bold; display: flex; align-items: center; justify-content: center;" aria-label="Hapus Baris">✖</button>
        </div>
      `;
      container.insertAdjacentHTML('beforeend', rowHtml);
      rowCount++;
      updateHapusButtons();
      attachNominalListeners();
    }

    function tambahKategori() {
      const container = document.getElementById('items-container');
      let optHtml = '<option value="">— Pilih Kategori —</option>';
      jenisData.forEach(j => { optHtml += `<option value="${j.id}">${j.nama}</option>`; });
      const rowHtml = `
        <div class="form-grid item-row" style="position: relative; padding-bottom: 20px; margin-bottom: 20px; border-bottom: 1px dashed #fcd34d; animation: slideUp 0.3s ease;">
          <div class="form-group span-2" style="background:linear-gradient(135deg,rgba(251,191,36,0.08),transparent); border-radius:10px; padding:12px; border:1px dashed #fcd34d;">
            <label class="form-label" style="color:#b45309;">Kategori Penerimaan <span style="color:#ef4444;">*</span></label>
            <select class="form-input" name="id_jenis_penerimaan[]" required style="border-color:#fde68a;">
              ${optHtml}
            </select>
          </div>
          <div class="form-group span-2">
            <label class="form-label" for="uraian_${rowCount}">Keterangan / Uraian</label>
            <input type="text" class="form-input uraian-input" id="uraian_${rowCount}" name="uraian[]" placeholder="Contoh: Transfer dari bendahara, dana hibah..." maxlength="255" />
          </div>
          <div class="form-group">
            <label class="form-label" for="nominal_${rowCount}">
              <span class="required-dot"></span> Nominal (IDR)
            </label>
            <div class="nominal-wrapper">
              <span class="nominal-prefix">Rp</span>
              <input type="number" class="form-input with-prefix nominal-input" id="nominal_${rowCount}" name="nominal[]" placeholder="0" min="1" required />
            </div>
          </div>
          <button type="button" class="btn-hapus-baris" onclick="hapusBaris(this)" style="position: absolute; top: -5px; right: -5px; background: #fef3c7; border: 1px solid #fcd34d; color: #b45309; width: 28px; height: 28px; border-radius: 50%; cursor: pointer; font-size: 14px; font-weight: bold; display: flex; align-items: center; justify-content: center;" aria-label="Hapus Baris">✖</button>
        </div>
      `;
      container.insertAdjacentHTML('beforeend', rowHtml);
      rowCount++;
      updateHapusButtons();
      attachNominalListeners();
    }

    function hapusBaris(btn) {
      const row = btn.closest('.item-row');
      row.remove();
      updateHapusButtons();
      updateTotal();
    }

    function updateHapusButtons() {
      const rows = document.querySelectorAll('.item-row');
      const btns = document.querySelectorAll('.btn-hapus-baris');
      if (rows.length > 1) {
        btns.forEach(b => b.style.display = 'flex');
      } else {
        btns.forEach(b => b.style.display = 'none');
      }
    }

    function updateTotal() {
      const nominalInputs = document.querySelectorAll('.nominal-input');
      let total = 0;
      nominalInputs.forEach(input => {
        const value = parseInt(input.value) || 0;
        total += value;
      });
      const totalElement = document.getElementById('total-nominal');
      totalElement.textContent = total.toLocaleString('id-ID');
    }

    function attachNominalListeners() {
      const nominalInputs = document.querySelectorAll('.nominal-input');
      nominalInputs.forEach(input => {
        input.removeEventListener('input', updateTotal);
        input.addEventListener('input', updateTotal);
      });
    }
  </script>
</body>
</html>