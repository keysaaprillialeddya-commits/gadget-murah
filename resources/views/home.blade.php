{{-- ================================================
     FILE: resources/views/home.blade.php
     FITUR: Full Page (Slider + Category + Products)
     ================================================ --}}

@extends('layouts.app')

@section('title', 'Beranda - Toko Online')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

<style>
    :root {
        --premium-black: #1d1d1f;
        --soft-gray: #f5f5f7;
    }

    /* Mencegah tampilan pecah sebelum JavaScript siap */
    .hero-section {
        padding: 20px 0 40px;
        opacity: 0;
        transition: opacity 0.5s ease;
    }
    .hero-section.ready { opacity: 1; }

    .heroSwiper {
        width: 100%;
        height: 500px; 
        border-radius: 30px;
        background-color: var(--soft-gray);
        overflow: hidden;
        position: relative;
    }

    .swiper-slide { display: flex !important; align-items: center; width: 100%; }
    .hero-content { padding: 60px; }
    .hero-image-container img { max-height: 380px; width: auto; object-fit: contain; }

    /* --- NAVIGATION & PAGINATION --- */
    .swiper-button-next, .swiper-button-prev {
        width: 50px;
        height: 50px;
        background: rgba(255, 255, 255, 0.8);
        border-radius: 50%;
        color: var(--premium-black);
    }
    .swiper-button-next:after, .swiper-button-prev:after { font-size: 18px !important; font-weight: bold; }

    .swiper-pagination-bullet {
        width: 30px !important;
        height: 5px !important;
        border-radius: 10px !important;
        background: var(--premium-black) !important;
        opacity: 0.15 !important;
        transition: all 0.4s ease !important;
    }
    .swiper-pagination-bullet-active { width: 60px !important; opacity: 1 !important; }

    /* --- CATEGORY STYLE --- */
    .category-link { text-decoration: none; color: var(--premium-black); transition: 0.3s; }
    .category-link:hover { transform: translateY(-5px); }
    .cat-circle {
        width: 100px; height: 100px;
        background: var(--soft-gray);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 10px;
        overflow: hidden;
    }

    /* --- PRODUCT STYLE --- */
    .section-title { font-weight: 700; font-size: 1.8rem; margin-bottom: 30px; }

    @media (max-width: 768px) {
        .heroSwiper { height: auto; }
        .hero-content { padding: 40px 20px; text-align: center; }
        .hero-image-container { padding-bottom: 40px; }
        .swiper-button-next, .swiper-button-prev { display: none; }
    }
</style>
@endpush

@section('content')

    {{-- 1. HERO SLIDER SECTION --}}
    <section class="hero-section" id="hero-wrapper">
        <div class="container">
            <div class="swiper heroSwiper">
                <div class="swiper-wrapper">
                    
                    <div class="swiper-slide">
                        <div class="row w-100 align-items-center g-0">
                            <div class="col-md-6 order-2 order-md-1">
                                <div class="hero-content">
                                    <h5 class="fw-bold text-primary mb-2">New Arrival</h5>
                                    <h1 class="display-4 fw-bold mb-3">AirPods Pro</h1>
                                    <p class="lead text-muted mb-4">Pengalaman audio yang benar-benar baru. Kini dengan fitur Noise Cancellation yang lebih cerdas.</p>
                                    <a href="{{ route('catalog.index') }}" class="btn btn-dark rounded-pill px-5 py-2 fw-bold">Beli Sekarang</a>
                                </div>
                            </div>
                            <div class="col-md-6 order-1 order-md-2 text-center">
                                <div class="hero-image-container">
                                    <img src="{{ asset('images/airpods2.jfif') }}" alt="Airpods Pro">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="swiper-slide ">
                        <div class="row w-100 align-items-center g-0">
                            <div class="col-md-6 order-2 order-md-1">
                                <div class="hero-content">
                                    <h5 class="fw-bold text-danger mb-2">Best Performance</h5>
                                    <h1 class="display-4 fw-bold mb-3">iPad Pro M5</h1>
                                    <p class="lead text-muted mb-4">Tablet paling bertenaga yang pernah ada. Siap mendukung kreativitasmu tanpa batas.</p>
                                    <a href="{{ route('catalog.index') }}" class="btn btn-dark rounded-pill px-5 py-2 fw-bold">Cek Detail</a>
                                </div>
                            </div>
                            <div class="col-md-6 order-1 order-md-2 text-center">
                                <div class="hero-image-container">
                                    <img src="images/icon.jfif" alt="iPad Pro">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </section>

    {{-- 2. KATEGORI SECTION --}}
    <section class="py-5">
        <div class="container text-center">
            <h3 class="section-title">Cari Berdasarkan Kategori</h3>
            <div class="row g-4 justify-content-center">
                @foreach($categories as $category)
                <div class="col-6 col-md-3 col-lg-2">
                    <a href="{{ route('catalog.index', ['category' => $category->slug]) }}" class="category-link">
                        <div class="cat-circle">
                            <img src="{{ $category->image_url }}" alt="{{ $category->name }}" width="70">
                        </div>
                        <h6 class="fw-bold mb-0">{{ $category->name }}</h6>
                        <small class="text-muted">{{ $category->products_count }} Produk</small>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- 3. PRODUK SECTION (KATALOG) --}}
    <section class="py-5 bg-light" style="border-radius: 50px 50px 0 0;">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <h3 class="fw-bold mb-0">Produk Unggulan</h3>
                    <p class="text-muted mb-0">Koleksi terbaik yang paling banyak dicari.</p>
                </div>
                <a href="{{ route('catalog.index') }}" class="btn btn-outline-dark rounded-pill px-4">Lihat Semua →</a>
            </div>

            <div class="row g-4">
                @forelse($featuredProducts as $product)
                    <div class="col-6 col-md-4 col-lg-3">
                        @include('partials.product-card', ['product' => $product])
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <p class="text-muted">Belum ada produk yang tersedia.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi Swiper
        const swiper = new Swiper('.heroSwiper', {
            loop: true,
            grabCursor: true,
            speed: 1000,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
        });

        // Tampilkan konten setelah inisialisasi agar tidak bug (stacking)
        document.getElementById('hero-wrapper').classList.add('ready');
    });
</script>
@endpush