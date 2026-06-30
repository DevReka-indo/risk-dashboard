<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SsoTokenService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Throwable;

class SsoCallbackController extends Controller
{
    public function __invoke(Request $request, SsoTokenService $ssoTokenService): RedirectResponse
    {
        $request->validate([
            'sso_token' => ['required', 'string'],
            'state' => ['required', 'string'],
        ]);

        $expectedState = $request->session()->pull('sso_state');
        $requestState = $request->string('state')->toString();

        if (! $expectedState || ! hash_equals($expectedState, $requestState)) {
            return redirect()
                ->route('login')
                ->with('error', 'Login SSO gagal: state tidak valid. Silakan login ulang.');
        }

        try {
            $ssoUser = $ssoTokenService->verify(
                $request->string('sso_token')->toString()
            );

            $user = $this->resolveUser($ssoUser);

            $user->name = $ssoUser['name']
                ?? $user->name
                ?? $ssoUser['employee_id']
                ?? 'SSO User';

            if (! empty($ssoUser['email'])) {
                $user->email = strtolower(trim((string) $ssoUser['email']));
            }

            if (Schema::hasColumn('users', 'employee_id')) {
                $user->employee_id = $ssoUser['employee_id'] ?? $user->employee_id;
            }

            if (Schema::hasColumn('users', 'sso_id')) {
                $user->sso_id = $ssoUser['sso_id'] ?? $ssoUser['sub'] ?? $user->sso_id;
            }

            if (! $user->exists || empty($user->password)) {
                $user->password = Hash::make(Str::random(40));
            }

            $user->save();

            $this->syncUserRoles($user, $ssoUser['roles'] ?? []);

            Auth::login($user, true);

            $request->session()->regenerate();

            return redirect(config('services.sso.after_login_url', '/'));
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('login')
                ->with('error', 'Login SSO gagal: ' . $exception->getMessage());
        }
    }

    private function resolveUser(array $ssoUser): User
    {
        $user = null;

        if (! empty($ssoUser['employee_id']) && Schema::hasColumn('users', 'employee_id')) {
            $user = User::query()
                ->where('employee_id', $ssoUser['employee_id'])
                ->first();
        }

        if (! $user && ! empty($ssoUser['email'])) {
            $user = User::query()
                ->where('email', strtolower(trim((string) $ssoUser['email'])))
                ->first();
        }

        if ($user) {
            return $user;
        }

        return new User();
    }

    private function syncUserRoles(User $user, array $ssoRoles): void
    {
        $mappedRoles = collect($ssoRoles)
            ->map(fn (string $role): ?string => $this->mapSsoRoleToLocalRole($role))
            ->filter()
            ->unique()
            ->values()
            ->all();

        if ($mappedRoles === []) {
            $mappedRoles = ['user'];
        }

        $existingRoles = Role::query()
            ->whereIn('name', $mappedRoles)
            ->pluck('name')
            ->all();

        if ($existingRoles === []) {
            $existingRoles = ['user'];
        }

        $user->syncRoles($existingRoles);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function mapSsoRoleToLocalRole(string $ssoRole): ?string
    {
        return match ($ssoRole) {
            'superadmin',
            'super-admin',
            'super_admin' => 'superadmin',

            'admin',
            'administrator' => 'admin',

            'user',
            'employee',
            'operator',
            'approver' => 'user',

            default => null,
        };
    }
}
