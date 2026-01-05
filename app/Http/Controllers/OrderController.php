<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class OrderController extends Controller
{
    /**
     * Menampilkan daftar semua pesanan milik user yang sedang login.
     */
    public function index()
    {
        $orders = auth()->user()->orders()
            ->with(['items.product']) 
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Menampilkan detail satu pesanan dan mengelola Snap Token Midtrans.
     */
    public function show(Order $order)
    {
        // 1. Keamanan: Pastikan user hanya bisa melihat pesanannya sendiri
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        // 2. Normalisasi status untuk pengecekan logika
        $status = strtolower($order->status);
        $snapToken = $order->snap_token;

        // 3. Logika Midtrans: Generate Snap Token jika status masih 'pending'
        if ($status === 'pending') {
            
            // Konfigurasi Midtrans
            Config::$serverKey = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production');
            Config::$isSanitized = true;
            Config::$is3ds = true;

            // Jika belum ada snap_token di database, buatkan yang baru ke Midtrans
            if (!$snapToken) {
                try {
                    $params = [
                        'transaction_details' => [
                            // Gunakan order_number asli + suffix waktu agar ID unik di Midtrans jika ada percobaan ulang
                            'order_id' => $order->order_number . '-' . time(), 
                            'gross_amount' => (int) $order->total_amount,
                        ],
                        'customer_details' => [
                            'first_name' => auth()->user()->name,
                            'email' => auth()->user()->email,
                            'phone' => $order->shipping_phone ?? auth()->user()->phone,
                        ],
                        'item_details' => $order->items->map(function ($item) {
                            return [
                                'id' => $item->product_id,
                                'price' => (int) $item->price,
                                'quantity' => $item->quantity,
                                'name' => substr($item->product_name, 0, 50), // Limit 50 karakter sesuai spek Midtrans
                            ];
                        })->toArray(),
                    ];

                    // Request Token ke Midtrans
                    $snapToken = Snap::getSnapToken($params);

                    // Simpan snap_token ke database agar tidak request API terus-menerus saat refresh halaman
                    $order->update(['snap_token' => $snapToken]);
                    
                } catch (Exception $e) {
                    Log::error('Midtrans Error for Order #' . $order->order_number . ': ' . $e->getMessage());
                    // Biarkan snapToken null, View akan menangani tampilan error-nya
                }
            }
        }

        // 4. Load data relasi untuk kebutuhan View agar tidak ada N+1 query
        $order->load(['items.product']);

        return view('orders.show', compact('order', 'snapToken'));
    }

    /**
     * Opsi Tambahan: Menangani pembatalan pesanan oleh user (jika diizinkan).
     */
    public function cancel(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if ($order->status !== 'pending') {
            return back()->with('error', 'Hanya pesanan pending yang dapat dibatalkan.');
        }

        try {
            $order->update(['status' => 'cancelled']);
            
            // Logika opsional: Kembalikan stok produk jika dibatalkan
            foreach ($order->items as $item) {
                $item->product->increment('stock', $item->quantity);
            }

            return redirect()->route('orders.show', $order->id)->with('success', 'Pesanan berhasil dibatalkan.');
        } catch (Exception $e) {
            return back()->with('error', 'Gagal membatalkan pesanan.');
        }
    }
}