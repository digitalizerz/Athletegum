<x-superadmin-dashboard-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">Audit Logs</h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Filters -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-4">
            <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="flex flex-wrap gap-4">
                <select name="action" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All Actions</option>
                    <option value="user.suspended" {{ request('action') === 'user.suspended' ? 'selected' : '' }}>User Suspended</option>
                    <option value="user.reactivated" {{ request('action') === 'user.reactivated' ? 'selected' : '' }}>User Reactivated</option>
                    <option value="user.impersonated" {{ request('action') === 'user.impersonated' ? 'selected' : '' }}>User Impersonated</option>
                    <option value="deal.cancelled" {{ request('action') === 'deal.cancelled' ? 'selected' : '' }}>Deal Cancelled</option>
                    <option value="athlete.profile.hidden" {{ request('action') === 'athlete.profile.hidden' ? 'selected' : '' }}>Profile Hidden</option>
                    <option value="athlete.profile.shown" {{ request('action') === 'athlete.profile.shown' ? 'selected' : '' }}>Profile Shown</option>
                </select>
                <select name="entity_type" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All Entity Types</option>
                    <option value="App\Models\User" {{ request('entity_type') === 'App\Models\User' ? 'selected' : '' }}>User</option>
                    <option value="App\Models\Deal" {{ request('entity_type') === 'App\Models\Deal' ? 'selected' : '' }}>Deal</option>
                    <option value="App\Models\Athlete" {{ request('entity_type') === 'App\Models\Athlete' ? 'selected' : '' }}>Athlete</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-800">
                    Filter
                </button>
                @if(request()->hasAny(['action', 'entity_type']))
                    <a href="{{ route('admin.audit-logs.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <!-- Audit Logs Table -->
        <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Admin</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Entity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($logs as $log)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $log->created_at->format('M d, Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $log->admin->name ?? 'System' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                        {{ str_replace('.', ' ', $log->action) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ class_basename($log->entity_type) }} #{{ $log->entity_id }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $log->description }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No audit logs found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</x-superadmin-dashboard-layout>

