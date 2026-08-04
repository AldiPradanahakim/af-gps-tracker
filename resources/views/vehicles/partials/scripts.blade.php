@include('vehicles.partials.scripts.data')
@include('vehicles.partials.scripts.api')
@include('vehicles.partials.scripts.map')
@include('vehicles.partials.scripts.path')
@include('vehicles.partials.scripts.marker')
@include('vehicles.partials.scripts.information')
@include('vehicles.partials.scripts.history')
@include('vehicles.partials.scripts.stop')
@include('vehicles.partials.scripts.home-location')
@include('vehicles.partials.scripts.geofence')
@include('vehicles.partials.scripts.playback')
@include('vehicles.partials.scripts.setting')
@include('vehicles.partials.scripts.navigation')
@include('vehicles.partials.scripts.realtime')
@include('vehicles.partials.scripts.app')

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const menuButtons = document.querySelectorAll('.vehicle-menu-btn');

        const sections = {

            information: document.getElementById('vehicleInformationSection'),

            'home-location': document.getElementById('vehicleHomeLocationSection'),

            geofence: document.getElementById('vehicleGeofenceSection'),

            history: document.getElementById('vehicleHistorySection'),

            stop: document.getElementById('vehicleStopSection'),

            setting: document.getElementById('vehicleSettingSection')

        };

        function hideAllSections() {

            Object.values(sections).forEach(section => {

                if (section) {

                    section.classList.add('hidden');

                }

            });

        }

        function resetMenu() {

            menuButtons.forEach(button => {

                button.classList.remove('bg-blue-50');

                const icon = button.querySelector('span:first-child');

                const text = button.querySelector('span:last-child');

                if (icon) {

                    icon.classList.remove('bg-blue-100', 'text-blue-600');

                    icon.classList.add('bg-slate-100', 'text-slate-500');

                }

                if (text) {

                    text.classList.remove('text-blue-700');

                    text.classList.add('text-slate-700');

                }

            });

        }

        menuButtons.forEach(button => {

            button.addEventListener('click', function () {

                const section = this.dataset.section;

                hideAllSections();

                if (sections[section]) {

                    sections[section].classList.remove('hidden');

                }

                resetMenu();

                this.classList.add('bg-blue-50');

                const icon = this.querySelector('span:first-child');

                const text = this.querySelector('span:last-child');

                if (icon) {

                    icon.classList.remove('bg-slate-100', 'text-slate-500');

                    icon.classList.add('bg-blue-100', 'text-blue-600');

                }

                if (text) {

                    text.classList.remove('text-slate-700');

                    text.classList.add('text-blue-700');

                }

                window.scrollTo({

                    top: 0,

                    behavior: 'smooth'

                });

            });

        });

    });

</script>