<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Petugas - Digital Form Bandara</title>
    
    <!-- Load Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    
    <!-- font awesome -->
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#F3F6FA] min-h-screen flex flex-col justify-between relative overflow-hidden font-sans text-[#1F2937]">

    <!-- Header Banner Bandara -->
    <div class="relative h-64 w-full bg-[#12345A]">
        <img src="{{ asset('images/gambar-login-mobile.png') }}" 
            alt="Background Login Bandara" 
            class="w-full h-full object-cover opacity-65">
        <div class="absolute inset-0 bg-gradient-to-b from-[#12345A]/50 via-transparent to-[#12345A]/90"></div>
    </div>

    <!-- Card Form Login -->
    <div class="relative z-10 -mt-24 px-6 flex-1 flex flex-col">
        <div class="bg-white rounded-3xl shadow-lg p-7 border border-slate-100">
            <div class="text-center mb-6">
                <h1 class="font-['poppins'] text-3xl font-extrabold tracking-wide text-[#12345A] mt-1">
                    MASUK
                </h1>
            </div>

            @if ($errors->any())
                <div class="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-600 text-xs text-center">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-[#12345A] mb-1.5">Nama Pengguna</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-regular fa-user text-sm"></i>
                        </span>
                        <input type="text" name="id" value="{{ old('id') }}" required
                            placeholder="Masukan Nama Pengguna"
                            class="w-full pl-10 pr-4 py-3 text-xs rounded-xl bg-white border border-slate-200 text-[#1F2937] placeholder-slate-400 focus:outline-none focus:border-[#19A7A0] transition">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#12345A] mb-1.5">Kata Sandi</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-lock text-sm"></i>
                        </span>
                        <input type="password" name="password" required
                            placeholder="••••••••"
                            class="w-full pl-11 pr-4 py-3 text-sm rounded-xl bg-[#F3F6FA] border border-slate-200 focus:bg-white focus:outline-none focus:border-[#19A7A0] transition">
                    </div>
                </div>

                <button type="submit" 
                    class="w-full py-3.5 mt-2 rounded-xl bg-[#1D4E89] hover:bg-[#12345A] active:scale-[0.99] text-white font-bold text-sm tracking-wide shadow-md transition">
                    MASUK
                </button>
            </form>
        </div>
        <div class="absolute bottom-0 left-0 right-0 z-20 pointer-events-none leading-none">
        <svg class="w-full h-36 block" viewBox="0 0 390 150" preserveAspectRatio="none">
            <path d="M90 128 C 190 130, 260 65, 390 25 L 390 150 L 90 150 Z" fill="#19A7A0"/>
            <path d="M90 135 C 195 135, 270 75, 390 42 L 390 150 L 90 150 Z" fill="#38B2AC" opacity="0.5"/>
            <path d="M120 145 C 210 142, 290 85, 390 65 L 390 150 L 120 150 Z" fill="#1D4E89"/>
            <path d="M0 45 C 65 55, 95 95, 115 150 L 0 150 Z" fill="#1D4E89"/>
        </svg>
    </div>
    </div>
    

</body>
</html>