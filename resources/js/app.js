/**
 * Small progressive enhancements for the admin pages.
 *
 * - [data-modal-open="id"] opens the <dialog id="id"> as a modal.
 * - [data-modal-close] closes the closest open <dialog>.
 * - Clicking a dialog's backdrop closes it. Closing a dialog resets its forms to the saved values (data-nilai-asli)
 *   or the Tambah defaults (data-nilai-awal) and removes their error messages, so "Batal" discards the typed input.
 * - A form[data-buka-ulang] (rendered after a validation error) reopens its dialog on page load.
 * - An invalid required field inside a closed <details> dropdown opens it, so the browser can point at the field.
 * - Submit buttons are disabled once a form is sent, to prevent double submits.
 * - In a form[data-nilai-asli] (an Ubah modal) the submit button stays disabled while the input equals the saved values.
 * - Open <details data-dropdown> menus close on outside click or Escape.
 * - form[data-auto-submit] submits itself when one of its <select> filters changes.
 * - [data-select-badge] mirrors the checked radio option into the closed select's summary.
 * - [data-kategori-daftar] lists add, rename and delete kategori with fetch, then mirror the change into every
 *   kategori list on the page so open alat forms keep their input. The page reloads once all dialogs are closed.
 * - [data-unggah-gambar] image fields accept a picked or dropped PNG / JPG, preview it, and let "Hapus gambar" clear it.
 *   Closing the dialog restores the original image.
 * - [data-unduh-qr="svg-id"] downloads that QR <svg> as a black-and-white .jpg file.
 * - [data-tabs] toggles [data-tab-panel] elements from radio inputs.
 * - [data-line-chart] shows the tooltip of the point nearest the pointer (or picked with the arrow keys),
 *   and falls back to its data-sorot point when the pointer leaves.
 * - [data-donut-chart] highlights the hovered segment (or linked [data-donut-legend] row) and shows its value in the centre.
 * - Below the xl breakpoint [data-sidebar-buka] slides the [data-sidebar] drawer in from the left. The backdrop,
 *   [data-sidebar-tutup], Escape and widening the window to desktop close it again.
 * - A [data-dropdown] inside a [data-gulir-tabel] scroll box opens as a fixed panel, so the scroll box cannot clip it.
 * - [data-perbesar-gambar] buttons open their image in the [data-perbesar] zoom popup (wheel, pinch, buttons, drag).
 */

document.addEventListener('click', (event) => {
    const opener = event.target.closest('[data-modal-open]');

    if (opener) {
        event.preventDefault();
        document.getElementById(opener.dataset.modalOpen)?.showModal();
        opener.closest('details[data-dropdown]')?.removeAttribute('open');

        return;
    }

    const closer = event.target.closest('[data-modal-close]');

    if (closer) {
        event.preventDefault();
        closer.closest('dialog')?.close();

        return;
    }

    if (event.target instanceof HTMLDialogElement) {
        const box = event.target.getBoundingClientRect();
        const clickedBackdrop =
            event.clientX < box.left || event.clientX > box.right || event.clientY < box.top || event.clientY > box.bottom;

        if (clickedBackdrop) {
            event.target.close();
        }
    }

    document.querySelectorAll('details[data-dropdown][open]').forEach((dropdown) => {
        if (!dropdown.contains(event.target)) {
            dropdown.removeAttribute('open');
        }
    });
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        document.querySelectorAll('details[data-dropdown][open]').forEach((dropdown) => dropdown.removeAttribute('open'));
    }
});

document.querySelector('form[data-buka-ulang]')?.closest('dialog')?.showModal();

document.addEventListener(
    'invalid',
    (event) => {
        event.target.closest('details:not([open])')?.setAttribute('open', '');
    },
    true,
);

document.addEventListener('submit', (event) => {
    if (event.defaultPrevented || event.target.method === 'dialog') {
        return;
    }

    event.target.querySelectorAll('button[type="submit"]').forEach((button) => {
        button.disabled = true;
    });
});

/**
 * Whether a form[data-nilai-asli] differs from its saved values (a JSON object of field name to value).
 * Values are compared trimmed, like the server stores them, and a picked file always counts as a change.
 */
const hasUnsavedChanges = (form) => {
    const savedValues = JSON.parse(form.dataset.nilaiAsli);
    const currentValues = new FormData(form);
    const pickedFile = [...currentValues.values()].some((value) => value instanceof File && value.name !== '');

    return (
        pickedFile ||
        Object.entries(savedValues).some(
            ([name, saved]) => String(currentValues.get(name) ?? '').trim() !== String(saved ?? '').trim(),
        )
    );
};

const syncSaveButtons = (form) => {
    const isUnchanged = !hasUnsavedChanges(form);

    form.querySelectorAll('button[type="submit"]').forEach((button) => {
        button.disabled = isUnchanged;
    });
};

const editForms = document.querySelectorAll('form[data-nilai-asli]');

editForms.forEach((form) => {
    form.addEventListener('input', () => syncSaveButtons(form));
    form.addEventListener('change', () => syncSaveButtons(form));
    form.addEventListener('reset', () => setTimeout(() => syncSaveButtons(form)));
    syncSaveButtons(form);
});

window.addEventListener('pageshow', (event) => {
    if (event.persisted) {
        document.querySelectorAll('form button[type="submit"]:disabled').forEach((button) => {
            button.disabled = false;
        });
        editForms.forEach(syncSaveButtons);
    }
});

/**
 * Puts the saved values (Ubah) or the empty defaults (Tambah) back into a form, also as its defaults, so a form
 * reopened with the old input of a failed submit shows the current data again. Then removes every error message.
 */
const restoreForm = (form) => {
    const values = JSON.parse(form.dataset.nilaiAsli ?? form.dataset.nilaiAwal ?? '{}');

    Object.entries(values).forEach(([name, value]) => {
        const text = String(value ?? '');

        form.querySelectorAll(`[name="${name}"]`).forEach((field) => {
            if (field.type === 'radio' || field.type === 'checkbox') {
                field.checked = field.defaultChecked = field.value === text;
            } else if (field instanceof HTMLSelectElement) {
                [...field.options].forEach((option) => {
                    option.selected = option.defaultSelected = option.value === text;
                });
            } else {
                field.value = field.defaultValue = text;
            }
        });
    });

    form.querySelectorAll('[data-galat]').forEach((message) => message.remove());
    form.querySelectorAll('[aria-invalid]').forEach((field) => field.removeAttribute('aria-invalid'));
    form.removeAttribute('data-buka-ulang');
};

document.querySelectorAll('dialog').forEach((dialog) => {
    dialog.addEventListener('close', () => {
        dialog.querySelectorAll('form').forEach((form) => {
            form.reset();
            restoreForm(form);
        });
        dialog.querySelectorAll('details[data-dropdown][open]').forEach((dropdown) => dropdown.removeAttribute('open'));
        syncSelectDisplays();
    });
});

document.querySelectorAll('[data-select-badge]').forEach((select) => {
    select.addEventListener('change', (event) => {
        if (event.target.type !== 'radio') {
            return;
        }

        syncSelectDisplays();
        select.removeAttribute('open');
    });
});

document.querySelectorAll('[data-tabs]').forEach((tabs) => {
    const sync = () => {
        const active = tabs.querySelector('input[type="radio"]:checked')?.value;

        tabs.querySelectorAll('[data-tab-panel]').forEach((panel) => {
            panel.hidden = panel.dataset.tabPanel !== active;
        });
    };

    tabs.addEventListener('change', sync);
    sync();
});

document.querySelectorAll('form[data-auto-submit]').forEach((form) => {
    form.addEventListener('change', (event) => {
        if (event.target instanceof HTMLSelectElement) {
            form.requestSubmit();
        }
    });
});

/**
 * Re-renders every [data-select-display] from its checked radio, or the placeholder when none is checked.
 */
function syncSelectDisplays() {
    document.querySelectorAll('[data-select-badge]').forEach((select) => {
        const display = select.querySelector('[data-select-display]');
        const badge = select.querySelector('input[type="radio"]:checked')?.closest('label')?.querySelector('[data-option-badge]');

        if (badge) {
            display.replaceChildren(badge.cloneNode(true));

            return;
        }

        const placeholder = document.createElement('span');
        placeholder.className = 'text-muted';
        placeholder.textContent = display.dataset.placeholder ?? '';
        display.replaceChildren(placeholder);
    });
}

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
let kategoriChanged = false;

const sendKategori = async (url, method, data) => {
    const response = await fetch(url, {
        method,
        headers: { Accept: 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify(data),
    });
    const body = await response.json().catch(() => ({}));

    if (!response.ok) {
        throw new Error(Object.values(body.errors ?? {})[0]?.[0] ?? 'Terjadi kesalahan. Coba lagi.');
    }

    return body;
};

const showMode = (option, mode) => {
    option.querySelectorAll(':scope > [data-mode]').forEach((panel) => {
        panel.hidden = panel.dataset.mode !== mode;
    });
};

const showError = (container, message) => {
    const error = container.querySelector('[data-kategori-galat]');
    error.textContent = message;
    error.hidden = message === '';
};

const optionsWithId = (id) => document.querySelectorAll(`[data-kategori-opsi][data-kategori-id="${id}"]`);

const usageText = (option) => {
    const parts = [];

    if (Number(option.dataset.jumlahAlat) > 0) {
        parts.push(`${option.dataset.jumlahAlat} alat`);
    }

    if (Number(option.dataset.jumlahForm) > 0) {
        parts.push(`${option.dataset.jumlahForm} form`);
    }

    return parts.join(' dan ');
};

const openDeleteConfirmation = (option) => {
    const name = option.querySelector('[data-kategori-nama]').textContent.trim();
    const usage = usageText(option);
    const target = option.querySelector('[data-kategori-tujuan]');
    const confirm = option.querySelector('[data-kategori-konfirmasi-hapus]');
    const others = [...option.closest('[data-kategori-options]').querySelectorAll(':scope > [data-kategori-opsi]')].filter(
        (other) => other !== option,
    );

    target.replaceChildren(
        ...others.map((other) => new Option(other.querySelector('[data-kategori-nama]').textContent.trim(), other.dataset.kategoriId)),
    );

    option.querySelector('[data-kategori-pindah]').hidden = usage === '' || others.length === 0;
    confirm.disabled = usage !== '' && others.length === 0;
    option.querySelector('[data-kategori-pesan]').textContent =
        usage === ''
            ? `Hapus ${name}? Kategori ini belum dipakai alat atau form.`
            : others.length === 0
              ? `${name} masih dipakai ${usage}. Tambahkan kategori lain dulu untuk memindahkannya.`
              : `Hapus ${name}? ${usage} masih memakai kategori ini.`;

    showError(option, '');
    showMode(option, 'hapus');
};

const addKategoriToLists = (kategori, sourceList) => {
    document.querySelectorAll('[data-kategori-daftar]').forEach((list) => {
        const option = list.querySelector('template[data-kategori-template]').content.firstElementChild.cloneNode(true);
        const radio = option.querySelector('input[type="radio"]');

        option.dataset.kategoriId = kategori.id;
        option.querySelector('[data-kategori-nama]').textContent = kategori.nama;

        if (radio) {
            radio.value = kategori.id;
            radio.checked = list === sourceList;
        }

        list.querySelector('[data-kategori-options]').append(option);

        if (radio?.checked) {
            radio.dispatchEvent(new Event('change', { bubbles: true }));
        }
    });
};

const removeKategoriFromLists = (id, targetId) => {
    optionsWithId(id).forEach((option) => {
        const target = option.parentElement.querySelector(`:scope > [data-kategori-opsi][data-kategori-id="${targetId}"]`);
        const radio = option.querySelector('input[type="radio"]');

        if (target) {
            target.dataset.jumlahAlat = Number(target.dataset.jumlahAlat) + Number(option.dataset.jumlahAlat);
            target.dataset.jumlahForm = Number(target.dataset.jumlahForm) + Number(option.dataset.jumlahForm);

            if (radio?.checked) {
                target.querySelector('input[type="radio"]').checked = true;
            }
        }

        option.remove();
    });
};

const saveKategori = async (button) => {
    const list = button.closest('[data-kategori-daftar]');
    const option = button.closest('[data-kategori-opsi]');
    const container = option ?? list.querySelector('[data-kategori-baru]');
    const name = container.querySelector('[data-kategori-input]').value.trim();

    if (name === '') {
        showError(container, 'Nama kategori wajib diisi.');

        return;
    }

    if (option) {
        const kategori = await sendKategori(`${list.dataset.url}/${option.dataset.kategoriId}`, 'PUT', { nama: name });

        optionsWithId(kategori.id).forEach((same) => {
            same.querySelector('[data-kategori-nama]').textContent = kategori.nama;
            showMode(same, 'lihat');
        });
    } else {
        addKategoriToLists(await sendKategori(list.dataset.url, 'POST', { nama: name }), list);
        container.hidden = true;
        container.querySelector('[data-kategori-input]').value = '';
    }

    syncSelectDisplays();
};

const deleteKategori = async (button) => {
    const list = button.closest('[data-kategori-daftar]');
    const option = button.closest('[data-kategori-opsi]');
    const targetId = option.querySelector('[data-kategori-pindah]').hidden ? null : option.querySelector('[data-kategori-tujuan]').value;

    await sendKategori(`${list.dataset.url}/${option.dataset.kategoriId}`, 'DELETE', { pindah_ke: targetId });

    removeKategoriFromLists(option.dataset.kategoriId, targetId);
    syncSelectDisplays();
};

const kategoriActions = {
    kategoriTambah: (button) => {
        const container = button.closest('[data-kategori-daftar]').querySelector('[data-kategori-baru]');
        container.hidden = false;
        showError(container, '');
        container.querySelector('[data-kategori-input]').focus();
    },
    kategoriUbah: (button) => {
        const option = button.closest('[data-kategori-opsi]');
        const input = option.querySelector('[data-kategori-input]');

        input.value = option.querySelector('[data-kategori-nama]').textContent.trim();
        showError(option, '');
        showMode(option, 'ubah');
        input.select();
    },
    kategoriBatal: (button) => {
        const option = button.closest('[data-kategori-opsi]');

        if (option) {
            showMode(option, 'lihat');

            return;
        }

        const container = button.closest('[data-kategori-baru]');
        container.hidden = true;
        container.querySelector('[data-kategori-input]').value = '';
    },
    kategoriHapus: (button) => openDeleteConfirmation(button.closest('[data-kategori-opsi]')),
    kategoriSimpan: saveKategori,
    kategoriKonfirmasiHapus: deleteKategori,
};

document.addEventListener('click', async (event) => {
    const button = event.target.closest('[data-kategori-daftar] button');
    const action = Object.keys(kategoriActions).find((key) => button?.dataset[key] !== undefined);

    if (!action) {
        return;
    }

    const container = button.closest('[data-kategori-opsi]') ?? button.closest('[data-kategori-baru]');
    button.disabled = true;

    try {
        await kategoriActions[action](button);

        if (action === 'kategoriSimpan' || action === 'kategoriKonfirmasiHapus') {
            kategoriChanged = true;
        }
    } catch (error) {
        showError(container, error.message);
    } finally {
        button.disabled = false;
    }
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Enter' && event.target.matches('[data-kategori-input]')) {
        event.preventDefault();
        event.target.parentElement.querySelector('[data-kategori-simpan]').click();
    }
});

document.querySelectorAll('dialog').forEach((dialog) => {
    dialog.addEventListener('close', () => {
        if (kategoriChanged && !document.querySelector('dialog[open]')) {
            window.location.reload();
        }
    });
});

const UKURAN_GAMBAR_MAKSIMAL = 5 * 1024 * 1024;

document.querySelectorAll('[data-unggah-gambar]').forEach((field) => {
    const input = field.querySelector('[data-unggah-input]');
    const removeFlag = field.querySelector('[data-unggah-hapus]');
    const dropZone = field.querySelector('[data-unggah-kosong]');
    const preview = field.querySelector('[data-unggah-pratinjau]');
    const previewImage = field.querySelector('[data-unggah-gambar-pratinjau]');
    const error = field.querySelector('[data-unggah-galat]');
    const originalUrl = field.dataset.urlAsli ?? '';

    const showImage = (url) => {
        previewImage.src = url;
        preview.hidden = url === '';
        dropZone.hidden = url !== '';
    };

    const showUploadError = (message) => {
        error.textContent = message;
        error.hidden = message === '';
    };

    const currentUrl = () => (removeFlag.value === '1' ? '' : originalUrl);

    /** Values set from script fire no events, so the form's Simpan button state is refreshed by hand. */
    const notifyForm = () => field.dispatchEvent(new Event('input', { bubbles: true }));

    const choose = (file) => {
        if (!file) {
            return;
        }

        const message = !['image/png', 'image/jpeg'].includes(file.type)
            ? 'Gambar harus berupa file PNG atau JPG.'
            : file.size > UKURAN_GAMBAR_MAKSIMAL
              ? 'Ukuran gambar maksimal 5 MB.'
              : '';

        showUploadError(message);

        if (message !== '') {
            input.value = '';
            showImage(currentUrl());

            return;
        }

        const transfer = new DataTransfer();
        transfer.items.add(file);
        input.files = transfer.files;
        showImage(URL.createObjectURL(file));
    };

    input.addEventListener('change', () => choose(input.files[0]));

    dropZone.addEventListener('dragover', (event) => {
        event.preventDefault();
        dropZone.dataset.seret = '';
    });

    dropZone.addEventListener('dragleave', () => delete dropZone.dataset.seret);

    dropZone.addEventListener('drop', (event) => {
        event.preventDefault();
        delete dropZone.dataset.seret;
        choose(event.dataTransfer.files[0]);
        notifyForm();
    });

    field.querySelector('[data-unggah-hapus-tombol]').addEventListener('click', () => {
        input.value = '';
        removeFlag.value = originalUrl === '' ? '0' : '1';
        showUploadError('');
        showImage('');
        notifyForm();
    });

    field.closest('dialog')?.addEventListener('close', () => {
        input.value = '';
        removeFlag.value = '0';
        showUploadError('');
        showImage(originalUrl);
    });
});

document.querySelectorAll('[data-unduh-qr]').forEach((button) => {
    button.addEventListener('click', () => {
        const svg = document.getElementById(button.dataset.unduhQr).cloneNode(true);
        const modules = svg.viewBox.baseVal.width;
        // Whole pixels per module keep every module edge sharp; ~1024px is plenty for printed labels.
        const size = modules * Math.ceil(1024 / modules);
        const canvas = document.createElement('canvas');
        const image = new Image();

        svg.setAttribute('width', size);
        svg.setAttribute('height', size);
        canvas.width = size;
        canvas.height = size;

        image.addEventListener('load', () => {
            const context = canvas.getContext('2d');

            // JPG has no transparency, so paint white first in case any pixel is left uncovered.
            context.fillStyle = '#FFFFFF';
            context.fillRect(0, 0, size, size);
            context.imageSmoothingEnabled = false;
            context.drawImage(image, 0, 0, size, size);

            canvas.toBlob((blob) => {
                const link = document.createElement('a');

                link.href = URL.createObjectURL(blob);
                link.download = `${button.dataset.namaFile ?? 'qr'}.jpg`;
                link.click();
                URL.revokeObjectURL(link.href);
            }, 'image/jpeg', 1);
        });

        image.src = `data:image/svg+xml;charset=utf-8,${encodeURIComponent(new XMLSerializer().serializeToString(svg))}`;
    });
});

document.querySelectorAll('[data-line-chart]').forEach((chart) => {
    const dots = [...chart.querySelectorAll('circle[data-chart-index]')];
    const defaultIndex = chart.dataset.sorot === '' ? null : Number(chart.dataset.sorot);
    const viewBoxWidth = chart.viewBox.baseVal.width;
    let activeIndex = defaultIndex;

    const activate = (index) => {
        activeIndex = index;
        chart.querySelectorAll('[data-chart-index]').forEach((element) => {
            element.toggleAttribute('data-aktif', Number(element.dataset.chartIndex) === index);
        });
    };

    const nearestIndex = (clientX) => {
        const box = chart.getBoundingClientRect();
        const x = ((clientX - box.left) / box.width) * viewBoxWidth;

        return dots.reduce(
            (nearest, dot, index) => (Math.abs(Number(dot.dataset.x) - x) < Math.abs(Number(dots[nearest].dataset.x) - x) ? index : nearest),
            0,
        );
    };

    chart.addEventListener('pointermove', (event) => {
        const index = nearestIndex(event.clientX);

        if (index !== activeIndex) {
            activate(index);
        }
    });

    chart.addEventListener('pointerdown', (event) => activate(nearestIndex(event.clientX)));

    chart.addEventListener('pointerleave', (event) => {
        if (event.pointerType !== 'touch') {
            activate(defaultIndex);
        }
    });
    chart.addEventListener('blur', () => activate(defaultIndex));

    chart.addEventListener('keydown', (event) => {
        const steps = { ArrowLeft: -1, ArrowRight: 1 };

        if (!(event.key in steps) || dots.length === 0) {
            return;
        }

        event.preventDefault();
        const start = activeIndex ?? (steps[event.key] > 0 ? -1 : dots.length);
        activate(Math.min(Math.max(start + steps[event.key], 0), dots.length - 1));
    });
});

document.querySelectorAll('[data-donut-chart]').forEach((chart) => {
    const legend = chart.id ? document.querySelector(`[data-donut-legend="${chart.id}"]`) : null;
    const parts = [...chart.querySelectorAll('[data-donut-segment]'), ...(legend?.querySelectorAll('[data-donut-segment]') ?? [])];
    const value = chart.querySelector('[data-donut-value]');
    const caption = chart.querySelector('[data-donut-caption]');
    const total = Number(chart.dataset.total);

    const activate = (source) => {
        const index = source?.dataset.donutSegment;

        parts.forEach((part) => {
            const isActive = index !== undefined && part.dataset.donutSegment === index;
            part.toggleAttribute('data-aktif', isActive);
            part.toggleAttribute('data-redup', index !== undefined && !isActive);
        });

        if (index === undefined) {
            value.textContent = chart.dataset.total;
            caption.textContent = chart.dataset.caption;

            return;
        }

        const percentage = total === 0 ? 0 : Math.round((Number(source.dataset.value) / total) * 100);
        value.textContent = source.dataset.value;
        caption.textContent = `${source.dataset.label} · ${percentage}%`;
    };

    parts.forEach((part) => {
        part.addEventListener('pointerenter', () => activate(part));
        part.addEventListener('pointerleave', () => activate(null));
    });
});

const sidebar = document.querySelector('[data-sidebar]');
const sidebarOpener = document.querySelector('[data-sidebar-buka]');
const sidebarBackdrop = document.querySelector('[data-sidebar-latar]');
const mainContent = document.querySelector('[data-konten-utama]');
const desktopQuery = window.matchMedia('(width >= 80rem)');

/**
 * Opens or closes the mobile / tablet sidebar drawer. While it is open the page behind it is inert and does not scroll.
 */
const setSidebarOpen = (isOpen) => {
    if (!sidebar || isOpen === sidebar.hasAttribute('data-terbuka')) {
        return;
    }

    sidebar.toggleAttribute('data-terbuka', isOpen);
    sidebarBackdrop?.toggleAttribute('data-terbuka', isOpen);
    document.body.toggleAttribute('data-sidebar-terbuka', isOpen);
    sidebarOpener?.setAttribute('aria-expanded', String(isOpen));

    if (mainContent) {
        mainContent.inert = isOpen;
    }

    if (isOpen) {
        sidebar.querySelector('[data-sidebar-tutup]')?.focus();
    } else if (sidebar.contains(document.activeElement)) {
        sidebarOpener?.focus();
    }
};

sidebarOpener?.addEventListener('click', () => setSidebarOpen(true));
sidebarBackdrop?.addEventListener('click', () => setSidebarOpen(false));
sidebar?.querySelector('[data-sidebar-tutup]')?.addEventListener('click', () => setSidebarOpen(false));

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        setSidebarOpen(false);
    }
});

desktopQuery.addEventListener('change', (event) => {
    if (event.matches) {
        setSidebarOpen(false);
    }
});

/**
 * Opens an action menu that sits in a scrolling table as a fixed panel under its button (or above it when there is no
 * room below), so the scroll box cannot clip it. From xl up the table does not scroll and the menu stays absolute.
 */
document.addEventListener('click', (event) => {
    const summary = event.target.closest('[data-gulir-tabel] details[data-dropdown] > summary');
    const dropdown = summary?.parentElement;
    const panel = summary?.nextElementSibling;

    if (!summary || dropdown.open || !panel || getComputedStyle(dropdown.closest('[data-gulir-tabel]')).overflowX === 'visible') {
        return;
    }

    event.preventDefault();
    dropdown.open = true;

    const button = summary.getBoundingClientRect();
    const gap = parseFloat(getComputedStyle(panel).marginTop) || 0;
    const fitsBelow = button.bottom + gap * 2 + panel.offsetHeight <= window.innerHeight;

    panel.style.position = 'fixed';
    panel.style.right = `${document.documentElement.clientWidth - button.right}px`;
    panel.style.top = fitsBelow ? `${button.bottom}px` : `${button.top - panel.offsetHeight - gap * 2}px`;
});

document.addEventListener(
    'toggle',
    (event) => {
        if (event.target instanceof HTMLDetailsElement && event.target.matches('[data-dropdown]') && !event.target.open) {
            event.target.querySelector(':scope > summary + [style]')?.removeAttribute('style');
        }
    },
    true,
);

const closeFixedDropdowns = () => {
    document.querySelectorAll('details[data-dropdown][open]').forEach((dropdown) => {
        if (dropdown.querySelector(':scope > summary + *')?.style.position === 'fixed') {
            dropdown.removeAttribute('open');
        }
    });
};

window.addEventListener('resize', closeFixedDropdowns);
document.addEventListener('scroll', closeFixedDropdowns, { capture: true, passive: true });

const zoomDialog = document.querySelector('[data-perbesar]');

if (zoomDialog) {
    const stage = zoomDialog.querySelector('[data-perbesar-panggung]');
    const image = zoomDialog.querySelector('[data-perbesar-gambar-besar]');
    const title = zoomDialog.querySelector('[data-perbesar-judul]');
    const scaleLabel = zoomDialog.querySelector('[data-perbesar-skala]');
    const zoomOutButton = zoomDialog.querySelector('[data-perbesar-aksi="perkecil"]');
    const zoomInButton = zoomDialog.querySelector('[data-perbesar-aksi="perbesar"]');

    const SKALA_MINIMAL = 1;
    const SKALA_MAKSIMAL = 5;
    const LANGKAH_SKALA = 0.5;
    const SKALA_KETUK_GANDA = 2.5;

    const pointers = new Map();
    let view = { scale: SKALA_MINIMAL, x: 0, y: 0 };
    let gesture = null;
    let dragged = false;
    let lastTap = null;

    const clampScale = (scale) => Math.min(Math.max(scale, SKALA_MINIMAL), SKALA_MAKSIMAL);
    const distance = (a, b) => Math.hypot(a.x - b.x, a.y - b.y);
    const midpoint = (a, b) => ({ x: (a.x + b.x) / 2, y: (a.y + b.y) / 2 });

    /**
     * Viewport position of the image centre without the zoom transform, the origin all zoom math is relative to.
     */
    const imageCenter = () => {
        const box = image.getBoundingClientRect();

        return { x: box.left + box.width / 2 - view.x, y: box.top + box.height / 2 - view.y };
    };

    const isOnImage = (point) => {
        const box = image.getBoundingClientRect();

        return point.x >= box.left && point.x <= box.right && point.y >= box.top && point.y <= box.bottom;
    };

    /**
     * Applies a zoom level and offset, keeping the zoomed image from being dragged past the edges of the stage.
     */
    const apply = ({ scale, x, y }, smooth = false) => {
        const nextScale = clampScale(scale);
        const maxX = Math.max(0, (image.offsetWidth * nextScale - stage.clientWidth) / 2);
        const maxY = Math.max(0, (image.offsetHeight * nextScale - stage.clientHeight) / 2);

        view = { scale: nextScale, x: Math.min(Math.max(x, -maxX), maxX), y: Math.min(Math.max(y, -maxY), maxY) };

        image.toggleAttribute('data-halus', smooth);
        image.style.transform = `translate(${view.x}px, ${view.y}px) scale(${view.scale})`;
        image.style.cursor = view.scale > SKALA_MINIMAL ? 'grab' : 'zoom-in';
        scaleLabel.textContent = `${Math.round(view.scale * 100)}%`;
        zoomOutButton.disabled = view.scale <= SKALA_MINIMAL;
        zoomInButton.disabled = view.scale >= SKALA_MAKSIMAL;
    };

    /**
     * Zooms to `scale` while the image point under `point` stays under it.
     */
    const zoomAt = (scale, point, smooth = false) => {
        const center = imageCenter();
        const ratio = clampScale(scale) / view.scale;
        const offsetX = point.x - center.x;
        const offsetY = point.y - center.y;

        apply({ scale: clampScale(scale), x: offsetX - (offsetX - view.x) * ratio, y: offsetY - (offsetY - view.y) * ratio }, smooth);
    };

    const startGesture = () => {
        const points = [...pointers.values()];

        gesture = { view: { ...view }, center: imageCenter(), start: points[0] };

        if (points.length >= 2) {
            gesture.distance = distance(points[0], points[1]) || 1;
            gesture.mid = midpoint(points[0], points[1]);
        }
    };

    const toggleZoom = (point) => {
        if (view.scale > SKALA_MINIMAL) {
            apply({ scale: SKALA_MINIMAL, x: 0, y: 0 }, true);
        } else {
            zoomAt(SKALA_KETUK_GANDA, point, true);
        }
    };

    document.addEventListener('click', (event) => {
        const source = event.target.closest('[data-perbesar-gambar]')?.querySelector('img');

        if (!source?.getAttribute('src')) {
            return;
        }

        image.src = source.currentSrc || source.src;
        image.alt = source.alt;
        title.textContent = source.alt;
        lastTap = null;
        apply({ scale: SKALA_MINIMAL, x: 0, y: 0 });
        zoomDialog.showModal();
    });

    zoomDialog.addEventListener('click', (event) => {
        const action = event.target.closest('[data-perbesar-aksi]')?.dataset.perbesarAksi;

        if (action === 'atur-ulang') {
            apply({ scale: SKALA_MINIMAL, x: 0, y: 0 }, true);
        } else if (action) {
            zoomAt(view.scale + (action === 'perbesar' ? LANGKAH_SKALA : -LANGKAH_SKALA), imageCenter(), true);
        }
    });

    zoomDialog.addEventListener('keydown', (event) => {
        const keys = { '+': LANGKAH_SKALA, '=': LANGKAH_SKALA, '-': -LANGKAH_SKALA };

        if (event.key in keys) {
            event.preventDefault();
            zoomAt(view.scale + keys[event.key], imageCenter(), true);
        } else if (event.key === '0') {
            apply({ scale: SKALA_MINIMAL, x: 0, y: 0 }, true);
        }
    });

    stage.addEventListener(
        'wheel',
        (event) => {
            event.preventDefault();
            const delta = event.deltaMode === WheelEvent.DOM_DELTA_LINE ? event.deltaY * 33 : event.deltaY;
            zoomAt(view.scale * Math.exp(-delta * 0.002), { x: event.clientX, y: event.clientY });
        },
        { passive: false },
    );

    stage.addEventListener('pointerdown', (event) => {
        if (event.pointerType === 'mouse' && event.button !== 0) {
            return;
        }

        if (pointers.size === 0) {
            dragged = false;
        }

        stage.setPointerCapture(event.pointerId);
        pointers.set(event.pointerId, { x: event.clientX, y: event.clientY });
        startGesture();
    });

    stage.addEventListener('pointermove', (event) => {
        if (!pointers.has(event.pointerId)) {
            return;
        }

        pointers.set(event.pointerId, { x: event.clientX, y: event.clientY });
        const points = [...pointers.values()];

        if (points.length >= 2 && gesture.distance) {
            const scale = clampScale((gesture.view.scale * distance(points[0], points[1])) / gesture.distance);
            const ratio = scale / gesture.view.scale;
            const mid = midpoint(points[0], points[1]);
            const startX = gesture.mid.x - gesture.center.x;
            const startY = gesture.mid.y - gesture.center.y;

            dragged = true;
            apply({
                scale,
                x: mid.x - gesture.center.x - (startX - gesture.view.x) * ratio,
                y: mid.y - gesture.center.y - (startY - gesture.view.y) * ratio,
            });

            return;
        }

        const moveX = event.clientX - gesture.start.x;
        const moveY = event.clientY - gesture.start.y;

        if (Math.hypot(moveX, moveY) > 4) {
            dragged = true;
        }

        if (dragged && view.scale > SKALA_MINIMAL) {
            stage.style.cursor = 'grabbing';
            apply({ ...gesture.view, x: gesture.view.x + moveX, y: gesture.view.y + moveY });
        }
    });

    const endPointer = (event) => {
        if (!pointers.delete(event.pointerId)) {
            return;
        }

        if (pointers.size > 0) {
            startGesture();

            return;
        }

        gesture = null;
        stage.style.cursor = '';

        if (dragged || event.type === 'pointercancel') {
            return;
        }

        const point = { x: event.clientX, y: event.clientY };

        if (!isOnImage(point)) {
            lastTap = null;
            zoomDialog.close();

            return;
        }

        if (lastTap && event.timeStamp - lastTap.time < 300 && distance(point, lastTap) < 24) {
            lastTap = null;
            toggleZoom(point);

            return;
        }

        lastTap = { ...point, time: event.timeStamp };
    };

    stage.addEventListener('pointerup', endPointer);
    stage.addEventListener('pointercancel', endPointer);

    window.addEventListener('resize', () => {
        if (zoomDialog.open) {
            apply(view);
        }
    });
}
