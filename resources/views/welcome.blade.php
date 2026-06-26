<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Pengeluaran — Dashboard</title>
  @vite(['resources/css/style.css','resources/css/welcome.css','resources/js/script.js'])
  <script>window.receiptParseUrl = "{{ route('receipt.parse') }}";</script>
</head>
<body>
 
  <div class="app">
 
    <!-- ── Sidebar ── -->
    <aside class="sidebar">
      <div class="sidebar-user">
        <div class="avatar">
          <svg viewBox="0 0 24 24" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
        </div>
        <div>
          <div class="sidebar-username">{{ session('user_name', 'USERNAME') }}</div>
          <div class="sidebar-role">{{ session('user_role', 'Administrator') }}</div>
        </div>
      </div>
 
      <nav class="sidebar-nav">
        <a href="{{ route('dashboard') }}" class="nav-item">
          <svg viewBox="0 0 24 24" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
          Dashboard
        </a>
        @unless(session('user_role') == 'Kepala Lab')
          <a href="{{ route('welcome') }}" class="nav-item active">
            <svg viewBox="0 0 24 24" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M12 8v4l3 3"/></svg>
            Pengeluaran
          </a>
          <a href="{{ route('pemasukan') }}" class="nav-item">
            <svg viewBox="0 0 24 24" stroke-width="1.8"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            Pemasukan
          </a>
        @endunless
        <a href="{{ route('laporan') }}" class="nav-item">
          <svg viewBox="0 0 24 24" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
          Laporan
        </a>

        @unless(session('user_role') == 'Kepala Lab')
          <a href="{{ route('recycle') }}" class="nav-item">
            <svg viewBox="0 0 24 24" stroke-width="1.8"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
            Recycle Bin
          </a>
        @endunless
      </nav>
 
      <div class="sidebar-logout">
        <form action="{{ route('logout') }}" method="POST">
          @csrf
          <button type="submit" class="logout-btn">
            <svg viewBox="0 0 24 24" stroke-width="1.8"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Log-out
          </button>
        </form>
      </div>
    </aside>
 
    <!-- ── Konten ── -->
    <main class="main">
 
      <!-- Panel Input -->
      <section class="input-panel">
        <h2 class="panel-title">Input Pengeluaran</h2>
        <p class="panel-subtitle">Tambahkan catatan pengeluaran baru secara manual atau foto struk fisik Anda.</p>
 
        <div class="upload-zone" role="button" tabindex="0" aria-label="Unggah foto struk">
          <span id="upload-preview">
            <svg viewBox="0 0 24 24" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
            <span class="upload-label">Unggah foto di sini</span>
          </span>
        </div>

        <p class="divider-text">Atau secara manual</p>

        <form id="receipt_form" method="POST" action="{{ route('pengeluaran.store') }}" enctype="multipart/form-data" class="input-form">
          @csrf
          <input type="file" id="receipt_image" name="receipt_image" accept="image/*" hidden>
          <input type="hidden" id="receipt_type" name="type" value="pengeluaran">
          @csrf
          <div class="form-group">
            <label class="form-label" for="id_jenis_pengeluaran">Kategori</label>
            <select class="form-input" id="id_jenis_pengeluaran" name="id_jenis_pengeluaran" required>
              <option value="">Pilih</option>
              @foreach($jenis as $j)
                <option value="{{ $j->id }}">{{ $j->nama }}</option>
              @endforeach
            </select>
          </div>

          <div class="form-group">
            <label class="form-label" for="nominal">Jumlah (IDR)</label>
            <input type="number" class="form-input" id="nominal" name="nominal" placeholder="Rp" min="0" required />
          </div>

          <div class="form-group">
            <label class="form-label" for="tanggal">Tanggal</label>
            <input type="date" class="form-input" id="tanggal" name="tanggal" required />
          </div>

          <button id="save-btn" class="save-btn" type="submit">
            <svg viewBox="0 0 24 24" stroke-width="1.8"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Simpan
          </button>
        </form>
      </section>
 
      <!-- Panel Pratinjau -->
      <section class="preview-panel">
        <div class="preview-header">
          <div>
            <h1 class="preview-title">Pratinjau Entri</h1>
            <p class="preview-subtitle">Daftar transaksi terbaru yang sudah tersimpan</p>
          </div>
        </div>

        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th style="width:18%">ID_Pengeluaran</th>
                <th style="width:25%">Kategori</th>
                <th style="width:18%">Jumlah (IDR)</th>
                <th style="width:15%">Tanggal</th>
                <th style="width:24%">Aksi</th>
              </tr>
            </thead>
            <tbody id="table-body">
              @forelse($expenses as $expense)
                <tr>
                  <td>{{ $expense->id }}</td>
                  <td>{{ $expense->kategori }}</td>
                  <td>Rp {{ number_format($expense->jumlah, 0, ',', '.') }}</td>
                  <td>{{ \Illuminate\Support\Carbon::parse($expense->tanggal)->format('d/m/Y') }}</td>
                  <td class="action-cell">
                    <a href="{{ route('pengeluaran.edit', ['id' => $expense->id]) }}" class="btn-edit">Edit</a>
                    <span class="sep">/</span>
                    <form action="{{ route('pengeluaran.delete', ['id' => $expense->id]) }}" method="POST" style="display:inline">
                      @csrf
                      <button type="submit" class="btn-hapus">Hapus</button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="empty-row">Belum ada data pengeluaran.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </section>
 
    </main>
  </div>
</body>
</html>