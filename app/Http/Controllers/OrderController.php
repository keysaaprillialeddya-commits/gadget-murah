<?php
namespace App\Http\Controllers;

use App\Models\Order;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

class OrderController extends Controller
{
    public function __construct()
    {
        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized  = config('midtrans.is_sanitized');
        Config::$is3ds        = config('midtrans.is_3ds');
    }

    /**
     * Menampilkan Daftar Pesanan
     */
    public function index()
    {
        $orders = auth()->user()->orders()->with(['items.product'])->latest()->paginate(10);

        // SYNC STATUS: Cek 3 pesanan terbaru yang masih 'unpaid' agar tidak berat
        $recentPending = $orders->where('payment_status', 'unpaid')->take(3);

        foreach ($recentPending as $order) {
            try {
                $status = Transaction::status($order->order_number);
                if ($status->transaction_status == 'settlement' || $status->transaction_status == 'capture') {
                    $order->update(['payment_status' => 'paid', 'status' => 'processing']);
                }
            } catch (\Exception $e) {
                // Skip jika transaksi belum terdaftar di Midtrans
                continue;
            }
        }

        return view('orders.index', compact('orders'));
    }

    /**
     * Menampilkan Detail Pesanan
     */
    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // SYNC STATUS: Jemput bola jika status di DB belum paid
        if ($order->payment_status !== 'paid') {
            try {
                $status = Transaction::status($order->order_number);
                if ($status->transaction_status == 'settlement' || $status->transaction_status == 'capture') {
                    $order->update(['payment_status' => 'paid', 'status' => 'processing']);
                }
            } catch (\Exception $e) {
                \Log::info("Sync detail failed for {$order->order_number}: " . $e->getMessage());
            }
        }

        $order->load(['items.product']);
        $snapToken = $order->snap_token;

        // Generate Snap Token jika belum ada
        if ($order->status === 'pending' && ! $snapToken) {
            $params = [
                'transaction_details' => [
                    'order_id'     => $order->order_number,
                    'gross_amount' => (int) $order->total_amount,
                ],
                'customer_details'    => [
                    'first_name' => auth()->user()->name,
                    'email'      => auth()->user()->email,
                    'phone'      => $order->shipping_phone,
                ],
            ];

            try {
                $snapToken = Snap::getSnapToken($params);
                $order->update(['snap_token' => $snapToken]);
            } catch (\Exception $e) {
                \Log::error("Midtrans Error: " . $e->getMessage());
            }
        }

        return view('orders.show', compact('order', 'snapToken'));
    }
}