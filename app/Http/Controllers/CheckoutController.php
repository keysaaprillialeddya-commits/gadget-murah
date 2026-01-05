<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    /**
     * Menampilkan halaman checkout.
     */
    public function index()
    {
        $user = auth()->user();
        // Memuat keranjang beserta item dan produknya untuk efisiensi (Eager Loading)
        $cart = $user->cart()->with('items.product')->first();

        // Validasi: Jika keranjang tidak ada atau kosong
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Keranjang belanja Anda masih kosong.');
        }

        // Menyiapkan variabel yang dibutuhkan oleh file Blade Anda
        $cartItems = $cart->items;
        $subtotal = $cartItems->sum(function($item) {
            return ($item->product?->price ?? 0) * $item->quantity;
        });
        
        // Biaya pengiriman statis (bisa diubah sesuai logika Anda nanti)
        $shippingCost = 15000;

        return view('checkout.index', compact('cartItems', 'subtotal', 'shippingCost'));
    }

    /**
     * Menyimpan pesanan baru.
     */
    public function store(Request $request, OrderService $orderService)
    {
        // Penyesuaian nama field dengan <input name="..."> yang ada di Blade Anda
        $request->validate([
            'shipping_name'    => 'required|string|max:255',
            'shipping_phone'   => 'required|string|max:20',
            'shipping_address' => 'required|string|max:500',
            'postal_code'      => 'nullable|string|max:10',
            'notes'            => 'nullable|string|max:255',
        ]);

        try {
            $order = DB::transaction(function () use ($orderService, $request) {
                // Memetakan input form ke dalam array yang dipahami oleh OrderService
                $payload = [
                    'name'    => $request->shipping_name,
                    'phone'   => $request->shipping_phone,
                    'address' => $request->shipping_address . ' (Kodepos: ' . $request->postal_code . ')',
                    'notes'   => $request->notes,
                ];

                return $orderService->createOrder(auth()->user(), $payload);
            });

            // Redirect ke detail order milik USER (bukan admin)
            return redirect()->route('orders.show', $order->id)
                ->with('success', 'Pesanan berhasil dibuat! Silakan selesaikan pembayaran.');

        } catch (\Exception $e) {
            Log::error("Checkout Error: " . $e->getMessage());
            
            return back()
                ->withInput()
                ->with('error', 'Gagal memproses pesanan: ' . $e->getMessage());
        }
    }
}