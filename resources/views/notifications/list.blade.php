@if(session('success'))
    <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
@endif

<div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Notifications</h2>
        <p class="mt-1 text-sm text-slate-500">{{ $notifications->where('is_read', false)->count() }} unread notification(s)</p>
    </div>
    <form method="POST" action="{{ route($markAllRoute) }}">
        @csrf
        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg border border-brand-200 bg-brand-50 px-4 py-2 text-sm font-semibold text-brand-700 transition hover:bg-brand-100 disabled:cursor-not-allowed disabled:opacity-50" @disabled($notifications->where('is_read', false)->isEmpty())>
            <i class="fas fa-check-double" aria-hidden="true"></i> Mark all as read
        </button>
    </form>
</div>

<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    @forelse ($notifications as $notification)
        <div class="flex flex-col gap-4 border-b border-slate-100 p-5 last:border-b-0 sm:flex-row sm:items-center sm:justify-between {{ $notification->is_read ? 'bg-white' : 'bg-brand-50/60' }}">
            <div class="flex min-w-0 items-start gap-3">
                <span class="mt-2 h-2.5 w-2.5 shrink-0 rounded-full {{ $notification->is_read ? 'bg-slate-300' : 'bg-brand-500 ring-4 ring-brand-100' }}" aria-label="{{ $notification->is_read ? 'Read' : 'Unread' }}"></span>
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <p class="font-semibold text-slate-800">{{ $notification->title }}</p>
                        @unless($notification->is_read)
                            <span class="rounded-full bg-brand-100 px-2 py-0.5 text-[11px] font-bold uppercase tracking-wide text-brand-700">New</span>
                        @endunless
                    </div>
                    <p class="mt-1 text-sm text-slate-600">{{ $notification->message }}</p>
                    <p class="mt-2 text-xs text-slate-400">{{ $notification->created_at?->format('M d, Y h:i A') }}</p>
                </div>
            </div>
            <a href="{{ route($gotoRoute, $notification->id) }}" class="inline-flex shrink-0 items-center justify-center gap-2 self-end rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-brand-700 sm:self-center">
                View <i class="fas fa-arrow-right text-xs" aria-hidden="true"></i>
            </a>
        </div>
    @empty
        <div class="px-6 py-12 text-center">
            <i class="fas fa-bell-slash text-3xl text-slate-300" aria-hidden="true"></i>
            <p class="mt-3 text-sm text-slate-500">You don't have any notifications yet.</p>
        </div>
    @endforelse
</div>
