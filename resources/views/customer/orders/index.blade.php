<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Order History') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if ($orders->isEmpty())
                    <div class="text-center py-8">
                        <p class="text-gray-500 italic">{{ __('You have no past orders yet.') }}</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 border-b">
                                    <th class="px-4 py-3 text-xs font-bold uppercase text-gray-600">{{ __('Order #') }}
                                    </th>
                                    <th class="px-4 py-3 text-xs font-bold uppercase text-gray-600">
                                        {{ __('Date Submitted') }}</th>
                                    <th class="px-4 py-3 text-xs font-bold uppercase text-gray-600">{{ __('Status') }}
                                    </th>
                                    <th class="px-4 py-3 text-xs font-bold uppercase text-gray-600 text-right">
                                        {{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    <tr class="border-b hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-4 font-mono font-bold text-sm text-blue-600">
                                            {{ $order->order_number ?? 'Processing...' }}
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-600">
                                            {{ $order->created_at->format('Y-m-d H:i') }}
                                        </td>
                                        <td class="px-4 py-4">
                                            <span
                                                class="px-2 py-1 rounded-full text-[10px] font-black uppercase shadow-sm
                                                @if ($order->status === 'pending') bg-yellow-100 text-yellow-800 border border-yellow-300
                                                @elseif($order->status === 'approved') bg-green-100 text-green-800 border border-green-300
                                                @elseif($order->status === 'cancelled') bg-red-100 text-red-800 border border-red-300
                                                @else bg-blue-100 text-blue-800 border border-blue-300 @endif">
                                                {{ $order->status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-right">
                                            <a href="{{ route('customer.orders.show', $order) }}"
                                                class="text-blue-600 font-bold hover:underline text-sm">
                                                {{ __('View Details') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
