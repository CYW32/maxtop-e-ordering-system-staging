<div class="py-4 flex flex-col h-full justify-between">

    <div class="space-y-4">
        <div class="px-6 flex items-center justify-center">
            <a href="{{ route('dashboard') }}">
                <x-application-logo class="block h-16 w-auto fill-current text-gray-800" />
            </a>
        </div>

        <div class="flex flex-col space-y-1 mt-8">

            {{-- Dashboard (For Everyone) --}}
            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="px-6 py-2 block border-l-4">
                {{ __('Dashboard') }}
            </x-nav-link>

            {{-- ADMIN / STAFF TOOLS --}}
            @hasanyrole('admin|cs_leader')
                <div class="px-6 mt-6 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                    Management
                </div>

                {{-- Feature: Activity Log (Admin Only) --}}
                @role('admin')
                    <x-nav-link :href="route('roles.matrix')" :active="request()->routeIs('roles.matrix')" class="px-6 py-2 block border-l-4">
                        {{ __('Feature Settings') }}
                    </x-nav-link>

                    <x-nav-link href="#" class="px-6 py-2 block border-l-4 border-transparent hover:bg-gray-50">
                        {{ __('Activity Log') }}
                    </x-nav-link>
                @endrole
            @endhasanyrole

            {{-- Orders (For Everyone) --}}
            <x-nav-link href="#" class="px-6 py-2 block border-l-4 border-transparent hover:bg-gray-50">
                {{ __('My Orders') }}
            </x-nav-link>

            {{-- Feature: User List (View Only) --}}
            @can('view_users')
                <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.index')" class="px-6 py-2 block border-l-4">
                    {{ __('User Management') }}
                </x-nav-link>
            @endcan

            @can('view_assigned_customers')
                <x-nav-link :href="route('users.assigned')" :active="request()->routeIs('users.assigned')">
                    {{ __('My Customers') }}
                </x-nav-link>
            @endcan

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
