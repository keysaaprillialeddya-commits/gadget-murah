@extends('components.app')

@section('title', 'Checkout')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h2 class="fw-bold text-center mb-5 text-primary">
                <i class="bi bi-cart-check me-2"></i> Checkout Pesanan
            </h2>
            @if($cart->items->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-cart-x display-1 text-muted mb-4"></i>
                    <h4>Keranjang belanja kosong</h4>
                    <p class="text-muted">Yuk, tambahkan produk favoritmu dulu!</p>
                    <a href="{{ route('catalog.index') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-shop me-2"></i> Mulai Belanja
                    </a>
                </div>
            @else
                @php
                    $subtotal = $cart->items->sum(fn($item) => ($item->product?->price ?? 0) * $item->quantity);
                    $shippingCost = 15000; // Ganti dengan logika ongkir dinamis nanti
                    $total = $subtotal + $shippingCost;
                @endphp

                <div class="row g-5">
                    <!-- Form Pengiriman -->
                    <div class="col-lg-7">
                        <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                            <div class="card-header bg-primary text-white py-4">
                                <h5 class="mb-0 fw-bold">
                                    <i class="bi bi-truck me-2"></i> Data Pengiriman
                                </h5>
                            </div>
                            <div class="card-body p-4 p-md-5">
                                <form action="{{ route('checkout.store') }}" method="POST">
                                    @csrf

                                    <div class="row g-4">
                                        <div class="col-12">
                                            <label for="name" class="form-label fw-semibold">Nama Penerima <span class="text-danger">*</span></label>
                                            <input type="text" name="name" id="name" class="form-control form-control-lg rounded-3"
                                                   value="{{ old('name', auth()->user()->name) }}" required>
                                            @error('name')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="phone" class="form-label fw-semibold">No. HP / WhatsApp <span class="text-danger">*</span></label>
                                            <input type="text" name="phone" id="phone" class="form-control form-control-lg rounded-3"
                                                   value="{{ old('phone', auth()->user()->phone) }}" required>
                                            @error('phone')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="email" class="form-label fw-semibold">Email (opsional)</label>
                                            <input type="email" name="email" id="email" class="form-control form-control-lg rounded-3"
                                                   value="{{ old('email', auth()->user()->email) }}">
                                        </div>

                                        <div class="col-12">
                                            <label for="address" class="form-label fw-semibold">Alamat Lengkap <span class="text-danger">*</span></label>
                                            <textarea name="address" id="address" rows="4" class="form-control form-control-lg rounded-3" required>{{ old('address', auth()->user()->address) }}</textarea>
                                            @error('address')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="col-12">
                                            <label for="notes" class="form-label fw-semibold">Catatan untuk Kurir (opsional)</label>
                                            <textarea name="notes" id="notes" rows="3" class="form-control rounded-3">{{ old('notes') }}</textarea>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-success btn-lg w-100 mt-4 rounded-3 shadow-sm py-3 fw-bold">
                                        <i class="bi bi-credit-card-2-back me-2"></i>
                                        Buat Pesanan & Lanjut Pembayaran
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Ringkasan Pesanan -->
                    <div class="col-lg-5">
                        <div class="card border-0 shadow-lg rounded-4 sticky-top" style="top: 100px;">
                            <div class="card-header bg-dark text-white py-4">
                                <h5 class="mb-0 fw-bold">
                                    <i class="bi bi-bag-check me-2"></i> Ringkasan Pesanan
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="list-group list-group-flush mb-4" style="max-height: 400px; overflow-y: auto;">
                                    @foreach($cart->items as $item)
                                        <div class="list-group-item border-0 py-3">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1 me-3">
                                                    <h6 class="fw-semibold mb-1">{{ $item->product?->name ?? 'Produk dihapus' }}</h6>
                                                    <small class="text-muted">{{ $item->quantity }} × Rp {{ number_format($item->product?->price ?? 0) }}</small>
                                                </div>
                                                <span class="fw-bold text-primary">
                                                    Rp {{ number_format(($item->product?->price ?? 0) * $item->quantity) }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="border-top pt-4">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="fw-semibold">Subtotal</span>
                                        <span>Rp {{ number_format($subtotal) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-3 text-muted">
                                        <span>Ongkos Kirim</span>
                                        <span>Rp {{ number_format($shippingCost) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center bg-light rounded-3 p-3">
                                        <h5 class="fw-bold mb-0">Total Bayar</h5>
                                        <h4 class="fw-bold text-success mb-0">
                                            Rp {{ number_format($total) }}
                                        </h4>
                                    </div>
                                </div>

                                <div class="mt-4 p-3 bg-light rounded-3">
                                    <small class="text-muted">
                                        <i class="bi bi-shield-lock me-1"></i>
                                        Pembayaran aman & terenkripsi. Pesanan akan diproses setelah pembayaran dikonfirmasi.
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