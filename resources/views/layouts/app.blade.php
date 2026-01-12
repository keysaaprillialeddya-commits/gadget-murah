<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- CSRF Token untuk AJAX --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Toko Online') - {{ config('app.name') }}</title>
    
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body>
    {{-- NAVBAR --}}
    @include('profile.partials.navbar')

    {{-- FLASH MESSAGES --}}
    <div class="container mt-3">
        @include('profile.partials.flash-messages')
    </div>

    {{-- MAIN CONTENT --}}
    <main class="min-vh-100">
        @yield('content')
    </main>

    {{-- FOOTER --}}
    @include('profile.partials.footer')

    {{-- SCRIPTS GLOBAL --}}
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>AOS.init({once: true});</script>

    <script>
    /**
     * FUNGSI UNGGULAN: Toggle Wishlist (Hanya satu fungsi global)
     */
    async function toggleWishlist(productId) {
        try {
            // 1. Ambil Token CSRF
            const token = document.querySelector('meta[name="csrf-token"]').content;

            // 2. Kirim Request ke Server (Gunakan URL yang sesuai dengan Route Anda)
            const response = await fetch(`/wishlist/toggle/${productId}`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": token,
                    "X-Requested-With": "XMLHttpRequest"
                }
            });

            // 3. Handle jika user belum login (Error 401)
            if (response.status === 401) {
                window.location.href = "{{ route('login') }}";
                return;
            }

            const data = await response.json();

            if (data.success || data.status === "success") {
                // 4. Update UI Icon Secara Otomatis
                updateWishlistUI(productId, data.in_wishlist || data.added);
                
                // 5. Update Angka Counter di Navbar (jika ada)
                const badge = document.getElementById("wishlist-count");
                if (badge && data.count !== undefined) {
                    badge.innerText = data.count;
                    badge.style.display = data.count > 0 ? "inline-block" : "none";
                }
            } else {
                alert(data.message || "Gagal memperbarui wishlist.");
            }
        } catch (error) {
            console.error("Error:", error);
            alert("Gagal memperbarui wishlist. Coba lagi.");
        }
    }

    /**
     * Helper untuk mengubah warna icon hati
     */
    function updateWishlistUI(productId, isAdded) {
        // Cari tombol berdasarkan class (mendukung banyak tombol di satu halaman)
        const buttons = document.querySelectorAll(`.wishlist-btn-${productId}`);
        const singleIcon = document.getElementById('wishlist-icon-' + productId);

        // Jika menggunakan sistem Class
        buttons.forEach((btn) => {
            const icon = btn.querySelector("i");
            if (isAdded) {
                icon.classList.replace("bi-heart", "bi-heart-fill");
                icon.classList.add("text-danger");
                if(btn.innerText.includes("Simpan")) btn.innerHTML = '<i class="bi bi-heart-fill me-2 text-danger"></i> Hapus dari Wishlist';
            } else {
                icon.classList.replace("bi-heart-fill", "bi-heart");
                icon.classList.remove("text-danger");
                if(btn.innerText.includes("Hapus")) btn.innerHTML = '<i class="bi bi-heart me-2"></i> Simpan ke Wishlist';
            }
        });

        // Jika menggunakan sistem ID (Fallback)
        if (singleIcon) {
            if (isAdded) {
                singleIcon.className = "bi bi-heart-fill text-danger";
            } else {
                singleIcon.className = "bi bi-heart text-dark";
            }
        }
    }
    </script>

    @stack('scripts')
</body>
</html>