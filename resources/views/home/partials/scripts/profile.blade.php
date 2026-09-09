<script>

document.addEventListener('DOMContentLoaded', function () {

    const button = document.getElementById('profileButton');

    const dropdown = document.getElementById('profileDropdown');

    if (!button || !dropdown) {

        return;

    }

    function renderProfile() {

        const currentName = document
            .querySelector('#profileButton [data-profile-name]')
            ?.textContent
            ?.trim() ?? '';

        const currentEmail = document
            .querySelector('#profileButton [data-profile-email]')
            ?.textContent
            ?.trim() ?? '';

        dropdown.innerHTML = `

            <div class="border-b border-slate-200 px-5 py-4">

                <div
                    data-profile-name
                    class="font-semibold text-slate-900">

                    ${currentName}

                </div>

                <div
                    data-profile-email
                    class="mt-1 text-sm text-slate-500">

                    ${currentEmail}

                </div>

            </div>

            <div class="py-2">

                <button
                    id="openProfileModal"
                    type="button"
                    class="flex w-full items-center gap-3 px-5 py-3 text-left text-sm text-slate-700 transition hover:bg-slate-50">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 text-[#2563EB]"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M5.121 17.804A9 9 0 1 1 18.88 17.8M15 11a3 3 0 1 1-6 0a3 3 0 0 1 6 0m-9 9a9 9 0 0 1 18 0"/>

                    </svg>

                    Profil

                </button>

            <div class="border-t border-slate-200 p-2">

                <form
                    method="POST"
                    action="{{ route('logout') }}">

                    @csrf

                    <button
                        type="submit"
                        class="flex w-full items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium text-red-600 transition hover:bg-red-50">

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M17 16l4-4m0 0l-4-4m4 4H9m4 4v1a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h5a2 2 0 0 1 2 2v1"/>

                        </svg>

                        Keluar

                    </button>

                </form>

            </div>

        `;

    }

    const profileModal = document.getElementById('profileModal');

    const closeProfileModal = document.getElementById('closeProfileModal');

    const editProfileButton = document.getElementById('editProfileButton');

    const cancelProfileButton = document.getElementById('cancelProfileButton');

    const saveProfileButton = document.getElementById('saveProfileButton');

    const profileForm = document.getElementById('profileForm');

    /*
    |--------------------------------------------------------------------------
    | Termasuk <select> (Zona Waktu) - kalau hanya <input>, selektor zona
    | waktunya tetap terkunci walau tombol Edit sudah ditekan.
    |--------------------------------------------------------------------------
    */

    const inputs = profileForm
        ? profileForm.querySelectorAll('input, select')
        : [];

    let profileDefault = {};

    function openProfileModal() {

        GPSTracker.closeDropdowns();

        profileModal.classList.remove('hidden');

        profileModal.classList.add('flex');

        disableProfileForm();

    }

    function closeProfile() {

        profileModal.classList.add('hidden');

        profileModal.classList.remove('flex');

    }

    function disableProfileForm() {

        profileDefault = {};

        inputs.forEach(input => {

            profileDefault[input.name] = input.value;

            input.disabled = true;

            input.classList.add('bg-slate-100');

            input.classList.remove('bg-white');

        });

        editProfileButton.classList.remove('hidden');

        cancelProfileButton.classList.add('hidden');

        saveProfileButton.classList.add('hidden');

        // Syarat kata sandi hanya relevan saat pengguna benar-benar
        // sedang mengetik kata sandi baru.
        document.getElementById('passwordRequirements')
            ?.classList.add('hidden');

    }

    function enableProfileForm() {

        inputs.forEach(input => {

            input.disabled = false;

            input.classList.remove('bg-slate-100');

            input.classList.add('bg-white');

        });

        editProfileButton.classList.add('hidden');

        cancelProfileButton.classList.remove('hidden');

        saveProfileButton.classList.remove('hidden');

        document.getElementById('passwordRequirements')
            ?.classList.remove('hidden');

    }

    document.addEventListener('click', function(e){

        if(e.target.closest('#openProfileModal')){

            openProfileModal();

        }

    });

    editProfileButton?.addEventListener('click', function(){

        enableProfileForm();

    });

    cancelProfileButton?.addEventListener('click', function(){

        Object.keys(profileDefault).forEach(function(key){

            const input = profileForm.querySelector(`[name="${key}"]`);

            if(input){

                input.value = profileDefault[key];

            }

        });

        disableProfileForm();

    });

    closeProfileModal?.addEventListener('click', function(){

        closeProfile();

    });

    profileModal?.addEventListener('click', function(e){

        if(e.target === profileModal){

            closeProfile();

        }

    });

    document.addEventListener('keydown', function(e){

        if(e.key === 'Escape'){

            closeProfile();

        }

    });

    profileForm?.addEventListener('submit', async function (e) {

        e.preventDefault();

        saveProfileButton.disabled = true;

        saveProfileButton.innerHTML = 'Menyimpan...';

        const formData = new FormData(profileForm);

        formData.append('_method', 'PATCH');

        try {

            const response = await fetch("{{ route('profile.update') }}", {

                method: 'POST',

                headers: {

                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name="csrf-token"]')
                        .content,

                    'Accept': 'application/json',

                },

                body: formData,

            });

            const result = await response.json();

            if (!response.ok) {

                throw result;

            }

            updateProfileUI(result.user);

            disableProfileForm();

            closeProfile();

            if (typeof GPSTracker.showToast === 'function') {

                GPSTracker.showToast(
                    'success',
                    'Berhasil',
                    result.message
                );

            } else {

                alert(result.message);

            }
        } catch (error) {

            console.error(error);

            let message = 'Gagal memperbarui profil.';

            if (error.errors) {

                const first = Object.values(error.errors)[0];

                if (Array.isArray(first)) {

                    message = first[0];

                }

            }

            if (typeof GPSTracker.showToast === 'function') {

                GPSTracker.showToast(
                    'error',
                    'Gagal',
                    message
                );

            } else {

                alert(message);

            }

        } finally {

            saveProfileButton.disabled = false;

            saveProfileButton.innerHTML = 'Simpan Perubahan';

        }

    });

    function updateProfileUI(user) {

        document
            .querySelectorAll('[data-profile-name]')
            .forEach(el => {

                el.textContent = user.name;

            });

        document
            .querySelectorAll('[data-profile-email]')
            .forEach(el => {

                el.textContent = user.email;

            });

        document
            .querySelectorAll('[data-profile-avatar]')
            .forEach(el => {

                el.textContent = user.name
                    ? user.name.charAt(0).toUpperCase()
                    : '';

            });

        document.getElementById('profileName').value = user.name;

        document.getElementById('profileEmail').value = user.email;

        document.getElementById('profilePhone').value = user.phone ?? '';

        /*
        |--------------------------------------------------------------------------
        | Zona waktu ikut disegarkan supaya nilainya tidak balik ke pilihan
        | lama kalau server menormalisasi/menolak kiriman.
        |--------------------------------------------------------------------------
        */

        const timezoneSelect = document.getElementById('profileTimezone');

        if (timezoneSelect && user.timezone) {

            timezoneSelect.value = user.timezone;

        }

        document.getElementById('currentPassword').value = '';

        document.getElementById('newPassword').value = '';

        document.getElementById('confirmPassword').value = '';

        profileDefault = {

            name: user.name,

            email: user.email,

            phone: user.phone ?? '',

            current_password: '',

            password: '',

            password_confirmation: '',

        };

    }

    button.addEventListener('click', function (event) {

        event.stopPropagation();

        renderProfile();

        const opened = !dropdown.classList.contains('hidden');

        GPSTracker.closeDropdowns();

        if (!opened) {

            dropdown.classList.remove('hidden');

            document
                .getElementById('profileArrow')
                ?.classList.add('rotate-180');

        }

    });

    document.addEventListener('click', function (event) {

        if (

            !dropdown.contains(event.target) &&

            !button.contains(event.target)

        ) {

            GPSTracker.closeDropdowns();

        }

    });

});

</script>