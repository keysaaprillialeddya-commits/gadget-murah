@extends('components.app')

@section('title', 'Detail Pesanan #' . $order->order_number)

@section('content')
<style>
    :root {
        --biru-steel: #3B6181;
        --biru-steel-soft: #e6f0f7;
        --biru-steel-hover: #2d4a63;
    }

    body { background-color: #f8f9fa; }
    .order-card { border-radius: 15px; border: none; overflow: hidden; }
    
    /* Status Tracker */
    .status-tracker { display: flex; justify-content: space-between; position: relative; margin-bottom: 30px; }
    .status-step { text-align: center; position: relative; z-index: 2; flex: 1; }
    .status-icon { width: 45px; height: 45px; border-radius: 50%; background: #e9ecef; color: #adb5bd; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 8px; transition: 0.3s; border: 3px solid #fff; }
    
    .status-step.active .status-icon { 
        background: var(--biru-steel); 
        color: white; 
        box-shadow: 0 0 0 4px var(--biru-steel-soft); 
    }
    
    .status-text { font-size: 12px; font-weight: 700; color: #adb5bd; }
    .status-step.active .status-text { color: var(--biru-steel); }
    
    .line-tracker { position: absolute; top: 22px; left: 10%; right: 10%; height: 3px; background: #e9ecef; z-index: 1; }
    .line-fill { height: 100%; background: var(--biru-steel); transition: 0.8s ease; }
    
    /* Logic Progress Bar */
    @php
        $progress = 0;
        if($order->status == 'pending') $progress = 0;
        elseif($order->status == 'processing') $progress = 33;
        elseif($order->status == 'shipped') $progress = 66;
        elseif($order->status == 'delivered') $progress = 100;
    @endphp
    .line-fill { width: {{ $progress }}%; }

    .btn-biru-steel {
        background-color: var(--biru-steel);
        color: white;
        border: none;
        transition: 0.3s;
        border-radius: 50px;
    }
    .btn-biru-steel:hover {
        background-color: var(--biru-steel-hover);
        color: white;
        transform: translateY(-2px);
    }

    .text-biru-steel { color: var(--biru-steel) !important; }
    .bg-biru-soft { background-color: var(--biru-steel-soft) !important; }
    .product-img { width: 60px; height: 60px; object-fit: cover; border-radius: 10px; }
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">

            {{-- Breadcrumb --}}
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb px-3 py-2 bg-white rounded-pill shadow-sm" style="width: fit-content;">
                    <li class="breadcrumb-item"><a href="{{ route('orders.index') }}" class="text-decoration-none text-biru-steel fw-bold">Pesanan Saya</a></li>
                    <li class="breadcrumb-item active text-muted">Detail #{{ $order->order_number }}</li>
                </ol>
            </nav>

            <div class="card order-card shadow-sm mb-4">
                {{-- Header --}}
                <div class="card-header bg-white p-4 border-bottom">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                        <div>
                            <span class="text-muted small text-uppercase fw-bold">Informasi Pesanan</span>
                            <h4 class="fw-bold mb-0 text-dark">#{{ $order->order_number }}</h4>
                        </div>
                        <div class="text-md-end mt-3 mt-md-0">
                            <span class="text-muted small d-block">Waktu Transaksi</span>
                            <span class="fw-bold text-dark"><i class="bi bi-calendar3 me-1 text-biru-steel"></i> {{ $order->created_at->translatedFormat('d F Y, H:i') }} WIB</span>
                        </div>
                    </div>
                </div>

                {{-- Status Tracker --}}
                <div class="card-body p-4 p-md-5 border-bottom bg-white">
                    @if($order->status == 'cancelled')
                        <div class="alert alert-danger border-0 rounded-4 d-flex align-items-center mb-0">
                            <i class="bi bi-x-circle-fill fs-1 me-3"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Pesanan Dibatalkan</h6>
                                <p class="mb-0 small">Maaf, pesanan ini tidak dapat dilanjutkan karena telah dibatalkan.</p>
                            </div>
                        </div>
                    @else
                        <div class="status-tracker">
                            <div class="line-tracker"><div class="line-fill"></div></div>
                            
                            <div class="status-step active">
                                <div class="status-icon"><i class="bi bi-wallet2"></i></div>
                                <div class="status-text">Menunggu</div>
                            </div>
                            <div class="status-step {{ in_array($order->status, ['processing', 'shipped', 'delivered']) ? 'active' : '' }}">
                                <div class="status-icon"><i class="bi bi-box-seam"></i></div>
                                <div class="status-text">Diproses</div>
                            </div>
                            <div class="status-step {{ in_array($order->status, ['shipped', 'delivered']) ? 'active' : '' }}">
                                <div class="status-icon"><i class="bi bi-truck"></i></div>
                                <div class="status-text">Dikirim</div>
                            </div>
                            <div class="status-step {{ $order->status == 'delivered' ? 'active' : '' }}">
                                <div class="status-icon"><i class="bi bi-check2-circle"></i></div>
                                <div class="status-text">Selesai</div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Item List --}}
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-4 text-dark"><i class="bi bi-box me-2 text-biru-steel"></i>Produk yang Dibeli</h6>
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3 rounded-start small text-muted text-uppercase">Produk</th>
                                    <th class="small text-muted text-uppercase text-center">Jumlah</th>
                                    <th class="text-end pe-3 rounded-end small text-muted text-uppercase">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr class="border-bottom">
                                    <td class="py-3 ps-3">
                                        <div class="d-flex align-items-center">
                                            {{-- Penanganan Gambar --}}
                                            @php 
                                                $imagePath = $item->product && $item->product->image ? asset('storage/' . $item->product->image) : 'https://placehold.co/100x100?text=Gadget';
                                            @endphp
                                            <img src="{{ $imagePath }}" class="product-img border me-3 shadow-sm">
                                            <div>
                                                <span class="fw-bold text-dark d-block text-truncate" style="max-width: 250px;">{{ $item->product_name }}</span>
                                                <small class="text-muted">Rp {{ number_format($item->price, 0, ',', '.') }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center text-dark fw-medium">
                                        {{ $item->quantity }}x
                                    </td>
                                    <td class="text-end pe-3 fw-bold text-dark">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Billing Details --}}
                    <div class="row mt-4 justify-content-end">
                        <div class="col-md-5">
                            <div class="bg-biru-soft p-3 rounded-4 border">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">Total Harga</span>
                                    <span class="small fw-bold text-dark">Rp {{ number_format($order->items->sum('subtotal'), 0, ',', '.') }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">Biaya Pengiriman</span>
                                    <span class="small fw-bold text-dark">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                                </div>
                                <hr class="my-2 border-biru-steel opacity-25">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-dark">Total Bayar</span>
                                    <span class="fw-bold fs-5 text-biru-steel">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Delivery & Payment Info --}}
                <div class="card-body p-4 bg-light border-top">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-geo-alt me-2 text-biru-steel"></i>Alamat Pengiriman</h6>
                            <div class="ps-3 border-start border-3 border-biru-steel">
                                <p class="mb-1 fw-bold text-dark">{{ $order->shipping_name }}</p>
                                <p class="mb-1 text-muted small">{{ $order->shipping_phone }}</p>
                                <p class="mb-0 text-muted small lh-base">{{ $order->shipping_address }}</p>
                            </div>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-credit-card me-2 text-biru-steel"></i>Info Pembayaran</h6>
                            <p class="mb-2 text-muted small">Metode: Online Payment (Midtrans)</p>
                            @if($order->status == 'pending')
                                <span class="badge bg-warning text-dark border px-3 py-2 fw-bold">
                                    <i class="bi bi-clock-history me-1"></i> Menunggu Pembayaran
                                </span>
                            @else
                                <span class="badge bg-white text-success border px-3 py-2 fw-bold shadow-sm">
                                    <i class="bi bi-shield-check me-1"></i> Terverifikasi / Lunas
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Pay Button --}}
                @if(isset($snapToken) && $order->status === 'pending')
                <div class="card-footer bg-white py-5 border-top-0 text-center">
                    <div class="mb-4">
                        <h5 class="fw-bold">Yuk, Selesaikan Pembayaran!</h5>
                        <p class="text-muted small">Pesanan akan otomatis dibatalkan jika tidak dibayar tepat waktu.</p>
                    </div>
                    <button id="pay-button" class="btn btn-biru-steel btn-lg px-5 py-3 shadow fw-bold">
                        <i class="bi bi-wallet2 me-2"></i> Bayar Sekarang
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Midtrans Scripts --}}
@if(isset($snapToken))
    @push('scripts')
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
        <script type="text/javascript">
            const payButton = document.getElementById('pay-button');
            if (payButton) {
                payButton.addEventListener('click', function() {
                    payButton.disabled = true;
                    payButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memuat...';

                    window.snap.pay('{{ $snapToken }}', {
                        onSuccess: function(result) { window.location.href = '{{ route("orders.index") }}?status=success'; },
                        onPending: function(result) { window.location.href = '{{ route("orders.show", $order) }}'; },
                        onError: function(result) { 
                            alert('Pembayaran gagal, silakan coba lagi.'); 
                            payButton.disabled = false;
                            payButton.innerHTML = '<i class="bi bi-wallet2 me-2"></i> Bayar Sekarang';
                        },
                        onClose: function() { 
                            payButton.disabled = false;
                            payButton.innerHTML = '<i class="bi bi-wallet2 me-2"></i> Bayar Sekarang';
                        }
                    });
                });
            }
        </script>
    @endpush
@endif
@endsection