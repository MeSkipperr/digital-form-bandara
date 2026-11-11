/**
 * Small progressive enhancements for the admin pages.
 *
 * - [data-modal-open="id"] opens the <dialog id="id"> as a modal.
 * - [data-modal-close] closes the closest open <dialog>.
 * - Clicking a dialog's backdrop closes it.
 * - Open <details data-dropdown> menus close on outside click or Escape.
 * - form[data-auto-submit] submits itself when one of its <select> filters changes.
 * - form[data-tambah-kategori] adds the new kategori to every [data-kategori-options] list and selects it
 *   in the dropdown whose [data-kategori-asal] button opened the popup.
 * - [data-select-badge] mirrors the checked radio option into the closed select's summary.
 * - [data-tabs] toggles [data-tab-panel] elements from radio inputs.
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

document.querySelectorAll('[data-select-badge]').forEach((select) => {
    const display = select.querySelector('[data-select-display]');

    select.addEventListener('change', (event) => {
        const option = event.target.closest('label')?.querySelector('[data-option-badge]');

        if (option) {
            display.replaceChildren(option.cloneNode(true));
        }

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

let daftarKategoriAsal = null;

document.addEventListener('click', (event) => {
    const asal = event.target.closest('[data-kategori-asal]');

    if (asal) {
        daftarKategoriAsal = asal.closest('details')?.querySelector('[data-kategori-options]') ?? null;
    }
});

document.querySelectorAll('form[data-tambah-kategori]').forEach((form) => {
    const template = form.closest('dialog').querySelector('template[data-template-opsi-kategori]');

    form.addEventListener('submit', () => {
        const nama = form.elements.nama.value.trim();

        if (nama === '') {
            return;
        }

        const idSementara = `baru-${Date.now()}`;

        document.querySelectorAll('[data-kategori-options]').forEach((daftar) => {
            const opsi = template.content.firstElementChild.cloneNode(true);
            const input = opsi.querySelector('input');

            input.value = idSementara;
            opsi.querySelector('[data-option-badge]').textContent = nama;
            daftar.append(opsi);

            if (daftar === daftarKategoriAsal) {
                input.checked = true;
                input.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });

        daftarKategoriAsal = null;
        form.reset();
    });
});
