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

// ── CUSTOM DELETE CONFIRMATION MODAL (pengganti confirm() bawaan browser) ──
// Dipakai di semua halaman lewat @vite('resources/js/script.js').
// Cukup tambahkan atribut data-confirm="soft" atau data-confirm="permanent"
// pada <form> yang melakukan aksi hapus — modal akan otomatis menangani submit-nya.
// Dibuat sebagai IIFE terpisah (bukan di dalam DOMContentLoaded di atas) supaya
// tetap berjalan di semua halaman meski elemen lain (mis. .upload-zone) tidak ada.
(function () {
  'use strict';

  const CONFIRM_COPY = {
    soft: {
      title: 'Konfirmasi Hapus',
      message: 'Apakah Anda yakin ingin menghapus data ini?<br><br>Data akan dipindahkan ke Back Up dan masih dapat dipulihkan kembali.'
    },
    permanent: {
      title: 'Konfirmasi Hapus Permanen',
      message: 'Apakah Anda yakin ingin menghapus permanen data ini?<br><br>Data yang telah dihapus permanen tidak dapat dipulihkan kembali.'
    }
  };

  let overlayEl = null;
  let titleEl, messageEl, cancelBtn, deleteBtn, closeBtn;
  let pendingForm = null;

  function buildModal() {
    if (overlayEl) return;

    overlayEl = document.createElement('div');
    overlayEl.id = 'confirm-modal-overlay';
    overlayEl.className = 'confirm-modal-overlay';
    overlayEl.innerHTML = `
      <div class="confirm-modal" role="dialog" aria-modal="true" aria-labelledby="confirm-modal-title">
        <button type="button" class="confirm-modal-close" aria-label="Tutup">&times;</button>
        <h3 class="confirm-modal-title" id="confirm-modal-title"></h3>
        <p class="confirm-modal-message"></p>
        <div class="confirm-modal-actions">
          <button type="button" class="confirm-modal-btn confirm-modal-cancel">Batal</button>
          <button type="button" class="confirm-modal-btn confirm-modal-delete">Hapus</button>
        </div>
      </div>
    `;
    document.body.appendChild(overlayEl);

    titleEl   = overlayEl.querySelector('.confirm-modal-title');
    messageEl = overlayEl.querySelector('.confirm-modal-message');
    cancelBtn = overlayEl.querySelector('.confirm-modal-cancel');
    deleteBtn = overlayEl.querySelector('.confirm-modal-delete');
    closeBtn  = overlayEl.querySelector('.confirm-modal-close');

    cancelBtn.addEventListener('click', closeModal);
    closeBtn.addEventListener('click', closeModal);
    overlayEl.addEventListener('click', function (e) {
      if (e.target === overlayEl) closeModal();
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && overlayEl.classList.contains('active')) closeModal();
    });

    deleteBtn.addEventListener('click', function () {
      const form = pendingForm;
      closeModal();
      if (form) {
        form.dataset.confirmed = 'true';
        form.submit();
      }
    });
  }

  function closeModal() {
    if (overlayEl) overlayEl.classList.remove('active');
    pendingForm = null;
  }

  function openModal(type, form) {
    buildModal();
    const copy = CONFIRM_COPY[type] || CONFIRM_COPY.soft;
    titleEl.textContent = copy.title;
    messageEl.innerHTML = copy.message;
    pendingForm = form;
    overlayEl.classList.add('active');
  }

  // Event delegation di level document: otomatis menangkap form yang di-render
  // ulang lewat AJAX (mis. tabel Laporan yang live-update), tanpa perlu binding manual.
  document.addEventListener('submit', function (e) {
    const form = e.target.closest('form[data-confirm]');
    if (!form) return;

    // Form sudah dikonfirmasi lewat modal → biarkan submit berjalan normal.
    if (form.dataset.confirmed === 'true') {
      form.dataset.confirmed = 'false';
      return;
    }

    e.preventDefault();
    const type = form.dataset.confirm === 'permanent' ? 'permanent' : 'soft';
    openModal(type, form);
  }, true);
})();
