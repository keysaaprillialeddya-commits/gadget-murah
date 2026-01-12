@extends('layouts.admin')

@section('title', 'Detail Pesanan #' . $order->order_number)

@section('content')
<div class="container-fluid">
    {{-- Header Navigation --}}
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-white border shadow-sm rounded-circle me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h2 class="h4 mb-0 fw-bold text-gray-800">Detail Pesanan #{{ $order->order_number }}</h2>
            <p class="text-muted small mb-0">Dipesan pada {{ $order->created_at->translatedFormat('d F Y, H:i') }} WIB</p>
        </div>
    </div>

    <div class="row">
        {{-- LEFT COLUMN: Items & Payment Details --}}
        <div class="col-lg-8">
            {{-- Order Items --}}
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-box-seam me-2 text-primary"></i>Item Pesanan</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle">
                            <thead>
                                <tr class="text-muted small text-uppercase">
                                    <th style="width: 50%">Produk</th>
                                    <th class="text-center">Harga</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->items as $item)
                                    <tr class="border-bottom">
                                        <td class="py-3">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $item->product->image_url }}" class="rounded-3 me-3 border"
                                                    style="width: 64px; height: 64px; object-fit: cover;">
                                                <div>
                                                    <h6 class="mb-0 fw-bold text-dark">{{ $item->product->name }}</h6>
                                                    <small class="text-muted">SKU: {{ $item->product->sku ?? 'N/A' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                        <td class="text-center fw-bold">{{ $item->quantity }}</td>
                                        <td class="text-end fw-bold">
                                            Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Billing Summary --}}
                    <div class="row justify-content-end mt-4">
                        <div class="col-md-5">
                            <div class="bg-light rounded-4 p-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Subtotal</span>
                                    <span class="fw-semibold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Biaya Layanan</span>
                                    <span class="fw-semibold text-success">Gratis</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-dark">Total Bayar</span>
                                    <span class="h4 mb-0 fw-bold text-primary">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: Customer & Actions --}}
        <div class="col-lg-4">
            {{-- Status Update Action --}}
            <div class="card shadow-sm border-0 rounded-4 mb-4 overflow-hidden">
                <div class="card-header bg-primary py-3">
                    <h6 class="fw-bold mb-0 text-white">Update Status Order</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Ubah Status Menjadi:</label>
                            <select name="status" class="form-select border-0 bg-light rounded-3 py-2 shadow-none">
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending (Menunggu Pembayaran)</option>
                                <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Diproses (Packing)</option>
                                <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Dikirim (In Transit)</option>
                                <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Selesai (Delivered)</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Batalkan Pesanan</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold rounded-pill py-2">
                            Simpan Perubahan
                        </button>
                    </form>

                    @if ($order->status == 'cancelled')
                        <div class="alert alert-soft-danger mt-3 border-0 rounded-3 mb-0">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> Pesanan ini telah dibatalkan.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Customer Information --}}
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold">Informasi Pelanggan</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($order->user->name) }}&background=EEF2FF&color=4E73DF&bold=true" class="rounded-circle me-3" width="48">
                        <div>
                            <p class="mb-0 fw-bold text-dark">{{ $order->user->name }}</p>
                            <p class="mb-0 text-muted small">{{ $order->user->email }}</p>
                        </div>
                    </div>
                    <hr class="text-muted opacity-25">
                    <div class="mt-3">
                        <label class="text-muted small text-uppercase fw-bold mb-1 d-block">Metode Pembayaran</label>
                        <p class="text-dark fw-semibold mb-0">
                            <i class="bi bi-credit-card me-2"></i> Midtrans / Virtual Account
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* UI Utility Classes */
    .btn-white {
        background-color: #fff;
        color: #333;
    }
    .btn-white:hover {
        background-color: #f8f9fa;
        color: #4e73df;
    }
    .alert-soft-danger {
        background-color: #ffeeee;
        color: #dc3545;
    }
    .bg-light {
        background-color: #f8f9fc !important;
    }
    
    /* Input Styling */
    .form-select:focus {
        border-color: #4e73df;
        box-shadow: none;
    }

    /* Table Styling */
    .table thead th {
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }
</style>
@endsection