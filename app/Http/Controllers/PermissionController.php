<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();

        $permissions = Permission::query()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->withCount('roles')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('permissions.index', compact('permissions', 'search'));
    }

    public function create(): View
    {
        return view('permissions.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name'],
        ]);

        Permission::create([
            'name' => strtolower(trim($validated['name'])),
            'guard_name' => 'web',
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()
            ->route('permissions.index')
            ->with('success', 'Permission berhasil ditambahkan.');
    }

    public function edit(Permission $permission): View|RedirectResponse
    {
        if ($permission->name === '*') {
            return redirect()
                ->route('permissions.index')
                ->with('error', 'Permission wildcard tidak boleh diedit.');
        }

        return view('permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission): RedirectResponse
    {
        if ($permission->name === '*') {
            return redirect()
                ->route('permissions.index')
                ->with('error', 'Permission wildcard tidak boleh diedit.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name,' . $permission->id],
        ]);

        $permission->update([
            'name' => strtolower(trim($validated['name'])),
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()
            ->route('permissions.index')
            ->with('success', 'Permission berhasil diperbarui.');
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        if ($permission->name === '*') {
            return redirect()
                ->route('permissions.index')
                ->with('error', 'Permission wildcard tidak boleh dihapus.');
        }

        if ($permission->roles()->exists()) {
            return redirect()
                ->route('permissions.index')
                ->with('error', 'Permission masih digunakan oleh role, lepaskan dari role terlebih dahulu.');
        }

        $permission->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()
            ->route('permissions.index')
            ->with('success', 'Permission berhasil dihapus.');
    }
}
