<aside class="flex h-screen w-72 shrink-0 flex-col bg-slate-900 text-slate-100">
    <div class="px-6 py-6 border-b border-slate-800">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-brand-500/20 text-brand-400 flex items-center justify-center">
                <i class="fas fa-tshirt"></i>
            </div>
            <div>
                <p class="font-semibold">QuickWash</p>
                <p class="text-xs text-slate-400">Admin Center</p>
            </div>
        </a>
    </div>

    <nav class="flex-1 overflow-y-auto p-4 space-y-1">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('admin.dashboard') ? 'bg-brand-500 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-home w-5"></i> Dashboard
        </a>
        <a href="{{ route('admin.bookings') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('admin.bookings*') ? 'bg-brand-500 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-shopping-bag w-5"></i> Bookings
        </a>
        <a href="{{ route('admin.customers') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('admin.customers*') ? 'bg-brand-500 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-users w-5"></i> Customers
        </a>
        <a href="{{ route('admin.staff') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('admin.staff') ? 'bg-brand-500 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-id-card w-5"></i> Staff
        </a>
        <a href="{{ route('admin.assign-staff') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('admin.assign-staff*') ? 'bg-brand-500 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-user-check w-5"></i> Assign Staff
        </a>
        <a href="{{ route('admin.services') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('admin.services*') ? 'bg-brand-500 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-soap w-5"></i> Services
        </a>
        <a href="{{ route('admin.reports') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('admin.reports') ? 'bg-brand-500 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-chart-bar w-5"></i> Reports
        </a>
        <a href="{{ route('admin.payments') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('admin.payments') ? 'bg-brand-500 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-wallet w-5"></i> Payments
        </a>
        <a href="{{ route('admin.announcements') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('admin.announcements*') ? 'bg-brand-500 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-bullhorn w-5"></i> Announcements
        </a>
        <a href="{{ route('admin.notifications') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('admin.notifications*') ? 'bg-brand-500 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-bell w-5"></i> Notifications
        </a>
        <a href="{{ route('admin.settings') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('admin.settings*') ? 'bg-brand-500 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i class="fas fa-cog w-5"></i> Settings
        </a>
    </nav>

    <div class="border-t border-slate-800 p-4">
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-slate-800 px-4 py-3 text-sm font-semibold text-red-500 transition hover:bg-slate-700">
                <i class="fas fa-sign-out-alt text-lg"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>
