<script>

window.VehicleProfile = {

    /*
    |--------------------------------------------------------------------------
    | Properties
    |--------------------------------------------------------------------------
    */

    state: null,

    button: null,

    dropdown: null,

    modal: null,

    form: null,

    inputs: [],

    defaults: {},

    /*
    |--------------------------------------------------------------------------
    | Initialize
    |--------------------------------------------------------------------------
    */

    init(state) {

        this.state = state;

        this.button = document.getElementById('profileButton');

        this.dropdown = document.getElementById('profileDropdown');

        this.modal = document.getElementById('profileModal');

        this.form = document.getElementById('profileForm');

        this.inputs = this.form
            ? this.form.querySelectorAll('input')
            : [];

        if (!this.button || !this.dropdown) {

            return;

        }

        this.bindDropdown();

        this.bindModal();

        this.bindForm();

    },

    /*
    |--------------------------------------------------------------------------
    | Toast
    |--------------------------------------------------------------------------
    */

    toast(type, title, message) {

        GPSTracker.showToast(type, title, message);

    },

    /*
    |--------------------------------------------------------------------------
    | Dropdown
    |--------------------------------------------------------------------------
    */

    renderDropdown() {

        const currentName = document
            .querySelector('#profileButton [data-profile-name]')
            ?.textContent
            ?.trim() ?? '';

        const currentEmail = document
            .querySelector('#profileButton [data-profile-email]')
            ?.textContent
            ?.trim() ?? '';

        this.dropdown.innerHTML = `

            <div class="border-b border-slate-200 px-5 py-4">

                <div data-profile-name class="font-semibold text-slate-900">

                    ${currentName}

                </div>

                <div data-profile-email class="mt-1 text-sm text-slate-500">

                    ${currentEmail}

                </div>

            </div>

            <div class="py-2">

                <button
                    id="openVehicleProfileModal"
                    type="button"
                    class="flex w-full items-center gap-3 px-5 py-3 text-left text-sm text-slate-700 transition hover:bg-slate-50">

                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">

                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A9 9 0 1 1 18.88 17.8M15 11a3 3 0 1 1-6 0a3 3 0 0 1 6 0m-9 9a9 9 0 0 1 18 0"/>

                    </svg>

                    Profil

                </button>

            </div>

            <div class="border-t border-slate-200 p-2">

                <form method="POST" action="{{ route('logout') }}">

                    @csrf

                    <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium text-red-600 transition hover:bg-red-50">

                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">

                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H9m4 4v1a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h5a2 2 0 0 1 2 2v1"/>

                        </svg>

                        Keluar

                    </button>

                </form>

            </div>

        `;

    },

    closeDropdown() {

        this.dropdown?.classList.add('hidden');

        document.getElementById('profileArrow')?.classList.remove('rotate-180');

    },

    bindDropdown() {

        this.button.addEventListener('click', (event) => {

            event.stopPropagation();

            this.renderDropdown();

            const opened = !this.dropdown.classList.contains('hidden');

            this.closeDropdown();

            if (!opened) {

                this.dropdown.classList.remove('hidden');

                document.getElementById('profileArrow')?.classList.add('rotate-180');

            }

        });

        document.addEventListener('click', (event) => {

            if (

                !this.dropdown.contains(event.target) &&

                !this.button.contains(event.target)

            ) {

                this.closeDropdown();

            }

        });

        document.addEventListener('click', (event) => {

            if (event.target.closest('#openVehicleProfileModal')) {

                this.openModal();

            }

        });

    },

    /*
    |--------------------------------------------------------------------------
    | Modal
    |--------------------------------------------------------------------------
    */

    openModal() {

        this.closeDropdown();

        this.modal?.classList.remove('hidden');

        this.modal?.classList.add('flex');

        this.disableForm();

    },

    closeModal() {

        this.modal?.classList.add('hidden');

        this.modal?.classList.remove('flex');

    },

    disableForm() {

        this.defaults = {};

        this.inputs.forEach(input => {

            this.defaults[input.name] = input.value;

            input.disabled = true;

            input.classList.add('bg-slate-100');

            input.classList.remove('bg-white');

        });

        document.getElementById('editProfileButton')?.classList.remove('hidden');

        document.getElementById('cancelProfileButton')?.classList.add('hidden');

        document.getElementById('saveProfileButton')?.classList.add('hidden');

    },

    enableForm() {

        this.inputs.forEach(input => {

            input.disabled = false;

            input.classList.remove('bg-slate-100');

            input.classList.add('bg-white');

        });

        document.getElementById('editProfileButton')?.classList.add('hidden');

        document.getElementById('cancelProfileButton')?.classList.remove('hidden');

        document.getElementById('saveProfileButton')?.classList.remove('hidden');

    },

    bindModal() {

        document.getElementById('closeProfileModal')
            ?.addEventListener('click', () => this.closeModal());

        this.modal?.addEventListener('click', (event) => {

            if (event.target === this.modal) {

                this.closeModal();

            }

        });

        document.addEventListener('keydown', (event) => {

            if (event.key === 'Escape') {

                this.closeModal();

            }

        });

        document.getElementById('editProfileButton')
            ?.addEventListener('click', () => this.enableForm());

        document.getElementById('cancelProfileButton')
            ?.addEventListener('click', () => {

                Object.keys(this.defaults).forEach(key => {

                    const input = this.form.querySelector(`[name="${key}"]`);

                    if (input) {

                        input.value = this.defaults[key];

                    }

                });

                this.disableForm();

            });

    },

    /*
    |--------------------------------------------------------------------------
    | Form Submit
    |--------------------------------------------------------------------------
    */

    bindForm() {

        this.form?.addEventListener('submit', async (event) => {

            event.preventDefault();

            const saveButton = document.getElementById('saveProfileButton');

            saveButton.disabled = true;

            saveButton.innerHTML = 'Menyimpan...';

            const formData = new FormData(this.form);

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

                this.updateUI(result.user);

                this.disableForm();

                this.closeModal();

                this.toast('success', 'Berhasil', result.message);

            } catch (error) {

                console.error(error);

                let message = 'Gagal memperbarui profil.';

                if (error.errors) {

                    const first = Object.values(error.errors)[0];

                    if (Array.isArray(first)) {

                        message = first[0];

                    }

                }

                this.toast('error', 'Gagal', message);

            } finally {

                saveButton.disabled = false;

                saveButton.innerHTML = 'Simpan Perubahan';

            }

        });

    },

    updateUI(user) {

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

        document.getElementById('currentPassword').value = '';

        document.getElementById('newPassword').value = '';

        document.getElementById('confirmPassword').value = '';

        this.defaults = {

            name: user.name,

            email: user.email,

            phone: user.phone ?? '',

            current_password: '',

            password: '',

            password_confirmation: '',

        };

    },

};

</script>
