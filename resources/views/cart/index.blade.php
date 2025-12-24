{{-- resources/views/cart/index.blade.php --}}
@extends('components.app')

@section('title', 'Keranjang Belanja')

@section('content')
<div class="container mx-auto px-4 py-4 max-w-5xl">

    <h1 class="text-2xl font-semibold mb-4">Keranjang Belanja</h1>

    @if ($cart && $cart->items->count() > 0)

    <div class="bg-white border rounded-lg overflow-hidden">

        {{-- ITEM LIST --}}
        <div class="divide-y">
            @foreach ($cart->items as $item)
            <div class="flex items-center gap-4 p-3">

                {{-- IMAGE --}}
                @if ($item->product->image)
                    <img src="{{ asset('storage/' . $item->product->image) }}"
                         class="w-14 h-14 object-cover rounded"
                         alt="{{ $item->product->name }}">
                @else
                    <div class="w-14 h-14 bg-gray-200 rounded flex items-center justify-center text-xs text-gray-500">
                        No Image
                    </div>
                @endif

                {{-- PRODUCT INFO --}}
                <div class="flex-1">
                    <p class="font-medium text-sm">{{ $item->product->name }}</p>
                    <p class="text-xs text-gray-600">
                        Rp {{ number_format($item->product->price, 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-gray-500">
                        Subtotal: Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                    </p>
                </div>

                {{-- QUANTITY --}}
                <form action="{{ route('cart.update', $item->id) }}"
                      method="POST"
                      class="flex items-center gap-1">
                    @csrf
                    @method('PATCH')

                    <button type="button"
                        onclick="this.nextElementSibling.stepDown()"
                        class="px-2 py-0.5 bg-gray-200 text-sm rounded">
                        −
                    </button>

                    <input type="number"
                        name="quantity"
                        min="0"
                        value="{{ $item->quantity }}"
                        class="w-12 text-center border text-sm rounded">

                    <button type="button"
                        onclick="this.previousElementSibling.stepUp()"
                        class="px-2 py-0.5 bg-gray-200 text-sm rounded">
                        +
                    </button>

                    <button type="submit"
                        class="ml-2 text-xs text-blue-600 hover:underline">
                        Update
                    </button>
                </form>

                {{-- REMOVE --}}
                <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        onclick="return confirm('Hapus item?')"
                        class="text-red-500 hover:text-red-700">
                        ✕
                    </button>
                </form>

            </div>
            @endforeach
        </div>

        {{-- SUMMARY --}}
        <div class="bg-gray-50 p-4 flex justify-between items-center text-sm font-semibold">
            <span>Total ({{ $cart->total_quantity }} item)</span>
            <span>Rp {{ number_format($cart->grand_total ?? 0, 0, ',', '.') }}</span>
        </div>

        {{-- CHECKOUT --}}
        <div class="p-4">
            <a href="{{ route('checkout') }}"
               class="block text-center bg-green-600 text-white py-3 rounded text-sm font-semibold hover:bg-green-700">
                Checkout
            </a>
        </div>
    </div>

    {{-- CLEAR CART --}}
    <div class="text-center mt-4">
        <form action="{{ route('cart.clear') }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit"
                onclick="return confirm('Kosongkan keranjang?')"
                class="text-xs text-red-600 hover:underline">
                Kosongkan Keranjang
            </button>
        </form>
    </div>

    @else
        {{-- EMPTY CART --}}
        <div class="text-center py-12 border rounded-lg bg-gray-50">
            <p class="text-gray-600 mb-4">Keranjang masih kosong</p>
            <a href="{{ route('catalog.index') }}"
               class="inline-block bg-blue-600 text-white px-6 py-2 rounded text-sm hover:bg-blue-700">
                Lihat Produk
            </a>
        </div>
    @endif

</div>
@endsection
