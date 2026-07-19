<script>

document.addEventListener('DOMContentLoaded', () => {

    const addButton = document.getElementById('addVehicle');

    const activateModal = document.getElementById('activateDeviceModal');

    const vehicleModal = document.getElementById('vehicleInformationModal');

    const activateForm = document.getElementById('activateDeviceForm');

    const closeActivate = document.getElementById('closeActivateDevice');

    const cancelActivate = document.getElementById('cancelActivateDevice');

    const cancelVehicle = document.getElementById('cancelVehicleInformation');

    function openActivate() {

        activateModal.classList.remove('hidden');

        activateModal.classList.add('flex');

    }

    function closeActivateModal() {

        activateModal.classList.remove('flex');

        activateModal.classList.add('hidden');

    }

    function openVehicle() {

        vehicleModal.classList.remove('hidden');

        vehicleModal.classList.add('flex');

    }

    function closeVehicleModal() {

        vehicleModal.classList.remove('flex');

        vehicleModal.classList.add('hidden');

    }

    addButton?.addEventListener('click', openActivate);

    closeActivate?.addEventListener('click', closeActivateModal);

    cancelActivate?.addEventListener('click', closeActivateModal);

    cancelVehicle?.addEventListener('click', closeVehicleModal);

    activateForm?.addEventListener('submit', function(e){

        e.preventDefault();

        closeActivateModal();

        openVehicle();

    });

});

</script>