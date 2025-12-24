<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi ke User (nullable)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke semua item di keranjang
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    // Helper: Total jumlah barang (bukan total harga)
    public function getTotalQuantityAttribute(): int
    {
        return $this->items()->sum('quantity');
    }

    // Optional: Cek apakah cart ini milik user saat ini
    public function isOwnedByCurrentUser(): bool
    {
        if (!auth()->check()) {
            return false;
        }

        return $this->user_id === auth()->id();
    }

    // Optional: Scope untuk cart yang masih aktif
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNotNull('user_id')
              ->orWhereNotNull('session_id');
        });
    }
}