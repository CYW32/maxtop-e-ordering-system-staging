<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('users.update', $user->id) }}">
                    @csrf
                    @method('PUT') {{-- Required for Update --}}

                    <div class="mb-4">
                        <x-input-label for="name" :value="__('Full Name')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                            :value="$user->name" required />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="login_id" :value="__('Login ID')" />
                        <x-text-input id="login_id" class="block mt-1 w-full bg-gray-100" type="text"
                            :value="$user->login_id" disabled />
                        <span class="text-xs text-gray-500">Login ID cannot be changed.</span>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                            :value="$user->email" required />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="role" :value="__('Role')" />
                        <select name="role" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}" @if ($user->hasRole($role->name)) selected @endif>
                                    {{ ucfirst($role->name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-6">
                        <x-input-label for="status" :value="__('Status')" />
                        <select name="status" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            <option value="active" @if ($user->status === 'active') selected @endif>Active</option>
                            <option value="deactive" @if ($user->status === 'deactive') selected @endif>Deactive</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="password" :value="__('New Password (Optional)')" />
                        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password"
                            autocomplete="new-password" />
                        <p class="text-xs text-gray-500 mt-1">Leave blank to keep the current password.</p>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="flex justify-end">
                        <a href="{{ route('users.index') }}" class="mr-4 text-gray-600">Cancel</a>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update User</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
