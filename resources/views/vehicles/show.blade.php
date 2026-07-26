<x-app-layout>

    @include('vehicles.partials.sidebar')

    @include('vehicles.partials.topbar')

    <main>

        @include('vehicles.partials.map')

        @include('vehicles.partials.navigation')

        @include('vehicles.partials.sections.information')

        @include('vehicles.partials.sections.home-location')

        @include('vehicles.partials.sections.geofence')

        @include('vehicles.partials.sections.history')

        @include('vehicles.partials.sections.stop')

        @include('vehicles.partials.sections.setting')

    </main>

    @include('vehicles.partials.scripts.app')

    @include('vehicles.partials.scripts.map')

    @include('vehicles.partials.scripts.navigation')

</x-app-layout>