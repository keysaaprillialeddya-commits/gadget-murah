{{-- resources/views/admin/products/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Manajemen Produk')

@section('content')
<div class="row">
    <div class="col-lg-12">

        {{-- Flash Message --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary fw-bold">Daftar Produk</h5>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                    <i class="bi bi-plus-lg"></i> Tambah Baru
                </button>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Nama Produk</th>
                                <th class="text-center">Kategori</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Harga</th>
                                <th class="text-center">Stok</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($products as $product)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            @if($product->primaryImage)
                                                <img src="{{ $product->primaryImage->image_url }}"
                                                     class="rounded me-2"
                                                     width="40" height="40"
                                                     style="object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center me-2"
                                                     style="width:40px;height:40px;">
                                                    <i class="bi bi-image text-muted"></i>
                                                </div>
                                            @endif

                                            <div>
                                                <div class="fw-bold">{{ $product->name }}</div>
                                                <small class="text-muted">{{ $product->slug }}</small>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ $product->category->name }}</span>
                                    </td>

                                    <td class="text-center">
                                        @if($product->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">Nonaktif</span>
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        <span class="badge bg-danger">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                    </td>

                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $product->stock }}</span>
                                    </td>

                                    <td class="text-end pe-4">
                                        <div class="d-inline-flex gap-2">
                                            <button class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editProduk{{ $product->id }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>

                                            <form action="{{ route('admin.products.destroy', $product) }}"
                                                  method="POST"
                                                  class="d-inline-flex"
                                                  onsubmit="return confirm('Hapus produk ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        Tidak ada produk tersedia.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer bg-white">
                {{ $products->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

{{-- ===================== MODAL CREATE ===================== --}}
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Tambah Produk Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-bold">Nama Produk</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="Contoh: Tempered Glass iPhone">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Kategori</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Pilih...</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Harga (Rp)</label>
                            <input type="number" name="price" class="form-control" value="{{ old('price') }}" required placeholder="100000">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Harga Diskon (Opsional)</label>
                            <input type="number" name="discount_price" class="form-control" value="{{ old('discount_price') }}" placeholder="85000">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Stok</label>
                            <input type="number" name="stock" class="form-control" value="{{ old('stock') }}" required placeholder="10">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Berat (gram)</label>
                            <input type="number" name="weight" class="form-control" value="{{ old('weight') }}" required placeholder="500">
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Deskripsi</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Masukkan deskripsi detail produk...">{{ old('description') }}</textarea>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Gambar Produk</label>
                            <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                            <small class="text-muted">Bisa pilih lebih dari 1 gambar.</small>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                                <label class="form-check-label">Aktifkan Produk</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                                <label class="form-check-label">Jadikan Unggulan</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan Produk</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===================== MODAL EDIT ===================== --}}
@foreach($products as $product)
<div class="modal fade" id="editProduk{{ $product->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="modal-header">
                <h5 class="modal-title fw-bold">Edit Produk: {{ $product->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label fw-bold">Nama Produk</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Kategori</label>
                        <select name="category_id" class="form-select" required>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ (old('category_id', $product->category_id) == $category->id) ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Harga (Rp)</label>
                        <input type="number" name="price" class="form-control" value="{{ old('price', (int)$product->price) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Harga Diskon (Opsional)</label>
                        <input type="number" name="discount_price" class="form-control" value="{{ old('discount_price', $product->discount_price ? (int)$product->discount_price : '') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Stok</label>
                        <input type="number" name="stock" class="form-control" value="{{ old('stock', $product->stock) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Berat (gram)</label>
                        <input type="number" name="weight" class="form-control" value="{{ old('weight', $product->weight) }}" required>
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label fw-bold">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="4">{{ old('description', $product->description) }}</textarea>
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label fw-bold d-block">Gambar Saat Ini</label>
                        <div class="d-flex gap-2 flex-wrap mb-2">
                            @foreach($product->images as $img)
                                <div class="position-relative">
                                    <img src="{{ asset('storage/' . $img->image_path) }}" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                                    @if($img->is_primary)
                                        <span class="badge bg-primary position-absolute top-0 start-0" style="font-size: 0.6rem;">Utama</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <label class="form-label fw-bold mt-2">Tambah Gambar Baru (Opsional)</label>
                        <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                    </div>

                    <div class="col-md-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label">Produk Aktif</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                            <label class="form-check-label">Produk Unggulan</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endforeach

@endsection