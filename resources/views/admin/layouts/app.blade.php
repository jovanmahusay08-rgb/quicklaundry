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
    @include('admin.partials.sidebar')

    <div class="min-w-0 flex-1 flex flex-col lg:h-screen lg:overflow-y-auto">
        <header class="bg-white border-b border-slate-200 px-6 py-4 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-brand-600">Admin Portal</p>
                    <h1 class="text-xl font-bold text-slate-900">@yield('pageTitle', 'Dashboard')</h1>
                </div>
                <div class="flex items-center gap-3">
                    @include('staff.notification-bell')
                    <span class="text-sm text-slate-500">{{ auth('admin')->user()->full_name }}</span>
                </div>
            </div>
        </header>

        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
