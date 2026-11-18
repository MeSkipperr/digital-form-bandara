<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $template->nama }} - {{ $alat->id }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#F8FAFC] text-[#1F2937] font-['Inter'] min-h-screen relative overflow-x-hidden flex flex-col justify-between">

    <!-- 1. shape garis Atas Kanan -->
    <div class="fixed top-0 right-0 pointer-events-none z-0">
        <svg width="306" height="99" viewBox="0 0 306 99" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0.480469 -40.2725C90.7369 80.5451 226.122 13.4242 338.942 -13.4241" stroke-width="1.2" stroke="#1D4E89" stroke-opacity="0.22"/>
            <path d="M23.0444 -40.2725C107.66 93.9692 237.403 33.5605 338.942 13.4242" stroke-width="1.2" stroke="#1D4E89" stroke-opacity="0.22"/>
            <path d="M45.6089 -40.2725C124.583 107.393 248.686 53.6967 338.942 40.2726" stroke-width="1.2" stroke="#19A7A0" stroke-opacity="0.25"/>
            <path d="M68.1729 -40.2725C141.506 120.818 259.968 73.833 338.942 67.1209" stroke-width="1.2" stroke="#19A7A0" stroke-opacity="0.2"/>
            <path opacity="0.6" d="M90.7368 -40.2725C158.429 134.242 271.25 93.9692 338.942 93.9692" stroke-width="1.2" stroke="#1D4E89" stroke-opacity="0.22"/>
        </svg>
    </div>

    <!-- 2. shape garis Sisi Kiri Tengah -->
    <div class="fixed left-0 top-1/3 pointer-events-none z-0">
        <svg width="44" height="505" viewBox="0 0 44 505" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M-33.8462 0.460938C78.9743 94.4301 56.4102 309.217 -33.8462 403.186" stroke-width="1.2" stroke="#19A7A0" stroke-opacity="0.22"/>
            <path d="M-33.8462 27.3093C67.6923 114.566 45.1282 329.353 -33.8462 423.322" stroke-width="1.2" stroke="#1D4E89" stroke-opacity="0.18"/>
            <path d="M-33.8462 54.1576C56.4102 134.703 33.8461 349.489 -33.8462 443.459" stroke-width="1.2" stroke="#19A7A0" stroke-opacity="0.22"/>
            <path d="M-33.8462 81.006C45.1282 154.839 22.5641 369.626 -33.8462 463.595" stroke-width="1.2" stroke="#1D4E89" stroke-opacity="0.15"/>
            <path opacity="0.5" d="M-33.8462 107.854C33.8461 174.975 11.282 389.762 -33.8462 483.731" stroke-width="1.2" stroke="#19A7A0" stroke-opacity="0.22"/>
            <path opacity="0.3" d="M-33.8462 134.703C22.5641 195.111 -3.60012e-05 409.898 -33.8462 503.867" stroke-width="1.2" stroke="#19A7A0" stroke-opacity="0.22"/>
        </svg>
    </div>

    <!-- 3. shape garis Sisi Kanan Tengah -->
    <div class="fixed right-0 top-24 pointer-events-none z-0">
        <svg width="52" height="498" viewBox="0 0 52 498" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M85.426 0.503174C-38.6766 81.0482 -16.1125 268.987 85.426 376.38" stroke-width="1.2" stroke="#1D4E89" stroke-opacity="0.18"/>
            <path d="M85.426 27.3516C-27.3945 107.897 -4.83038 289.123 85.426 396.516" stroke-width="1.2" stroke="#19A7A0" stroke-opacity="0.25"/>
            <path d="M85.4259 54.2C-16.1126 134.745 6.45153 309.259 85.4259 416.653" stroke-width="1.2" stroke="#1D4E89" stroke-opacity="0.18"/>
            <path d="M85.426 81.0481C-4.83046 161.593 17.7336 329.395 85.426 436.789" stroke-width="1.2" stroke="#19A7A0" stroke-opacity="0.2"/>
            <path opacity="0.6" d="M85.4261 107.896C6.45177 188.442 29.0159 349.532 85.4261 456.925" stroke-width="1.2" stroke="#1D4E89" stroke-opacity="0.18"/>
            <path opacity="0.4" d="M85.4258 134.745C17.7335 215.29 40.2976 369.668 85.4258 477.061" stroke-width="1.2" stroke="#1D4E89" stroke-opacity="0.18"/>
            <path opacity="0.2" d="M85.4259 161.593C29.0157 242.138 51.5798 389.804 85.4259 497.198" stroke-width="1.2" stroke="#1D4E89" stroke-opacity="0.18"/>
        </svg>
    </div>

    <!-- 4. shape garis Kanan Bawah -->
    <div class="fixed right-0 bottom-0 pointer-events-none z-0">
        <svg width="44" height="230" viewBox="0 0 44 230" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M76.8874 0.362183C-35.9331 107.756 -13.369 282.27 76.8874 362.815" stroke="#1D4E89" stroke-opacity="0.1"/>
            <path d="M76.8873 27.2104C-24.6511 127.892 -2.08704 302.406 76.8873 382.951" stroke="#19A7A0" stroke-opacity="0.15"/>
        </svg>
    </div>


    <!-- form pengecekan -->
    <form action="{{ route('petugas.simpan', $formAlat->id) }}" method="POST" class="relative z-10 w-full max-w-md mx-auto px-5 pt-6 pb-8 flex-1 flex flex-col justify-between">
        @csrf
        <input type="hidden" name="catatan" id="hiddenCatatan" value="{{ old('catatan') }}">

        <div class="space-y-4">
            <!-- gambar alat        -->
            <div class="bg-white/95 backdrop-blur-xs rounded-2xl border border-[#19A7A0]/30 shadow-xs overflow-hidden">
                <div class="bg-[#12345A] px-4 py-3 flex items-center justify-between">
                    <h1 class="text-white text-xs font-bold tracking-wide">
                        {{ $template->nama }}
                    </h1>
                    <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-white/15 text-[#5eead4]">
                        {{ $alat->id }}
                    </span>
                </div>

                <!-- Isi Diagram -->
                <div class="p-3.5">
                    <!-- Kotak Gambar Panduan -->
                    <div class="bg-[#F3F6FA] border border-slate-200/80 rounded-xl p-3 flex items-center justify-center h-36 overflow-hidden">
                        @if($template->urlGambar())
                            <img src="{{ $template->urlGambar() }}" 
                                 alt="Diagram {{ $template->nama }}" 
                                 class="max-h-full max-w-full object-contain">
                        @else
                            <div class="text-center text-slate-400">
                                <i class="fa-solid fa-diagram-project text-2xl mb-1 block text-[#1D4E89]/60"></i>
                                <span class="text-[11px] font-medium">{{ $template->nama }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Baris Pill Info -->
                    <div class="flex items-center justify-between gap-2 mt-3">
                        <div class="flex items-center gap-1.5 overflow-x-auto">
                            <span class="px-3 py-1.5 rounded-lg bg-[#12345A] text-white text-[11px] font-semibold whitespace-nowrap">
                                {{ $alat->kategoriAlat->nama ?? 'Unit' }}
                            </span>
                            @if($alat->lokasi)
                                <span class="px-3 py-1.5 rounded-lg bg-[#EEF2F7] text-[#1D4E89] text-[11px] font-semibold whitespace-nowrap">
                                    {{ $alat->lokasi }}
                                </span>
                            @endif
                        </div>

                    </div>
                </div>
            </div>

            @if ($errors->any())
                <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-600 text-xs text-center font-medium">
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- isi Task -->
            <div class="space-y-3.5">
                @forelse($daftarTugas as $index => $tugas)
                    <div class="bg-[#F8FAFC]/90 backdrop-blur-xs rounded-2xl border border-slate-200/80 p-4 shadow-2xs">
                        <p class="text-xs font-bold text-[#1F2937] mb-3.5 leading-relaxed">
                            {{ $index + 1 }}. {{ $tugas->nama }}
                        </p>

                        <div class="grid grid-cols-2 gap-3 px-2">
                            <!-- Opsi 1: Terpenuhi (value: bagus) -->
                            <label class="cursor-pointer">
                                <input type="radio" name="jawaban[{{ $tugas->id }}]" value="bagus" 
                                       {{ old('jawaban.' . $tugas->id) === 'terpenuhi' ? 'checked' : '' }}
                                       class="peer sr-only" required>
                                <div class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl bg-[#EEF2F7] border border-transparent text-slate-600 peer-checked:bg-[#19A7A0]/15 peer-checked:border-[#19A7A0] peer-checked:text-[#12345A] transition">
                                    <span class="w-4 h-4 rounded-full border border-slate-400 flex items-center justify-center shrink-0 peer-checked:border-[#19A7A0]">
                                        <span class="w-2 h-2 rounded-full bg-[#19A7A0] opacity-0 transition-opacity"></span>
                                    </span>
                                    <span class="text-xs font-medium">Terpenuhi</span>
                                </div>
                            </label>

                            <!-- Opsi 2: Tidak (value: kurang_bagus) -->
                            <label class="cursor-pointer">
                                <input type="radio" name="jawaban[{{ $tugas->id }}]" value="kurang_bagus" 
                                       {{ old('jawaban.' . $tugas->id) === 'terpenuhi' ? 'checked' : '' }}
                                       class="peer sr-only">
                                <div class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl bg-[#EEF2F7] border border-transparent text-slate-600 peer-checked:bg-rose-50 peer-checked:border-rose-400 peer-checked:text-rose-700 transition">
                                    <span class="w-4 h-4 rounded-full border border-slate-400 flex items-center justify-center shrink-0">
                                        <span class="w-2 h-2 rounded-full bg-rose-500 opacity-0 transition-opacity"></span>
                                    </span>
                                    <span class="text-xs font-medium">Tidak</span>
                                </div>
                            </label>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-2xl border border-slate-200 p-6 text-center text-xs text-slate-400">
                        Belum ada daftar pertanyaan pada template form ini.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- tombol simpan & kembali -->
        <div class="grid grid-cols-2 gap-3.5 pt-8 mt-auto">
            <a href="{{ $jumlahBagian > 1 ? route('petugas.cek', $alat->id) : route('petugas.scan') }}" 
               class="py-3.5 rounded-full bg-gradient-to-r from-[#19A7A0] to-[#1D4E89] hover:opacity-95 text-white text-xs font-bold flex items-center justify-center gap-1.5 shadow-md transition">
                <i class="fa-solid fa-chevron-left text-[11px]"></i>
                <span>Kembali</span>
            </a>

            <button type="submit" 
                class="py-3.5 rounded-full bg-[#164278] hover:bg-[#12345A] text-white text-xs font-bold flex items-center justify-center shadow-md transition">
                Simpan Checklist
            </button>
        </div>
    </form>

    <!-- MODAL POP-UP CATATAN TEMUAN                               -->
    <div id="modalCatatan" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 backdrop-blur-xs p-5">
        <div class="w-full max-w-sm bg-white rounded-3xl p-5 shadow-2xl border border-slate-200 text-[#1F2937]">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-bold text-[#12345A] flex items-center gap-2">
                    <i class="fa-regular fa-note-sticky text-[#19A7A0]"></i>
                    <span>Catatan Pemeriksaan</span>
                </h3>
                <button type="button" onclick="tutupModalCatatan()" class="text-slate-400 hover:text-slate-600 text-lg leading-none">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <textarea id="inputCatatanModal" rows="4" 
                placeholder="Tuliskan keterangan tambahan atau kendala alat di sini..."
                class="w-full rounded-2xl bg-[#F3F6FA] border border-slate-200 text-[#1F2937] text-xs p-3.5 focus:outline-none focus:border-[#19A7A0] placeholder-slate-400"></textarea>

            <div class="grid grid-cols-2 gap-2.5 mt-4">
                <button type="button" onclick="tutupModalCatatan()" 
                    class="py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold transition">
                    Batal
                </button>
                <button type="button" onclick="simpanCatatanModal()" 
                    class="py-2.5 rounded-full bg-[#19A7A0] hover:bg-[#148f89] text-white text-xs font-bold shadow-sm transition">
                    Simpan
                </button>
            </div>
        </div>
    </div>

    <!-- Script untuk Titik Radio & Modal Catatan -->
    <style>
        input[type="radio"]:checked + div span:first-child {
            border-color: currentColor;
        }
        input[type="radio"]:checked + div span:first-child span {
            opacity: 1;
        }
    </style>

    <script>
        function bukaModalCatatan() {
            document.getElementById('inputCatatanModal').value = document.getElementById('hiddenCatatan').value;
            const modal = document.getElementById('modalCatatan');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function tutupModalCatatan() {
            const modal = document.getElementById('modalCatatan');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function simpanCatatanModal() {
            const teks = document.getElementById('inputCatatanModal').value.trim();
            document.getElementById('hiddenCatatan').value = teks;
            document.getElementById('badgeCatatan').classList.toggle('hidden', teks === '');
            tutupModalCatatan();
        }
    </script>
</body>
</html>