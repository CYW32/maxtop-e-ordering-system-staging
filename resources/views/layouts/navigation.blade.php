<div class="flex flex-col h-full overflow-y-auto overflow-x-hidden custom-scrollbar">
    <!-- Header: Logo & Hamburger Menu -->
    <div class="py-6 flex items-center h-24 transition-all duration-300 border-b border-white/5"
        :class="sidebarOpen ? 'px-6 justify-between' : 'px-0 justify-center'">

        <!-- Logo -->
        <a href="{{ route('dashboard') }}" x-show="sidebarOpen" x-transition.opacity>
            <img src="https://maxtop.com.my/wp-content/themes/maxtop/assets/img/logo.svg" alt="Maxtop Logo"
                class="h-10 w-auto object-contain filter brightness-0 invert" />
        </a>

        <!-- Hamburger Menu Button -->
        <button @click="sidebarOpen = !sidebarOpen"
            class="text-white/80 hover:text-white focus:outline-none p-2 rounded-xl hover:bg-white/10 transition-colors">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                </path>
            </svg>
        </button>
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 py-6 space-y-6 px-4">

        @role('customer')
            <div x-cloak x-show="sidebarOpen" x-transition.opacity class="pb-4 border-b border-white/5 last:border-none">
                <div class="text-[10px] font-black uppercase text-white/50 tracking-widest mb-3 px-2">
                    {{ __('Customer Portal') }}</div>
                <ul class="space-y-1.5">
                    <li><x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">{{ __('Dashboard') }}</x-nav-link></li>
                    <li><x-nav-link :href="route('customer.products.index')" :active="request()->routeIs('customer.products.*')">{{ __('Order Products') }}</x-nav-link></li>
                    <li><x-nav-link :href="route('customer.orders.index')" :active="request()->routeIs('customer.orders.*')">{{ __('My Order History') }}</x-nav-link></li>
                    <li><x-nav-link :href="route('reservation.index')" :active="request()->routeIs('reservation.*')">{{ __('My Reservation Draft') }}</x-nav-link></li>
                </ul>
            </div>
        @endrole

        @hasanyrole('admin|cs_leader|cs_staff')
            <div x-cloak x-show="sidebarOpen" x-transition.opacity class="pb-4 border-b border-white/5 last:border-none">
                <div class="text-[10px] font-black uppercase text-white/50 tracking-widest mb-3 px-2">
                    {{ __('Office Operations') }}</div>
                <ul class="space-y-1.5">
                    @can('view_items')
                        <li><x-nav-link :href="route('items.index')" :active="request()->routeIs('items.*')">{{ __('Product Items') }}</x-nav-link></li>
                        <li><x-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">{{ __('Product Categories') }}</x-nav-link></li>
                    @endcan

                    @can('view_catalogs')
                        <li><x-nav-link :href="route('catalogs.index')" :active="request()->routeIs('catalogs.*')">{{ __('Catalog Folders') }}</x-nav-link></li>
                    @endcan

                    @can('view_business_entities')
                        <li><x-nav-link :href="route('companys.index')" :active="request()->routeIs('companys.*')">{{ __('Business Entities') }}</x-nav-link></li>
                    @endcan

                    @can('view_login_credentials')
                        <li><x-nav-link :href="route('users.index')" :active="request()->routeIs('users.index')">{{ __('Login Credentials') }}</x-nav-link></li>
                    @endcan

                    <!-- Unified Dashboard Overview -->
                    <li><x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">{{ __('Order Overview') }}</x-nav-link></li>
                </ul>
            </div>
        @endhasanyrole

        @role('admin')
            <div x-cloak x-show="sidebarOpen" x-transition.opacity class="pb-4 border-b border-white/5 last:border-none">
                <div class="text-[10px] font-black uppercase text-white/50 tracking-widest mb-3 px-2">
                    {{ __('System Control') }}</div>
                <ul class="space-y-1.5">
                    <li><x-nav-link :href="route('admin.roles.manage.index')" :active="request()->routeIs('admin.roles.manage.*')">{{ __('Role Registry') }}</x-nav-link></li>
                    <li><x-nav-link :href="route('admin.roles.matrix')" :active="request()->routeIs('admin.roles.matrix')">{{ __('Feature Settings') }}</x-nav-link></li>
                    <li><x-nav-link :href="route('admin.activity.index')" :active="request()->routeIs('admin.activity.*')">{{ __('Activity Log') }}</x-nav-link></li>
                </ul>
            </div>
        @endrole
    </nav>

    <!-- Footer: Profile & Logout -->
    <div class="p-4 mt-auto border-t border-white/5 flex flex-col"
        :class="sidebarOpen ? 'items-start' : 'items-center'">

        <!-- Profile Section -->
        <a href="{{ route('profile.edit') }}" class="block mb-4 group w-full transition-all"
            :class="sidebarOpen ? 'text-left px-2' : 'text-center'">
            <div x-show="sidebarOpen">
                <div class="text-sm font-bold text-white group-hover:text-gray-200 truncate">{{ Auth::user()->name }}
                </div>
                <div class="text-xs text-white/50 truncate">{{ Auth::user()->email }}</div>
            </div>
            <!-- Minimized Avatar -->
            <div x-cloak x-show="!sidebarOpen"
                class="w-10 h-10 mx-auto bg-white/10 group-hover:bg-white/20 transition-colors rounded-full text-white flex items-center justify-center text-lg font-black shadow-sm"
                title="{{ Auth::user()->name }}">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
        </a>

        <!-- Logout Section -->
        <form method="POST" action="{{ route('logout') }}" class="w-full flex"
            :class="sidebarOpen ? 'justify-start' : 'justify-center'">
            @csrf
            <button type="submit" class="flex items-center transition-colors rounded-xl"
                :class="sidebarOpen ? 'px-2 py-2 text-sm font-bold text-white hover:text-gray-200 w-full' :
                    'p-2.5 hover:bg-white/10 text-white/70 hover:text-white'"
                title="{{ __('Log Out') }}">
                <span x-show="sidebarOpen">{{ __('Log Out') }}</span>
                <!-- Logout Icon -->
                <svg x-cloak x-show="!sidebarOpen" class="w-6 h-6" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
            </button>
        </form>
    </div>
</div>
