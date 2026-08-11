<!DOCTYPE html>
<html lang="en">
<head>
    @php($isAndroidApp = str_contains(request()->userAgent() ?? '', 'QuickWashCustomer/'))
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle ?? 'QuickWash' }} - QuickWash Express</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eff9ff', 100: '#dff2ff', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1', 900: '#0c4a6e',
                        },
                    },
                },
            },
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        html, body { width: 100%; min-height: 100%; overflow-x: clip; overflow-y: auto; }
        *, *::before, *::after { box-sizing: border-box; }
        input, select, textarea, button { max-width: 100%; }
        .customer-content, .customer-content > *, .customer-content section, .customer-content article { min-width: 0; }
        .customer-content img, .customer-content svg, .customer-content video { max-width: 100%; height: auto; }
        .customer-content table { max-width: 100%; }
        .customer-content td, .customer-content th { overflow-wrap: anywhere; }
        @media (max-width: 639px) {
            .customer-content .mobile-stack { align-items: stretch; flex-direction: column; }
            .customer-content table { font-size: .75rem; }
        }
    </style>
</head>
<body class="min-h-screen overflow-y-auto bg-slate-100 text-slate-800 {{ $isAndroidApp ? '' : 'pb-20 lg:pb-0' }}">
<div class="min-h-screen lg:flex lg:items-start">
    <aside class="hidden lg:sticky lg:top-0 lg:flex lg:h-screen lg:w-72 lg:shrink-0 bg-slate-950 text-white p-6 flex-col">
        <div class="mb-10">
            <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-3 mb-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-500/20 text-brand-400">
                    <i class="fas fa-tshirt"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold leading-tight">QuickWash</h1>
                    <p class="text-xs text-slate-400">Customer Portal</p>
                </div>
            </a>
        </div>

        <nav class="space-y-2 flex-1 lg:min-h-0 lg:overflow-y-auto">
            <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('customer.dashboard') ? 'bg-brand-500 text-white' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"></rect><path d="M7 4v16"></path><path d="M17 4v16"></path></svg>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('customer.bookings') }}" class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('customer.bookings*') ? 'bg-brand-500 text-white' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h16"></path><path d="M7 3v4"></path><path d="M17 3v4"></path><rect x="4" y="7" width="16" height="13" rx="2"></rect></svg>
                <span>Bookings</span>
            </a>
            <a href="{{ route('customer.tracking') }}" class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('customer.tracking') ? 'bg-brand-500 text-white' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="8"></circle><path d="M12 8v4l2 2"></path></svg>
                <span>Tracking</span>
            </a>
            <a href="{{ route('customer.loyalty') }}" class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('customer.loyalty') ? 'bg-brand-500 text-white' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l7 4v5c0 5-3.5 8-7 11-3.5-3-7-6-7-11V6l7-4z"></path></svg>
                <span>Loyalty</span>
            </a>
            <a href="{{ route('customer.notifications') }}" class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('customer.notifications') ? 'bg-brand-500 text-white' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 8a7 7 0 0 1 14 0c0 4.2 1.2 5.6 2 7H3c.8-1.4 2-2.8 2-7"></path><path d="M10 20a2 2 0 0 0 4 0"></path></svg>
                <span>Notifications</span>
            </a>
            <a href="{{ route('customer.profile') }}" class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('customer.profile') ? 'bg-brand-500 text-white' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"></path><path d="M4 20a8 8 0 0 1 16 0"></path></svg>
                <span>Profile</span>
            </a>
        </nav>

        <div class="p-2">
            <form method="POST" action="{{ route('customer.logout') }}">
                @csrf
                <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-slate-800 px-4 py-3 text-sm font-semibold text-red-400 transition hover:bg-slate-700 hover:text-red-300">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <div class="min-w-0 flex-1">
        <header class="sticky top-0 z-40 border-b border-slate-200 bg-white/95 shadow-sm backdrop-blur lg:static lg:bg-white/80 lg:shadow-none">
            <div class="px-4 py-3 sm:px-6 sm:py-4">
                <div class="flex items-center justify-between">
                    <div class="min-w-0 pr-2">
                        <p class="truncate text-sm text-slate-500">{{ $pageTitle ?? 'QuickWash' }}</p>
                        <h2 class="truncate text-xl font-semibold text-slate-800">{{ $pageTitle ?? 'QuickWash' }}</h2>
                    </div>
                    <div class="flex shrink-0 items-center gap-2 sm:gap-4">
                        @php($customer = auth('customer')->user())
                        @php($notifications = $customer ? \App\Models\AppNotification::where('user_type', 'Customer')->where('user_id', $customer->id)->latest('created_at')->limit(5)->get() : collect())
                        @php($unreadCount = $customer ? \App\Models\AppNotification::where('user_type', 'Customer')->where('user_id', $customer->id)->where('is_read', false)->count() : 0)

                        <div class="relative" x-data="{}">
                            <button id="notif-toggle" type="button" onclick="document.getElementById('notif-menu').classList.toggle('hidden')" class="relative inline-flex items-center rounded-md p-2 hover:bg-slate-100" aria-label="Notifications" aria-controls="notif-menu">
                                <svg class="h-6 w-6 text-slate-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0 1 18 14.158V11a6 6 0 1 0-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                                @if($unreadCount > 0)
                                    <span class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-0.5 text-xs font-semibold leading-none text-white bg-brand-600 rounded-full">{{ $unreadCount }}</span>
                                @endif
                            </button>

                            <div id="notif-menu" class="hidden absolute right-0 mt-2 w-[calc(100vw-2rem)] max-w-80 bg-white border border-slate-200 rounded-xl shadow-xl z-50">
                                <div class="p-3">
                                    <div class="flex items-center justify-between mb-2">
                                        <h3 class="text-sm font-semibold">Notifications</h3>
                                        <a href="{{ route('customer.notifications') }}" class="text-xs font-semibold text-brand-600 hover:underline">View all</a>
                                    </div>
                                    @if($notifications->isEmpty())
                                        <p class="text-sm text-slate-400">No recent notifications.</p>
                                    @else
                                        <ul class="space-y-2">
                                            @foreach($notifications as $n)
                                                <li class="flex items-start gap-2 rounded p-2 {{ $n->is_read ? 'bg-white' : 'bg-brand-50/60' }} hover:bg-slate-50">
                                                    <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full {{ $n->is_read ? 'bg-slate-300' : 'bg-brand-500' }}"></span>
                                                    <div class="min-w-0 flex-1">
                                                                <p class="text-sm font-medium text-slate-800">{{ $n->title }} @unless($n->is_read)<span class="ml-1 text-[10px] font-bold uppercase text-brand-600">New</span>@endunless</p>
                                                                <p class="text-xs text-slate-500">{{ \Illuminate\Support\Str::limit($n->message, 80) }}</p>
                                                                <p class="mt-1 text-xs text-slate-400">{{ $n->created_at?->diffForHumans() }}</p>
                                                    </div>
                                                    <a href="{{ route('customer.notifications.goto', $n->id) }}" class="shrink-0 rounded-md border border-brand-200 px-2 py-1 text-xs font-semibold text-brand-700 hover:bg-brand-50">View</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    <div class="mt-3 flex items-center justify-between border-t border-slate-100 pt-3">
                                        <form method="POST" action="{{ route('customer.notifications.mark-all-read') }}">
                                            @csrf
                                            <button type="submit" class="text-xs font-semibold text-slate-600 hover:text-brand-700 disabled:opacity-50" @disabled($unreadCount === 0)>Mark all as read</button>
                                        </form>
                                        <a href="{{ route('customer.notifications') }}" class="text-xs font-semibold text-brand-600 hover:underline">View all notifications</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($isAndroidApp)
                            <form method="POST" action="{{ route('customer.logout') }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-semibold text-red-600" aria-label="Log out">
                                    <i class="fas fa-right-from-bracket" aria-hidden="true"></i>
                                    <span class="hidden sm:inline">Log out</span>
                                </button>
                            </form>
                        @endif

                    </div>
                </div>
            </div>
        </header>

        <main class="customer-content min-w-0 p-4 sm:p-6">
            @yield('content')
        </main>
    </div>
</div>
@unless($isAndroidApp)
    <nav class="fixed inset-x-0 bottom-0 z-50 grid h-20 grid-cols-5 border-t border-slate-200 bg-white/95 px-2 pb-[env(safe-area-inset-bottom)] shadow-[0_-8px_24px_rgba(15,23,42,0.08)] backdrop-blur lg:hidden" aria-label="Customer navigation">
        @foreach([
            ['customer.dashboard', 'fas fa-house', 'Home'],
            ['customer.bookings', 'fas fa-calendar-check', 'Bookings'],
            ['customer.tracking', 'fas fa-location-dot', 'Tracking'],
            ['customer.loyalty', 'fas fa-gift', 'Rewards'],
            ['customer.profile', 'fas fa-user', 'Profile'],
        ] as [$route, $icon, $label])
            @php($active = request()->routeIs($route . '*'))
            <a href="{{ route($route) }}" class="flex min-w-0 flex-col items-center justify-center gap-1 rounded-xl px-1 text-[10px] font-bold {{ $active ? 'text-brand-600' : 'text-slate-500' }}" @if($active) aria-current="page" @endif>
                <span class="flex h-8 w-11 items-center justify-center rounded-xl {{ $active ? 'bg-brand-50' : '' }}"><i class="{{ $icon }} text-lg"></i></span>
                <span class="truncate">{{ $label }}</span>
            </a>
        @endforeach
    </nav>
@endunless
</body>
</html>
