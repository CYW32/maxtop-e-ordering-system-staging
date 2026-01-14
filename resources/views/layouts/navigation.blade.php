<div class="py-4 flex flex-col h-full justify-between">

    <div class="space-y-4">
        <div class="px-6 flex items-center justify-center">
            <a href="{{ route('dashboard') }}">
                <x-application-logo class="block h-16 w-auto fill-current text-gray-800" />
            </a>
        </div>

        <!-- Primary Navigation Menu -->
        <div class="flex flex-col h-full py-4">
            <!-- Section 1: General (Everyone) -->
            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-nav-link>

            <x-nav-link :href="route('dashboard')" :active="false">
                {{ __('My Orders') }}
            </x-nav-link>

            @role('customer')
                <x-nav-link :href="route('customer.products.index')" :active="request()->routeIs('customer.products.*')">
                    {{ __('Order Products') }}
                </x-nav-link>
            @endrole

            <!-- Section 2: Staff Operations (Admin, CS Leader, CS Staff) -->
            @hasanyrole('admin|cs_leader|cs_staff')
                <div class="pt-4 pb-2 px-6 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                    {{ __('Office Operations') }}
                </div>

                <!-- Product Management -->
                @can('view_items')
                    <x-nav-link :href="route('items.index')" :active="request()->routeIs('items.*')">
                        {{ __('Product Items') }}
                    </x-nav-link>
                @endcan

                <!-- Catalog Folders -->
                @can('view_catalogs')
                    <x-nav-link :href="route('catalogs.index')" :active="request()->routeIs('catalogs.*')">
                        {{ __('Catalog Folders') }}
                    </x-nav-link>
                @endcan

                <!-- User/Customer Management -->
                @can('view_users')
                    <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.index')">
                        {{ __('User Management') }}
                    </x-nav-link>
                @endcan

                @can('view_assigned_customers')
                    <x-nav-link :href="route('users.assigned')" :active="request()->routeIs('users.assigned')">
                        {{ __('My Customers') }}
                    </x-nav-link>
                @endcan
            @endhasanyrole

            <!-- Section 3: System Controls (Strict Admin Only) -->
            @role('admin')
                <div class="pt-4 pb-2 px-6 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                    {{ __('System Control') }}
                </div>

                <x-nav-link :href="route('roles.matrix')" :active="request()->routeIs('roles.matrix')">
                    {{ __('Feature Settings') }}
                </x-nav-link>

                <x-nav-link :href="route('roles.activity.index')" :active="request()->routeIs('roles.activity.index')">
                    {{ __('Activity Log') }}
                </x-nav-link>
            @endrole

        </div>
    </div>

    <div class="px-6 border-t border-gray-200 pt-4">
        <div class="text-sm font-medium text-gray-800">{{ Auth::user()->name }}</div>
        <div class="text-xs text-gray-500">{{ Auth::user()->email }}</div>

        <form method="POST" action="{{ route('logout') }}" class="mt-3">
            @csrf
            <button type="submit" class="text-sm text-red-600 hover:text-red-900">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</div>
