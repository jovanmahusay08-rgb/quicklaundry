<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'QuickWash Express' }}</title>
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
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col">
    @yield('content')
</body>
</html>
