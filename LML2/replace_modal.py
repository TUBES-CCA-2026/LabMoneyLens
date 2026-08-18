import os
import glob

views_path = r"c:\Users\LOQ\OneDrive\Documents\codingan\TUBES\LabMoneyLens\resources\views"

# SVG icons for ok and error states (for modal)
svg_ok_lg  = '<svg viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5" width="56" height="56"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>'
svg_err_lg = '<svg viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5" width="56" height="56"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6M9 9l6 6"/></svg>'

# Update showModal function to render SVG icons based on string key
OLD_SHOWMODAL_OTO = "function showModal(title, message, icon='✓') {\n      document.getElementById('modal-icon').textContent = icon;"
NEW_SHOWMODAL_OTO = """function showModal(title, message, icon='ok') {
      const modalIcon = document.getElementById('modal-icon');
      if (icon === 'ok') {
        modalIcon.innerHTML = '""" + svg_ok_lg + """';
      } else if (icon === 'error' || icon === 'err') {
        modalIcon.innerHTML = '""" + svg_err_lg + """';
      } else {
        modalIcon.textContent = icon;
      }"""

OLD_SHOWMODAL_EDIT = "function showModal(title, message, icon = '✓') {\n      document.getElementById('modal-icon').textContent = icon;"
NEW_SHOWMODAL_EDIT = """function showModal(title, message, icon = 'ok') {
      const modalIcon = document.getElementById('modal-icon');
      if (icon === 'ok') {
        modalIcon.innerHTML = '""" + svg_ok_lg + """';
      } else if (icon === 'error' || icon === 'err') {
        modalIcon.innerHTML = '""" + svg_err_lg + """';
      } else {
        modalIcon.textContent = icon;
      }"""

files = glob.glob(os.path.join(views_path, "*.blade.php"))
updated = 0
for fpath in sorted(files):
    fname = os.path.basename(fpath)
    with open(fpath, 'r', encoding='utf-8') as f:
        content = f.read()
    original = content

    content = content.replace(OLD_SHOWMODAL_OTO, NEW_SHOWMODAL_OTO)
    content = content.replace(OLD_SHOWMODAL_EDIT, NEW_SHOWMODAL_EDIT)

    if content != original:
        with open(fpath, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"Updated: {fname}")
        updated += 1

print(f"\nDone! {updated} files updated.")
