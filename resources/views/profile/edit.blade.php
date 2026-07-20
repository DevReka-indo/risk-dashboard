<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}"
               class="flex h-7 w-7 items-center justify-center rounded text-slate-800 hover:bg-slate-100 transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-base font-bold text-slate-900">Profile</h1>
                <p class="text-xs text-slate-500">Kelola informasi akun dan keamanan Anda</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-5">

        {{-- Flash Messages --}}
        @if(session('status') === 'profile-updated')
            <div style="border:1px solid #6ee7b7; background:#ecfdf5; border-radius:12px; padding:12px 16px; font-size:13px; color:#065f46; font-weight:600;">
                ✓ Profil berhasil diperbarui.
            </div>
        @endif

        @if(session('status') === 'password-updated')
            <div style="border:1px solid #6ee7b7; background:#ecfdf5; border-radius:12px; padding:12px 16px; font-size:13px; color:#065f46; font-weight:600;">
                ✓ Password berhasil diperbarui.
            </div>
        @endif

        @if ($errors->any())
            <div style="border:1px solid #fca5a5; background:#fef2f2; border-radius:12px; padding:12px 16px; font-size:13px; color:#991b1b;">
                <ul style="margin:0; padding-left:16px;">
                    @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        {{-- ═══════════ CARD 1: Profil ═══════════ --}}
        <div style="background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:24px; box-shadow:0 1px 4px rgba(0,0,0,0.04);">

            {{-- Header Card 1 --}}
            <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:20px;">
                <div>
                    <h2 style="font-size:15px; font-weight:700; color:#1e293b; margin:0 0 4px;">Profil</h2>
                    <p style="font-size:12px; color:#94a3b8; margin:0;">Perbarui informasi profil dan alamat email Anda</p>
                </div>
            </div>

            {{-- Isi Card 1 --}}
            <form method="post" action="{{ route('profile.update') }}" style="display:flex; flex-direction:column; gap:18px;">
                @csrf
                @method('patch')

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                    {{-- Nama Lengkap --}}
                    <div>
                        <label style="display:block; font-size:13px; font-weight:700; color:#1e293b; margin-bottom:8px;">
                            Nama Lengkap <span style="color:#ef4444;">*</span>
                        </label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name', $user->name) }}"
                            required
                            autofocus
                            autocomplete="name"
                            style="width:100%; border:1px solid #e2e8f0; border-radius:10px; padding:9px 12px; font-size:13px; color:#475569; background:#fff; outline:none; box-sizing:border-box; transition:border-color 0.2s;"
                            onfocus="this.style.borderColor='#4F7EF0'"
                            onblur="this.style.borderColor='#e2e8f0'"
                            placeholder="Masukkan nama lengkap..."
                        >
                        @error('name')
                            <p style="margin-top:4px; font-size:11px; color:#ef4444;">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Alamat Email --}}
                    <div>
                        <label style="display:block; font-size:13px; font-weight:700; color:#1e293b; margin-bottom:8px;">
                            Alamat Email <span style="color:#ef4444;">*</span>
                        </label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email', $user->email) }}"
                            required
                            autocomplete="username"
                            style="width:100%; border:1px solid #e2e8f0; border-radius:10px; padding:9px 12px; font-size:13px; color:#475569; background:#fff; outline:none; box-sizing:border-box; transition:border-color 0.2s;"
                            onfocus="this.style.borderColor='#4F7EF0'"
                            onblur="this.style.borderColor='#e2e8f0'"
                            placeholder="Masukkan alamat email..."
                        >
                        @error('email')
                            <p style="margin-top:4px; font-size:11px; color:#ef4444;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Tombol --}}
                <div style="display:flex; justify-content:flex-end; gap:10px; padding-top:4px;">
                    <button type="submit"
                            style="background:#4F7EF0; border:none; border-radius:8px; padding:8px 24px; font-size:13px; font-weight:700; color:#fff; cursor:pointer; transition:all 0.2s;"
                            onmouseover="this.style.background='#3b66d9';"
                            onmouseout="this.style.background='#4F7EF0';">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        {{-- ═══════════ CARD 2: Ubah Password ═══════════ --}}
        <div style="background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:24px; box-shadow:0 1px 4px rgba(0,0,0,0.04);">

            {{-- Header Card 2 --}}
            <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:20px;">
                <div>
                    <h2 style="font-size:15px; font-weight:700; color:#1e293b; margin:0 0 4px;">Ubah Password</h2>
                    <p style="font-size:12px; color:#94a3b8; margin:0;">Pastikan akun Anda menggunakan password yang panjang dan aman</p>
                </div>
            </div>

            {{-- Isi Card 2 --}}
            <form method="post" action="{{ route('password.update') }}" style="display:flex; flex-direction:column; gap:18px;">
                @csrf
                @method('put')

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                    {{-- Password Saat Ini --}}
                    <div>
                        <label style="display:block; font-size:13px; font-weight:700; color:#1e293b; margin-bottom:8px;">
                            Password Saat Ini <span style="color:#ef4444;">*</span>
                        </label>
                        <input
                            id="update_password_current_password"
                            name="current_password"
                            type="password"
                            autocomplete="current-password"
                            style="width:100%; border:1px solid #e2e8f0; border-radius:10px; padding:9px 12px; font-size:13px; color:#475569; background:#fff; outline:none; box-sizing:border-box; transition:border-color 0.2s;"
                            onfocus="this.style.borderColor='#4F7EF0'"
                            onblur="this.style.borderColor='#e2e8f0'"
                            placeholder="Masukkan password saat ini..."
                        >
                        @error('current_password', 'updatePassword')
                            <p style="margin-top:4px; font-size:11px; color:#ef4444;">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password Baru --}}
                    <div>
                        <label style="display:block; font-size:13px; font-weight:700; color:#1e293b; margin-bottom:8px;">
                            Password Baru <span style="color:#ef4444;">*</span>
                        </label>
                        <input
                            id="update_password_password"
                            name="password"
                            type="password"
                            autocomplete="new-password"
                            style="width:100%; border:1px solid #e2e8f0; border-radius:10px; padding:9px 12px; font-size:13px; color:#475569; background:#fff; outline:none; box-sizing:border-box; transition:border-color 0.2s;"
                            onfocus="this.style.borderColor='#4F7EF0'"
                            onblur="this.style.borderColor='#e2e8f0'"
                            placeholder="Masukkan password baru..."
                        >
                        @error('password', 'updatePassword')
                            <p style="margin-top:4px; font-size:11px; color:#ef4444;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Konfirmasi Password Baru --}}
                <div>
                    <label style="display:block; font-size:13px; font-weight:700; color:#1e293b; margin-bottom:8px;">
                        Konfirmasi Password Baru <span style="color:#ef4444;">*</span>
                    </label>
                    <input
                        id="update_password_password_confirmation"
                        name="password_confirmation"
                        type="password"
                        autocomplete="new-password"
                        style="width:100%; border:1px solid #e2e8f0; border-radius:10px; padding:9px 12px; font-size:13px; color:#475569; background:#fff; outline:none; box-sizing:border-box; transition:border-color 0.2s;"
                        onfocus="this.style.borderColor='#4F7EF0'"
                        onblur="this.style.borderColor='#e2e8f0'"
                        placeholder="Konfirmasi password baru..."
                    >
                    @error('password_confirmation', 'updatePassword')
                        <p style="margin-top:4px; font-size:11px; color:#ef4444;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tombol --}}
                <div style="display:flex; justify-content:flex-end; gap:10px; padding-top:4px;">
                    <button type="submit"
                            style="background:#4F7EF0; border:none; border-radius:8px; padding:8px 24px; font-size:13px; font-weight:700; color:#fff; cursor:pointer; transition:all 0.2s;"
                            onmouseover="this.style.background='#3b66d9';"
                            onmouseout="this.style.background='#4F7EF0';">
                        Simpan Password
                    </button>
                </div>
            </form>
        </div>

        {{-- ═══════════ CARD 3: Hapus Akun ═══════════ --}}
        <div style="background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:24px; box-shadow:0 1px 4px rgba(0,0,0,0.04);">

            {{-- Header Card 3 --}}
            <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:20px;">
                <div>
                    <h2 style="font-size:15px; font-weight:700; color:#1e293b; margin:0 0 4px;">Hapus Akun</h2>
                    <p style="font-size:12px; color:#94a3b8; margin:0;">Setelah akun Anda dihapus, semua data akan dihapus secara permanen</p>
                </div>
            </div>

            {{-- Isi Card 3 --}}
            <div style="display:flex; flex-direction:column; gap:18px;">
                {{-- Warning --}}
                <div style="border:1px solid #fca5a5; background:#fef2f2; border-radius:12px; padding:12px 16px; display:flex; align-items:flex-start; gap:10px;">
                    <svg style="width:18px; height:18px; color:#ef4444; flex-shrink:0; margin-top:1px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                    <div>
                        <p style="font-size:13px; font-weight:600; color:#991b1b; margin:0;">Tindakan ini tidak dapat dibatalkan</p>
                        <p style="font-size:12px; color:#ef4444; margin:4px 0 0 0;">Semua data yang terkait dengan akun Anda akan dihapus secara permanen.</p>
                    </div>
                </div>

                {{-- Tombol --}}
                <div style="display:flex; justify-content:flex-end;">
                    <button
                        x-data=""
                        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                        style="border:1px solid #fca5a5; border-radius:8px; padding:8px 20px; font-size:13px; font-weight:600; color:#ef4444; background:#fff; cursor:pointer; transition:all 0.2s;"
                        onmouseover="this.style.background='#fef2f2';"
                        onmouseout="this.style.background='#fff';"
                    >
                        Hapus Akun
                    </button>
                </div>
            </div>
        </div>

    </div>

    {{-- Modal Konfirmasi Hapus Akun --}}
    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" style="padding:24px; background:#fff; border-radius:16px;">
            @csrf
            @method('delete')

            <div style="display:flex; align-items:flex-start; gap:16px; margin-bottom:20px;">
                <div style="flex:0 0 48px; height:48px; display:flex; align-items:center; justify-content:center; border-radius:50%; background:#fef2f2;">
                    <svg style="width:24px; height:24px; color:#ef4444;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                </div>
                <div>
                    <h3 style="font-size:16px; font-weight:700; color:#1e293b; margin:0 0 4px;">Konfirmasi Hapus Akun</h3>
                    <p style="font-size:13px; color:#94a3b8; margin:0;">Apakah Anda yakin ingin menghapus akun? Tindakan ini tidak dapat dibatalkan. Silakan masukkan password Anda untuk konfirmasi.</p>
                </div>
            </div>

            <div style="margin-bottom:20px;">
                <label for="password" style="display:block; font-size:13px; font-weight:700; color:#1e293b; margin-bottom:8px;">Password</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    style="width:100%; border:1px solid #e2e8f0; border-radius:10px; padding:9px 12px; font-size:13px; color:#475569; background:#fff; outline:none; box-sizing:border-box; transition:border-color 0.2s;"
                    onfocus="this.style.borderColor='#4F7EF0'"
                    onblur="this.style.borderColor='#e2e8f0'"
                    placeholder="Masukkan password Anda..."
                >
                @error('password', 'userDeletion')
                    <p style="margin-top:4px; font-size:11px; color:#ef4444;">{{ $message }}</p>
                @enderror
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button
                    type="button"
                    x-on:click="$dispatch('close')"
                    style="border:1px solid #e2e8f0; border-radius:8px; padding:8px 20px; font-size:13px; font-weight:600; color:#475569; background:#fff; cursor:pointer; transition:all 0.2s;"
                    onmouseover="this.style.background='#f8fafc';"
                    onmouseout="this.style.background='#fff';"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    style="background:#ef4444; border:none; border-radius:8px; padding:8px 24px; font-size:13px; font-weight:700; color:#fff; cursor:pointer; transition:all 0.2s;"
                    onmouseover="this.style.background='#dc2626';"
                    onmouseout="this.style.background='#ef4444';"
                >
                    Hapus Akun
                </button>
            </div>
        </form>
    </x-modal>

</x-admin-layout>