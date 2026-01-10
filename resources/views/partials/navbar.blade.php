{{-- ================================================
     FILE: resources/views/partials/navbar.blade.php
     FUNGSI: Navbar Style Modern + Biru Steel Theme
     ================================================ --}}

<nav class="navbar navbar-expand-lg bg-white sticky-top tokopedia-navbar py-2 border-bottom">
    <div class="container-fluid px-lg-5 px-3 align-items-center">

        {{-- BRAND --}}
        <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="{{ route('home') }}" style="color: #3B6181;">
            <img src="{{ asset('images/icon.jfif') }}" alt="Logo" width="36" height="36">
            <span class="fs-4 d-none d-sm-inline" style="letter-spacing: -1px;">Gadget Murah</span>
        </a>

        {{-- KATEGORI (FIXED: Sekarang bisa diklik) --}}
        <div class="dropdown ms-2 d-none d-lg-block">
            <button class="btn btn-light border d-flex align-items-center gap-2 category-btn shadow-none" 
                    type="button" 
                    id="dropdownCategory" 
                    data-bs-toggle="dropdown" 
                    aria-expanded="false" 
                    style="border-radius: 8px;">
                <i class="bi bi-grid-fill" style="color: #3B6181;"></i>
                <span class="fw-medium">Kategori</span>
            </button>
            <ul class="dropdown-menu border-0 shadow-lg mt-3 rounded-4 p-2" aria-labelledby="dropdownCategory" style="min-width: 220px;">
                <li><h6 class="dropdown-header text-dark fw-bold">Kategori Populer</h6></li>
                <li><a class="dropdown-item py-2 rounded-3" href="{{ route('catalog.index', ['category' => 'Earphone']) }}">Earphone</a></li>
                <li><a class="dropdown-item py-2 rounded-3" href="{{ route('catalog.index', ['category' => 'case-hp']) }}">Case Hp</a></li>
                <li><a class="dropdown-item py-2 rounded-3" href="{{ route('catalog.index', ['category' => 'charger-powerbank']) }}">Charger & Powerbank</a></li>
                <li><a class="dropdown-item py-2 rounded-3" href="{{ route('catalog.index', ['category' => 'flash-disk']) }}">Flash Disk</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item py-2 rounded-3 fw-medium text-center" href="{{ route('catalog.index') }}" style="color: #3B6181;">Lihat Semua Kategori</a></li>
            </ul>
        </div>

        {{-- NAVBAR CONTENT --}}
        <div class="collapse navbar-collapse" id="navbarMain">
            {{-- SEARCH BAR --}}
            <form class="mx-lg-4 my-3 my-lg-0 flex-grow-1" action="{{ route('catalog.index') }}" method="GET">
                <div class="input-group search-box" style="border-radius: 8px; overflow: hidden;">
                    <span class="input-group-text bg-white border-end-0 pe-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="q" class="form-control border-start-0 py-2 shadow-none" 
                           placeholder="Cari barang impianmu di sini..." value="{{ request('q') }}">
                </div>
            </form>

            {{-- RIGHT MENU --}}
            <ul class="navbar-nav align-items-center gap-3 ms-lg-auto">
                
                @auth
                    {{-- WISHLIST --}}
                    <li class="nav-item">
                        <a class="nav-link position-relative px-2" href="{{ route('wishlist.index') }}">
                            <i class="bi bi-heart fs-5 text-dark"></i>
                            @php $wishlistCount = auth()->user()->wishlists()->count(); @endphp
                            @if($wishlistCount > 0)
                                <span class="badge rounded-pill bg-danger position-absolute top-0 start-100 translate-middle" style="font-size: 10px; border: 2px solid white;">
                                    {{ $wishlistCount }}
                                </span>
                            @endif
                        </a>
                    </li>

                    {{-- KERANJANG --}}
                    <li class="nav-item">
                        <a class="nav-link position-relative px-2" href="{{ route('cart.index') }}">
                            <i class="bi bi-cart3 fs-5 text-dark"></i>
                            @php 
                                $cartCount = auth()->user()->cart?->items()->count() ?? 0; 
                            @endphp
                            @if($cartCount > 0)
                                <span class="badge rounded-pill bg-danger position-absolute top-0 start-100 translate-middle" style="font-size: 10px; border: 2px solid white;">
                                    {{ $cartCount }}
                                </span>
                            @endif
                        </a>
                    </li>

                    <div class="vr d-none d-lg-block mx-1" style="height: 24px; opacity: 0.1;"></div>

                    {{-- USER PROFILE DROPDOWN --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 py-0 shadow-none" data-bs-toggle="dropdown" role="button">
                            <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=3B6181&color=fff' }}" 
                                 class="rounded-circle border" width="32" height="32">
                            <span class="fw-bold text-dark d-none d-xl-inline" style="font-size: 14px;">{{ Str::words(auth()->user()->name, 1, '') }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg mt-3 rounded-4 p-2" style="min-width: 220px;">
                            <div class="px-3 py-2 border-bottom mb-2">
                                <p class="mb-0 fw-bold small text-dark">{{ auth()->user()->name }}</p>
                                <p class="mb-0 text-muted" style="font-size: 11px;">{{ auth()->user()->email }}</p>
                            </div>

                            @if(auth()->user()->role === 'admin' || auth()->user()->is_admin)
                            <li>
                                <a class="dropdown-item py-2 rounded-3 text-admin-link fw-bold mb-1" href="{{ url('/admin/dashboard') }}">
                                    <i class="bi bi-speedometer2 me-2"></i> Admin Panel
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            @endif

                            <li><a class="dropdown-item py-2 rounded-3" href="{{ route('profile.show' , auth()->id()) }}"><i class="bi bi-person me-2 text-muted"></i> Profil Saya</a></li>
                            <li><a class="dropdown-item py-2 rounded-3" href="{{ route('orders.index') }}"><i class="bi bi-bag-check me-2 text-muted"></i> Pesanan</a></li>
                            <li><a class="dropdown-item py-2 rounded-3" href="{{ route('wishlist.index') }}"><i class="bi bi-heart me-2 text-muted"></i> Wishlist</a></li>
                            
                            <li><hr class="dropdown-divider"></li>
                            
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item text-danger py-2 rounded-3 fw-medium w-100 text-start">
                                        <i class="bi bi-box-arrow-right me-2"></i> Keluar
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="btn btn-outline-primary fw-bold px-4 btn-sm" href="{{ route('login') }}" 
                           style="border-radius: 8px; border-color: #3B6181; color: #3B6181;">
                            Masuk
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary fw-bold px-4 btn-sm" href="{{ route('register') }}" 
                           style="border-radius: 8px; background-color: #3B6181; border: none;">
                            Daftar
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<style>
    /* Styling Global Dropdown Item */
    .dropdown-item {
        transition: all 0.2s ease;
        font-size: 14px;
        color: #31353b;
    }

    .dropdown-item:hover {
        background-color: #f0f3f7;
        color: #3B6181;
    }

    .text-admin-link {
        color: #3B6181 !important;
    }
    
    .dropdown-item.text-admin-link:hover {
        background-color: #e8eff5 !important;
    }

    .dropdown-item.text-danger:hover {
        background-color: #fff5f5;
        color: #dc3545 !important;
    }

    /* Menghilangkan panah dropdown default */
    .dropdown-toggle::after {
        display: none !important;
    }

    .dropdown-menu {
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
        animation: dropdownFadeIn 0.2s ease-out;
    }

    @keyframes dropdownFadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Search Box Focus */
    .search-box {
        border: 1px solid #dee2e6;
        transition: all 0.2s;
    }
    .search-box:focus-within {
        border-color: #3B6181;
        box-shadow: 0 0 0 0.2rem rgba(59, 97, 129, 0.1);
    }

    .category-btn:hover {
        background-color: #f8f9fa;
        border-color: #3B6181 !important;
    }
</style>