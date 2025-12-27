<?php
namespace App\Http\Controllers;

use App\Models\Order;

class OrderController extends Controller
{
    /**
     * Menampilkan detail satu order milik user yang login
     */
    public function show(Order $order)
    {
        // Pastikan user hanya bisa melihat order miliknya sendiri
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $snapToken = null;

        // Logika pembuatan Snap Token Midtrans
        if ($order->status === 'pending') {
            // Jika Anda sudah menginstal library Midtrans, aktifkan ini:
            /*
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production');
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;

            $params = [
                'transaction_details' => [
                    'order_id' => $order->order_number,
                    'gross_amount' => $order->total_amount,
                ],
                'customer_details' => [
                    'first_name' => auth()->user()->name,
                    'email' => auth()->user()->email,
                ],
            ];

            $snapToken = \Midtrans\Snap::getSnapToken($params);
            */

            // Untuk sementara, jika belum set up Midtrans, biarkan null atau isi string dummy
            $snapToken = $order->snap_token;
        }

        return view('orders.show', compact('order', 'snapToken'));
    }

    /**
     * Daftar semua order user (history)
     */
    public function index()
    {
        $orders = auth()->user()
            ->orders()
            ->with('items')
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }
}