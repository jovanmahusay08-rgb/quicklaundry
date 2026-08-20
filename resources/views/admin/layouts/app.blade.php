<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Portal') - QuickWash Express</title>
    <link rel="icon" type="image/png" href="{{ asset('images/quickwash-logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eff9ff',
                            100: '#dff2ff',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            900: '#0c4a6e',
                        },
                    },
                },
            },
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 lg:h-screen lg:overflow-hidden">
<div class="min-h-screen flex lg:h-screen">
    <div id="admin-sidebar-overlay" class="fixed inset-0 z-40 hidden bg-slate-950/50 backdrop-blur-sm lg:hidden"></div>
    @include('admin.partials.sidebar')

    <div class="min-w-0 flex-1 flex flex-col lg:h-screen lg:overflow-y-auto">
        <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 px-4 py-3 shadow-sm backdrop-blur sm:px-6 sm:py-4">
            <div class="flex items-center justify-between">
                <div class="flex min-w-0 items-center gap-3">
                    <button id="admin-sidebar-open" type="button" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 lg:hidden" aria-label="Open navigation">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="min-w-0">
                    <p class="text-sm font-semibold uppercase tracking-wide text-brand-600">Admin Portal</p>
                    <h1 class="truncate text-lg font-bold text-slate-900 sm:text-xl">@yield('pageTitle', 'Dashboard')</h1>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    @include('staff.notification-bell')
                    <span class="hidden text-sm text-slate-500 sm:inline">{{ auth('admin')->user()->full_name }}</span>
                </div>
            </div>
        </header>

        <main class="flex-1 p-4 sm:p-6">
            @yield('content')
        </main>
    </div>
</div>
<script>
    (() => {
        const sidebar = document.getElementById('admin-sidebar');
        const overlay = document.getElementById('admin-sidebar-overlay');
        const openButton = document.getElementById('admin-sidebar-open');
        const closeButton = document.getElementById('admin-sidebar-close');

        const setSidebarOpen = (isOpen) => {
            sidebar?.classList.toggle('-translate-x-full', !isOpen);
            overlay?.classList.toggle('hidden', !isOpen);
            document.body.classList.toggle('overflow-hidden', isOpen && window.innerWidth < 1024);
        };

        openButton?.addEventListener('click', () => setSidebarOpen(true));
        closeButton?.addEventListener('click', () => setSidebarOpen(false));
        overlay?.addEventListener('click', () => setSidebarOpen(false));
        window.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') setSidebarOpen(false);
        });
    })();
</script>
</body>
</html>
