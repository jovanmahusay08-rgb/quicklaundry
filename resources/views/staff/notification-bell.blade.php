@php
    $notifications = collect();
    $unreadCount = 0;
    $notificationRoute = null;
    $isStaffUser = isset($staff) && $staff instanceof \App\Models\Staff;
    $isAdminUser = isset($admin) && $admin instanceof \App\Models\Admin;

    if ($isAdminUser || auth('admin')->check()) {
        $admin = $admin ?? auth('admin')->user();
        $notifications = \App\Models\AppNotification::where('user_type', 'Admin')
            ->where(fn ($query) => $query->where('user_id', $admin->id)->orWhereNull('user_id'))
            ->latest()
            ->limit(10)
            ->get();
        $unreadCount = \App\Models\AppNotification::where('user_type', 'Admin')
            ->where(fn ($query) => $query->where('user_id', $admin->id)->orWhereNull('user_id'))
            ->where('is_read', false)
            ->count();
        $notificationRoute = 'admin.notifications.goto';
        $notificationsIndexRoute = 'admin.notifications';
        $markAllRoute = 'admin.notifications.mark-all-read';
    } elseif ($isStaffUser || auth('staff')->check()) {
        $staff = $staff ?? auth('staff')->user();
        $notifications = \App\Models\AppNotification::where('user_type', 'Staff')
            ->where('user_id', $staff->id)
            ->latest()
            ->limit(10)
            ->get();
        $unreadCount = \App\Models\AppNotification::where('user_type', 'Staff')
            ->where('user_id', $staff->id)
            ->where('is_read', false)
            ->count();
        $notificationRoute = 'staff.notifications.goto';
        $notificationsIndexRoute = 'staff.notifications';
        $markAllRoute = 'staff.notifications.mark-all-read';
    }
@endphp

@if ($isStaffUser || $isAdminUser || auth('admin')->check() || auth('staff')->check())
    <div class="relative">
        <button type="button" onclick="toggleNotificationPanel()" class="relative rounded-full p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700" aria-label="Notifications">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5m6 0a3 3 0 1 1-6 0" />
            </svg>
            @if ($unreadCount > 0)
                <span class="absolute -right-0.5 -top-0.5 flex min-h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-semibold text-white">
                    {{ $unreadCount }}
                </span>
            @endif
        </button>

        <div id="notification-panel" class="absolute right-0 top-full mt-3 hidden w-96 max-w-[90vw] overflow-hidden rounded-xl border border-slate-200 bg-white shadow-2xl">
            <div class="border-b border-slate-100 px-4 py-3">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-slate-800">Notifications</h3>
                    <a href="{{ route($notificationsIndexRoute) }}" class="text-xs font-semibold text-brand-600 hover:underline">View all</a>
                </div>
                <p class="mt-1 text-xs text-slate-500">{{ $unreadCount }} unread</p>
            </div>

            <div class="max-h-80 overflow-y-auto">
                @forelse ($notifications as $notification)
                <div class="flex items-start gap-3 border-b border-slate-100 px-4 py-3 transition {{ $notification->is_read ? 'bg-white' : 'bg-brand-50/50' }} hover:bg-slate-50">
                        <div class="flex items-start gap-3">
                            <div class="mt-1.5 h-2.5 w-2.5 rounded-full {{ $notification->is_read ? 'bg-slate-300' : 'bg-brand-500' }}"></div>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-slate-800">{{ $notification->title }} @unless($notification->is_read)<span class="ml-1 text-[10px] font-bold uppercase text-brand-600">New</span>@endunless</p>
                                <p class="mt-1 text-sm text-slate-600">{{ $notification->message }}</p>
                                <p class="mt-2 text-xs text-slate-400">{{ $notification->created_at ? $notification->created_at->diffForHumans() : '' }}</p>
                            </div>
                        </div>
                        <a href="{{ route($notificationRoute, $notification->id) }}" class="ml-auto shrink-0 rounded-md border border-brand-200 px-2.5 py-1 text-xs font-semibold text-brand-700 hover:bg-brand-50">View</a>
                    </div>
                @empty
                    <div class="px-4 py-6 text-center text-sm text-slate-500">No notifications yet.</div>
                @endforelse
            </div>
            <div class="flex items-center justify-between gap-3 border-t border-slate-100 bg-slate-50 px-4 py-3">
                <form method="POST" action="{{ route($markAllRoute) }}">
                    @csrf
                    <button type="submit" class="text-xs font-semibold text-slate-600 hover:text-brand-700 disabled:opacity-50" @disabled($unreadCount === 0)>Mark all as read</button>
                </form>
                <a href="{{ route($notificationsIndexRoute) }}" class="text-xs font-semibold text-brand-600 hover:underline">View all notifications</a>
            </div>
        </div>
    </div>

    <script>
        function toggleNotificationPanel() {
            const panel = document.getElementById('notification-panel');
            if (panel) {
                panel.classList.toggle('hidden');
            }
        }

        document.addEventListener('click', function (event) {
            const panel = document.getElementById('notification-panel');
            if (!panel) {
                return;
            }

            const button = event.target.closest('button[onclick="toggleNotificationPanel()"]');
            const clickedInsidePanel = event.target.closest('#notification-panel');

            if (!button && !clickedInsidePanel) {
                panel.classList.add('hidden');
            }
        });
    </script>
@endif
