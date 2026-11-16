# Design System — X-Ray Check (Digital Form Bandara)

Dokumen ini menjelaskan tampilan panel admin: palet warna, tipografi, ikon, breakpoint responsif, dan pola komponen.
Semua token desain didefinisikan di `resources/css/app.css` (blok `@theme` Tailwind CSS v4), berasal dari frame Figma
**"00-design-system"**: light mode, font Inter, radius 12, grid 8px.

---

## 1. Palet Warna

Palet ini adalah **palet kustom navy + teal** dari desain Figma. Warna status dan warna netralnya diambil dari palet
bawaan Tailwind (green-600, red-600, amber-600, blue-600, slate, gray), jadi tetap selaras dengan utilitas Tailwind lain.

Setiap token `--color-*` otomatis menjadi class Tailwind: `bg-*`, `text-*`, `border-*`, `fill-*`, `stroke-*`, `ring-*`,
`outline-*`, termasuk modifier opasitas seperti `bg-primary/10` atau `border-primary/40`.

### 1.1 Warna utama

| Peran | Token | Hex | Class Tailwind | Dipakai untuk |
| --- | --- | --- | --- | --- |
| **Primary** | `--color-primary` | `#1D4E89` | `bg-primary`, `text-primary`, `border-primary` | Tombol utama, link, fokus input, halaman aktif di pagination |
| Primary (gelap) | `--color-primary-dark` | `#12345A` | `bg-primary-dark`, `text-primary-dark`, `hover:bg-primary-dark` | Hover tombol utama, judul halaman, latar gambar form |
| **Secondary** | `--color-secondary` (alias `primary-dark`) | `#12345A` | `bg-secondary`, `text-secondary` | Permukaan gelap: sidebar, latar gambar, judul |
| **Accent** | `--color-accent` (alias `teal`) | `#19A7A0` | `bg-accent`, `text-accent`, `stroke-accent` | Logo, penanda menu aktif, garis grafik, avatar admin |

`secondary` dan `accent` adalah alias peran. `bg-secondary` sama dengan `bg-primary-dark`, dan `bg-accent` sama dengan
`bg-teal`. Kode lama memakai `primary-dark` dan `teal`; kode baru boleh memakai nama peran agar lebih mudah dibaca.

> Tombol **secondary** (`<x-button variant="secondary">`) bukan tombol berwarna navy. Itu tombol putih bergaris
> (`border-line bg-white text-ink`). Nama varian mengikuti desain Figma, bukan token warna.

### 1.2 Warna status

| Token | Hex | Setara Tailwind | Class | Dipakai untuk |
| --- | --- | --- | --- | --- |
| `--color-success` | `#16A34A` | green-600 | `text-success`, `bg-success/10` | Status Bagus / Aktif, pesan berhasil |
| `--color-danger` | `#DC2626` | red-600 | `bg-danger`, `text-danger` | Tombol hapus, pesan galat |
| `--color-danger-dark` | `#B91C1C` | red-700 | `hover:bg-danger-dark` | Hover tombol hapus |
| `--color-warning` | `#D97706` | amber-600 | `border-warning/25` | Peringatan, konfirmasi hapus kategori |
| `--color-info` | `#2563EB` | blue-600 | `text-info` | Informasi |

Badge status (`<x-badge>`) menerima hex apa pun lewat `color`, lalu memakai warna itu dengan opasitas 12% untuk latar.

### 1.3 Warna netral dan permukaan

| Token | Hex | Class | Dipakai untuk |
| --- | --- | --- | --- |
| `--color-ink` | `#1F2937` | `text-ink` | Teks utama |
| `--color-body` | `#334155` | `text-body` | Teks isi |
| `--color-muted` | `#64748B` | `text-muted` | Label, breadcrumb, teks sekunder |
| `--color-canvas` | `#F3F6FA` | `bg-canvas` | Latar halaman dan latar input |
| `--color-line` | `#D5DEEA` | `border-line` | Garis kartu, input, tombol |
| `--color-line-soft` | `#E6ECF4` | `divide-line-soft` | Pemisah baris tabel dan daftar |
| `--color-hover` | `#E8EEF6` | `hover:bg-hover` | Hover baris dan tombol |
| `--color-overlay` | `#0B1F3A` | `bg-overlay/45` | Latar modal, latar sidebar mobile, popup zoom gambar |

### 1.4 Warna sidebar

| Token | Hex | Dipakai untuk |
| --- | --- | --- |
| `--color-sidebar` | `#12345A` | Latar sidebar |
| `--color-sidebar-line` | `#24466F` | Garis pemisah di sidebar |
| `--color-sidebar-text` | `#B8C7DB` | Teks menu tidak aktif |
| `--color-sidebar-icon` | `#A9BBD3` | Ikon menu tidak aktif |
| `--color-sidebar-muted` | `#8FA6C4` | Subjudul dan username |
| `--color-sidebar-label` | `#6F89AB` | Label "MENU" |

Menu aktif memakai `bg-white/10 text-white` dengan garis kiri `before:bg-teal`.

---

## 2. Tipografi

| Font | Token | Class | Dipakai untuk |
| --- | --- | --- | --- |
| **Inter** (400, 500, 600, 700) | `--font-sans` | `font-sans` (bawaan) | Semua teks |
| **JetBrains Mono** (400, 500, 700) | `--font-mono` | `font-mono` | ID alat, kode, persentase zoom |

Kedua font dimuat dari **Bunny Fonts** melalui `laravel-vite-plugin/fonts` (`vite.config.js`), lalu dipasang di layout
dengan direktif `@fonts`. File font ikut di-build ke `public/build/assets`.

Skala ukuran yang dipakai: `text-xs` (label), `text-sm` (isi, tabel, tombol), `text-base` (judul kartu),
`text-xl` / `text-2xl` (judul halaman), `font-bold` untuk penekanan.

---

## 3. Ikon

Ikon berasal dari **[Lucide](https://lucide.dev)** (lisensi ISC, open source). Ikon tidak dipasang sebagai paket npm.
Path SVG-nya disalin langsung ke komponen `resources/views/components/icon.blade.php`, jadi tidak ada dependensi
tambahan.

- Grid 24×24, `stroke-width="2"`, `stroke="currentColor"`, ujung dan sambungan garis bulat.
- Ukuran dan warna diatur dari class pemanggil: `<x-icon name="tambah" class="size-4 text-muted" />`.
- Ikon bersifat dekoratif (`aria-hidden="true"`). Tombol yang hanya berisi ikon wajib punya `aria-label`.

| Nama di aplikasi | Ikon Lucide | | Nama di aplikasi | Ikon Lucide |
| --- | --- | --- | --- | --- |
| `dasbor` | layout-dashboard | | `label` | tag |
| `pengguna` | users | | `unduh` | download |
| `alat` | scan-line | | `unggah` | upload |
| `riwayat` | clipboard-list | | `lihat` | eye |
| `form` | file-text | | `lokasi` | map-pin |
| `keluar` | log-out | | `pensil` | pencil |
| `kalender` | calendar | | `hapus` | trash-2 |
| `lonceng` | bell | | `centang` | check |
| `cari` | search | | `peringatan` | triangle-alert |
| `tambah` | plus | | `menu-aksi` | ellipsis-vertical (titik diisi) |
| `tutup` | x | | `menu` | menu (burger) |
| `chevron-bawah` | chevron-down | | `perbesar` | zoom-in |
| `chevron-kiri` | chevron-left | | `perkecil` | zoom-out |
| `chevron-kanan` | chevron-right | | `atur-ulang` | rotate-ccw |

**Menambah ikon:** buka ikonnya di lucide.dev, salin isi `<svg>` (elemen `path`, `circle`, `rect`), lalu tambahkan
`@case('nama-ikon')` baru di `icon.blade.php`.

---

## 4. Bentuk, Jarak, dan Bayangan

- **Radius:** `rounded-xl` (12px) untuk kartu, `rounded-lg` untuk tombol, input, dan menu, `rounded-2xl` untuk modal,
  `rounded-md` untuk badge dan gambar.
- **Jarak:** skala spacing Tailwind (kelipatan 0.25rem, mengikuti grid 8px). Antar elemen sejajar memakai `gap-*`.
- **Bayangan:** `shadow-lg` untuk menu dropdown, `shadow-xl` untuk modal dan sidebar mobile.
- **Ukuran dinamis:** tidak memakai nilai piksel arbitrer (`w-[300px]`) untuk layout baru. Gunakan skala spacing
  (`w-72`), pecahan (`w-4/5`, `w-2/5`), `w-full`, `max-w-xs`, `min-w-4xl`, rasio (`aspect-video`), dan unit viewport
  (`h-dvh`).

---

## 5. Breakpoint Responsif

Layout ditulis **mobile-first**: class tanpa prefix berlaku untuk mobile, lalu ditimpa oleh prefix yang lebih besar.

| Perangkat | Lebar layar | Prefix | Perilaku utama |
| --- | --- | --- | --- |
| Mobile | < 640px | (tanpa prefix) | Sidebar tersembunyi, navbar menampilkan tombol burger, filter dan tombol memenuhi lebar, kartu satu kolom |
| Tablet | 640px – 1279px | `sm:`, `md:`, `lg:` | Sidebar tetap berupa drawer, filter dalam grid 4 kolom, kartu 2–3 kolom |
| Desktop | ≥ 1280px | `xl:` | Tampilan desktop asli: sidebar tetap di kiri, tabel penuh tanpa scroll |

**Aturan:** tampilan desktop adalah acuan dan tidak boleh berubah. Nilai desktop ditulis dengan prefix `xl:`
(misalnya `xl:w-75`, `xl:object-cover`), jadi perubahan untuk mobile dan tablet tidak memengaruhi desktop.

---

## 6. Pola Komponen

### 6.1 Sidebar dan navbar (`components/layouts/admin.blade.php`)

- **Desktop (`xl:`):** sidebar `w-60` menempel di kiri (`xl:sticky`).
- **Mobile dan tablet:** sidebar menjadi drawer `fixed` yang tersembunyi (`-translate-x-full invisible`). Tombol
  burger (`data-sidebar-buka`) di navbar menggesernya masuk dari kiri layar.
- Drawer ditutup dengan tombol ×, klik latar gelap (`data-sidebar-latar`), tombol Escape, atau saat layar dilebarkan
  ke ukuran desktop.
- Selama drawer terbuka, halaman di belakangnya `inert` dan tidak bisa di-scroll (`data-sidebar-terbuka:overflow-hidden`).
- Navbar menempel di atas (`sticky`) pada mobile dan tablet. Judul yang panjang dipotong dengan `truncate`.

### 6.2 Tabel (`<x-table-scroll>`)

Tabel tidak dibuat compact dan tidak diubah menjadi kartu. Di bawah `xl`, tabel dibungkus kotak scroll horizontal:

```blade
<x-card>
    <x-table-scroll>
        <table class="w-full min-w-4xl xl:min-w-0">...</table>
    </x-table-scroll>
</x-card>
```

- `min-w-*` menjaga semua kolom tetap utuh, lalu pengguna menggeser tabel ke samping (`overflow-x-auto`).
- Di `xl:` kotak scroll menjadi `overflow-visible` dan `min-w-0`, jadi tabel desktop tetap seperti semula.
- Menu aksi (⋮) di dalam tabel yang di-scroll dibuka sebagai panel `fixed`, jadi tidak terpotong oleh kotak scroll.
  Panel muncul di atas tombol jika ruang di bawahnya tidak cukup, dan menutup sendiri saat halaman di-scroll.

| Halaman | Lebar minimum tabel |
| --- | --- |
| Alat | `min-w-5xl` (64rem) |
| Pengguna | `min-w-4xl` (56rem) |
| Riwayat | `min-w-6xl` (72rem) |

### 6.3 Baris filter

Filter memakai grid pada mobile dan tablet, lalu kembali ke flex pada desktop:

```blade
<form class="grid grid-cols-2 items-center gap-3 sm:grid-cols-4 xl:flex">
    <x-search-input class="col-span-2 xl:mr-2 xl:w-75" />
    <x-form.select ... />
    <x-form.select ... />
</form>
```

Mobile: pencarian satu baris penuh, dua filter berdampingan. Tablet: semuanya dalam satu baris 4 kolom.

### 6.4 Gambar form

- Kartu gambar memakai rasio **`aspect-video`** dan **`object-contain`** di mobile dan tablet, jadi ukurannya mengikuti
  lebar kartu dan gambar tidak terpotong. Sisa ruang tertutup latar `bg-primary-dark`.
- Desktop tetap memakai desain asli (`xl:h-30 xl:object-cover`, atau `xl:aspect-[328/120]` di Detail Alat).
- Pratinjau unggah gambar dan dokumentasi di Detail Riwayat memakai `aspect-video` di mobile dan tinggi tetap di `sm:`.

### 6.5 Popup zoom gambar (`<x-zoomable-image>` dan `<x-image-zoom>`)

Berlaku di desktop, tablet, dan mobile. Setiap gambar form yang dibungkus `<x-zoomable-image>` bisa ditekan untuk
membuka popup layar penuh:

```blade
<x-zoomable-image :src="$urlGambar" alt="Gambar {{ $form->nama }}" class="size-full object-contain xl:object-cover" />
```

| Aksi | Desktop | Mobile / tablet |
| --- | --- | --- |
| Memperbesar / memperkecil | Scroll mouse, tombol + / −, tombol keyboard `+` `-` | Cubit dua jari, tombol + / − |
| Zoom cepat | Klik dua kali | Ketuk dua kali |
| Menggeser gambar | Seret | Seret satu jari |
| Kembali ke 100% | Tombol ↺ atau `0` | Tombol ↺ |
| Menutup | Tombol ×, Escape, klik area gelap | Tombol ×, ketuk area gelap |

Zoom antara 100% dan 500%. Gambar tidak bisa digeser melewati tepinya. Popup dirender sekali di layout admin
(`<x-image-zoom />`) dan dipakai bersama oleh semua gambar di halaman.

Lokasi gambar yang bisa di-zoom: kartu di halaman Form, Kelola Form alat, Detail Alat (Form Pemeriksaan), pratinjau
"Unggah Gambar" di modal Tambah / Ubah Form, dan Dokumentasi Gambar di Detail Riwayat.

### 6.6 Komponen lain

| Komponen | File | Catatan |
| --- | --- | --- |
| Tombol | `components/button.blade.php` | `primary` (biru), `secondary` (putih bergaris), `danger` (merah); tinggi `h-10` |
| Tombol ikon | `components/icon-button.blade.php` | Ikon saja, wajib `aria-label` |
| Kartu | `components/card.blade.php` | `rounded-xl border border-line bg-white` |
| Modal | `components/modal.blade.php` | Elemen `<dialog>` native, lebar maksimal `100vw - 2rem` di mobile |
| Badge | `components/badge.blade.php` | Warna dari hex apa pun |
| Pagination | `components/pagination.blade.php` | Bertumpuk dan rata tengah di mobile |

---

## 7. File Terkait

| File | Isi |
| --- | --- |
| `resources/css/app.css` | Token warna dan font (`@theme`) |
| `resources/js/app.js` | Interaksi: modal, dropdown, drawer sidebar, menu tabel, popup zoom |
| `resources/views/components/layouts/admin.blade.php` | Layout admin: sidebar, navbar, popup zoom |
| `resources/views/components/icon.blade.php` | Ikon Lucide |
| `resources/views/components/table-scroll.blade.php` | Kotak scroll horizontal untuk tabel |
| `resources/views/components/zoomable-image.blade.php` | Gambar yang bisa ditekan untuk zoom |
| `resources/views/components/image-zoom.blade.php` | Popup zoom gambar |
| `vite.config.js` | Pemuatan font Inter dan JetBrains Mono dari Bunny Fonts |
