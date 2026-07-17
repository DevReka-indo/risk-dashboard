<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2">
            <h1 class="text-lg font-bold text-slate-900">
                Profile
            </h1>
            <p class="text-sm text-slate-500">
                Kelola informasi akun dan keamanan Anda.
            </p>
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- Profile Information --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            @include('profile.partials.update-profile-information-form')
        </div>

        {{-- Update Password --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            @include('profile.partials.update-password-form')
        </div>

        {{-- Delete Account --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-admin-layout>