@extends('admin.layouts.app')

@section('title', 'Settings')
@section('pageTitle', 'Settings')

@section('content')
<div class="grid lg:grid-cols-2 gap-6">
    @if(session('success'))
        <div class="lg:col-span-2 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="lg:col-span-2 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <h2 class="font-semibold text-slate-800 mb-4">System Snapshot</h2>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between"><span class="text-slate-500">Customers</span><span class="font-medium text-slate-800">{{ $stats['customers'] }}</span></div>
            <div class="flex justify-between"><span class="text-slate-500">Staff</span><span class="font-medium text-slate-800">{{ $stats['staff'] }}</span></div>
            <div class="flex justify-between"><span class="text-slate-500">Bookings</span><span class="font-medium text-slate-800">{{ $stats['bookings'] }}</span></div>
            <div class="flex justify-between"><span class="text-slate-500">Revenue</span><span class="font-medium text-slate-800">₱{{ number_format($stats['revenue'], 2) }}</span></div>
            <div class="flex justify-between"><span class="text-slate-500">This Month</span><span class="font-medium text-slate-800">₱{{ number_format($stats['this_month'], 2) }}</span></div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <h2 class="font-semibold text-slate-800 mb-4">Change Password</h2>
        <form method="POST" action="{{ route('admin.settings.password') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Current Password</label>
                <input type="password" name="current_password" class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">New Password</label>
                <input type="password" name="password" class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Confirm Password</label>
                <input type="password" name="password_confirmation" class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>
            <button class="rounded-lg bg-brand-600 px-4 py-2 text-white">Update Password</button>
        </form>
    </div>
</div>
@endsection
