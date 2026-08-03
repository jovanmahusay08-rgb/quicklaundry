@extends('customer.layout')

@section('content')
@php($pageTitle = 'Loyalty')
<div class="py-2">
    <div class="max-w-4xl mx-auto">
            <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Loyalty Rewards</h1>
            </div>
            <x-back-button :href="route('customer.dashboard')">Back</x-back-button>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
            <div class="grid md:grid-cols-2 gap-4">
                <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-5">
                    <p class="text-sm text-emerald-700">Available Points</p>
                    <p class="text-3xl font-bold text-emerald-800">{{ $loyalty->available_points ?? 0 }}</p>
                </div>
                <div class="rounded-xl bg-slate-50 border border-slate-200 p-5">
                    <p class="text-sm text-slate-500">Total Points Earned</p>
                    <p class="text-3xl font-bold text-slate-800">{{ $loyalty->total_points ?? 0 }}</p>
                </div>
            </div>

            <div class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-5 text-sm text-slate-600">
                <p>Earn points on every booking and enjoy discounts on future orders.</p>
            </div>
        </div>
    </div>
</div>
@endsection
