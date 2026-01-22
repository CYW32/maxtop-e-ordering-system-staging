<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Order Management Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Section 1: Unassigned Orders (Claiming Queue) [3] --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-orange-500">
                <h3 class="font-black text-lg text-orange-700 uppercase tracking-wider mb-4">
                    {{ __('⚠️ Claiming Queue (Unassigned Orders)') }}
                </h3>

                @if ($unassigned->isEmpty())
                    <p class="text-gray-500 italic">{{ __('No new orders awaiting assignment.') }}</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 border-b">
                                    <th class="px-4 py-3 text-xs font-bold uppercase text-gray-600">{{ __('Order #') }}
                                    </th>
                                    <th class="px-4 py-3 text-xs font-bold uppercase text-gray-600">
                                        {{ __('Company / Customer') }}</th>
                                    <th class="px-4 py-3 text-xs font-bold uppercase text-gray-600">
                                        {{ __('Date Submitted') }}</th>
                                    <th class="px-4 py-3 text-xs font-bold uppercase text-gray-600 text-right">
                                        {{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($unassigned as $order)
                                    <tr class="border-b hover:bg-orange-50 transition-colors">
                                        <td class="px-4 py-4 font-mono font-bold text-sm text-blue-600">
                                            {{ $order->order_number }}</td>
                                        <td class="px-4 py-4">
                                            <div class="font-bold text-gray-900">
                                                {{ $order->user->details->company_name ?? $order->user->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $order->user->email }}</div>
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-600">
                                            {{ $order->created_at->format('Y-m-d H:i') }}</td>
                                        <td class="px-4 py-4 text-right">
                                            <form action="{{ route('office.orders.claim', $order) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg text-xs font-black uppercase transition shadow-md">
                                                    {{ __('Claim Order') }}
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Section 2: My Active Orders (Claimed) [5] --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-black text-lg text-gray-800 uppercase tracking-wider mb-4">
                    {{ __('📋 My Active Orders') }}
                </h3>

                @if ($myOrders->isEmpty())
                    <p class="text-gray-500 italic">{{ __('You are not currently handling any active orders.') }}</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 border-b">
                                    <th class="px-4 py-3 text-xs font-bold uppercase text-gray-600">{{ __('Order #') }}
                                    </th>
                                    <th class="px-4 py-3 text-xs font-bold uppercase text-gray-600">
                                        {{ __('Customer') }}</th>
                                    <th class="px-4 py-3 text-xs font-bold uppercase text-gray-600">{{ __('Status') }}
                                    </th>
                                    <th class="px-4 py-3 text-xs font-bold uppercase text-gray-600 text-right">
                                        {{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($myOrders as $order)
                                    <tr class="border-b hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-4 font-mono text-sm">{{ $order->order_number }}</td>
                                        <td class="px-4 py-4 text-sm">
                                            <span class="font-bold text-gray-800">{{ $order->user->name }}</span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span
                                                class="px-2 py-1 rounded-full text-[10px] font-black uppercase shadow-sm
                                                {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800 border border-yellow-300' : 'bg-blue-100 text-blue-800 border border-blue-300' }}">
                                                {{ $order->status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-right">
                                            <a href="{{ route('office.orders.show', $order) }}"
                                                class="inline-block bg-blue-50 text-blue-700 hover:bg-blue-700 hover:text-white px-4 py-2 rounded-lg text-xs font-black uppercase transition border border-blue-200">
                                                {{ __('View & Process') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $myOrders->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
