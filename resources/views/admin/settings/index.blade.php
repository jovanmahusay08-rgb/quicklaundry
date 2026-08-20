@extends('admin.layouts.app')

@section('title', 'Settings')
@section('pageTitle', 'Settings')

@section('content')
<div class="mx-auto grid max-w-6xl gap-6 lg:grid-cols-2">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 lg:col-span-2" role="status">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 lg:col-span-2" role="alert">
            <ul class="list-inside list-disc">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm lg:col-span-2">
        <div class="bg-gradient-to-r from-slate-950 to-brand-900 px-6 py-6 text-white sm:px-8">
            <p class="text-xs font-bold uppercase tracking-[.2em] text-sky-300">Main administrator</p>
            <h2 class="mt-2 text-2xl font-black">Account Details</h2>
            <p class="mt-1 text-sm text-slate-300">Update your own administrator profile. Your current password is required to save changes.</p>
        </div>
        <form method="POST" action="{{ route('admin.settings.profile') }}" class="grid gap-5 p-6 sm:grid-cols-2 sm:p-8">
            @csrf
            @method('PUT')
            <div><label for="first_name" class="mb-1 block text-sm font-medium text-slate-600">First name</label><input id="first_name" name="first_name" value="{{ old('first_name', $admin->first_name) }}" required maxlength="50" autocomplete="given-name" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-sky-100"></div>
            <div><label for="last_name" class="mb-1 block text-sm font-medium text-slate-600">Last name</label><input id="last_name" name="last_name" value="{{ old('last_name', $admin->last_name) }}" required maxlength="50" autocomplete="family-name" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-sky-100"></div>
            <div><label for="email" class="mb-1 block text-sm font-medium text-slate-600">Email address</label><input id="email" type="email" name="email" value="{{ old('email', $admin->email) }}" required autocomplete="email" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-sky-100"></div>
            <div><label for="phone" class="mb-1 block text-sm font-medium text-slate-600">Phone number</label><input id="phone" name="phone" value="{{ old('phone', $admin->phone) }}" maxlength="20" autocomplete="tel" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-sky-100"></div>
            <div class="sm:col-span-2">
                <label for="profile_current_password" class="mb-1 block text-sm font-medium text-slate-600">Current password</label>
                <input id="profile_current_password" type="password" name="current_password" required autocomplete="current-password" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-sky-100 sm:max-w-md">
                <p class="mt-1 text-xs text-slate-400">Required to protect changes to the main administrator account.</p>
            </div>
            <div class="flex justify-end border-t border-slate-100 pt-5 sm:col-span-2"><button class="w-full rounded-xl bg-brand-600 px-5 py-2.5 font-semibold text-white shadow-lg shadow-sky-600/15 hover:bg-brand-700 sm:w-auto">Save Admin Details</button></div>
        </form>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="mb-4 font-semibold text-slate-800">System Snapshot</h2>
        <div class="space-y-3 text-sm">
            <div class="flex justify-between"><span class="text-slate-500">Customers</span><span class="font-medium text-slate-800">{{ $stats['customers'] }}</span></div>
            <div class="flex justify-between"><span class="text-slate-500">Staff</span><span class="font-medium text-slate-800">{{ $stats['staff'] }}</span></div>
            <div class="flex justify-between"><span class="text-slate-500">Bookings</span><span class="font-medium text-slate-800">{{ $stats['bookings'] }}</span></div>
            <div class="flex justify-between"><span class="text-slate-500">Revenue</span><span class="font-medium text-slate-800">&#8369;{{ number_format($stats['revenue'], 2) }}</span></div>
            <div class="flex justify-between"><span class="text-slate-500">This Month</span><span class="font-medium text-slate-800">&#8369;{{ number_format($stats['this_month'], 2) }}</span></div>
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="mb-1 font-semibold text-slate-800">Change Password</h2>
        <p class="mb-4 text-sm text-slate-500">Use a strong password with at least eight characters.</p>
        <form method="POST" action="{{ route('admin.settings.password') }}" class="space-y-4">
            @csrf
            <div><label for="password_current" class="mb-1 block text-sm font-medium text-slate-600">Current password</label><input id="password_current" type="password" name="current_password" required autocomplete="current-password" class="w-full rounded-xl border border-slate-300 px-3 py-2.5"></div>
            <div><label for="new_password" class="mb-1 block text-sm font-medium text-slate-600">New password</label><input id="new_password" type="password" name="password" required minlength="8" autocomplete="new-password" class="w-full rounded-xl border border-slate-300 px-3 py-2.5"></div>
            <div><label for="new_password_confirmation" class="mb-1 block text-sm font-medium text-slate-600">Confirm password</label><input id="new_password_confirmation" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password" class="w-full rounded-xl border border-slate-300 px-3 py-2.5"></div>
            <button class="w-full rounded-xl bg-slate-900 px-4 py-2.5 font-semibold text-white hover:bg-slate-800 sm:w-auto">Update Password</button>
        </form>
    </section>
</div>
@endsection
