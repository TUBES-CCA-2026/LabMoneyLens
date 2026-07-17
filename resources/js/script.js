// ── Load Turbo (async, non-blocking) ──
// Jika CDN gagal, kode tetap jalan tanpa Turbo (fallback ke DOMContentLoaded).
let _turboLoaded = false;
try {
    await import('https://cdn.skypack.dev/@hotwired/turbo');
    _turboLoaded = true;
} catch (e) {
    console.warn('Turbo gagal dimuat, fallback ke navigasi biasa:', e);
}

// Fallback: jika Turbo tidak aktif, jalankan handler saat DOMContentLoaded
if (!_turboLoaded) {
    document.addEventListener('DOMContentLoaded', () => {
        document.dispatchEvent(new Event('turbo:load'));
    });
}

// ── HAMBURGER MENU TOGGLE ──
function initHamburgerMenu() {
    const hamburger = document.getElementById("hamburger-menu");
    const sidebar = document.querySelector(".sidebar");
    const overlay = document.getElementById("sidebar-overlay");
    const app = document.querySelector(".app");

    if (!hamburger || hamburger.dataset.initialized) return;
    hamburger.dataset.initialized = "true";

    function closeSidebar() {
        hamburger.classList.remove("active");
        sidebar.classList.remove("active");
        overlay.classList.remove("active");
        // Backup: Reset inline style
        sidebar.style.transform = "translateX(-100%)";
    }

    function isMobile() {
        return window.innerWidth <= 1024;
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

    // Hanya tutup sidebar di mobile saat klik nav-item
    document.querySelectorAll(".nav-item").forEach((item) => {
        item.addEventListener("click", () => {
            if (isMobile()) {
                closeSidebar();
            }
        });
    });

    // Hanya tutup sidebar di mobile saat logout
    document
        .querySelector(".sidebar-logout")
        ?.addEventListener("click", () => {
            if (isMobile()) {
                closeSidebar();
            }
        });
}

// ── SATU turbo:load untuk semuanya ──
document.addEventListener("turbo:load", () => {
    initHamburgerMenu();

    // ── Update active nav-item berdasarkan URL saat ini ──
    // Karena sidebar pakai data-turbo-permanent, DOM-nya tetap sama
    // jadi class 'active' dari Blade tidak ter-update — kita update via JS.
    const currentPath = window.location.pathname;
    document.querySelectorAll(".sidebar-nav .nav-item").forEach((item) => {
        const href = item.getAttribute("href");
        // Cocokkan path (tanpa query string)
        const linkPath = href ? new URL(href, window.location.origin).pathname : "";
        if (linkPath && currentPath === linkPath) {
            item.classList.add("active");
        } else {
            item.classList.remove("active");
        }
    });

    // ── Drag & Drop (hanya di halaman yang punya .upload-zone) ──
    const uploadZone = document.querySelector(".upload-zone");
    if (uploadZone) {
        const receiptForm = document.getElementById("receipt_form");
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
            const receiptType =
                document.getElementById("receipt_type")?.value || "";
            const csrfToken = document.querySelector(
                'meta[name="csrf-token"]',
            )?.content;
            const formData = new FormData();
            formData.append("receipt_image", file);
            formData.append("type", receiptType);

            try {
                if (!window?.receiptParseUrl) {
                    throw new Error("URL parsing struk tidak tersedia.");
                }
                const response = await fetch(window.receiptParseUrl, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                        Accept: "application/json",
                    },
                    body: formData,
                });
                const result = await response.json().catch(() => null);
                console.log("Receipt parse response", response.status, result);
                if (!response.ok) {
                    throw new Error(
                        result?.error ||
                        result?.message ||
                        `Status ${response.status}`,
                    );
                }
                if (!result?.data) {
                    throw new Error(
                        result?.error || "Respons parsing tidak berisi data.",
                    );
                }
                fillParsedReceipt(result.data);
            } catch (error) {
                console.error("Receipt parse failed", error);
                alert(
                    "Gagal menganalisis struk: " +
                    (error?.message || "Silakan isi manual."),
                );
            }
        }

        function fillParsedReceipt(data) {
            const nominalField =
                document.getElementById("jumlah") ||
                document.getElementById("nominal");
            if (nominalField && data.nominal) {
                nominalField.value = data.nominal;
            }
            if (data.tanggal) {
                const tanggalField = document.getElementById("tanggal");
                if (tanggalField) {
                    tanggalField.value = data.tanggal;
                }
            }
            if (data.kategori) {
                const select =
                    document.getElementById("id_jenis_pengeluaran") ||
                    document.getElementById("kategori");
                if (select) {
                    const lower = data.kategori.toLowerCase();
                    for (const option of select.options) {
                        if (
                            option.text.toLowerCase().includes(lower) ||
                            lower.includes(option.text.toLowerCase())
                        ) {
                            option.selected = true;
                            break;
                        }
                    }
                }
            }
            const preview = document.getElementById("upload-preview");
            if (preview) {
                preview.innerHTML +=
                    '<div style="font-size:.75rem;color:#0d9488;margin-top:4px;">Struk terdeteksi</div>';
            }
        }

        function showPreview(src, name) {
            uploadZone.innerHTML = `
          <img src="${src}" style="max-height:160px;max-width:100%;border-radius:8px;object-fit:cover;margin-bottom:8px;"/>
          <span style="font-size:.85rem;color:#555;">${name}</span>
          <button id="remove-foto" style="display:block;margin-top:6px;background:none;border:none;color:#e74c3c;cursor:pointer;font-size:.85rem;">✕ Hapus foto</button>
        `;
            document
                .getElementById("remove-foto")
                .addEventListener("click", (e) => {
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
    } // end if (uploadZone)
});

// ── BEAUTIFUL PREMIUM DELETE CONFIRMATION MODAL ──
// Menggunakan SweetAlert2 via CDN untuk UI/UX yang jauh lebih bagus.
// Fallback ke native confirm jika CDN gagal dimuat.
document.addEventListener(
    "submit",
    async function (e) {
        const form = e.target.closest("form[data-confirm]");
        if (!form) return;

        // Jika sudah dikonfirmasi, biarkan submit berjalan
        if (form.dataset.confirmed === "true") {
            form.dataset.confirmed = "false";
            return;
        }

        e.preventDefault();
        
        const isPermanent = form.dataset.confirm === "permanent";
        const titleText = isPermanent ? "Hapus Permanen?" : "Konfirmasi Hapus";
        const msgText = isPermanent 
            ? "Data yang telah dihapus permanen tidak dapat dipulihkan kembali." 
            : "Data akan dipindahkan ke Back Up dan masih dapat dipulihkan kembali.";

        try {
            // Import SweetAlert2 secara dinamis
            const { default: Swal } = await import('https://cdn.jsdelivr.net/npm/sweetalert2@11/+esm');
            
            const result = await Swal.fire({
                title: titleText,
                text: msgText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                background: '#ffffff',
                color: '#1e293b',
                padding: '1.5em',
                width: '400px',
                shape: 'border-radius: 20px',
                backdrop: `rgba(15, 23, 42, 0.6) backdrop-filter: blur(4px)`,
                showClass: {
                    popup: `
                      animate__animated
                      animate__fadeInUp
                      animate__faster
                    `
                },
                hideClass: {
                    popup: `
                      animate__animated
                      animate__fadeOutDown
                      animate__faster
                    `
                },
                customClass: {
                    popup: 'swal-premium-popup',
                    confirmButton: 'swal-premium-confirm',
                    cancelButton: 'swal-premium-cancel'
                }
            });

            if (result.isConfirmed) {
                form.dataset.confirmed = "true";
                form.submit();
            }
        } catch (error) {
            console.warn("SweetAlert2 gagal dimuat, fallback ke native confirm.", error);
            // Fallback native confirm (goblok pop up) jika ada masalah jaringan/CDN
            if (confirm(titleText + "\\n\\n" + msgText)) {
                form.dataset.confirmed = "true";
                form.submit();
            }
        }
    },
    true
);
