<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Edit Pemasukan — LabMoneyLens</title>
  @vite(['resources/css/style.css','resources/css/welcome.css','resources/js/script.js'])
  
  <!-- Inline mobile hamburger styling as backup -->
  <style>
    /* ── Override layout ── */
    .main {
      flex-direction: row;
      align-items: flex-start;
      padding: 32px 24px;
      gap: 28px;
      overflow-y: auto;
      height: 100vh;
    }

    .page-wrapper {
      width: 100%;
      max-width: 1100px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: 360px 1fr;
      gap: 28px;
      align-items: flex-start;
    }

    /* ── Sticky photo panel ── */
    .struk-panel {
      position: sticky;
      top: 24px;
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .struk-card {
      background: linear-gradient(135deg, #f0fdf9 0%, #ecfdf5 100%);
      border: 2px solid #a5e8e3;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 4px 16px rgba(13, 148, 136, 0.1);
    }

    .struk-card-header {
      background: linear-gradient(90deg, #f0fdf9 0%, #ecfdf5 100%);
      border-bottom: 2px solid #a5e8e3;
      padding: 14px 20px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .struk-card-header .struk-icon {
      width: 32px;
      height: 32px;
      background: linear-gradient(135deg, #0d9488, #059669);
      border-radius: 9px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .struk-card-header .struk-icon svg {
      width: 16px; height: 16px; stroke: #fff; fill: none;
    }

    .struk-card-header span {
      font-size: 12px;
      font-weight: 800;
      color: #0f766e;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .struk-img-wrap {
      padding: 16px;
      text-align: center;
    }

    .struk-img-wrap img {
      width: 100%;
      max-height: 420px;
      object-fit: contain;
      border-radius: 12px;
      box-shadow: 0 4px 16px rgba(13, 148, 136, 0.12);
    }

    .struk-no-photo {
      padding: 32px 16px;
      text-align: center;
      color: #94a3b8;
      font-size: 13px;
    }

    .struk-no-photo svg {
      width: 48px; height: 48px; stroke: #cbd5e1; fill: none; margin-bottom: 10px;
    }

    /* ── Form panel ── */
    .form-panel {
      min-width: 0;
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

<<<<<<< HEAD
=======

    /* ── Quantity / nominal layout ── */
    .nominal-qty-group {
      display: grid;
      grid-template-columns: minmax(0, 1fr) 150px;
      gap: 14px;
      align-items: end;
    }

    .qty-wrapper {
      display: flex;
      flex-direction: column;
      gap: 7px;
    }

    .qty-label {
      font-size: 11px;
      font-weight: 700;
      color: #0f766e;
      text-transform: uppercase;
      letter-spacing: 0.6px;
    }

    .qty-input {
      width: 100%;
      min-height: 43px;
      padding: 10px 12px;
      border: 2px solid #ccf0ee;
      border-radius: 10px;
      font-size: 13px;
      font-family: inherit;
      color: #0f766e;
      background: #fff;
      outline: none;
      box-sizing: border-box;
      transition: all .2s ease;
    }

    .qty-input:hover { border-color: #a5e8e3; }
    .qty-input:focus {
      border-color: #0d9488;
      box-shadow: 0 0 0 4px rgba(13,148,136,.12);
    }

    .item-total-hint {
      margin-top: 10px;
      padding: 9px 12px;
      border-radius: 9px;
      background: #f0fdf9;
      color: #64748b;
      font-size: 12px;
      border: 1px solid #d8f3ef;
    }

    .item-total-hint strong { color: #0f766e; }

    .item-row {
      background: #fbfffe;
      border: 1px solid #e0f7f5;
      border-radius: 14px;
      padding: 18px 18px 20px !important;
      margin-bottom: 14px !important;
      box-shadow: 0 2px 8px rgba(13,148,136,.05);
    }

    .item-row .btn-hapus-baris {
      top: 12px !important;
      right: 12px !important;
      width: 30px;
      height: 30px;
      border: 1px solid #d8f3ef !important;
      border-radius: 8px;
      background: #fff !important;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    @media (max-width: 600px) {
      .nominal-qty-group { grid-template-columns: 1fr; gap: 10px; }
      .qty-wrapper { display: grid; grid-template-columns: 55px 1fr; align-items: center; gap: 8px; }
    }

>>>>>>> 0026227 (Baru)
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

    @media (max-width: 900px) {
      .page-wrapper {
        grid-template-columns: 1fr;
      }
      .struk-panel {
        position: static;
      }
      .main {
        flex-direction: column;
        height: auto;
      }
    }

    @media (max-width: 700px) {
      .form-grid { grid-template-columns: 1fr; }
      .form-grid .span-2 { grid-column: span 1; }
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
      <div class="page-wrapper">

        <!-- ── Kolom Kiri: Foto Struk (Sticky) ── -->
        <div class="struk-panel">
          <div class="struk-card">
            <div class="struk-card-header">
              <div class="struk-icon">
                <svg viewBox="0 0 24 24" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
              </div>
              <span>Foto Struk / Kwitansi</span>
            </div>
            <div class="struk-img-wrap">
              @if($income->foto_struk)
                <img src="{{ asset('storage/' . $income->foto_struk) }}" alt="Foto Struk Pemasukan">
                <p style="margin-top:10px; font-size:11px; color:#94a3b8;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14" style="display:inline-block;vertical-align:middle;margin-right:3px;"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg> Tersimpan di server</p>
              @else
                <div class="struk-no-photo">
                  <svg viewBox="0 0 24 24" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                  <p>Tidak ada foto struk</p>
                </div>
              @endif
            </div>
          </div>
        </div>

        <!-- ── Kolom Kanan: Form Edit ── -->
        <div class="form-panel">
          <div class="form-card">
            <div class="form-card-header">
              <div class="header-icon">
                <svg viewBox="0 0 24 24" stroke-width="1.8"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
              </div>
              <div>
                <h2>Edit Data Pemasukan</h2>
                <p>Semua field bertanda <span style="color:#ef4444;font-weight:700;">●</span> wajib diisi</p>
              </div>
            </div>

            <div class="form-card-body">
<<<<<<< HEAD
=======
              @if(session('error'))
                <div style="margin-bottom:18px;padding:12px 14px;border:1px solid #fecaca;background:#fff1f2;color:#b91c1c;border-radius:10px;font-size:13px;line-height:1.5;">
                  {{ session('error') }}
                </div>
              @endif
              @if($errors->any())
                <div style="margin-bottom:18px;padding:12px 14px;border:1px solid #fecaca;background:#fff1f2;color:#b91c1c;border-radius:10px;font-size:13px;line-height:1.5;">
                  <strong>Periksa kembali data:</strong>
                  <ul style="margin:6px 0 0 18px;padding:0;">
                    @foreach($errors->all() as $error)
                      <li>{{ $error }}</li>
                    @endforeach
                  </ul>
                </div>
              @endif
>>>>>>> 0026227 (Baru)
              <form method="POST" action="{{ route('pemasukan.update', ['id' => $income->id]) }}" class="input-form">
                @csrf

                <!-- Tanggal & Kategori -->
                <div class="form-grid">
                  <div class="form-group">
                    <label class="form-label" for="tanggal">
                      <span class="required-dot"></span> Tanggal Transaksi
                    </label>
                    <input type="date" class="form-input" id="tanggal" name="tanggal" required value="{{ \Illuminate\Support\Carbon::parse($income->tanggal)->format('Y-m-d') }}" />
                  </div>

                  <div class="form-group">
                    <label class="form-label" for="id_jenis_penerimaan">
                      <span class="required-dot"></span> Kategori Penerimaan
                    </label>
                    <select class="form-input" id="id_jenis_penerimaan" name="id_jenis_penerimaan" required>
                      <option value="">— Pilih Kategori —</option>
                      @foreach($jenis as $j)
                        <option value="{{ $j->id }}" {{ $income->id_jenis_penerimaan == $j->id ? 'selected' : '' }}>{{ $j->nama }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <!-- Item rows -->
                <div id="items-container" style="margin-top: 18px;">
                  @foreach($incomes as $index => $item)
                  <div class="form-grid item-row" style="position: relative; padding-bottom: 20px; margin-bottom: 20px; border-bottom: 1px dashed #e0f7f5;">
                    <input type="hidden" name="id_pemasukan[]" value="{{ $item->id }}">

                    <!-- Keterangan -->
                    <div class="form-group span-2">
                      <label class="form-label" for="uraian_{{ $index }}">Keterangan / Uraian</label>
                      <input type="text" class="form-input uraian-input" id="uraian_{{ $index }}" name="uraian[]"
                             placeholder="Contoh: Transfer dari bendahara, dana hibah..." maxlength="255" value="{{ $item->uraian }}" />
                    </div>

                    <!-- Nominal -->
                    <div class="form-group span-2">
                      <label class="form-label" for="nominal_{{ $index }}">
<<<<<<< HEAD
                        <span class="required-dot"></span> Nominal (IDR)
                      </label>
                      <div class="nominal-wrapper">
                        <span class="nominal-prefix">Rp</span>
                        <input type="number" class="form-input with-prefix nominal-input" id="nominal_{{ $index }}" name="nominal[]" placeholder="0" min="0" required value="{{ $item->jumlah }}" />
                      </div>
=======
                        <span class="required-dot"></span> Nominal Satuan (IDR)
                      </label>
                      <div class="nominal-qty-group">
                        <div class="nominal-wrapper">
                          <span class="nominal-prefix">Rp</span>
                          <input type="number" class="form-input with-prefix nominal-input" id="nominal_{{ $index }}" name="nominal[]" placeholder="0" min="1" step="1" inputmode="numeric" required value="{{ (int) round($item->jumlah / max(1, (int) ($item->quantity ?? 1))) }}" />
                        </div>
                        <div class="qty-wrapper">
                          <span class="qty-label">Qty</span>
                          <input type="number" class="qty-input kuantiti-input" id="kuantiti_{{ $index }}" name="kuantiti[]" placeholder="1" min="1" value="{{ max(1, (int) ($item->quantity ?? 1)) }}" required />
                        </div>
                      </div>
                      <div class="item-total-hint">Total item: <strong>Rp <span class="item-total-value">0</span></strong></div>
>>>>>>> 0026227 (Baru)
                    </div>

                    @if($index > 0)
                    <button type="button" class="btn-hapus-baris" onclick="hapusBaris(this)" style="position: absolute; top: 0; right: 0; background: none; border: none; color: #0d9488; cursor: pointer; font-size: 18px;" aria-label="Hapus Baris"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="12" height="12" style="display:inline-block;vertical-align:middle;"><path d="M18 6 6 18M6 6l12 12"/></svg></button>
                    @else
                    <button type="button" class="btn-hapus-baris" onclick="hapusBaris(this)" style="display: none; position: absolute; top: 0; right: 0; background: none; border: none; color: #0d9488; cursor: pointer; font-size: 18px;" aria-label="Hapus Baris"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="12" height="12" style="display:inline-block;vertical-align:middle;"><path d="M18 6 6 18M6 6l12 12"/></svg></button>
                    @endif
                  </div>
                  @endforeach
                </div>

                <!-- Total Section -->
                <div class="form-group span-2" style="background: linear-gradient(135deg, rgba(13, 148, 136, 0.08) 0%, rgba(16, 185, 129, 0.04) 100%); border: 2px solid #a5e8e3; border-radius: 12px; padding: 16px 18px; margin-bottom: 20px;">
                  <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px;">
                    <span style="font-size: 13px; font-weight: 700; color: #0f766e; text-transform: uppercase; letter-spacing: 0.5px;">Total Pemasukan</span>
                    <div style="display: flex; align-items: center; gap: 6px;">
                      <span style="font-size: 12px; font-weight: 600; color: #059669;">Rp</span>
                      <span id="total-nominal" style="font-size: 18px; font-weight: 800; color: #059669;">0</span>
                    </div>
                  </div>
                </div>

                <button type="button" class="reset-btn" onclick="tambahBaris()" style="width: 100%; margin-bottom: 20px; border-style: dashed; color: #059669; border-color: #a7f3d0;">
                  <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" width="16" height="16"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                  Tambah Baris Item
                </button>

                <!-- Actions -->
                <div class="form-actions">
                  <a href="{{ route('laporan') }}" class="reset-btn" style="text-decoration: none; flex: 1; display: flex; text-align: center;">Batal</a>
                  <button id="save-btn" class="save-btn" type="submit" style="flex: 1;">
                    <svg viewBox="0 0 24 24" stroke-width="1.8"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Simpan Perubahan
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>

  </div>

  <script>
    let rowCount = {{ count($incomes) }};

    function tambahBaris() {
      const container = document.getElementById('items-container');
      const newRow = document.createElement('div');
      newRow.className = 'form-grid item-row';
      newRow.style = 'position: relative; padding-bottom: 20px; margin-bottom: 20px; border-bottom: 1px dashed #e0f7f5;';
      
      newRow.innerHTML = `
        <div class="form-group span-2">
          <label class="form-label" for="uraian_${rowCount}">Keterangan / Uraian</label>
          <input type="text" class="form-input uraian-input" id="uraian_${rowCount}" name="uraian[]"
                 placeholder="Contoh: Transfer dari bendahara, dana hibah..." maxlength="255" />
        </div>
        <div class="form-group span-2">
          <label class="form-label" for="nominal_${rowCount}">
<<<<<<< HEAD
            <span class="required-dot"></span> Nominal (IDR)
          </label>
          <div class="nominal-wrapper">
            <span class="nominal-prefix">Rp</span>
            <input type="number" class="form-input with-prefix nominal-input" id="nominal_${rowCount}" name="nominal[]" placeholder="0" min="1" required />
          </div>
=======
            <span class="required-dot"></span> Nominal Satuan (IDR)
          </label>
          <div class="nominal-qty-group">
            <div class="nominal-wrapper">
              <span class="nominal-prefix">Rp</span>
              <input type="number" class="form-input with-prefix nominal-input" id="nominal_${rowCount}" name="nominal[]" placeholder="0" min="1" step="1" inputmode="numeric" required />
            </div>
            <div class="qty-wrapper">
              <span class="qty-label">Qty</span>
              <input type="number" class="qty-input kuantiti-input" id="kuantiti_${rowCount}" name="kuantiti[]" placeholder="1" min="1" value="1" required />
            </div>
          </div>
          <div class="item-total-hint">Total item: <strong>Rp <span class="item-total-value">0</span></strong></div>
>>>>>>> 0026227 (Baru)
        </div>
        <button type="button" class="btn-hapus-baris" onclick="hapusBaris(this)" style="position: absolute; top: 0; right: 0; background: none; border: none; color: #0d9488; cursor: pointer; font-size: 18px;" aria-label="Hapus Baris"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="12" height="12" style="display:inline-block;vertical-align:middle;"><path d="M18 6 6 18M6 6l12 12"/></svg></button>
      `;
      
      container.appendChild(newRow);
      
<<<<<<< HEAD
      // Re-bind calculation to new input
      const newNominalInput = newRow.querySelector('.nominal-input');
      newNominalInput.addEventListener('input', calculateTotal);
      
=======
      // Event calculation menggunakan delegation pada form sehingga nominal dan qty
      // pada baris baru tetap ikut dihitung.
>>>>>>> 0026227 (Baru)
      updateHapusButtons();
      rowCount++;
    }

    function hapusBaris(button) {
      const row = button.closest('.item-row');
      row.remove();
      updateHapusButtons();
      calculateTotal();
    }

    function updateHapusButtons() {
      const rows = document.querySelectorAll('.item-row');
      rows.forEach((row, index) => {
        const btn = row.querySelector('.btn-hapus-baris');
        if (btn) {
          btn.style.display = rows.length > 1 ? 'block' : 'none';
        }
      });
    }

<<<<<<< HEAD
    function calculateTotal() {
      const nominalInputs = document.querySelectorAll('.nominal-input');
      let total = 0;
      nominalInputs.forEach(input => {
        const raw = String(input.value || '');
        const digits = raw.replace(/[^0-9]/g, '');
        const val = parseFloat(digits) || 0;
        total += val;
      });
      document.getElementById('total-nominal').textContent = total.toLocaleString('id-ID');
    }

    document.addEventListener('DOMContentLoaded', function() {
      const nominalInputs = document.querySelectorAll('.nominal-input');
      nominalInputs.forEach(input => {
        input.addEventListener('input', calculateTotal);
      });
      // sanitize on submit
      document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
          form.querySelectorAll('.nominal-input').forEach(input => {
            if (input.value) input.value = String(input.value).replace(/[^0-9]/g, '');
          });
        });
      });
=======
    function formatRupiah(value) {
      return Math.max(0, Math.round(value)).toLocaleString('id-ID');
    }

    function calculateTotal() {
      const rows = document.querySelectorAll('.item-row');
      let total = 0;
      rows.forEach(row => {
        const nominalInput = row.querySelector('.nominal-input');
        const quantityInput = row.querySelector('.kuantiti-input');
        const nominal = Number(nominalInput?.value || 0);
        const quantity = Math.max(1, Number(quantityInput?.value || 1));
        const itemTotal = nominal * quantity;
        total += itemTotal;
        const itemTotalEl = row.querySelector('.item-total-value');
        if (itemTotalEl) itemTotalEl.textContent = formatRupiah(itemTotal);
      });
      document.getElementById('total-nominal').textContent = formatRupiah(total);
    }

    document.addEventListener('DOMContentLoaded', function() {
      const form = document.querySelector('.input-form');
      document.querySelectorAll('.nominal-input, .kuantiti-input').forEach(input => {
        input.addEventListener('input', calculateTotal);
        input.addEventListener('change', calculateTotal);
      });

      if (form) {
        form.addEventListener('submit', function(e) {
          const rows = document.querySelectorAll('.item-row');
          if (!rows.length) {
            e.preventDefault();
            showModal('Gagal', 'Minimal satu item pemasukan harus diisi.', 'error');
            return;
          }

          let valid = true;
          rows.forEach(row => {
            const nominal = Number(row.querySelector('.nominal-input')?.value || 0);
            const quantity = Number(row.querySelector('.kuantiti-input')?.value || 0);
            if (nominal < 1 || quantity < 1) {
              valid = false;
              row.querySelector('.nominal-input')?.closest('.form-group')?.classList.add('has-error');
            }
          });

          if (!valid) {
            e.preventDefault();
            showModal('Data belum lengkap', 'Nominal harus lebih dari Rp0 dan Qty minimal 1.', 'error');
            return;
          }

          form.querySelectorAll('.nominal-input').forEach(input => {
            input.value = String(input.value).replace(/[^0-9]/g, '');
          });
        });
      }

>>>>>>> 0026227 (Baru)
      calculateTotal();
      updateHapusButtons();
    });
  </script>

  <!-- Custom Modal Pop-up -->
  <div id="custom-modal" class="custom-modal">
    <div class="modal-content">
      <div class="modal-icon" id="modal-icon"></div>
      <h2 id="modal-title"></h2>
      <p id="modal-message"></p>
      <button class="modal-btn" onclick="closeModal()">Tutup</button>
    </div>
  </div>

  <style>
    .custom-modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      z-index: 9999;
      justify-content: center;
      align-items: center;
      animation: fadeIn 0.3s ease;
    }

    .custom-modal.show {
      display: flex;
    }

    .modal-content {
      background: linear-gradient(135deg, #ffffff 0%, #f0fbff 100%);
      border-radius: 20px;
      padding: 40px;
      max-width: 420px;
      width: 90%;
      box-shadow: 0 20px 60px rgba(13, 148, 136, 0.2);
      text-align: center;
      animation: slideUp 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .modal-icon {
      font-size: 64px;
      margin-bottom: 20px;
      animation: popIn 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .modal-content h2 {
      font-size: 24px;
      color: #1f2937;
      margin-bottom: 12px;
      font-weight: 700;
    }

    .modal-content p {
      font-size: 14px;
      color: #6b7280;
      margin-bottom: 28px;
      line-height: 1.6;
    }

    .modal-btn {
      background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%);
      color: white;
      border: none;
      padding: 12px 32px;
      border-radius: 10px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(13, 148, 136, 0.3);
    }

    .modal-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(13, 148, 136, 0.4);
    }

    .modal-btn:active {
      transform: translateY(0);
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
      }
      to {
        opacity: 1;
      }
    }

    @keyframes slideUp {
      from {
        transform: translateY(30px);
        opacity: 0;
      }
      to {
        transform: translateY(0);
        opacity: 1;
      }
    }

    @keyframes popIn {
      0% {
        transform: scale(0);
      }
      50% {
        transform: scale(1.1);
      }
      100% {
        transform: scale(1);
      }
    }
  </style>

  <script>
    function showModal(title, message, icon = 'ok') {
      const modalIcon = document.getElementById('modal-icon');
      if (icon === 'ok') {
        modalIcon.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5" width="56" height="56"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>';
      } else if (icon === 'error' || icon === 'err') {
        modalIcon.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5" width="56" height="56"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6M9 9l6 6"/></svg>';
      } else {
        modalIcon.textContent = icon;
      }
      document.getElementById('modal-title').textContent = title;
      document.getElementById('modal-message').textContent = message;
      document.getElementById('custom-modal').classList.add('show');
    }

    function closeModal() {
      document.getElementById('custom-modal').classList.remove('show');
    }

    // Close modal when clicking outside
    document.getElementById('custom-modal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeModal();
      }
    });

    // Tampilkan pop-up untuk error/success messages
    document.addEventListener('DOMContentLoaded', function() {
      @if(session('error'))
        showModal('Gagal', '{{ session("error") }}', 'error');
      @elseif(session('success'))
        showModal('Berhasil', '{{ session("success") }}', 'ok');
      @endif
    });
  </script>
</body>
</html>
