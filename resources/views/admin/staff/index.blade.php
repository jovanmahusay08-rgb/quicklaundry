@extends('admin.layouts.app')

@section('title', 'Staff')
@section('pageTitle', 'Staff')

@section('content')
<div class="space-y-6">
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

    <details class="rounded-xl border border-slate-200 bg-white shadow-sm" @if($errors->any()) open @endif>
        <summary class="cursor-pointer list-none px-5 py-4 font-semibold text-slate-800 [&::-webkit-details-marker]:hidden">
            <span class="flex items-center justify-between gap-4">
                <span><i class="fas fa-user-plus mr-2 text-brand-600"></i>Create Staff Account</span>
                <span class="text-sm font-normal text-slate-500">Add a driver, processor, or quality checker</span>
            </span>
        </summary>

        <form method="POST" action="{{ route('admin.staff.store') }}" class="grid gap-4 border-t border-slate-100 p-5 md:grid-cols-2 xl:grid-cols-3">
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

    <form method="GET" class="bg-white rounded-xl border border-slate-200 p-4 flex flex-wrap gap-4 items-end">
        <div class="min-w-[220px]">
            <label class="block text-sm font-medium text-slate-600 mb-1">Role</label>
            <select name="role" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                <option value="">All roles</option>
                <option value="Driver" {{ request('role') === 'Driver' ? 'selected' : '' }}>Driver</option>
                <option value="Processor" {{ request('role') === 'Processor' ? 'selected' : '' }}>Processor</option>
                <option value="Quality Check" {{ request('role') === 'Quality Check' ? 'selected' : '' }}>Quality Check</option>
            </select>
        </div>
        <button class="rounded-lg bg-brand-600 px-4 py-2 text-white">Filter</button>
    </form>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
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
                        <tr>
                            <td class="px-5 py-3">{{ $member->full_name }}</td>
                            <td class="px-5 py-3">{{ $member->role }}</td>
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
