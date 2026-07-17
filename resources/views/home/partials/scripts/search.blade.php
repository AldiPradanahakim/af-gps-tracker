<script>

document.addEventListener('DOMContentLoaded', function () {

    const input = document.getElementById('searchVehicle');

    const resultBox = document.getElementById('searchResult');

    let debounceTimer = null;

    if (!input || !resultBox) {

        return;

    }

    input.addEventListener('input', function () {

        clearTimeout(debounceTimer);

        const keyword = this.value.trim();

        if (keyword.length < 2) {

            resultBox.innerHTML = '';

            resultBox.classList.add('hidden');

            return;

        }

        debounceTimer = setTimeout(() => {

            search(keyword);

        }, 400);

    });

    async function search(keyword) {

        try {

            resultBox.classList.remove('hidden');

            resultBox.innerHTML = `

                <div class="px-4 py-3 text-sm text-slate-500">

                    Mencari...

                </div>

            `;

            const response = await fetch(

                `/api/search?keyword=${encodeURIComponent(keyword)}`,

                {

                    headers: {

                        'Accept': 'application/json',

                        'X-Requested-With': 'XMLHttpRequest',

                    },

                    credentials: 'same-origin'

                }

            );

            if (!response.ok) {

                throw new Error();

            }

            const data = await response.json();

            render(data);

        } catch (error) {

            resultBox.innerHTML = `

                <div class="px-4 py-3 text-sm text-red-500">

                    Gagal mengambil data.

                </div>

            `;

        }

    }

    function render(results) {

        resultBox.innerHTML = '';

        if (results.length === 0) {

            resultBox.innerHTML = `

                <div class="px-4 py-3 text-sm text-slate-500">

                    Data tidak ditemukan.

                </div>

            `;

            return;

        }

        results.forEach(item => {

            const button = document.createElement('button');

            button.type = 'button';

            button.className = 'flex w-full items-start gap-3 border-b border-slate-100 px-4 py-3 text-left transition hover:bg-slate-50';

            let icon = '📍';

            if (item.type === 'vehicle') {

                icon = '🚗';

            }

            if (item.type === 'administrative') {

                icon = '🗺️';

            }

            button.innerHTML = `

                <div class="text-lg">

                    ${icon}

                </div>

                <div class="flex-1">

                    <div class="font-medium text-slate-900">

                        ${item.title}

                    </div>

                    <div class="mt-1 text-xs text-slate-500">

                        ${item.subtitle ?? ''}

                    </div>

                </div>

            `;

            button.addEventListener('click', function () {

                resultBox.classList.add('hidden');

                input.value = item.title;

                if (item.type === 'vehicle') {

                    focusVehicle(item.id);

                    return;

                }

                if (

                    item.type === 'location' ||

                    item.type === 'administrative'

                ) {

                    GPSTracker.flyToLocation(

                        item.latitude,

                        item.longitude,

                        17

                    );

                }

            });

            resultBox.appendChild(button);

        });

    }

    document.addEventListener('click', function (event) {

        if (

            !resultBox.contains(event.target) &&

            event.target !== input

        ) {

            resultBox.classList.add('hidden');

        }

    });

});

</script>