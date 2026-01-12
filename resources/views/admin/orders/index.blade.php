@extends('layouts.admin')

@section('title', 'Manajemen Pesanan')

@section('content')
<div class="container-fluid">
    {{-- Header & Statistik Singkat --}}
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h2 class="h3 mb-1 text-gray-800 fw-bold">Manajemen Pesanan</h2>
            <p class="text-muted small">Pantau dan kelola semua transaksi masuk pelanggan Anda.</p>
        </div>
        <div class="col-md-6 text-md-end">
            <button onclick="window.location.reload()" class="btn btn-white shadow-sm border rounded-pill px-3">
                <i class="bi bi-arrow-clockwise me-1"></i> Refresh Data
            </button>
        </div>
    </div>

    {{-- Ringkasan Status --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-primary border-4">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Pesanan</div>
                    <div class="h5 mb-0 fw-bold text-gray-800">{{ $orders->total() }}</div>
                </div>
            </div>
        </div>
        {{-- Anda bisa menambahkan hitungan spesifik di sini jika variabel tersedia --}}
    </div>

    {{-- Main Card --}}
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                {{-- Filter Tabs --}}
                <ul class="nav nav-pills custom-pills p-1 bg-light rounded-pill">
                    <li class="nav-item">
                        <a class="nav-link rounded-pill {{ !request('status') ? 'active bg-primary shadow-sm' : 'text-muted' }}" href="{{ route('admin.orders.index') }}">Semua</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link rounded-pill {{ request('status') == 'pending' ? 'active bg-warning text-dark shadow-sm' : 'text-muted' }}" href="{{ route('admin.orders.index', ['status' => 'pending']) }}">Pending</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link rounded-pill {{ request('status') == 'processing' ? 'active bg-info text-dark shadow-sm' : 'text-muted' }}" href="{{ route('admin.orders.index', ['status' => 'processing']) }}">Diproses</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link rounded-pill {{ request('status') == 'completed' ? 'active bg-success shadow-sm' : 'text-muted' }}" href="{{ route('admin.orders.index', ['status' => 'completed']) }}">Selesai</a>
                    </li>
                </ul>

                {{-- Search Box --}}
                <div class="search-box">
                    <form action="{{ route('admin.orders.index') }}" method="GET" class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-0" placeholder="Cari No. Order..." value="{{ request('search') }}">
                    </form>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4 border-0 py-3">No. Order</th>
                            <th class="border-0 py-3">Customer</th>
                            <th class="border-0 py-3">Tanggal</th>
                            <th class="border-0 py-3">Total Tagihan</th>
                            <th class="border-0 py-3 text-center">Status</th>
                            <th class="border-0 py-3 text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td class="ps-4">
                                <span class="fw-bold text-dark">#{{ $order->order_number }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-2 bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 12px;">
                                        {{ strtoupper(substr($order->user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark mb-0" style="font-size: 0.9rem;">{{ $order->user->name }}</div>
                                        <div class="text-muted small">{{ $order->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-muted small">
                                {{ $order->created_at->translatedFormat('d M Y') }}<br>
                                <span class="text-xs">{{ $order->created_at->format('H:i') }} WIB</span>
                            </td>
                            <td>
                                <span class="fw-bold text-dark">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </td>
                            <td class="text-center">
                                @php
                                    $statusClasses = [
                                        'pending' => 'bg-soft-warning text-warning',
                                        'processing' => 'bg-soft-info text-info',
                                        'shipped' => 'bg-soft-info text-info',
                                        'completed' => 'bg-soft-success text-success',
                                        'cancelled' => 'bg-soft-danger text-danger'
                                    ];
                                    $labels = [
                                        'pending' => 'Menunggu Pembayaran',
                                        'processing' => 'Diproses',
                                        'shipped' => 'Dikirim',
                                        'completed' => 'Selesai',
                                        'cancelled' => 'Dibatalkan'
                                    ];
                                    $class = $statusClasses[$order->status] ?? 'bg-light text-muted';
                                @endphp
                                <span class="badge rounded-pill px-3 py-2 {{ $class }}" style="font-size: 0.75rem; letter-spacing: 0.3px;">
                                    <i class="bi bi-dot"></i> {{ $labels[$order->status] ?? $order->status }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-white border shadow-sm rounded-3 px-3">
                                        Detail
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <img src="https://illustrations.popsy.co/gray/box.svg" alt="Empty" style="width: 120px;" class="mb-3 opacity-50">
                                <p class="text-muted fw-medium">Belum ada pesanan yang masuk.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <span class="small text-muted">Menampilkan {{ $orders->count() }} dari {{ $orders->total() }} pesanan</span>
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</div>

<style>
    /* Soft Background for Badges */
    .bg-soft-warning { background-color: #fff9e6 !important; color: #ffc107 !important; }
    .bg-soft-success { background-color: #e6f9f0 !important; color: #198754 !important; }
    .bg-soft-info { background-color: #e7f5ff !important; color: #0dcaf0 !important; }
    .bg-soft-danger { background-color: #ffeeee !important; color: #dc3545 !important; }
    .bg-soft-primary { background-color: #eef2ff !important; color: #4e73df !important; }

    /* Custom Nav Pills */
    .custom-pills .nav-link {
        font-size: 0.85rem;
        padding: 6px 16px;
        transition: all 0.3s;
    }

    /* Table Hover Effect */
    .table tbody tr:hover {
        background-color: #fcfcfd;
    }
    
    .btn-white {
        background-color: #fff;
        color: #333;
    }
    
    .btn-white:hover {
        background-color: #f8f9fa;
        border-color: #ddd;
    }

    .text-xs { font-size: 0.7rem; }
    .avatar-sm { font-weight: 700; }
</style>
@endsection