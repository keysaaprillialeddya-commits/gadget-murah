@extends('components.app')

@section('title', 'Checkout')

@section('content')
<style>
    /* Global Background & Font */
    body { background-color: #f0f3f7; font-family: 'Inter', -apple-system, sans-serif; }
    
    /* Card Styles */
    .checkout-card { border-radius: 12px; border: none; }
    .product-img-mini { width: 50px; height: 50px; object-fit: cover; border-radius: 8px; }
    
    /* Form Styles */
    .form-label { font-size: 13px; font-weight: 700; color: #6d7588; }
    .form-control { border-radius: 8px; border: 1px solid #e5e7eb; padding: 10px 12px; font-size: 14px; }
    
    /* Focus State: Biru Steel */
    .form-control:focus { 
        box-shadow: 0 0 0 0.2rem rgba(59, 97, 129, 0.1); 
        border-color: #3B6181; 
    }
    
    /* Text & Price Colors */
    .summary-item { font-size: 14px; color: #31353b; }
    .total-price { font-size: 18px; color: #3B6181; font-weight: 800; }
    .text-biru-steel { color: #3B6181 !important; }
    
    /* Payment Button: Biru Steel */
    .btn-pay { 
        background-color: #3B6181; 
        border: none; 
        font-weight: 700; 
        padding: 12px; 
        border-radius: 10px; 
        transition: 0.3s;
        color: white;
    }
    .btn-pay:hover { 
        background-color: #2d4a63; 
        color: white;
    }

    /* Link Colors */
    .checkout-link { color: #3B6181; text-decoration: none; font-weight: 600; }
    .checkout-link:hover { color: #2d4a63; text-decoration: underline; }

    /* Custom Scrollbar for Summary */
    .order-summary-list::-webkit-scrollbar { width: 6px; }
    .order-summary-list::-webkit-scrollbar-track { background: #f1f1f1; }
    .order-summary-list::-webkit-scrollbar-thumb { background: #ccc; border-radius: 10px; }
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h4 class="fw-bold mb-4 text-dark">Checkout</h4>

            @if(session('error'))
                <div class="alert alert-danger border-0 shadow-sm mb-4">{{ session('error') }}</div>
            @endif

            @if($cartItems->isEmpty())
                <div class="card checkout-card shadow-sm py-5 text-center">
                    <div class="card-body">
                        <i class="bi bi-cart-x display-1 text-muted opacity-50"></i>
                        <h5 class="mt-3">Keranjangmu kosong</h5>
                        <p class="text-muted">Yuk, cari barang menarik dulu!</p>
                        <a href="{{ route('catalog.index') }}" class="btn btn-biru-steel px-4 py-2 mt-2">Belanja Sekarang</a>
                    </div>
                </div>
            @else
                <div class="row g-4">
                    {{-- FORM DATA PENGIRIMAN --}}
                    <div class="col-lg-7">
                        <div class="card checkout-card shadow-sm border-0">
                            <div class="card-body p-4">
                                <h6 class="fw-bold mb-4 d-flex align-items-center">
                                    <i class="bi bi-geo-alt me-2 text-biru-steel"></i> Alamat Pengiriman
                                </h6>
                                <form action="{{ route('checkout.store') }}" method="POST">
                                    @csrf

                                    <div class="mb-3">
                                        <label for="shipping_name" class="form-label">Nama Penerima</label>
                                        <input type="text" name="shipping_name" id="shipping_name"
                                            class="form-control @error('shipping_name') is-invalid @enderror"
                                            value="{{ old('shipping_name', auth()->user()->name) }}" placeholder="Contoh: Budi Santoso" required>
                                        @error('shipping_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="shipping_phone" class="form-label">Nomor Telepon</label>
                                            <input type="text" name="shipping_phone" id="shipping_phone"
                                                class="form-control @error('shipping_phone') is-invalid @enderror"
                                                value="{{ old('shipping_phone', auth()->user()->phone) }}" placeholder="Contoh: 0812xxx" required>
                                            @error('shipping_phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="postal_code" class="form-label">Kode Pos</label>
                                            <input type="text" name="postal_code" id="postal_code" class="form-control" value="{{ old('postal_code') }}" placeholder="12345">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="shipping_address" class="form-label">Alamat Lengkap</label>
                                        <textarea name="shipping_address" id="shipping_address" rows="3"
                                            class="form-control @error('shipping_address') is-invalid @enderror" 
                                            placeholder="Nama jalan, nomor rumah, RT/RW, Kecamatan" required>{{ old('shipping_address', auth()->user()->address) }}</textarea>
                                        @error('shipping_address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label for="notes" class="form-label">Catatan untuk Penjual (Opsional)</label>
                                        <textarea name="notes" id="notes" rows="2" class="form-control" placeholder="Warna cadangan, posisi rumah, dll">{{ old('notes') }}</textarea>
                                    </div>

                                    <div class="p-3 bg-light rounded-3 mb-4 border-start border-4 border-biru-steel">
                                        <small class="text-muted d-block mb-1">Metode Pembayaran</small>
                                        <span class="fw-bold text-biru-steel"><i class="bi bi-wallet2 me-2"></i> Saldo / Transfer Bank</span>
                                    </div>

                                    <button type="submit" class="btn btn-pay w-100 shadow-sm d-flex justify-content-center align-items-center">
                                        <span>Bayar Sekarang</span>
                                        <i class="bi bi-arrow-right-short fs-4 ms-1"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- RINGKASAN PESANAN --}}
                    <div class="col-lg-5">
                        <div class="card checkout-card shadow-sm sticky-top border-0" style="top: 20px;">
                            <div class="card-body p-4">
                                <h6 class="fw-bold mb-4 d-flex align-items-center">
                                    <i class="bi bi-bag-check me-2 text-biru-steel"></i> Ringkasan Pesanan
                                </h6>
                                
                                <div class="order-summary-list mb-4" style="max-height: 300px; overflow-y: auto;">
                                    @foreach($cartItems as $item)
                                        <div class="d-flex align-items-center mb-3">
                                            <img src="{{ $item->product?->image_url }}" class="product-img-mini me-3 border">
                                            <div class="flex-grow-1">
                                                <div class="summary-item fw-bold text-truncate" style="max-width: 180px;">
                                                    {{ $item->product?->name ?? 'Produk dihapus' }}
                                                </div>
                                                <small class="text-muted">{{ $item->quantity }} x Rp {{ number_format($item->product?->price ?? 0, 0, ',', '.') }}</small>
                                            </div>
                                            <div class="summary-item fw-bold text-dark">
                                                Rp {{ number_format(($item->product?->price ?? 0) * $item->quantity, 0, ',', '.') }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <hr class="my-4" style="border-style: dashed;">

                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">Total Harga ({{ $cartItems->sum('quantity') }} barang)</span>
                                    <span class="small fw-bold">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">Total Ongkos Kirim</span>
                                    <span class="small fw-bold">Rp {{ number_format($shippingCost, 0, ',', '.') }}</span>
                                </div>
                                <div class="d-flex justify-content-between mt-3 pt-3 border-top">
                                    <span class="fw-bold fs-5 text-dark">Total Tagihan</span>
                                    <span class="total-price">Rp {{ number_format($subtotal + $shippingCost, 0, ',', '.') }}</span>
                                </div>

                                <div class="mt-4 p-3 bg-light rounded-3 text-center border">
                                    <small class="text-muted" style="font-size: 11px; line-height: 1.5; display: block;">
                                        Dengan membayar, Anda menyetujui <a href="#" class="text-biru-steel text-decoration-none fw-bold">Syarat & Ketentuan</a> yang berlaku di toko kami.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection