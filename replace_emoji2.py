import os
import glob

views_path = r"c:\Users\LOQ\OneDrive\Documents\codingan\TUBES\LabMoneyLens\resources\views"

svg_camera    = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="22" height="22" style="display:inline-block;vertical-align:middle;"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3z"/><circle cx="12" cy="13" r="3"/></svg>'
svg_camera_sm = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="26" height="26" style="display:inline-block;vertical-align:middle;"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3z"/><circle cx="12" cy="13" r="3"/></svg>'
svg_calendar  = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="12" height="12" style="display:inline-block;vertical-align:middle;margin-right:3px;"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>'
svg_save      = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15" style="display:inline-block;vertical-align:middle;margin-right:3px;"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>'
svg_x_btn     = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="12" height="12" style="display:inline-block;vertical-align:middle;"><path d="M18 6 6 18M6 6l12 12"/></svg>'
svg_check_sm2 = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="14" height="14" style="display:inline-block;vertical-align:middle;"><path d="m20 6-11 11-5-5"/></svg>'

replacements = [
    # pilih files - camera emoji in choice-icon div
    ('<div class="choice-icon">\U0001f4f8</div>',
     '<div class="choice-icon">' + svg_camera + '</div>'),

    # struk.blade.php - calendar emoji
    ('\U0001f4c5 ',  svg_calendar + ' '),

    # struk.blade.php - floppy disk save
    ('\U0001f4be Simpan Foto', svg_save + ' Simpan Foto'),

    # profile.blade.php - camera emoji
    ('\U0001f4f7', svg_camera_sm),

    # ✖ close/delete button character
    ('>\u2716<', '>' + svg_x_btn + '<'),

    # ✓ small check marks
    ('>\u2713<', '>' + svg_check_sm2 + '<'),
    ("'ok')\n      }", "'ok')\n      }"),  # noop to keep pass
]

files = glob.glob(os.path.join(views_path, "*.blade.php"))
for fpath in sorted(files):
    fname = os.path.basename(fpath)
    with open(fpath, 'r', encoding='utf-8') as f:
        content = f.read()
    original = content

    for find, replace in replacements:
        content = content.replace(find, replace)

    if content != original:
        with open(fpath, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"Updated: {fname}")

print("Done!")
