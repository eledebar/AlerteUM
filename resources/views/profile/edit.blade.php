<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Profil') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('status') === 'profil-mis-a-jour')
                <div class="mb-6 rounded-md border border-green-300 bg-green-50 p-4 text-green-800">
                    {{ __('Profil mis à jour.') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <div class="lg:col-span-7">
                    <div class="p-6 bg-white dark:bg-gray-900 shadow rounded-2xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <div class="lg:col-span-5 space-y-6">
                    <div class="p-6 bg-white dark:bg-gray-900 shadow rounded-2xl">
                        @include('profile.partials.update-password-form')
                    </div>

                    <div class="p-6 bg-white dark:bg-gray-900 shadow rounded-2xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
