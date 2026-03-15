<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('📋 My Claimed Orders Master List') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mb-8">
            <x-filter-toolbar :placeholder="__('Search Order # or Customer Name...')" :showDates="true">
                <select name="status" class="text-xs border-gray-200 rounded-xl font-bold uppercase text-gray-600">
                    <option value="">{{ __('All Status') }}</option>
                    @foreach ($status as $statusName)
                        <option value="{{ $statusName }}" {{ request('status') == $statusName ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $statusName)) }}
                        </option>
                    @endforeach
                </select>
            </x-filter-toolbar>
        </div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if ($history->isEmpty())
                    <div class="text-center py-8">
                        <p class="text-gray-500 italic">{{ __('You have not claimed any orders yet.') }}</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 border-b">
                                    <th class="px-4 py-3 text-xs font-bold uppercase text-gray-600">{{ __('Order #') }}
                                    </th>
                                    <th class="px-4 py-3 text-xs font-bold uppercase text-gray-600">{{ __('Customer') }}
                                    </th>
                                    <th class="px-4 py-3 text-xs font-bold uppercase text-gray-600">
                                        {{ __('Last Update') }}</th>
                                    <th class="px-4 py-3 text-xs font-bold uppercase text-gray-600">{{ __('Status') }}
                                    </th>
                                    <th class="px-4 py-3 text-xs font-bold uppercase text-gray-600 text-right">
                                        {{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($history as $order)
                                    <tr class="border-b hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-4 font-mono text-sm font-bold text-blue-600">
                                            {{ $order->order_number }}</td>
                                        <td class="px-4 py-4 text-sm">
                                            <div class="font-bold text-gray-900">
                                                {{ $order->user->details->company_name ?? $order->user->name }}</div>
                                            <div class="text-xs text-gray-400">{{ $order->user->email }}</div>
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-600">
                                            {{ $order->updated_at->format('Y-m-d H:i') }}
                                        </td>
                                        <td class="px-4 py-4 text-sm">
                                            {{-- Fulfills Section 4: All Lifecycle Statuses --}}
                                            @php
                                                $statusClasses = match ($order->status) {
                                                    'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                                                    'approved' => 'bg-green-100 text-green-800 border-green-300',
                                                    'in_transit' => 'bg-blue-100 text-blue-800 border-blue-300',
                                                    'completed' => 'bg-gray-100 text-gray-800 border-gray-300',
                                                    'cancelled' => 'bg-red-100 text-red-800 border-red-300',
                                                    default => 'bg-gray-50 text-gray-600 border-gray-200',
                                                };
                                            @endphp
                                            <span
                                                class="px-2 py-1 rounded-full text-[10px] font-black uppercase border shadow-sm {{ $statusClasses }}">
                                                {{ $order->status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-right">
                                            <a href="{{ route('office.orders.show', $order) }}"
                                                class="inline-block bg-white text-gray-700 hover:bg-gray-100 px-4 py-2 rounded-lg text-xs font-black uppercase transition border border-gray-200">
                                                {{ in_array($order->status, ['completed', 'cancelled']) ? __('View Summary') : __('Process Order') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $history->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
