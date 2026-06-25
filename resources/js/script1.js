const data = [];

function renderTable() {
  const tbody = document.getElementById("table-body");
  if (!tbody) {
    return;
  }
  tbody.innerHTML = data.map((row, i) => `
    <tr>
      <td>${row.id}</td>
      <td>${row.kategori}</td>
      <td>${row.jumlah.toLocaleString("id-ID")}</td>
      <td>${row.tanggal}</td>
      <td class="action-cell">
        <button class="btn-edit" onclick="editRow(${i})">Edit</button>
        <span class="sep">/</span>
        <button class="btn-hapus" onclick="deleteRow(${i})">Hapus</button>
      </td>
    </tr>
  `).join("");
}

function deleteRow(i) {
  if (confirm(`Hapus entri ${data[i].id}?`)) {
    data.splice(i, 1);
    renderTable();
  }
}

function editRow(i) {
  const row = data[i];
  const newJumlah = prompt(`Edit jumlah untuk ${row.id}:`, row.jumlah);
  if (newJumlah !== null && !isNaN(newJumlah) && newJumlah.trim() !== "") {
    data[i].jumlah = parseInt(newJumlah);
    renderTable();
  }
}

function formatTanggal(dateStr) {
  const d = new Date(dateStr);
  const mm = String(d.getMonth() + 1).padStart(2, "0");
  const dd = String(d.getDate()).padStart(2, "0");
  const yyyy = d.getFullYear();
  return `${mm}/${dd}/${yyyy}`;
}

function generateId() {
  return "PEM" + String(data.length + 1).padStart(6, "0");
}

// ── SATU DOMContentLoaded untuk semuanya ──
document.addEventListener("DOMContentLoaded", () => {
  renderTable();

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
      const select = document.getElementById('id_jenis_penerimaan') || document.getElementById('kategori');
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

  // ── Simpan ──
  document.getElementById("save-btn").addEventListener("click", (e) => {
    if (document.querySelector('form[enctype="multipart/form-data"]')) {
      return;
    }
    const kategori = document.getElementById("kategori").value;
    const jumlah   = document.getElementById("jumlah").value;
    const tanggal  = document.getElementById("tanggal").value;

    if (!kategori || !jumlah || !tanggal) {
      alert("Lengkapi semua field terlebih dahulu.");
      return;
    }

    data.push({
      id: generateId(),
      kategori,
      jumlah: parseInt(jumlah),
      tanggal: formatTanggal(tanggal),
      foto: uploadedFile ? URL.createObjectURL(uploadedFile) : null,
    });

    renderTable();

    document.getElementById("kategori").value = "";
    document.getElementById("jumlah").value   = "";
    document.getElementById("tanggal").value  = "";
    resetUploadZone();
  });

  // ── Konfirmasi ──
  document.getElementById("confirm-btn").addEventListener("click", () => {
    if (data.length === 0) {
      alert("Tidak ada entri untuk dikonfirmasi.");
      return;
    }
    alert(`${data.length} entri berhasil dikonfirmasi!`);
  });
});