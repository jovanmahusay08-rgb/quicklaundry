<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle ?? 'Staff Portal' }} - QuickWash Express</title>
    <link rel="icon" type="image/png" href="{{ asset('images/quickwash-logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eff9ff', 100: '#dff2ff', 500: '#0ea5e9',
                            600: '#0284c7', 700: '#0369a1', 900: '#0c4a6e',
                        },
                    },
                },
            },
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body class="min-h-screen bg-slate-100 text-slate-800 lg:h-screen lg:overflow-hidden">
<div class="min-h-screen lg:flex lg:h-screen">
    <aside class="flex w-full flex-col bg-slate-900 p-6 text-white lg:h-screen lg:w-72 lg:shrink-0">
        <div class="mb-8 border-b border-slate-800 pb-6">
            <a href="{{ route('staff.dashboard') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/quickwash-logo.png') }}" alt="QuickWash logo" class="h-12 w-12 rounded-full object-contain">
                <div>
                    <p class="font-semibold">QuickWash</p>
                    <p class="text-xs text-slate-400">Staff Portal</p>
                </div>
            </a>
        </div>

        <nav class="space-y-1 lg:min-h-0 lg:flex-1 lg:overflow-y-auto">
            <a href="{{ route('staff.dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('staff.dashboard') ? 'bg-brand-500 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-home w-5"></i> Dashboard
            </a>
            <a href="{{ route('staff.bookings') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('staff.bookings*') ? 'bg-brand-500 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-shopping-bag w-5"></i> Bookings
            </a>
            <a href="{{ route('staff.pickups') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('staff.pickups') ? 'bg-brand-500 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-arrow-up w-5"></i> Pickups
            </a>
            <a href="{{ route('staff.deliveries') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('staff.deliveries') ? 'bg-brand-500 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-arrow-down w-5"></i> Deliveries
            </a>
            <a href="{{ route('staff.notifications') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('staff.notifications*') ? 'bg-brand-500 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-bell w-5"></i> Notifications
            </a>
            <a href="{{ route('staff.profile') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('staff.profile') ? 'bg-brand-500 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-user w-5"></i> Profile
            </a>
        </nav>

        <div class="mt-auto p-4">
            <form method="POST" action="{{ route('staff.logout') }}">
                @csrf
                <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-slate-800 px-4 py-3 text-sm font-semibold text-red-500 transition hover:bg-slate-700">
                    <i class="fas fa-sign-out-alt text-lg"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <div class="min-w-0 flex-1 lg:h-screen lg:overflow-y-auto">
        <header class="border-b border-slate-200 bg-white/80 backdrop-blur">
            <div class="flex items-center justify-between px-6 py-4">
                <div>
                    <p class="text-sm text-slate-500">{{ $pageTitle ?? 'Staff Portal' }}</p>
                    <h1 class="text-xl font-semibold text-slate-800">{{ $pageTitle ?? 'Staff Portal' }}</h1>
                </div>
                <div class="flex items-center gap-3 rounded-full bg-slate-100 px-3 py-2">
                    @include('staff.notification-bell', ['staff' => $staff ?? auth('staff')->user(), 'admin' => auth('admin')->user()])
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-600 text-sm font-semibold text-white">
                        {{ strtoupper(substr($staff->first_name, 0, 1)) }}
                    </div>
                    <div class="text-sm">
                        <p class="font-medium text-slate-800">{{ $staff->full_name }}</p>
                        <p class="text-xs text-slate-500">{{ $staff->role }}</p>
                    </div>
                </div>
            </div>
        </header>

        <main class="p-6">
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
