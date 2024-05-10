<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{--
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests"> --}}
    <title>{{ isset($title) ? $title.' - '.config('app.name') : config('app.name') }}</title>
    <script src="{{ asset('js/app.js') }}" defer></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body class="min-h-screen font-sans antialiased bg-base-200/50 dark:bg-base-200">

    <div class="flex items-center justify-center h-screen">
        <div class="w-full md:w-2/3 lg:w-1/2">
            <x-main>
                <x-slot:content>
                    {{ $slot }}
                </x-slot:content>
            </x-main>
            <div class="flex justify-center mt-12">
                @if(Route::currentRouteName() == 'host-panel')
                <x-button icon="o-cog-6-tooth" class="btn-square btn-primary" onclick="setting.showModal()" />
                @endif
            </div>
            <div class="flex justify-center py-2">
                <x-theme-toggle class="btn btn-base-100" @theme-changed="console.log($event.detail)" />
            </div>
        </div>
    </div>

    <x-toast />

    {{-- setting modal --}}
    <livewire:room-setting-modal />

    <div id="modal17" class="fixed inset-0 z-50 flex items-center justify-center hidden backdrop-blur">
        <div class="p-6 bg-transparent rounded-lg">
            <h1 class="text-6xl font-black text-center text-primary" id="counterElement"></h1>
        </div>
    </div>

    @livewireScripts

    <script>
        document.addEventListener('livewire:init', () => {

            Livewire.on('refreshComponent', function () {
                window.location.reload();
            });

            Livewire.on('post-created', (data) => {
                showModal();

                console.log('data', data);
                let counter = 3;
                const intervalId = setInterval(() => {
                    if (counter >= 0) {
                        updateCounter(counter);
                        counter--;
                    } else {
                        clearInterval(intervalId); 
                        redirectToRoute(data);
                    }
                }, 1000);
            });
        });

        function showModal() {
            document.getElementById('modal17').classList.remove('hidden');
        }

        function updateCounter(counter) {
            document.getElementById('counterElement').textContent = counter;
        }

        function redirectToRoute(data) {
            let route = data[0].route;
            console.log('route', route);
            window.location.href = route; // Redirect to the route
        }
    </script>

</body>

</html>