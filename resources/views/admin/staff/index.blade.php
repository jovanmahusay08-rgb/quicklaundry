@extends('admin.layouts.app')

@section('title', 'Staff')
@section('pageTitle', 'Staff')

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    <section class="overflow-hidden rounded-2xl bg-gradient-to-r from-slate-950 via-slate-900 to-brand-900 p-6 text-white shadow-lg sm:p-8">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[.22em] text-sky-300">Team management</p>
                <h2 class="mt-2 text-2xl font-black sm:text-3xl">Staff Accounts</h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-300">Create accounts, assign roles, update employee details, and control portal access.</p>
            </div>
            <button type="button" onclick="document.getElementById('create-staff-panel').open = true; document.getElementById('first_name').focus()" class="inline-flex h-11 shrink-0 items-center justify-center gap-2 rounded-xl bg-white px-5 text-sm font-bold text-slate-900 shadow transition hover:bg-sky-50">
                <i class="fas fa-user-plus text-brand-600"></i> Add Staff
            </button>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm font-medium text-slate-500">Total staff</p><p class="mt-1 text-3xl font-black text-slate-900">{{ $staffStats['total'] }}</p></div>
        <div class="rounded-2xl border border-emerald-100 bg-emerald-50/70 p-5 shadow-sm"><p class="text-sm font-medium text-emerald-700">Active accounts</p><p class="mt-1 text-3xl font-black text-emerald-800">{{ $staffStats['active'] }}</p></div>
        <div class="rounded-2xl border border-sky-100 bg-sky-50/70 p-5 shadow-sm"><p class="text-sm font-medium text-sky-700">Drivers</p><p class="mt-1 text-3xl font-black text-sky-800">{{ $staffStats['drivers'] }}</p></div>
    </section>
    @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700" role="status">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
            {{ $errors->first() }}
        </div>
    @endif

    <details id="create-staff-panel" class="group rounded-2xl border border-slate-200 bg-white shadow-sm" @if($errors->any()) open @endif>
        <summary class="cursor-pointer list-none px-5 py-4 font-semibold text-slate-800 [&::-webkit-details-marker]:hidden sm:px-6">
            <span class="flex items-center justify-between gap-4">
                <span><i class="fas fa-user-plus mr-2 text-brand-600"></i>Create Staff Account</span>
                <span class="flex items-center gap-3 text-sm font-normal text-slate-500"><span class="hidden sm:inline">Add a driver, processor, or quality checker</span><i class="fas fa-chevron-down transition group-open:rotate-180"></i></span>
            </span>
        </summary>

        <form method="POST" action="{{ route('admin.staff.store') }}" class="grid gap-5 border-t border-slate-100 bg-slate-50/50 p-5 md:grid-cols-2 xl:grid-cols-3 sm:p-6">
            @csrf
            <div>
                <label for="first_name" class="mb-1 block text-sm font-medium text-slate-600">First name</label>
                <input id="first_name" name="first_name" value="{{ old('first_name') }}" required maxlength="50" class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>
            <div>
                <label for="last_name" class="mb-1 block text-sm font-medium text-slate-600">Last name</label>
                <input id="last_name" name="last_name" value="{{ old('last_name') }}" required maxlength="50" class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>
            <div>
                <label for="email" class="mb-1 block text-sm font-medium text-slate-600">Email address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>
            <div>
                <label for="phone" class="mb-1 block text-sm font-medium text-slate-600">Phone number</label>
                <input id="phone" name="phone" value="{{ old('phone') }}" required inputmode="numeric" pattern="[0-9]{11}" maxlength="11" placeholder="09XXXXXXXXX" class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>
            <div>
                <label for="role" class="mb-1 block text-sm font-medium text-slate-600">Role</label>
                <select id="role" name="role" required class="w-full rounded-lg border border-slate-300 px-3 py-2">
                    <option value="">Select role</option>
                    @foreach (['Driver', 'Processor', 'Quality Check'] as $role)
                        <option value="{{ $role }}" @selected(old('role') === $role)>{{ $role }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="barangay" class="mb-1 block text-sm font-medium text-slate-600">Barangay</label>
                <select id="barangay" name="barangay" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                    <option value="">Select barangay</option>
                    @foreach (['Balidbid', 'Bantigue', 'Langub', 'Maricaban', 'Okoy', 'Poblacion', 'Pooc', 'Talisay'] as $barangay)
                        <option value="{{ $barangay }}" @selected(old('barangay') === $barangay)>{{ $barangay }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2 xl:col-span-3">
                <label for="address" class="mb-1 block text-sm font-medium text-slate-600">Address</label>
                <textarea id="address" name="address" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2">{{ old('address') }}</textarea>
            </div>
            <div>
                <label for="salary" class="mb-1 block text-sm font-medium text-slate-600">Salary (optional)</label>
                <input id="salary" type="number" name="salary" value="{{ old('salary') }}" min="0" step="0.01" class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>
            <div>
                <label for="hire_date" class="mb-1 block text-sm font-medium text-slate-600">Hire date (optional)</label>
                <input id="hire_date" type="date" name="hire_date" value="{{ old('hire_date') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>
            <div></div>
            <div>
                <label for="password" class="mb-1 block text-sm font-medium text-slate-600">Temporary password</label>
                <input id="password" type="password" name="password" required minlength="8" autocomplete="new-password" class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>
            <div>
                <label for="password_confirmation" class="mb-1 block text-sm font-medium text-slate-600">Confirm password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password" class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full rounded-lg bg-brand-600 px-4 py-2.5 font-semibold text-white transition hover:bg-brand-700">Create Staff Account</button>
            </div>
        </form>
    </details>

    <form method="GET" class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:flex-row sm:items-end">
        <div class="min-w-0 flex-1">
            <label class="mb-1 block text-sm font-medium text-slate-600">Search staff</label>
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Name, email, or phone" class="w-full rounded-lg border border-slate-300 py-2 pl-9 pr-3">
            </div>
        </div>
        <div class="min-w-0 sm:w-56">
            <label class="block text-sm font-medium text-slate-600 mb-1">Role</label>
            <select name="role" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                <option value="">All roles</option>
                <option value="Driver" {{ request('role') === 'Driver' ? 'selected' : '' }}>Driver</option>
                <option value="Processor" {{ request('role') === 'Processor' ? 'selected' : '' }}>Processor</option>
                <option value="Quality Check" {{ request('role') === 'Quality Check' ? 'selected' : '' }}>Quality Check</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button class="flex-1 rounded-lg bg-brand-600 px-5 py-2 font-semibold text-white hover:bg-brand-700">Filter</button>
            @if(request()->hasAny(['search', 'role']))
                <a href="{{ route('admin.staff') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-slate-600 hover:bg-slate-50">Clear</a>
            @endif
        </div>
    </form>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-5 py-3 text-left">Name</th>
                        <th class="px-5 py-3 text-left">Role</th>
                        <th class="px-5 py-3 text-left">Phone</th>
                        <th class="px-5 py-3 text-left">Barangay</th>
                        <th class="px-5 py-3 text-left">Status</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($staff as $member)
                        <tr class="transition hover:bg-slate-50/80">
                            <td class="px-5 py-4"><p class="font-semibold text-slate-800">{{ $member->full_name }}</p><p class="mt-0.5 text-xs text-slate-500">{{ $member->email }}</p></td>
                            <td class="px-5 py-4"><span class="rounded-full bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700">{{ $member->role }}</span></td>
                            <td class="px-5 py-3">{{ $member->phone }}</td>
                            <td class="px-5 py-3">{{ $member->barangay }}</td>
                            <td class="px-5 py-3">
                                <form action="{{ route('admin.staff.toggle-active', $member->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="rounded-full {{ $member->is_active ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }} px-2.5 py-1 text-xs font-medium transition cursor-pointer">{{ $member->is_active ? 'Active' : 'Inactive' }}</button>
                                </form>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.staff.edit', $member) }}" class="rounded-lg border border-blue-200 px-3 py-1.5 text-xs font-semibold text-blue-700 transition hover:bg-blue-50">Edit</a>
                                    <form action="{{ route('admin.staff.destroy', $member) }}" method="POST" onsubmit="return confirm('Delete {{ addslashes($member->full_name) }}? Their assigned orders will become unassigned.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-700 transition hover:bg-red-50">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-6 text-center text-slate-400">No staff found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="{{ $staff->hasPages() ? 'flex justify-center' : '' }}">
        {{ $staff->links() }}
    </div>
</div>
@endsection
