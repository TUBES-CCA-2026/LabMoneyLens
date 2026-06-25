<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Pengeluaran</title>
  @vite(['resources/css/style.css','resources/css/welcome.css'])
</head>
<body>
  <div class="app">
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
        <a href="{{ route('dashboard') }}" class="nav-item">Dashboard</a>
        @unless(session('user_role') == 'Kepala Lab')
          <a href="{{ route('welcome') }}" class="nav-item active">Pengeluaran</a>
          <a href="{{ route('pemasukan') }}" class="nav-item">Pemasukan</a>
          <a href="{{ route('recycle') }}" class="nav-item">Recycle Bin</a>
        @endunless
        <a href="{{ route('laporan') }}" class="nav-item">Laporan</a>
      </nav>
      <div class="sidebar-logout">
        <form action="{{ route('logout') }}" method="POST">
          @csrf
          <button type="submit" class="logout-btn">Log-out</button>
        </form>
      </div>
    </aside>
    <main class="main">
      <section class="input-panel">
        <h2 class="panel-title">Edit Pengeluaran</h2>
        <p class="panel-subtitle">Perbarui kategori, tanggal, atau jumlah sebelum dikonfirmasi.</p>
        <form method="POST" action="{{ route('pengeluaran.update', ['id' => $expense->id]) }}" class="input-form">
          @csrf
          <div class="form-group">
            <label class="form-label" for="id_jenis_pengeluaran">Kategori</label>
            <select class="form-input" id="id_jenis_pengeluaran" name="id_jenis_pengeluaran" required>
              <option value="">Pilih</option>
              @foreach($jenis as $j)
                <option value="{{ $j->id }}" {{ $expense->id_jenis_pengeluaran == $j->id ? 'selected' : '' }}>{{ $j->nama }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label class="form-label" for="nominal">Jumlah (IDR)</label>
            <input type="number" class="form-input" id="nominal" name="nominal" placeholder="Rp" min="0" required value="{{ $expense->jumlah }}" />
          </div>
          <div class="form-group">
            <label class="form-label" for="tanggal">Tanggal</label>
            <input type="date" class="form-input" id="tanggal" name="tanggal" required value="{{ $expense->tanggal }}" />
          </div>
          <button class="save-btn" type="submit">Simpan Perubahan</button>
        </form>
      </section>
    </main>
  </div>
</body>
</html>
