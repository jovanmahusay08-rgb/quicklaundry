@extends('admin.layouts.app')

@section('title', 'Announcements')
@section('pageTitle', 'Announcements')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <h2 class="font-semibold text-slate-800 mb-4">Create Announcement</h2>
        <form method="POST" action="{{ route('admin.announcements.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Title</label>
                <input type="text" name="title" class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Content</label>
                <textarea name="content" rows="4" class="w-full rounded-lg border border-slate-300 px-3 py-2"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Visible To</label>
                <select name="visible_to" class="rounded-lg border border-slate-300 px-3 py-2">
                    <option value="All">All</option>
                    <option value="Customers">Customers</option>
                    <option value="Staff">Staff</option>
                </select>
            </div>
            <button class="rounded-lg bg-brand-600 px-4 py-2 text-white">Publish</button>
        </form>
    </div>

    <div class="space-y-4">
        @forelse($announcements as $announcement)
            <div class="bg-white rounded-xl border border-slate-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold text-slate-800">{{ $announcement->title }}</h3>
                        <p class="text-sm text-slate-500">{{ $announcement->admin->full_name ?? 'Admin' }} · {{ $announcement->created_at->format('M d, Y') }}</p>
                    </div>
                    <form method="POST" action="{{ route('admin.announcements.destroy', $announcement) }}">
                        @csrf
                        @method('DELETE')
                        <button class="text-sm text-rose-600">Delete</button>
                    </form>
                </div>
                <p class="mt-3 text-sm text-slate-600">{{ $announcement->content }}</p>
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-slate-300 bg-white p-8 text-center text-slate-400">No announcements yet.</div>
        @endforelse
    </div>

    <div class="{{ $announcements->hasPages() ? 'flex justify-center' : '' }}">
        {{ $announcements->links() }}
    </div>
</div>
@endsection
