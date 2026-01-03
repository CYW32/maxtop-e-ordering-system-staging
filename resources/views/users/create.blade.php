<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('users.store') }}">
                    @csrf

                    <div class="mb-4">
                        <x-input-label for="name" :value="__('Full Name')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                            :value="old('name')" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="login_id" :value="__('Login ID (e.g. staff01)')" />
                        <x-text-input id="login_id" class="block mt-1 w-full" type="text" name="login_id"
                            :value="old('login_id')" required />
                        <x-input-error :messages="$errors->get('login_id')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="email" :value="__('Email Address')" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                            :value="old('email')" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="role" :value="__('Assign Role')" />
                        <select id="role" name="role"
                            class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="" disabled selected>Select a Role</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}">
                                    {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('role')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="password" :value="__('Password')" />
                        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password"
                            required />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="mb-6">
                        <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                        <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                            name="password_confirmation" required />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end">
                        <a href="{{ route('users.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                            Create User
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
