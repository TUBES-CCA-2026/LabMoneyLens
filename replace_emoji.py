import os
import glob

views_path = r"c:\Users\LOQ\OneDrive\Documents\codingan\TUBES\LabMoneyLens\resources\views"

# SVG icon strings
svg = {
    'check_sm':  '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="15" height="15" style="display:inline-block;vertical-align:middle;margin-right:3px;"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>',
    'check_lg':  '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="28" height="28" style="display:inline-block;vertical-align:middle;opacity:0.4;"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>',
    'x_sm':      '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="15" height="15" style="display:inline-block;vertical-align:middle;margin-right:3px;"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6M9 9l6 6"/></svg>',
    'warn':      '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15" style="display:inline-block;vertical-align:middle;margin-right:3px;"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"/><path d="M12 9v4M12 17h.01"/></svg>',
    'clipboard': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15" style="display:inline-block;vertical-align:middle;margin-right:4px;"><rect x="9" y="2" width="6" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M12 11h4M12 16h4M8 11h.01M8 16h.01"/></svg>',
    'receipt_sm':'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15" style="display:inline-block;vertical-align:middle;margin-right:4px;"><path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z"/><path d="M16 8H8M16 12H8M12 16H8"/></svg>',
    'receipt_lg':'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="56" height="56" style="display:inline-block;vertical-align:middle;opacity:0.3;"><path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z"/><path d="M16 8H8M16 12H8M12 16H8"/></svg>',
    'paperclip': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14" style="display:inline-block;vertical-align:middle;margin-right:3px;"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>',
    'trash':     '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13" style="display:inline-block;vertical-align:middle;margin-right:3px;"><polyline points="3 6 5 6 21 6"/><path d="m19 6-.867 12.142A2 2 0 0 1 16.138 20H7.862a2 2 0 0 1-1.995-1.858L5 6"/><path d="M10 11v6M14 11v6M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>',
    'pencil':    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15" style="display:inline-block;vertical-align:middle;margin-right:3px;"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>',
    'tag':       '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="11" height="11" style="display:inline-block;vertical-align:middle;margin-right:3px;"><path d="M12.586 2.586A2 2 0 0 0 11.172 2H4a2 2 0 0 0-2 2v7.172a2 2 0 0 0 .586 1.414l8.704 8.704a2.426 2.426 0 0 0 3.42 0l6.58-6.58a2.426 2.426 0 0 0 0-3.42z"/><circle cx="7.5" cy="7.5" r="1.5"/></svg>',
    'download':  '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13" style="display:inline-block;vertical-align:middle;margin-right:3px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>',
}

# Map: (find_string, replace_with)
# HTML/template replacements
html_replacements = [
    # ⚠️ warning
    ('\u26a0\ufe0f Gagal Menyimpan:',      svg['warn'] + ' Gagal Menyimpan:'),
    ('\u26a0\ufe0f Foto struk harus',       svg['warn'] + ' Foto struk harus'),
    # ✅ check
    ('\u2705 Foto Struk Tersimpan',         svg['check_sm'] + ' Foto Struk Tersimpan'),
    ("\u2705 {{ session('success') }}",     svg['check_sm'] + " {{ session('success') }}"),
    # 🗑 trash
    ('\U0001f5d1 Hapus Foto',              svg['trash'] + ' Hapus Foto'),
    ('\U0001f5d1 Hapus',                   svg['trash'] + ' Hapus'),
    # 🧾 receipt large overlay
    ('<div style="font-size:48px;">\U0001f9fe</div>',
     '<div style="margin-bottom:8px;">' + svg['receipt_lg'] + '</div>'),
    # 🧾 receipt heading
    ('\U0001f9fe Galeri Struk',            svg['receipt_sm'] + ' Galeri Struk'),
    # 🧾 receipt empty-state div
    ('<div class="empty-icon">\U0001f9fe</div>',
     '<div class="empty-icon">' + svg['receipt_lg'] + '</div>'),
    # 📎 paperclip
    ('\U0001f4ce Pilih File Struk',        svg['paperclip'] + ' Pilih File Struk'),
    ('\U0001f4ce Tersimpan di server',     svg['paperclip'] + ' Tersimpan di server'),
    ('\U0001f4ce Upload Foto Struk Dulu',  svg['paperclip'] + ' Upload Foto Struk Dulu'),
    # 📋 clipboard
    ('\U0001f4cb Riwayat Pengeluaran',     svg['clipboard'] + ' Riwayat Pengeluaran'),
    ('\U0001f4cb Riwayat Pemasukan',       svg['clipboard'] + ' Riwayat Pemasukan'),
    # ✏️ pencil HTML
    ('\u270f\ufe0f Ganti Foto Struk',      svg['pencil'] + ' Ganti Foto Struk'),
    ('\u270f\ufe0f Ganti Foto',            svg['pencil'] + ' Ganti Foto'),
    ('<div class="choice-icon">\u270f\ufe0f</div>',
     '<div class="choice-icon">' + svg['pencil'] + '</div>'),
    # 🏷️ tag
    ('\U0001f3f7\ufe0f ',                  svg['tag'] + ' '),
    # ⬇️ download
    ('\u2b07\ufe0f Download',              svg['download'] + ' Download'),
]

# JS-only replacements (simpler string substitution, no SVG)
js_replacements = [
    # upload filename prefix emojis in JS strings
    ("'✅ ' + name",                        "'' + name"),
    ("'✅ ' + file.name",                   "'' + file.name"),
    ("textContent = '✅ ' +",               "textContent = '' +"),
    ("innerHTML = '✅ ' + name",            "innerHTML = '' + name"),
    ("innerHTML = '✅ ' + file.name",       "innerHTML = '' + file.name"),
    ("'📎 Upload Foto Struk Dulu",          "'Upload Foto Struk Dulu"),
    # modal icon params
    (", '❌')",                             ", 'error')"),
    (", '✅')",                             ", 'ok')"),
    (", '✓')",                              ", 'ok')"),
    # emoji in modal title/message
    ("'⚠️ Gagal'",                          "'Gagal'"),
    ("'✅ Berhasil'",                        "'Berhasil'"),
]

# Build final replacement list with unicode
def to_unicode(s):
    return s

files = glob.glob(os.path.join(views_path, "*.blade.php"))
for fpath in sorted(files):
    fname = os.path.basename(fpath)
    with open(fpath, 'r', encoding='utf-8') as f:
        content = f.read()

    original = content

    for find, replace in html_replacements:
        content = content.replace(find, replace)

    for find, replace in js_replacements:
        content = content.replace(find, replace)

    if content != original:
        with open(fpath, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"Updated: {fname}")
    else:
        print(f"No change: {fname}")

print("\nDone!")
