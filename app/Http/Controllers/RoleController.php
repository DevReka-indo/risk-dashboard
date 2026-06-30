<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();

        $roles = Role::query()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->withCount(['permissions', 'users'])
            ->orderByRaw("FIELD(name, 'superadmin', 'admin', 'user') DESC")
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('roles.index', compact('roles', 'search'));
    }

    public function create(): View
    {
        $permissions = $this->groupedPermissions();

        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $roleName = strtolower(trim($validated['name']));

        $role = Role::create([
            'name' => $roleName,
            'guard_name' => 'web',
        ]);

        if ($roleName === 'superadmin') {
            $role->syncPermissions(['*']);
        } else {
            $role->syncPermissions($validated['permissions'] ?? []);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role berhasil ditambahkan.');
    }

    public function edit(Role $role): View
    {
        $permissions = $this->groupedPermissions();

        $selectedPermissions = $role->permissions()
            ->pluck('name')
            ->toArray();

        return view('roles.edit', compact('role', 'permissions', 'selectedPermissions'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $rules = [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->ignore($role->id),
            ],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ];

        if ($role->name === 'superadmin') {
            $rules['name'][] = Rule::in(['superadmin']);
        }

        $validated = $request->validate($rules);

        $roleName = strtolower(trim($validated['name']));

        if ($role->name === 'superadmin' && $roleName !== 'superadmin') {
            return redirect()
                ->route('roles.index')
                ->with('error', 'Role superadmin tidak boleh diubah namanya.');
        }

        $role->update([
            'name' => $roleName,
        ]);

        if ($role->name === 'superadmin') {
            $role->syncPermissions(['*']);
        } else {
            $role->syncPermissions($validated['permissions'] ?? []);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->name === 'superadmin') {
            return redirect()
                ->route('roles.index')
                ->with('error', 'Role superadmin tidak boleh dihapus.');
        }

        if ($role->users()->exists()) {
            return redirect()
                ->route('roles.index')
                ->with('error', 'Role masih digunakan oleh user, lepaskan role dari user terlebih dahulu.');
        }

        $role->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role berhasil dihapus.');
    }

    private function groupedPermissions(): Collection
    {
        return Permission::query()
            ->orderBy('name')
            ->get()
            ->groupBy(function (Permission $permission) {
                if ($permission->name === '*') {
                    return 'Wildcard';
                }

                return str($permission->name)
                    ->before('.')
                    ->headline()
                    ->toString();
            });
    }
}
