@extends('customer.layout')

@section('content')
@php($pageTitle = 'Profile')
<div class="py-2">
    <div class="max-w-3xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">My Profile</h1>
            </div>
            <x-back-button :href="route('customer.dashboard')">Back</x-back-button>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
            @if (session('success'))
                <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('customer.profile.update') }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">First Name</label>
                        <input type="text" name="first_name" value="{{ old('first_name', $customer->first_name) }}" class="w-full rounded-lg border-slate-300" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Last Name</label>
                        <input type="text" name="last_name" value="{{ old('last_name', $customer->last_name) }}" class="w-full rounded-lg border-slate-300" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $customer->email) }}" class="w-full rounded-lg border-slate-300" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" class="w-full rounded-lg border-slate-300" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Address</label>
                    <textarea name="address" rows="2" class="w-full rounded-lg border-slate-300" required>{{ old('address', $customer->address) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Barangay</label>
                    <input type="text" name="barangay" value="{{ old('barangay', $customer->barangay) }}" class="w-full rounded-lg border-slate-300" required>
                </div>
                <button type="submit" class="inline-flex rounded-lg bg-brand-600 px-4 py-2 text-white hover:bg-brand-700">Save Changes</button>
            </form>
        </div>
    </div>
</div>
@endsection
