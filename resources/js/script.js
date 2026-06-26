// ── HAMBURGER MENU TOGGLE ──
function initHamburgerMenu() {
  const hamburger = document.getElementById("hamburger-menu");
  const sidebar = document.querySelector(".sidebar");
  const overlay = document.getElementById("sidebar-overlay");

  if (!hamburger) return;

  function closeSidebar() {
    hamburger.classList.remove("active");
    sidebar.classList.remove("active");
    overlay.classList.remove("active");
    // Backup: Reset inline style
    sidebar.style.transform = "translateX(-100%)";
  }

  hamburger.addEventListener("click", (e) => {
    e.stopPropagation();
    hamburger.classList.toggle("active");
    sidebar.classList.toggle("active");
    overlay.classList.toggle("active");
    // Backup: Apply inline style for transform
    if (sidebar.classList.contains("active")) {
      sidebar.style.transform = "translateX(0)";
    } else {
      sidebar.style.transform = "translateX(-100%)";
    }
  });

  overlay.addEventListener("click", closeSidebar);

  document.querySelectorAll(".nav-item").forEach((item) => {
    item.addEventListener("click", closeSidebar);
  });

  document.querySelector(".sidebar-logout")?.addEventListener("click", closeSidebar);
}

// ── SATU DOMContentLoaded untuk semuanya ──
document.addEventListener("DOMContentLoaded", () => {
  initHamburgerMenu();

  // ── Drag & Drop ──
  const uploadZone = document.querySelector(".upload-zone");
  const receiptForm = document.getElementById('receipt_form');
  let fileInput = document.getElementById("receipt_image");

  function createHiddenFileInput() {
    const input = document.createElement("input");
    input.type = "file";
    input.id = "receipt_image";
    input.name = "receipt_image";
    input.accept = "image/*";
    input.style.position = "absolute";
    input.style.left = "-9999px";
    input.style.top = "-9999px";
    input.style.width = "1px";
    input.style.height = "1px";
    input.style.opacity = "0";
    input.style.pointerEvents = "none";
    input.addEventListener("change", () => {
      if (input.files[0]) handleFile(input.files[0]);
    });
    if (receiptForm) {
      receiptForm.appendChild(input);
    } else {
      document.body.appendChild(input);
    }
    return input;
  }

  if (!fileInput) {
    fileInput = createHiddenFileInput();
  }

  let uploadedFile = null;

  uploadZone.addEventListener("click", () => fileInput.click());

  uploadZone.addEventListener("dragover", (e) => {
    e.preventDefault();
    uploadZone.classList.add("drag-over");
  });

  uploadZone.addEventListener("dragleave", () => {
    uploadZone.classList.remove("drag-over");
  });

  uploadZone.addEventListener("drop", (e) => {
    e.preventDefault();
    uploadZone.classList.remove("drag-over");
    const file = e.dataTransfer.files[0];
    if (file) handleFile(file);
  });

  fileInput.addEventListener("change", () => {
    if (fileInput.files[0]) handleFile(fileInput.files[0]);
  });

  function handleFile(file) {
    if (!file.type.startsWith("image/")) {
      alert("Harap unggah file gambar.");
      return;
    }
    uploadedFile = file;
    const dataTransfer = new DataTransfer();
    dataTransfer.items.add(file);
    fileInput.files = dataTransfer.files;
    if (!fileInput.files.length) {
      if (fileInput.parentNode) {
        fileInput.parentNode.removeChild(fileInput);
      }
      fileInput = createHiddenFileInput();
      fileInput.files = dataTransfer.files;
    }
    const reader = new FileReader();
    reader.onload = (e) => showPreview(e.target.result, file.name);
    reader.readAsDataURL(file);
    analyzeReceipt(file);
  }

  async function analyzeReceipt(file) {
    const receiptType = document.getElementById('receipt_type')?.value || '';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const formData = new FormData();
    formData.append('receipt_image', file);
    formData.append('type', receiptType);

    try {
      if (!window?.receiptParseUrl) {
        throw new Error('URL parsing struk tidak tersedia.');
      }
      const response = await fetch(window.receiptParseUrl, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json',
        },
        body: formData,
      });
      const result = await response.json().catch(() => null);
      console.log('Receipt parse response', response.status, result);
      if (!response.ok) {
        throw new Error(result?.error || result?.message || `Status ${response.status}`);
      }
      if (!result?.data) {
        throw new Error(result?.error || 'Respons parsing tidak berisi data.');
      }
      fillParsedReceipt(result.data);
    } catch (error) {
      console.error('Receipt parse failed', error);
      alert('Gagal menganalisis struk: ' + (error?.message || 'Silakan isi manual.'));
    }
  }

  function fillParsedReceipt(data) {
    const nominalField = document.getElementById('jumlah') || document.getElementById('nominal');
    if (nominalField && data.nominal) {
      nominalField.value = data.nominal;
    }
    if (data.tanggal) {
      const tanggalField = document.getElementById('tanggal');
      if (tanggalField) {
        tanggalField.value = data.tanggal;
      }
    }
    if (data.kategori) {
      const select = document.getElementById('id_jenis_pengeluaran') || document.getElementById('kategori');
      if (select) {
        const lower = data.kategori.toLowerCase();
        for (const option of select.options) {
          if (option.text.toLowerCase().includes(lower) || lower.includes(option.text.toLowerCase())) {
            option.selected = true;
            break;
          }
        }
      }
    }
    const preview = document.getElementById('upload-preview');
    if (preview) {
      preview.innerHTML += '<div style="font-size:.75rem;color:#0d9488;margin-top:4px;">Struk terdeteksi</div>';
    }
  }

  function showPreview(src, name) {
    uploadZone.innerHTML = `
      <img src="${src}" style="max-height:160px;max-width:100%;border-radius:8px;object-fit:cover;margin-bottom:8px;"/>
      <span style="font-size:.85rem;color:#555;">${name}</span>
      <button id="remove-foto" style="display:block;margin-top:6px;background:none;border:none;color:#e74c3c;cursor:pointer;font-size:.85rem;">✕ Hapus foto</button>
    `;
    document.getElementById("remove-foto").addEventListener("click", (e) => {
      e.stopPropagation();
      resetUploadZone();
    });
  }

  function resetUploadZone() {
    uploadedFile = null;
    fileInput.value = "";
    uploadZone.innerHTML = `
      <svg viewBox="0 0 24 24" stroke-width="1.5">
        <rect x="3" y="3" width="18" height="18" rx="2"/>
        <path d="M3 9h18M9 21V9"/>
      </svg>
      <span class="upload-label">Unggah foto di sini</span>
    `;
  }

});
