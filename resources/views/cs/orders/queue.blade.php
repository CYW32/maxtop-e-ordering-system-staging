<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('⚠️ Claiming Queue (Unassigned Customers)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-orange-500">
                <div class="mb-6">
                    <form action="{{ route('office.orders.queue') }}" method="GET">
                        <x-filter-toolbar :placeholder="__('Search queue by Order # or Customer Name...')" />
                    </form>
                </div>

                @if ($unassigned->isEmpty())
                    <p class="text-gray-500 italic">{{ __('No new orders from unassigned customers.') }}</p>
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
        </div>
    </div>
</x-app-layout>
