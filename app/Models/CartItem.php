<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    // Relasi ke Cart (parent)
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    // Relasi ke Produk
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Optional: Accessor untuk subtotal item ini
    public function getSubtotalAttribute(): float
    {
        // Asumsi Product punya field 'price'
        return $this->quantity * ($this->product?->price ?? 0);
    }
}