<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category; // pastikan modelnya ada
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        // Ambil semua kategori + hitung jumlah produk per kategori
        // Pakai withCount biar efisien
        $categories = Category::withCount('products')->orderBy('name')->paginate(10);

        // Kalau nggak ada relasi products, cukup:
        // $categories = Category::orderBy('name')->paginate(10);

        return view('admin.categories.index', compact('categories'));
    }

    // Method lain (store, update, destroy) nanti kita isi kalau perlu
    // Untuk sekarang, fokus ke index dulu biar halaman muncul
}