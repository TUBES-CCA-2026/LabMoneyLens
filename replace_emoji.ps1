$viewsPath = "c:\Users\LOQ\OneDrive\Documents\codingan\TUBES\LabMoneyLens\resources\views"

# SVG strings
$svgCheck     = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="16" height="16" style="display:inline-block;vertical-align:middle;"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>'
$svgCheckLg   = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="28" height="28" style="display:inline-block;vertical-align:middle;opacity:0.5;"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>'
$svgX         = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="16" height="16" style="display:inline-block;vertical-align:middle;"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6M9 9l6 6"/></svg>'
$svgWarn      = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16" style="display:inline-block;vertical-align:middle;"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"/><path d="M12 9v4M12 17h.01"/></svg>'
$svgClipboard = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16" style="display:inline-block;vertical-align:middle;"><rect x="9" y="2" width="6" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M12 11h4M12 16h4M8 11h.01M8 16h.01"/></svg>'
$svgReceiptSm = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16" style="display:inline-block;vertical-align:middle;"><path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z"/><path d="M16 8H8M16 12H8M12 16H8"/></svg>'
$svgReceiptLg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="56" height="56" style="display:inline-block;vertical-align:middle;opacity:0.35;"><path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z"/><path d="M16 8H8M16 12H8M12 16H8"/></svg>'
$svgPaperclip = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14" style="display:inline-block;vertical-align:middle;"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>'
$svgTrash     = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14" style="display:inline-block;vertical-align:middle;"><polyline points="3 6 5 6 21 6"/><path d="m19 6-.867 12.142A2 2 0 0 1 16.138 20H7.862a2 2 0 0 1-1.995-1.858L5 6"/><path d="M10 11v6M14 11v6M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>'
$svgPencil    = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16" style="display:inline-block;vertical-align:middle;"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>'
$svgTag       = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="12" height="12" style="display:inline-block;vertical-align:middle;"><path d="M12.586 2.586A2 2 0 0 0 11.172 2H4a2 2 0 0 0-2 2v7.172a2 2 0 0 0 .586 1.414l8.704 8.704a2.426 2.426 0 0 0 3.42 0l6.58-6.58a2.426 2.426 0 0 0 0-3.42z"/><circle cx="7.5" cy="7.5" r="1.5"/></svg>'
$svgDownload  = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14" style="display:inline-block;vertical-align:middle;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>'

# Build emoji unicode codepoints
$eCheck     = [System.Text.Encoding]::Unicode.GetString([System.Text.Encoding]::Unicode.GetBytes([char]0x2705))  # ✅
$eX         = [System.Text.Encoding]::Unicode.GetString([System.Text.Encoding]::Unicode.GetBytes([char]0x274C))  # ❌
$eWarn      = [System.Text.Encoding]::Unicode.GetString([System.Text.Encoding]::Unicode.GetBytes([char]0x26A0))  # ⚠
$eClipboard = [char]0x1F4CB  # 📋 (but this is surrogate pair)
$eReceipt   = [char[]]@(0xD83E, 0xDDFE)  # 🧾
$ePaperclip = [char[]]@(0xD83D, 0xDCCE)  # 📎
$eTrash     = [char[]]@(0xD83D, 0xDDD1)  # 🗑
$ePencil    = [char[]]@(0x270F)           # ✏ (variant: ✏️)
$eTag       = [char[]]@(0xD83C, 0xDFF7)  # 🏷
$eDown      = [char]0x2B07               # ⬇

$files = Get-ChildItem -Path $viewsPath -Filter "*.blade.php" -File

foreach ($file in $files) {
    Write-Host "Processing: $($file.Name)"
    $bytes = [System.IO.File]::ReadAllBytes($file.FullName)
    $content = [System.Text.Encoding]::UTF8.GetString($bytes)
    $original = $content

    # ⚠️ in HTML contexts
    $content = $content.Replace("`u{26A0}`u{FE0F} Gagal Menyimpan:", "$svgWarn Gagal Menyimpan:")
    $content = $content.Replace("`u{26A0}`u{FE0F} Foto struk harus", "$svgWarn Foto struk harus")
    
    # ✅ in HTML contexts
    $content = $content.Replace("`u{2705} Foto Struk Tersimpan", "$svgCheck Foto Struk Tersimpan")
    $content = $content.Replace("`u{2705} {{ session('success') }}", "$svgCheck {{ session('success') }}")
    
    # 🗑 in HTML (trash)
    $content = $content.Replace("`u{D83D}`u{DDD1} Hapus Foto", "$svgTrash Hapus Foto")
    $content = $content.Replace("`u{D83D}`u{DDD1} Hapus", "$svgTrash Hapus")

    # 🧾 large overlay icon
    $content = $content.Replace('<div style="font-size:48px;">' + "`u{D83E}`u{DDFE}" + '</div>', "<div style=`"margin-bottom:8px;`">$svgReceiptLg</div>")
    # 🧾 in heading/empty state
    $content = $content.Replace("`u{D83E}`u{DDFE} Galeri Struk", "$svgReceiptSm Galeri Struk")
    $content = $content.Replace('<div class="empty-icon">' + "`u{D83E}`u{DDFE}" + '</div>', "<div class=`"empty-icon`">$svgReceiptLg</div>")

    # 📎 in HTML
    $content = $content.Replace("`u{D83D}`u{DCCE} Pilih File Struk", "$svgPaperclip Pilih File Struk")
    $content = $content.Replace("`u{D83D}`u{DCCE} Tersimpan di server", "$svgPaperclip Tersimpan di server")
    
    # 📋 in HTML
    $content = $content.Replace("`u{D83D}`u{DCCB} Riwayat Pengeluaran", "$svgClipboard Riwayat Pengeluaran")
    $content = $content.Replace("`u{D83D}`u{DCCB} Riwayat Pemasukan", "$svgClipboard Riwayat Pemasukan")

    # ✏️ in HTML
    $content = $content.Replace("`u{270F}`u{FE0F} Ganti Foto Struk", "$svgPencil Ganti Foto Struk")
    $content = $content.Replace("`u{270F}`u{FE0F} Ganti Foto", "$svgPencil Ganti Foto")
    $content = $content.Replace('<div class="choice-icon">' + "`u{270F}`u{FE0F}" + '</div>', "<div class=`"choice-icon`">$svgPencil</div>")

    # 🏷️ in HTML
    $content = $content.Replace("`u{D83C}`u{DFF7}`u{FE0F} ", "$svgTag ")

    # ⬇️ Download in HTML
    $content = $content.Replace("`u{2B07}`u{FE0F} Download", "$svgDownload Download")

    # --- JavaScript contexts ---
    # Remove emoji from JS textContent/innerHTML assignments
    $content = $content.Replace("`u{2705} ' + name", "'' + name")
    $content = $content.Replace("`u{2705} ' + file.name", "'' + file.name")
    $content = $content.Replace("`u{D83D}`u{DCCE} Upload Foto Struk Dulu", "Upload Foto Struk Dulu")
    
    # showModal icon params: replace emoji icon chars with text codes
    $content = $content.Replace(", '`u{2705}')", ", 'ok')")
    $content = $content.Replace(", '`u{274C}')", ", 'err')")
    $content = $content.Replace(", '`u{2713}')", ", 'ok')")
    # showModal with emoji in title too
    $content = $content.Replace("'`u{26A0}`u{FE0F} Gagal'", "'Gagal'")
    $content = $content.Replace("'`u{2705} Berhasil'", "'Berhasil'")

    if ($content -ne $original) {
        $outBytes = [System.Text.Encoding]::UTF8.GetBytes($content)
        [System.IO.File]::WriteAllBytes($file.FullName, $outBytes)
        Write-Host "  -> Updated!"
    } else {
        Write-Host "  -> No changes."
    }
}

Write-Host "`nAll done!"
