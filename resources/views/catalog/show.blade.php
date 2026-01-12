{{-- ================================================
     FILE: resources/views/catalog/show.blade.php
     FUNGSI: Halaman detail produk
     ================================================ --}}

@extends('layouts.app')

@section('title', $product->name)

@section('content')
<style>
    /* Sinkronisasi Tema Biru Steel */
    .breadcrumb-item a {
        color: #3B6181;
        text-decoration: none;
    }
    .breadcrumb-item.active {
        color: #6d7588;
    }
    .text-biru-steel {
        color: #3B6181 !important;
    }
    .btn-biru-steel {
        background-color: #3B6181;
        border-color: #3B6181;
        color: white;
        transition: 0.3s;
    }
    .btn-biru-steel:hover {
        background-color: #2d4a63;
        border-color: #2d4a63;
        color: white;
    }
    .btn-outline-biru-steel {
        border-color: #3B6181;
        color: #3B6181;
    }
    .btn-outline-biru-steel:hover {
        background-color: #3B6181;
        color: white;
    }
    .cursor-pointer {
        cursor: pointer;
    }
    #main-image {
        transition: opacity 0.3s ease;
    }
</style>

<div class="container py-4">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('catalog.index') }}">Katalog</a></li>
            <li class="breadcrumb-item">
                <a href="{{ route('catalog.index', ['category' => $product->category->slug]) }}">
                    {{ $product->category->name }}
                </a>
            </li>
            <li class="breadcrumb-item active">{{ Str::limit($product->name, 30) }}</li>
        </ol>
    </nav>

    <div class="row">
        {{-- Product Images --}}
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 15px;">
                {{-- Main Image --}}
                <div class="position-relative">
                    <img src="{{ $product->image_url }}"
                         id="main-image"
                         class="card-img-top"
                         alt="{{ $product->name }}"
                         style="height: 450px; object-fit: contain; background: #ffffff; padding: 20px;">

                    @if($product->has_discount)
                        <span class="badge bg-danger position-absolute top-0 start-0 m-3 fs-6 px-3 py-2 shadow-sm">
                            -{{ $product->discount_percentage }}%
                        </span>
                    @endif
                </div>

                {{-- Thumbnail Gallery --}}
                @if($product->images->count() > 1)
                    <div class="card-body bg-light border-top">
                        <div class="d-flex gap-2 overflow-auto pb-2">
                            @foreach($product->images as $image)
                                <img src="{{ asset('storage/' . $image->image_path) }}"
                                     class="rounded border cursor-pointer thumbnail-img"
                                     style="width: 70px; height: 70px; object-fit: cover; transition: 0.2s;"
                                     onclick="changeImage(this.src)">
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Product Info --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body p-4">
                    {{-- Category --}}
                    <a href="{{ route('catalog.index', ['category' => $product->category->slug]) }}"
                       class="badge bg-light text-biru-steel text-decoration-none mb-2 px-3 py-2 border">
                        {{ $product->category->name }}
                    </a>

                    {{-- Title --}}
                    <h2 class="fw-bold mb-3" style="color: #31353b;">{{ $product->name }}</h2>

                    {{-- Price --}}
                    <div class="mb-4 p-3 bg-light rounded-3">
                        @if($product->has_discount)
                            <div class="text-muted text-decoration-line-through small">
                                {{ $product->formatted_original_price }}
                            </div>
                        @endif
                        <div class="h2 text-biru-steel fw-bold mb-0">
                            {{ $product->formatted_price }}
                        </div>
                    </div>

                    {{-- Stock Status --}}
                    <div class="mb-4">
                        @if($product->stock > 10)
                            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">
                                <i class="bi bi-check-circle-fill me-1"></i> Stok Tersedia
                            </span>
                        @elseif($product->stock > 0)
                            <span class="badge bg-warning bg-opacity-10 text-dark px-3 py-2 rounded-pill">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> Stok Terbatas: {{ $product->stock }}
                            </span>
                        @else
                            <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">
                                <i class="bi bi-x-circle-fill me-1"></i> Stok Habis
                            </span>
                        @endif
                    </div>

                    {{-- Add to Cart Form --}}
                    <form action="{{ route('cart.add') }}" method="POST" class="mb-4">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        <div class="row g-3 align-items-end">
                            <div class="col-auto">
                                <label class="form-label fw-bold small text-muted">Jumlah</label>
                                <div class="input-group shadow-sm" style="width: 140px;">
                                    <button type="button" class="btn btn-outline-secondary border-end-0"
                                            onclick="decrementQty()">-</button>
                                    <input type="number" name="quantity" id="quantity"
                                           value="1" min="1" max="{{ $product->stock }}"
                                           class="form-control text-center fw-bold border-start-0 border-end-0"
                                           readonly>
                                    <button type="button" class="btn btn-outline-secondary border-start-0"
                                            onclick="incrementQty()">+</button>
                                </div>
                            </div>
                            <div class="col">
                                <button type="submit" class="btn btn-biru-steel btn-lg w-100 fw-bold shadow-sm"
                                        @if($product->stock == 0) disabled @endif>
                                    <i class="bi bi-cart-plus me-2"></i> Tambah ke Keranjang
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- Wishlist --}}
                    @auth
    <button type="button"
            onclick="toggleWishlist({{ $product->id }})"
            class="btn btn-outline-danger w-100 mb-4 wishlist-btn-{{ $product->id }} border-2">
        <i class="bi {{ auth()->user()->hasInWishlist($product) ? 'bi-heart-fill' : 'bi-heart' }} me-2"></i>
        {{ auth()->user()->hasInWishlist($product) ? 'Hapus dari Wishlist' : 'Simpan ke Wishlist' }}
    </button>
@endauth

                    <hr class="my-4" style="border-style: dashed;">

                    {{-- Product Details --}}
                    <div class="mb-4">
                        <h6 class="fw-bold mb-2">Deskripsi Produk</h6>
                        <p class="text-muted lh-base" style="font-size: 15px;">
                            {!! nl2br(e($product->description)) !!}
                        </p>
                    </div>

                    <div class="row g-2 text-muted small">
                        <div class="col-6">
                            <div class="p-2 border rounded bg-light">
                                <i class="bi bi-box me-2 text-biru-steel"></i> Berat: {{ $product->weight }} gr
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded bg-light">
                                <i class="bi bi-tag me-2 text-biru-steel"></i> SKU: {{ $product->id + 1000 }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // 1. Fungsi Ganti Gambar Utama
    function changeImage(src) {
        const mainImg = document.getElementById('main-image');
        if(!mainImg) return;
        mainImg.style.opacity = '0';
        setTimeout(() => {
            mainImg.src = src;
            mainImg.style.opacity = '1';
        }, 200);
    }

    // 2. Fungsi Increment/Decrement Quantity
    function incrementQty() {
        const input = document.getElementById('quantity');
        const max = parseInt(input.max);
        if (parseInt(input.value) < max) {
            input.value = parseInt(input.value) + 1;
        }
    }

    function decrementQty() {
        const input = document.getElementById('quantity');
        if (parseInt(input.value) > 1) {
            input.value = parseInt(input.value) - 1;
        }
    }

    /**
     * 3. FUNGSI PERBAIKAN: toggleWishlist (AJAX)
     * Menangani klik tombol hati untuk tambah/hapus wishlist
     */
    function toggleWishlist(productId) {
        const btn = document.querySelector(`.wishlist-btn-${productId}`);
        const icon = btn.querySelector('i');
        
        // Proteksi klik ganda
        btn.disabled = true;

        // URL sesuai dengan Route: /wishlist/toggle/{product}
        fetch(`/wishlist/toggle/${productId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}', // Wajib untuk Laravel
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(async response => {
            const data = await response.json();
            
            // Cek jika user tidak terautentikasi (401)
            if (response.status === 401) {
                alert('Silakan login terlebih dahulu.');
                window.location.href = '{{ route("login") }}';
                return;
            }

            if (!response.ok) {
                throw new Error(data.message || 'Terjadi kesalahan pada server.');
            }
            
            return data;
        })
        .then(data => {
            if (data && data.success) {
                // Update tampilan icon dan teks tombol secara dinamis
                if (data.in_wishlist) {
                    icon.classList.replace('bi-heart', 'bi-heart-fill');
                    btn.innerHTML = `<i class="bi bi-heart-fill me-2"></i> Hapus dari Wishlist`;
                } else {
                    icon.classList.replace('bi-heart-fill', 'bi-heart');
                    btn.innerHTML = `<i class="bi bi-heart me-2"></i> Simpan ke Wishlist`;
                }
                
                // Update badge jumlah wishlist di navbar jika ada elemennya
                const navWishlistCount = document.getElementById('wishlist-count');
                if (navWishlistCount) {
                    navWishlistCount.innerText = data.count;
                }
            }
        })
        .catch(error => {
            console.error('Wishlist Error:', error);
            alert('Gagal memperbarui wishlist. Coba lagi.');
        })
        .finally(() => {
            // Aktifkan kembali tombol
            btn.disabled = false;
        });
    }
</script>
@endpush
@endsection