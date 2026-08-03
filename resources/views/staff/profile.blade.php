@extends('staff.layout')

@section('content')
@php($pageTitle = 'My Profile')
<div class="mx-auto max-w-4xl rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <h2 class="mb-4 text-xl font-semibold text-slate-800">Staff Profile</h2>
    <div class="grid gap-4 text-sm text-slate-700 md:grid-cols-2">
        <div>
            <p class="font-semibold text-slate-800">Name</p>
            <p>{{ $staff->full_name }}</p>
        </div>
        <div>
            <p class="font-semibold text-slate-800">Role</p>
            <p>{{ $staff->role }}</p>
        </div>
        <div>
            <p class="font-semibold text-slate-800">Email</p>
            <p>{{ $staff->email }}</p>
        </div>
        <div>
            <p class="font-semibold text-slate-800">Phone</p>
            <p>{{ $staff->phone }}</p>
        </div>
    </div>
</div>
@endsection
