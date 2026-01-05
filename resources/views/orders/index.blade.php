{{-- resources/views/orders/index.blade.php --}}

@extends('components.app')

@section('title', 'Daftar Transaksi')

@section('content')
<style>
    /* Sinkronisasi Tema Biru Steel */
    :root {
        --biru-steel: #3B6181;
        --biru-steel-soft: #f0f5f9;
        --biru-steel-hover: #2d4a63;
    }

    body { background-color: #f8f9fa; }

    .order-card {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        transition: all 0.2s ease;
        background: #fff;
        overflow: hidden;
    }

    .order-card:hover {
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        border-color: var(--biru-steel);
    }

    .status-badge {
        font-size: 11px;
        font-weight: 700;
        padding: 5px 10px;
        border-radius: 6px;
        text-transform: uppercase;
    }

    /* Status Colors */
    .status-pending { background: #fff3e0; color: #ef6c00; }
    .status-processing { background: #e3f2fd; color: #1976d2; }
    .status-shipped { background: #e0f2f1; color: #00796b; }
    .status-delivered { background: var(--biru-steel-soft); color: var(--biru-steel); }
    .status-cancelled { background: #ffebee; color: #c62828; }

    .btn-biru-outline {
        border: 1.5px solid var(--biru-steel);
        color: var(--biru-steel);
        font-weight: 700;
        transition: 0.2s;
        border-radius: 8px;
    }

    .btn-biru-outline:hover {
        background: var(--biru-steel);
        color: white;
    }

    .btn-biru-steel {
        background-color: var(--biru-steel);
        color: white;
        font-weight: 700;
        border-radius: 8px;
        border: none;
    }

    .btn-biru-steel:hover {
        background-color: var(--biru-steel-hover);
        color: white;
    }

    .text-biru-steel {
        color: var(--biru-steel) !important;
    }
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">

            {{-- Title --}}
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h4 class="fw-bold text-dark mb-0">Riwayat Transaksi</h4>
                <div class="text-muted small">Menampilkan semua pesanan Anda</div>
            </div>

            @if($orders->isEmpty())
                {{-- Empty State --}}
                <div class="text-center py-5 bg-white rounded-4 border shadow-sm">
                    <div class="mb-4">
                        <i class="bi bi-receipt text-muted opacity-25" style="font-size: 100px;"></i>
                    </div>
                    <h5 class="fw-bold">Belum ada transaksi, nih!</h5>
                    <p class="text-muted px-4">Ayo mulai belanja dan temukan produk impian Anda di katalog kami.</p>
                    <a href="{{ route('catalog.index') }}" class="btn btn-biru-steel px-5 py-2 mt-2 shadow-sm">
                        Mulai Belanja Sekarang
                    </a>
                </div>
            @else
                {{-- Loop Orders --}}
                @foreach($orders as $order)
                    <div class="order-card mb-4 shadow-sm">
                        <div class="p-3 border-bottom d-flex align-items-center justify-content-between bg-light">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-white p-2 rounded border shadow-sm d-none d-md-block">
                                    <i class="bi bi-bag-check-fill text-biru-steel"></i>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fw-bold small">Belanja</span>
                                        <span class="text-muted small border-start ps-2">{{ $order->created_at->format('d M Y') }}</span>
                                    </div>
                                    <div class="mt-1 d-md-none">
                                         <span class="text-muted extra-small">#{{ $order->order_number }}</span>
                                    </div>
                                </div>

                                @php
                                    $statusSlug = strtolower($order->status);
                                    $statusLabel = [
                                        'pending' => 'Menunggu Pembayaran',
                                        'processing' => 'Sedang Diproses',
                                        'shipped' => 'Dalam Pengiriman',
                                        'delivered' => 'Pesanan Selesai',
                                        'cancelled' => 'Dibatalkan',
                                    ];
                                @endphp
                                <span class="status-badge status-{{ $statusSlug }} ms-2">
                                    {{ $statusLabel[$statusSlug] ?? $order->status }}
                                </span>
                            </div>
                            <span class="text-muted small d-none d-md-block fw-medium">#{{ $order->order_number }}</span>
                        </div>

                        <div class="p-4">
                            <div class="row align-items-center g-3">
                                {{-- Produk Info --}}
                                <div class="col-md-7">
                                    @if($order->items->count() > 0)
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-3 border overflow-hidden me-3 shadow-sm" style="width: 70px; height: 70px; flex-shrink: 0;">
                                                <img src="{{ $order->items->first()->product_image_url ?? 'https://via.placeholder.com/70' }}" 
                                                     class="w-100 h-100 object-fit-cover">
                                            </div>
                                            <div class="overflow-hidden">
                                                <h6 class="fw-bold mb-1 text-truncate text-dark" style="max-width: 100%;">
                                                    {{ $order->items->first()->product_name }}
                                                </h6>
                                                <div class="text-muted small">
                                                    {{ $order->items->first()->quantity }} barang x <span class="text-dark fw-medium">Rp {{ number_format($order->items->first()->price, 0, ',', '.') }}</span>
                                                </div>
                                                @if($order->items->count() > 1)
                                                    <div class="mt-1">
                                                        <span class="badge bg-light text-muted border fw-normal" style="font-size: 10px;">
                                                            +{{ $order->items->count() - 1 }} produk lainnya
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                {{-- Total Harga --}}
                                <div class="col-md-3 border-start-md ps-md-4">
                                    <div class="ps-md-3 border-start ps-3">
                                        <small class="text-muted d-block mb-1">Total Belanja</small>
                                        <span class="fw-bold fs-5 text-biru-steel">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                                    </div>
                                </div>

                                {{-- Aksi --}}
                                <div class="col-md-2 text-md-end">
                                    <a href="{{ route('orders.show', $order) }}" class="btn btn-biru-outline btn-sm w-100 py-2 shadow-sm">
                                        Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Pagination & Footer --}}
                <div class="mt-5 d-flex flex-column align-items-center gap-4">
                    <div class="custom-pagination">
                        {{ $orders->links() }}
                    </div>
                    <a href="{{ url('/') }}" class="text-decoration-none fw-bold text-biru-steel d-flex align-items-center gap-2 border-bottom border-2 border-transparent hover-border-biru">
                        <i class="bi bi-arrow-left"></i> Kembali ke Beranda
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection