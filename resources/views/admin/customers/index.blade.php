@extends('admin.layouts.app')

@section('title', 'Customers')
@section('pageTitle', 'Customers')

@section('content')
<div class="space-y-6">
    <form method="GET" class="bg-white rounded-xl border border-slate-200 p-4 flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[260px]">
            <label class="block text-sm font-medium text-slate-600 mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2" placeholder="Name, email, phone">
        </div>
        <button class="rounded-lg bg-brand-600 px-4 py-2 text-white">Search</button>
    </form>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-5 py-3 text-left">Name</th>
                        <th class="px-5 py-3 text-left">Email</th>
                        <th class="px-5 py-3 text-left">Phone</th>
                        <th class="px-5 py-3 text-left">Total Spent</th>
                        <th class="px-5 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($customers as $customer)
                        <tr>
                            <td class="px-5 py-3">{{ $customer->full_name }}</td>
                            <td class="px-5 py-3">{{ $customer->email }}</td>
                            <td class="px-5 py-3">{{ $customer->phone }}</td>
                            <td class="px-5 py-3">₱{{ number_format($customer->total_spent, 2) }}</td>
                            <td class="px-5 py-3 text-right"><a href="{{ route('admin.customers.show', $customer) }}" class="text-brand-600">View</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-6 text-center text-slate-400">No customers found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="{{ $customers->hasPages() ? 'flex justify-center' : '' }}">
        {{ $customers->links() }}
    </div>
</div>
@endsection
