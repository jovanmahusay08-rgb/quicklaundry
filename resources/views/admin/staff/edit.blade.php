@extends('admin.layouts.app')

@section('title', 'Edit Staff')
@section('pageTitle', 'Edit Staff Account')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">
    <div class="flex flex-col gap-4 rounded-2xl bg-gradient-to-r from-slate-950 to-brand-900 p-6 text-white shadow-lg sm:flex-row sm:items-center sm:justify-between sm:p-8">
        <div>
            <p class="text-xs font-bold uppercase tracking-[.2em] text-sky-300">Staff profile</p>
            <h2 class="mt-2 text-2xl font-black">{{ $staff->full_name }}</h2>
            <p class="mt-1 text-sm text-slate-300">Update this staff member's account information and access credentials.</p>
        </div>
        <a href="{{ route('admin.staff') }}" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 text-sm font-semibold text-white hover:bg-white/20"><i class="fas fa-arrow-left"></i> Back to Staff</a>
    </div>

    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.staff.update', $staff) }}" class="grid gap-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm md:grid-cols-2 sm:p-7">
        @csrf
        @method('PUT')
        <div class="border-b border-slate-100 pb-3 md:col-span-2">
            <h3 class="font-bold text-slate-800">Personal and work details</h3>
            <p class="mt-1 text-sm text-slate-500">Basic contact information and job assignment.</p>
        </div>
        <div>
            <label for="first_name" class="mb-1 block text-sm font-medium text-slate-600">First name</label>
            <input id="first_name" name="first_name" value="{{ old('first_name', $staff->first_name) }}" required maxlength="50" class="w-full rounded-lg border border-slate-300 px-3 py-2">
        </div>
        <div>
            <label for="last_name" class="mb-1 block text-sm font-medium text-slate-600">Last name</label>
            <input id="last_name" name="last_name" value="{{ old('last_name', $staff->last_name) }}" required maxlength="50" class="w-full rounded-lg border border-slate-300 px-3 py-2">
        </div>
        <div>
            <label for="email" class="mb-1 block text-sm font-medium text-slate-600">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email', $staff->email) }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2">
        </div>
        <div>
            <label for="phone" class="mb-1 block text-sm font-medium text-slate-600">Phone number</label>
            <input id="phone" name="phone" value="{{ old('phone', $staff->phone) }}" required inputmode="numeric" pattern="[0-9]{11}" maxlength="11" class="w-full rounded-lg border border-slate-300 px-3 py-2">
        </div>
        <div>
            <label for="role" class="mb-1 block text-sm font-medium text-slate-600">Role</label>
            <select id="role" name="role" required class="w-full rounded-lg border border-slate-300 px-3 py-2">
                @foreach (['Driver', 'Processor', 'Quality Check'] as $role)
                    <option value="{{ $role }}" @selected(old('role', $staff->role) === $role)>{{ $role }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="barangay" class="mb-1 block text-sm font-medium text-slate-600">Barangay</label>
            <select id="barangay" name="barangay" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                <option value="">Select barangay</option>
                @foreach (['Balidbid', 'Bantigue', 'Langub', 'Maricaban', 'Okoy', 'Poblacion', 'Pooc', 'Talisay'] as $barangay)
                    <option value="{{ $barangay }}" @selected(old('barangay', $staff->barangay) === $barangay)>{{ $barangay }}</option>
                @endforeach
            </select>
        </div>
        <div class="md:col-span-2">
            <label for="address" class="mb-1 block text-sm font-medium text-slate-600">Address</label>
            <textarea id="address" name="address" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2">{{ old('address', $staff->address) }}</textarea>
        </div>
        <div>
            <label for="salary" class="mb-1 block text-sm font-medium text-slate-600">Salary</label>
            <input id="salary" type="number" name="salary" value="{{ old('salary', $staff->salary) }}" min="0" step="0.01" class="w-full rounded-lg border border-slate-300 px-3 py-2">
        </div>
        <div>
            <label for="hire_date" class="mb-1 block text-sm font-medium text-slate-600">Hire date</label>
            <input id="hire_date" type="date" name="hire_date" value="{{ old('hire_date', optional($staff->hire_date)->format('Y-m-d')) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2">
        </div>
        <div class="mt-2 border-b border-slate-100 pb-3 md:col-span-2">
            <h3 class="font-bold text-slate-800">Account security</h3>
            <p class="mt-1 text-sm text-slate-500">Leave both password fields empty to keep the current password.</p>
        </div>
        <div>
            <label for="password" class="mb-1 block text-sm font-medium text-slate-600">New password <span class="font-normal text-slate-400">(optional)</span></label>
            <input id="password" type="password" name="password" minlength="8" autocomplete="new-password" class="w-full rounded-lg border border-slate-300 px-3 py-2">
        </div>
        <div>
            <label for="password_confirmation" class="mb-1 block text-sm font-medium text-slate-600">Confirm new password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" minlength="8" autocomplete="new-password" class="w-full rounded-lg border border-slate-300 px-3 py-2">
        </div>
        <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end md:col-span-2">
            <a href="{{ route('admin.staff') }}" class="rounded-xl border border-slate-300 px-6 py-2.5 text-center font-semibold text-slate-600 hover:bg-slate-50">Cancel</a>
            <button type="submit" class="rounded-xl bg-brand-600 px-6 py-2.5 font-semibold text-white shadow-lg shadow-sky-600/15 transition hover:bg-brand-700">Save Changes</button>
        </div>
    </form>
</div>
@endsection
