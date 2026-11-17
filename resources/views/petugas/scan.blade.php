<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pindai QR - Digital Form Bandara</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <style>
        /* Paksa video kamera mengisi penuh kotak membulat tanpa gepeng */
        #reader {
            width: 100% !important;
            height: 100% !important;
            border: none !important;
        }
        #reader video {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
            border-radius: 28px;
        }
    </style>
</head>
<body class="bg-[#F3F6FA] min-h-screen flex flex-col font-['Inter'] text-[#1F2937]">

    <!-- ========================================================= -->
    <!-- BAGIAN ATAS: KARTU PROFIL PETUGAS                        -->
    <!-- ========================================================= -->
    <div class="px-6 pt-7 pb-6">
        <div class="bg-white rounded-2xl px-4 py-3.5 shadow-[0_4px_20px_rgba(0,0,0,0.06)] flex items-center justify-between max-w-md mx-auto">
            <div class="flex items-center gap-3.5">
                <!-- Ikon Profil Gradien Toska-Biru + Titik Hijau Aktif -->
                <div class="relative w-12 h-12 rounded-full p-[2.5px] bg-gradient-to-tr from-[#1D4E89] to-[#19A7A0] shrink-0">
                    <div class="w-full h-full rounded-full bg-white flex items-center justify-center overflow-hidden">
                        <svg class="w-8 h-8 text-[#1D4E89] mt-1.5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 12c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm0 2c-3.33 0-10 1.67-10 5v3h20v-3c0-3.33-6.67-5-10-5z"/>
                        </svg>
                    </div>
                    <span class="absolute bottom-0 right-0 w-3.5 h-3.5 rounded-full bg-[#22C55E] border-2 border-white"></span>
                </div>

                <div>
                    <p class="text-xs font-bold text-slate-400 leading-tight">Selamat Datang</p>
                    <h1 class="text-sm font-medium text-slate-500 mt-0.5">
                        {{ auth()->user()->nama }}
                    </h1>
                </div>
            </div>

            <!-- Tombol Logout -->
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" title="Keluar" class="p-2 text-slate-400 hover:text-rose-500 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </form>
        </div>

        @if(session('success'))
            <div class="mt-3 max-w-md mx-auto p-3 rounded-xl bg-[#19A7A0]/15 border border-[#19A7A0] text-[#12345A] text-xs text-center font-medium">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mt-3 max-w-md mx-auto p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-600 text-xs text-center font-medium">
                {{ session('error') }}
            </div>
        @endif
    </div>

    <!-- ========================================================= -->
    <!-- BAGIAN BAWAH: PANEL BIRU + LANGSUNG KAMERA LIVE           -->
    <!-- ========================================================= -->
    <div class="flex-1 bg-[#1D4E89] rounded-t-[32px] px-6 pt-10 pb-8 flex flex-col items-center justify-center shadow-inner">
        
        <!-- Kotak Kamera Otomatis Nyala (Ukuran sama persis dengan kotak QR di Figma) -->
        <div class="relative w-64 h-64 bg-[#12345A] rounded-[32px] p-1.5 border-2 border-[#19A7A0] shadow-xl overflow-hidden flex items-center justify-center">
            <!-- Feed Kamera -->
            <div id="reader"></div>

            <!-- Sudut Bidik (Viewfinder Corners) biar makin modern -->
            <div class="pointer-events-none absolute inset-5 border-2 border-white/40 rounded-2xl"></div>
            
            <!-- Pesan fallback jika izin kamera ditolak / belum aktif -->
            <p id="statusKamera" class="absolute text-xs text-blue-200 text-center px-4">
                Menyalakan kamera...
            </p>
        </div>


    </div>

    <script>
        const html5QrCode = new Html5Qrcode("reader");
        const statusKamera = document.getElementById('statusKamera');

        function onScanSuccess(decodedText) {
            // Hentikan kamera begitu QR terbaca lalu redirect
            html5QrCode.stop().then(() => {
                window.location.href = "/petugas/cek/" + encodeURIComponent(decodedText.trim());
            }).catch(() => {
                window.location.href = "/petugas/cek/" + encodeURIComponent(decodedText.trim());
            });
        }

        // Langsung nyalakan kamera otomatis saat halaman dibuka
        window.addEventListener('DOMContentLoaded', () => {
            html5QrCode.start(
                { facingMode: "environment" }, // Prioritaskan kamera belakang HP
                {
                    fps: 10,
                    qrbox: { width: 500, height: 500 }
                },
                onScanSuccess,
                (errorMessage) => {
                    // Abaikan error frame kosong saat kamera mencari QR
                }
            ).then(() => {
                statusKamera.style.display = 'none';
            }).catch((err) => {
                statusKamera.innerText = "Kamera tidak terdeteksi atau izin ditolak. Gunakan input manual di bawah.";
            });
        });

        function manualCek(e) {
            e.preventDefault();
            const kode = document.getElementById('inputKodeManual').value.trim();
            if (kode) {
                window.location.href = "/petugas/cek/" + encodeURIComponent(kode);
            }
        }
    </script>
</body>
</html>