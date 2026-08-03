@extends('admin.layouts.app')

@section('title', 'Staff')
@section('pageTitle', 'Staff')

@section('content')
<div class="space-y-6">
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
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-6 text-center text-slate-400">No staff found.</td></tr>
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
