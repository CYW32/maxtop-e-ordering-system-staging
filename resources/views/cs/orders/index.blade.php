<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('On-going Orders (My Active Tasks)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if ($myOrders->isEmpty())
                    <p class="text-gray-500 italic">{{ __('You are not currently handling any active orders.') }}</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 border-b">
                                    <th class="px-4 py-3 text-xs font-bold uppercase text-gray-600">{{ __('Order #') }}
                                    </th>
                                    <th class="px-4 py-3 text-xs font-bold uppercase text-gray-600">{{ __('Customer') }}
                                    </th>
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
                                        <td class="px-4 py-4 text-sm font-bold text-gray-800">{{ $order->user->name }}
                                        </td>
                                        <td class="px-4 py-4">
                                            <span
                                                class="px-2 py-1 rounded-full text-[10px] font-black uppercase shadow-sm
                                                {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800 border border-yellow-300' : 'bg-blue-100 text-blue-800 border border-blue-300' }}">
                                                {{ $order->status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-right">
                                            @if (is_null($order->handler_id))
                                                <form action="{{ route('office.orders.claim', $order) }}"
                                                    method="POST">
                                                    @csrf
                                                    <button type="submit"
                                                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-xs font-black uppercase shadow-md">
                                                        {{ __('Claim My Customer Order') }}
                                                    </button>
                                                </form>
                                            @else
                                                <a href="{{ route('office.orders.show', $order) }}"
                                                    class="inline-block bg-blue-50 text-blue-700 hover:bg-blue-700 hover:text-white px-4 py-2 rounded-lg text-xs font-black uppercase transition border border-blue-200">
                                                    {{ __('View & Process') }}
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $myOrders->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
