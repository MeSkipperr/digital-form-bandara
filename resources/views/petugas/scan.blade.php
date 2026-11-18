<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pindai QR - Digital Form Bandara</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <style>
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

    <!-- profile -->
    <div class="px-6 pt-7 pb-6">
        <div class="bg-white rounded-2xl px-4 py-3.5 shadow-[0_4px_20px_rgba(0,0,0,0.06)] flex items-center justify-between max-w-md mx-auto">
            <div class="flex items-center gap-3.5">
                <div class="relative w-12 h-12 rounded-full p-[2.5px] bg-gradient-to-tr from-[#1D4E89] to-[#19A7A0] shrink-0">
                    <div class="w-full h-full rounded-full bg-white flex items-center justify-center">
                        <i class="fa-solid fa-user text-[#1D4E89] text-xl"></i>
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

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" title="Keluar" class="p-2 text-slate-400 hover:text-rose-500 transition">
                    <i class="fa-solid fa-right-from-bracket text-lg"></i>
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

    <!-- kamera -->
    <div class="flex-1 bg-[#1D4E89] rounded-t-[32px] px-6 pt-10 pb-8 flex flex-col items-center justify-center shadow-inner">
        
        <!-- Kotak Kamera  -->
        <div class="relative w-64 h-64 bg-[#12345A] rounded-[32px] p-1.5 border-2 border-[#19A7A0] shadow-xl overflow-hidden flex items-center justify-center">
            <div id="reader"></div>

            <!-- Sudut Bidik -->
            <div class="pointer-events-none absolute inset-5 border-2 border-white/40 rounded-2xl"></div>
            
            <!-- Pesan fallback kamera -->
            <p id="statusKamera" class="absolute text-xs text-blue-200 text-center px-4">
                <i class="fa-solid fa-camera mb-1.5 text-lg block"></i>
                Menyalakan kamera...
            </p>
        </div>

        <p class="text-xs text-blue-200/80 mt-1 text-center">
            Arahkan kamera ke kode QR pada alat
        </p>

    </div>

    <script>
        const html5QrCode = new Html5Qrcode("reader");
        const statusKamera = document.getElementById('statusKamera');
        let sedangMemproses = false;

        // Ekstrak ID alat murni (aman jika QR berisi teks ID langsung maupun URL)
        function ekstrakIdAlat(rawText) {
            let teks = rawText.trim();
            if (teks.includes('/')) {
                teks = teks.replace(/\/+$/, '').split('/').pop();
            }
            return teks;
        }

        function onScanSuccess(decodedText) {
            if (sedangMemproses) return;
            sedangMemproses = true;

            const idAlat = ekstrakIdAlat(decodedText);
            statusKamera.style.display = 'block';
            statusKamera.innerHTML = '<i class="fa-solid fa-spinner fa-spin mb-1 text-lg block text-[#5eead4]"></i>Membuka ' + idAlat + '...';

            html5QrCode.stop().then(() => {
                window.location.href = "/petugas/cek/" + encodeURIComponent(idAlat);
            }).catch(() => {
                window.location.href = "/petugas/cek/" + encodeURIComponent(idAlat);
            });
        }

        window.addEventListener('DOMContentLoaded', () => {
            html5QrCode.start(
                { facingMode: "environment" },
                {
                    fps: 15,
                    qrbox: { width: 190, height: 190 }
                },
                onScanSuccess,
                () => {}
            ).then(() => {
                statusKamera.style.display = 'none';
            }).catch(() => {
                statusKamera.innerHTML = '<i class="fa-solid fa-video-slash mb-1.5 text-lg block text-rose-300"></i>Kamera tidak aktif. Pastikan akses via HTTPS / localhost atau ketik ID manual di bawah.';
            });
        });

        function manualCek(e) {
            e.preventDefault();
            const kode = ekstrakIdAlat(document.getElementById('inputKodeManual').value);
            if (kode) {
                window.location.href = "/petugas/cek/" + encodeURIComponent(kode);
            }
        }
    </script>
</body>
</html>