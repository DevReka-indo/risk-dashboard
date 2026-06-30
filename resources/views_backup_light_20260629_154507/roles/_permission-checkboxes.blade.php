<div class="space-y-5">
    @foreach ($permissions as $groupName => $groupPermissions)
        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-950/50">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <h3 class="font-semibold text-slate-900 dark:text-white">
                        {{ $groupName }}
                    </h3>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        {{ $groupPermissions->count() }} permission tersedia
                    </p>
                </div>

                <button
                    type="button"
                    onclick="togglePermissionGroup('{{ str($groupName)->slug() }}')"
                    class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-white dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
                    Toggle Group
                </button>
            </div>

            <div id="permission-group-{{ str($groupName)->slug() }}" class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($groupPermissions as $permission)
                    @php
                        $isWildcard = $permission->name === '*';
                        $isChecked = in_array($permission->name, old('permissions', $selectedPermissions ?? []), true);
                    @endphp

                    <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-slate-200 bg-white p-4 transition hover:border-indigo-300 hover:bg-indigo-50/40 dark:border-slate-800 dark:bg-slate-900 dark:hover:border-indigo-700 dark:hover:bg-indigo-950/20">
                        <input
                            type="checkbox"
                            name="permissions[]"
                            value="{{ $permission->name }}"
                            @checked($isChecked)
                            class="mt-1 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-950">

                        <span>
                            <span class="block text-sm font-semibold text-slate-900 dark:text-white">
                                {{ $permission->name }}
                            </span>

                            @if ($isWildcard)
                                <span class="mt-1 block text-xs text-indigo-600 dark:text-indigo-300">
                                    Memberikan akses penuh ke seluruh permission.
                                </span>
                            @else
                                <span class="mt-1 block text-xs text-slate-500 dark:text-slate-400">
                                    Guard: {{ $permission->guard_name }}
                                </span>
                            @endif
                        </span>
                    </label>
                @endforeach
            </div>
        </div>
    @endforeach
</div>

<script>
    function togglePermissionGroup(groupSlug) {
        const container = document.getElementById(`permission-group-${groupSlug}`);

        if (!container) {
            return;
        }

        const checkboxes = container.querySelectorAll('input[type="checkbox"]');
        const hasUnchecked = Array.from(checkboxes).some((checkbox) => !checkbox.checked);

        checkboxes.forEach((checkbox) => {
            checkbox.checked = hasUnchecked;
        });
    }
</script>
