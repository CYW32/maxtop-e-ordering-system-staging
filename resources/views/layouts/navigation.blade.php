<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>
            </div>

        </div>
    </div>

    <!-- Sidebar -->
    <div class="pt-4 pb-1 border-t border-gray-200">
        @role('customer')
            <x-nav-link :href="route('customer.products.index')" :active="request()->routeIs('customer.products.index')">
                {{ __('Order Products') }}
            </x-nav-link>

            <x-nav-link :href="route('customer.orders.index')" :active="request()->routeIs('customer.orders.index')">
                {{ __('My Order History') }}
            </x-nav-link>

            <x-nav-link :href="route('reservation.index')" :active="request()->routeIs('reservation.index')">
                {{ __('My Reservation Draft') }}
            </x-nav-link>
        @endrole
        @hasanyrole('admin|cs_leader|cs_staff')
            <div class="px-4 py-2 font-black text-xs uppercase text-gray-400 tracking-widest">{{ __('Office Operations') }}
            </div>

            @can('view_items')
                <x-nav-link :href="route('items.index')" :active="request()->routeIs('items.*')">
                    {{ __('Product Items') }}
                </x-nav-link>

                {{-- Fulfills Item Grouping Management Requirement --}}
                <x-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
                    {{ __('Product Categories') }}
                </x-nav-link>
            @endcan

            @can('view_catalogs')
                <x-nav-link :href="route('catalogs.index')" :active="request()->routeIs('catalogs.*')">
                    {{ __('Catalog Folders') }}
                </x-nav-link>
            @endcan

            @can('view_users')
                <div class="mt-4 mb-2 px-4 border-t border-gray-100 pt-4">
                    <p class="text-[9px] font-black uppercase text-gray-400 tracking-widest">{{ __('Accounts') }}</p>
                </div>
                <x-nav-link :href="route('companys.index')" :active="request()->routeIs('companys.*')">
                    {{ __('Business Entities') }}
                </x-nav-link>
                <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.index')">
                    {{ __('Login Credentials') }}
                </x-nav-link>
            @endcan

            @can('view_assigned_customers')
                <x-nav-link :href="route('users.assigned')" :active="request()->routeIs('users.assigned')">
                    {{ __('My Customers') }}
                </x-nav-link>
            @endcan

            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Order Overview') }}
            </x-nav-link>
        @endhasanyrole

        @role('admin')
            <div class="pt-4 pb-1 border-t border-gray-100">
                <div class="px-4 text-[10px] font-black uppercase text-gray-400 tracking-widest mb-4">
                    {{ __('System Control') }}
                </div>

                <x-nav-link :href="route('admin.roles.manage.index')" :active="request()->routeIs('admin.roles.manage.*')">
                    {{ __('Role Registry') }}
                </x-nav-link>

                <x-nav-link :href="route('admin.roles.matrix')" :active="request()->routeIs('admin.roles.matrix')">
                    {{ __('Feature Settings') }}
                </x-nav-link>

                {{-- ARCHITECTURE FIX: Updated to match new 'admin.activity.index' name --}}
                <x-nav-link :href="route('admin.activity.index')" :active="request()->routeIs('admin.activity.*')">
                    {{ __('Activity Log') }}
                </x-nav-link>
            </div>
        @endrole
    </div>

    <!-- Responsive Settings Options -->
    <div class="pt-4 pb-1 border-t border-gray-200">
        {{-- Consolidated Identity & Profile Link: Fulfills standard UI/UX consolidation --}}
        <a href="{{ route('profile.edit') }}"
            class="block px-4 py-2 hover:bg-gray-100 transition duration-150 ease-in-out">
            <div class="text-sm font-medium text-gray-800">{{ Auth::user()->name }}</div>
            <div class="text-xs text-gray-500">{{ Auth::user()->email }}</div>
        </a>

        <div class="mt-3 space-y-1">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-responsive-nav-link :href="route('logout')"
                    onclick="event.preventDefault();
                                    this.closest('form').submit();">
                    {{ __('Log Out') }}
                </x-responsive-nav-link>
            </form>
        </div>
    </div>
</nav>
