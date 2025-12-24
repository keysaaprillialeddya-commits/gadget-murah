<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product; // Asumsi kamu sudah punya model Order

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Hitung Statistik untuk Stats Cards
        $stats = [
            'total_revenue'  => Order::where('status', 'completed')->sum('total_amount'),
            'total_orders'   => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'low_stock'      => Product::where('stock', '<=', 5)->count(), // Produk stok <= 5
        ];

        // 2. Ambil 5 Pesanan Terbaru untuk tabel
        // Kita gunakan Eager Loading (with('user')) agar tidak berat
        $recentOrders = Order::with('user')->latest()->take(5)->get();

        // 3. Kirim semua data ke Blade
        return view('admin.dashboard', compact('stats', 'recentOrders'));
    }
}