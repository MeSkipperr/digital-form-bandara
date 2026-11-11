<?php

/**
 * Sample data taken from the Figma design, shared by the Kelola Alat pages.
 * Replace with real queries when the backend is wired up.
 *
 * Status follows the `alat.status` enum and is fixed to two values; it is not managed from Kelola Alat.
 *
 * @return array{
 *     status: array<string, array{label: string, warna: string}>,
 *     kategori: array<int, string>,
 *     alat: array<string, array{id_kategori_alat: int, dibuat_pada: string, model: string, status: string, lokasi: string, deskripsi: string}>,
 *     form: array<int, array{nama: string, gambar: string, jumlah_task: int, kondisi: string, keterangan: string}>,
 *     task: array<int, array{nama: string, catatan: string}>
 * }
 */
return [
    'status' => [
        'bagus' => ['label' => 'Bagus', 'warna' => '#16A34A'],
        'perlu_perbaikan' => ['label' => 'Perlu Perbaikan', 'warna' => '#D97706'],
    ],

    'kategori' => [
        1 => 'X-Ray',
        2 => 'Pendeteksi Bom',
        3 => 'Metal Detector',
    ],

    'alat' => [
        'XRAY-01' => ['id_kategori_alat' => 1, 'dibuat_pada' => '12 Jan 2026, 08:30', 'model' => 'Xray Pro Max', 'status' => 'bagus', 'lokasi' => 'Gudang Belakang', 'deskripsi' => "Mesin X-Ray untuk pemeriksaan barang kargo di gudang belakang.\nDipasang 2024. Pengecekan rutin dilakukan setiap hari kerja."],
        'XRAY-02' => ['id_kategori_alat' => 1, 'dibuat_pada' => '12 Jan 2026, 09:10', 'model' => 'Xray Pro Max', 'status' => 'perlu_perbaikan', 'lokasi' => 'Terminal Kargo', 'deskripsi' => 'Mesin X-Ray untuk pemeriksaan barang di terminal kargo.'],
        'XRAY-03' => ['id_kategori_alat' => 1, 'dibuat_pada' => '03 Feb 2026, 10:00', 'model' => 'Xray Scan 200', 'status' => 'bagus', 'lokasi' => 'Pintu Masuk A', 'deskripsi' => 'Mesin X-Ray untuk pemeriksaan bagasi di pintu masuk A.'],
        'BOMB-01' => ['id_kategori_alat' => 2, 'dibuat_pada' => '17 Mar 2026, 13:45', 'model' => 'Itemiser 4DX', 'status' => 'perlu_perbaikan', 'lokasi' => 'Lobi Utama', 'deskripsi' => 'Pendeteksi jejak bahan peledak untuk pemeriksaan tas di lobi utama.'],
        'BOMB-02' => ['id_kategori_alat' => 2, 'dibuat_pada' => '17 Mar 2026, 14:20', 'model' => 'IONSCAN 600', 'status' => 'bagus', 'lokasi' => 'Gudang Depan', 'deskripsi' => 'Pendeteksi jejak bahan peledak untuk barang di gudang depan.'],
        'METL-01' => ['id_kategori_alat' => 3, 'dibuat_pada' => '21 Apr 2026, 09:05', 'model' => 'Garrett PD 6500i', 'status' => 'bagus', 'lokasi' => 'Ruang Transit', 'deskripsi' => 'Gerbang metal detector untuk pemeriksaan penumpang di ruang transit.'],
    ],

    'form' => [
        1 => ['nama' => 'Form Xray Atas', 'gambar' => 'images/contoh/form-xray-atas.svg', 'jumlah_task' => 4, 'kondisi' => 'baik', 'keterangan' => 'Terakhir diisi hari ini'],
        2 => ['nama' => 'Form Xray Samping', 'gambar' => 'images/contoh/form-xray-samping.svg', 'jumlah_task' => 3, 'kondisi' => 'buruk', 'keterangan' => 'Ada item gagal'],
        3 => ['nama' => 'Form Kelistrikan', 'gambar' => 'images/contoh/form-kelistrikan.svg', 'jumlah_task' => 5, 'kondisi' => 'baik', 'keterangan' => 'Terakhir diisi kemarin'],
        4 => ['nama' => 'Form Kebersihan Lensa', 'gambar' => 'images/contoh/form-kebersihan-lensa.svg', 'jumlah_task' => 2, 'kondisi' => 'baik', 'keterangan' => 'Terakhir diisi kemarin'],
    ],

    'task' => [
        ['nama' => 'Periksa kabel daya', 'catatan' => 'Pastikan kabel tidak terkelupas dan konektor terpasang kencang.'],
        ['nama' => 'Cek konveyor', 'catatan' => 'Jalankan konveyor kosong selama satu menit, dengarkan suara tidak normal.'],
        ['nama' => 'Uji tabung X-Ray', 'catatan' => 'Lakukan scan benda uji dan bandingkan dengan hasil standar.'],
        ['nama' => 'Bersihkan jendela deteksi', 'catatan' => 'Lap jendela detektor dengan kain bersih tanpa cairan berlebih.'],
        ['nama' => 'Kalibrasi detektor', 'catatan' => 'Catat hasil kalibrasi dan laporkan jika di luar batas toleransi.'],
    ],
];
