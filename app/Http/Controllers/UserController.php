<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();

        $users = User::query()
            ->with('roles')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('users.index', compact('users', 'search'));
    }

    public function create(): View
    {
        $roles = Role::query()
            ->orderByRaw("FIELD(name, 'superadmin', 'admin', 'user') DESC")
            ->orderBy('name')
            ->get();

        return view('users.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', 'exists:roles,name'],
        ]);

        $user = User::create([
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
        ]);

        $user->syncRoles($validated['roles'] ?? []);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        $roles = Role::query()
            ->orderByRaw("FIELD(name, 'superadmin', 'admin', 'user') DESC")
            ->orderBy('name')
            ->get();

        $selectedRoles = $user->roles()
            ->pluck('name')
            ->toArray();

        return view('users.edit', compact('user', 'roles', 'selectedRoles'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', 'exists:roles,name'],
        ]);

        $user->update([
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
        ]);

        if (!empty($validated['password'])) {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        $newRoles = $validated['roles'] ?? [];

        if ($user->id === auth()->id() && !in_array('superadmin', $newRoles, true) && $user->hasRole('superadmin')) {
            return redirect()
                ->route('users.edit', $user)
                ->with('error', 'Kamu tidak bisa melepas role superadmin dari akun sendiri.');
        }

        if ($user->hasRole('superadmin') && !in_array('superadmin', $newRoles, true) && $this->isLastSuperadmin($user)) {
            return redirect()
                ->route('users.edit', $user)
                ->with('error', 'Tidak bisa melepas role superadmin terakhir.');
        }

        $user->syncRoles($newRoles);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Kamu tidak bisa menghapus akun sendiri.');
        }

        if ($user->hasRole('superadmin') && $this->isLastSuperadmin($user)) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Tidak bisa menghapus superadmin terakhir.');
        }

        $user->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    private function isLastSuperadmin(User $user): bool
    {
        return User::role('superadmin')
            ->where('id', '!=', $user->id)
            ->doesntExist();
    }
}
