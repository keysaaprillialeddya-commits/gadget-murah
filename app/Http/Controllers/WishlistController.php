<?php
// app/Http/Controllers/WishlistController.php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Menampilkan halaman wishlist
     */
    public function index()
    {
        $products = auth()->user()
            ->wishlistProducts()
            ->with(['category', 'primaryImage'])
            ->orderByPivot('created_at', 'desc')
            ->paginate(12);

        return view('wishlist.index', compact('products'));
    }

    /**
     * Toggle wishlist (tambah / hapus) - dipakai tombol hati di detail produk
     */
    public function toggle(Product $product)
    {
        $user   = auth()->user();
        $exists = $user->wishlistProducts()->where('product_id', $product->id)->exists();

        if ($exists) {
            $user->wishlistProducts()->detach($product->id);
            $message    = 'Produk dihapus dari wishlist 💔';
            $inWishlist = false;
        } else {
            $user->wishlistProducts()->attach($product->id);
            $message    = 'Produk ditambahkan ke wishlist ❤️';
            $inWishlist = true;
        }

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success'     => true,
                'message'     => $message,
                'in_wishlist' => $inWishlist,
                'count'       => $user->wishlistProducts()->count(),
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Hapus satu produk dari wishlist (tombol hati patah di halaman wishlist)
     */
    public function destroy(Product $product)
    {
        $user = auth()->user();

        if ($user->wishlistProducts()->where('product_id', $product->id)->exists()) {
            $user->wishlistProducts()->detach($product->id);

            return response()->json([
                'success' => true,
                'message' => 'Produk dihapus dari wishlist 💔',
                'count'   => $user->wishlistProducts()->count(),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Produk tidak ada di wishlist',
        ], 404);
    }

    /**
     * Hapus banyak produk sekaligus (bulk delete via checkbox)
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'product_ids'   => 'required|array',
            'product_ids.*' => 'exists:products,id',
        ]);

        $user = auth()->user();
        $user->wishlistProducts()->detach($request->product_ids);

        return response()->json([
            'success' => true,
            'message' => count($request->product_ids) . ' produk dihapus dari wishlist',
            'count'   => $user->wishlistProducts()->count(),
        ]);
    }
}