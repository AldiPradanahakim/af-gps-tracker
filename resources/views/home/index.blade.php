<x-app-layout>
    <div class="p-6 pb-20">
        @include('home.partials.navbar')

        <div class="grid gap-6">
            @include('home.partials.device-card')
            @include('home.partials.status-card')
            @include('home.partials.shortcut')
            @include('home.partials.notifications')
        </div>
    </div>

    @include('home.partials.bottom-nav')
</x-app-layout>
