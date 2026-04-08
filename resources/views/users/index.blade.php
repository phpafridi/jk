<x-app-layout>
    <x-slot name="header">User Management</x-slot>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
        <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search users..."
                   class="border border-slate-300 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 w-56">
            <button type="submit" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 rounded-xl text-sm text-slate-600">
                <i class="fas fa-search"></i>
            </button>
        </form>
        <button onclick="document.getElementById('modal-add-user').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-5 py-2.5 btn-primary text-white rounded-xl text-sm font-medium shadow-sm">
            <i class="fas fa-user-plus"></i> Add User
        </button>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">User</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Email</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Role</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Joined</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-medium text-slate-800">{{ $user->name }}</p>
                                    @if($user->id === auth()->id())
                                    <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">You</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-slate-500">{{ $user->email }}</td>
                        <td class="px-5 py-3">
                            @foreach($user->roles as $role)
                            <span class="text-xs px-2 py-1 rounded-full font-medium {{ $role->name === 'admin' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ ucfirst($role->name) }}
                            </span>
                            @endforeach
                            @if($user->roles->isEmpty())
                            <span class="text-xs text-slate-400">No role</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-slate-500 text-xs">{{ $user->created_at->format('d M Y') }}</td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if($user->id !== auth()->id())
                                {{-- Change role inline --}}
                                <form method="POST" action="{{ route('users.role', $user) }}" class="inline-flex items-center gap-1">
                                    @csrf @method('PATCH')
                                    <select name="role" onchange="this.form.submit()"
                                            class="border border-slate-300 rounded-xl px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                                        <option value="admin"  {{ $user->hasRole('admin')  ? 'selected' : '' }}>Admin</option>
                                        <option value="viewer" {{ $user->hasRole('viewer') ? 'selected' : '' }}>Viewer</option>
                                    </select>
                                </form>
                                {{-- Reset password --}}
                                <button onclick="openResetPassword({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                        class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Reset Password">
                                    <i class="fas fa-key text-xs"></i>
                                </button>
                                {{-- Delete --}}
                                <form method="POST" action="{{ route('users.destroy', $user) }}"
                                      onsubmit="return confirm('Delete {{ addslashes($user->name) }}? This cannot be undone.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete User">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                                @else
                                <span class="text-xs text-slate-400 italic">Current user</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center text-slate-400">
                            <i class="fas fa-users text-4xl mb-3 block"></i>
                            No users found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-slate-100">{{ $users->links() }}</div>
    </div>

    {{-- ── Add User Modal ─────────────────────────────────────────────────── --}}
    <div id="modal-add-user" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 modal-overlay">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="flex items-center justify-between p-5 border-b border-slate-100">
                <h3 class="font-semibold text-slate-800"><i class="fas fa-user-plus text-indigo-500 mr-2"></i>Add New User</h3>
                <button onclick="document.getElementById('modal-add-user').classList.add('hidden')"
                        class="text-slate-400 hover:text-slate-600 w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('users.store') }}" class="p-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Full Name *</label>
                    <input type="text" name="name" required value="{{ old('name') }}"
                           class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                           placeholder="e.g. Ahmed Khan">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Email Address *</label>
                    <input type="email" name="email" required value="{{ old('email') }}"
                           class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                           placeholder="user@example.com">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Password *</label>
                    <input type="password" name="password" required minlength="8"
                           class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                           placeholder="Min. 8 characters">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Role *</label>
                    <select name="role" required class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="admin">Admin — Full access (add/edit/delete everything)</option>
                        <option value="viewer" selected>Viewer — Read-only access</option>
                    </select>
                </div>
                <div class="bg-slate-50 rounded-xl p-3 text-xs text-slate-500">
                    <p><i class="fas fa-info-circle text-indigo-400 mr-1"></i>
                    <strong>Admin:</strong> can create markets, add shops, record payments, upload files, manage all data.<br>
                    <strong>Viewer:</strong> can only view data, no add/edit/delete access.</p>
                </div>
                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="document.getElementById('modal-add-user').classList.add('hidden')"
                            class="flex-1 py-2.5 rounded-xl border border-slate-300 text-sm font-medium text-slate-600 hover:bg-slate-50">Cancel</button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl btn-primary text-white text-sm font-medium">
                        <i class="fas fa-user-plus mr-1"></i> Create User
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Reset Password Modal ────────────────────────────────────────────── --}}
    <div id="modal-reset-pw" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 modal-overlay">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm">
            <div class="flex items-center justify-between p-5 border-b border-slate-100">
                <h3 class="font-semibold text-slate-800"><i class="fas fa-key text-amber-500 mr-2"></i>Reset Password</h3>
                <button onclick="document.getElementById('modal-reset-pw').classList.add('hidden')"
                        class="text-slate-400 hover:text-slate-600 w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="reset-pw-form" method="POST" class="p-5 space-y-4">
                @csrf @method('PATCH')
                <p class="text-sm text-slate-600">Set new password for <strong id="reset-pw-name"></strong>:</p>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">New Password *</label>
                    <input type="password" name="password" required minlength="8"
                           class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                           placeholder="Min. 8 characters">
                </div>
                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="document.getElementById('modal-reset-pw').classList.add('hidden')"
                            class="flex-1 py-2.5 rounded-xl border border-slate-300 text-sm font-medium text-slate-600 hover:bg-slate-50">Cancel</button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium">
                        Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Auto-open modal on validation error --}}
    @if($errors->any())
    <script>document.getElementById('modal-add-user').classList.remove('hidden');</script>
    @endif

    <script>
    function openResetPassword(userId, userName) {
        document.getElementById('reset-pw-name').textContent = userName;
        document.getElementById('reset-pw-form').action = '/users/' + userId + '/password';
        document.getElementById('modal-reset-pw').classList.remove('hidden');
    }
    </script>

</x-app-layout>
