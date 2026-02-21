<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
            {{ __('System Role Registry') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- CREATE SECTION --}}
            <div class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm">
                <form method="POST" action="{{ route('admin.roles.manage.store') }}"
                    class="flex flex-col md:flex-row gap-4 items-end">
                    @csrf
                    <div class="flex-1">
                        <x-input-label :value="__('New Custom Role Name')" class="text-[10px] font-black uppercase text-gray-400 mb-2" />
                        <x-text-input name="name" required placeholder="e.g. branch_manager" class="w-full" />
                    </div>
                    <x-primary-button
                        class="bg-blue-600 hover:bg-blue-700 h-12 px-8 rounded-xl text-[10px] font-black uppercase">
                        {{ __('Add Custom Role') }}
                    </x-primary-button>
                </form>
                <p class="mt-3 text-[9px] text-gray-400 uppercase italic">
                    {{ __('Note: Creating an "Admin" role is programmatically restricted to maintain single-authority integrity.') }}
                </p>
            </div>

            {{-- LIST SECTION --}}
            <div class="bg-white shadow-sm sm:rounded-[2.5rem] border border-gray-100 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                {{ __('Role Identity') }}</th>
                            <th
                                class="px-6 py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                {{ __('Active Users') }}</th>
                            <th
                                class="px-8 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                {{ __('Registry Status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 bg-white">
                        @foreach ($roles as $role)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-8 py-5">
                                    <span
                                        class="text-sm font-black text-gray-900 uppercase">{{ str_replace('_', ' ', $role->name) }}</span>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span
                                        class="px-3 py-1 bg-indigo-50 text-indigo-700 rounded-full text-[10px] font-black uppercase">
                                        {{ $role->users_count }} {{ __('Users') }}
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    @if (in_array($role->name, $protectedRoles))
                                        <span
                                            class="text-[9px] font-black text-amber-500 uppercase tracking-widest border border-amber-200 px-3 py-1 rounded-lg bg-amber-50">
                                            {{ __('ðŸ”’ System Protected') }}
                                        </span>
                                    @else
                                        <form action="{{ route('admin.roles.manage.destroy', $role) }}" method="POST"
                                            onsubmit="return confirm('Permanently remove this role?');">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="text-red-400 hover:text-red-600 text-[10px] font-black uppercase tracking-widest transition">
                                                {{ __('Remove Role') }}
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
