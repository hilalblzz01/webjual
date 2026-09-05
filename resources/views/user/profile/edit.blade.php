@extends('layouts.app')

@section('title', 'Edit Profil')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">

    <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Profil</h1>

    <div class="bg-white rounded-2xl border border-gray-100 p-6">

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Avatar -->
            <div class="flex flex-col items-center mb-8">
                <div class="relative">
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                         id="avatarPreview"
                         class="w-24 h-24 rounded-full object-cover border-4 border-primary-100">
                    <label for="avatarInput" class="absolute bottom-0 right-0 w-8 h-8 gradient-primary rounded-full flex items-center justify-center cursor-pointer hover:opacity-90 transition-opacity shadow-lg">
                        <i class="fas fa-camera text-white text-xs"></i>
                    </label>
                    <input type="file" id="avatarInput" name="avatar" accept="image/*" class="hidden"
                           onchange="previewAvatar(this)">
                </div>
                <p class="text-sm text-gray-500 mt-2">Klik kamera untuk ganti foto</p>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-1.5 block">Nama Lengkap *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                           class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500 transition-colors @error('name') border-red-400 @enderror">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700 mb-1.5 block">Email</label>
                    <input type="email" value="{{ $user->email }}" disabled
                           class="w-full px-4 py-2.5 text-sm border border-gray-100 bg-gray-50 rounded-xl text-gray-500 cursor-not-allowed">
                    <p class="text-xs text-gray-400 mt-1">Email tidak dapat diubah (terhubung dengan Google)</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700 mb-1.5 block">Nomor Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                           placeholder="08xxxxxxxxxx"
                           class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500 transition-colors @error('phone') border-red-400 @enderror">
                    @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700 mb-1.5 block">Alamat</label>
                    <textarea name="address" rows="3"
                              placeholder="Alamat lengkap Anda..."
                              class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500 resize-none transition-colors @error('address') border-red-400 @enderror">{{ old('address', $user->address) }}</textarea>
                    @error('address')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit" class="flex-1 gradient-primary text-white font-bold py-3 rounded-xl hover:opacity-90 transition-opacity">
                    <i class="fas fa-save mr-2"></i>Simpan Perubahan
                </button>
                <a href="{{ route('home') }}" class="px-6 py-3 border border-gray-200 text-gray-600 font-medium rounded-xl hover:bg-gray-50 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>

    <!-- Quick Links -->
    <div class="grid grid-cols-3 gap-3 mt-5">
        <a href="{{ route('orders.index') }}" class="bg-white rounded-2xl border border-gray-100 p-4 text-center hover:border-primary-200 hover:shadow-md transition-all group">
            <i class="fas fa-box text-2xl text-primary-500 mb-2 group-hover:scale-110 transition-transform block"></i>
            <p class="text-xs font-medium text-gray-700">Pesanan</p>
        </a>
        <a href="{{ route('wishlist.index') }}" class="bg-white rounded-2xl border border-gray-100 p-4 text-center hover:border-primary-200 hover:shadow-md transition-all group">
            <i class="far fa-heart text-2xl text-red-400 mb-2 group-hover:scale-110 transition-transform block"></i>
            <p class="text-xs font-medium text-gray-700">Wishlist</p>
        </a>
        <a href="{{ route('cart.index') }}" class="bg-white rounded-2xl border border-gray-100 p-4 text-center hover:border-primary-200 hover:shadow-md transition-all group">
            <i class="fas fa-shopping-cart text-2xl text-blue-400 mb-2 group-hover:scale-110 transition-transform block"></i>
            <p class="text-xs font-medium text-gray-700">Keranjang</p>
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => document.getElementById('avatarPreview').src = e.target.result;
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
