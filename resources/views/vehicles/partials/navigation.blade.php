<div
    id="vehicleNavigation"
    class="rounded-3xl border border-slate-200 bg-white shadow-sm"
>

    <div
        class="flex flex-wrap items-center gap-2 p-3"
    >

        <button
            type="button"
            data-section="information"
            class="vehicle-navigation-item active"
        >

            <i class="fa-solid fa-circle-info"></i>

            <span>
                Informasi Kendaraan
            </span>

        </button>

        <button
            type="button"
            data-section="home-location"
            class="vehicle-navigation-item"
        >

            <i class="fa-solid fa-house"></i>

            <span>
                Home Location
            </span>

        </button>

        <button
            type="button"
            data-section="geofence"
            class="vehicle-navigation-item"
        >

            <i class="fa-solid fa-draw-polygon"></i>

            <span>
                Geofence
            </span>

        </button>

        <button
            type="button"
            data-section="history"
            class="vehicle-navigation-item"
        >

            <i class="fa-solid fa-route"></i>

            <span>
                Riwayat Perjalanan
            </span>

        </button>

        <button
            type="button"
            data-section="stop"
            class="vehicle-navigation-item"
        >

            <i class="fa-solid fa-clock"></i>

            <span>
                Kendaraan Berhenti
            </span>

        </button>

        <button
            type="button"
            data-section="setting"
            class="vehicle-navigation-item"
        >

            <i class="fa-solid fa-gear"></i>

            <span>
                Pengaturan
            </span>

        </button>

    </div>

</div>

<style>

.vehicle-navigation-item{

    display:flex;

    align-items:center;

    gap:.65rem;

    border-radius:14px;

    padding:.8rem 1.2rem;

    color:#475569;

    transition:.2s;

    font-size:.9rem;

    font-weight:600;

}

.vehicle-navigation-item:hover{

    background:#EFF6FF;

    color:#2563EB;

}

.vehicle-navigation-item.active{

    background:#2563EB;

    color:white;

}

</style>

