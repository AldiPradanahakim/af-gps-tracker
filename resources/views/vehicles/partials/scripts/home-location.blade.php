<script>
window.VehicleHomeLocation = {
    state: null,
    marker: null,
    selectedLocation: null,
    els: {},

    init(state) {
        this.state = state;
        this.bindElements();
        this.bindEvents();
        this.render();
    },

    bindElements() {
        this.els = {
            createBtns: document.querySelectorAll('#createHomeLocationButton'),
            editBtn: document.getElementById('editHomeLocationButton'),
            deleteBtn: document.getElementById('deleteHomeLocationButton'),
            
            readContainer: document.getElementById('homeLocationReadContainer'),
            emptyContainer: document.getElementById('homeLocationEmptyContainer'),
            editContainer: document.getElementById('homeLocationEditContainer'),
            
            form: document.getElementById('homeLocationForm'),
            search: document.getElementById('homeLocationSearch'),
            searchResult: document.getElementById('homeLocationSearchResult'),
            
            cancelBtn: document.getElementById('cancelHomeLocation'),
            saveBtn: document.getElementById('saveHomeLocation'),
            
            deleteModal: document.getElementById('deleteHomeLocationModal'),
            cancelDeleteBtn: document.getElementById('cancelDeleteHomeLocation'),
            confirmDeleteBtn: document.getElementById('confirmDeleteHomeLocation'),
            
            latInput: document.getElementById('homeLatitude'),
            lngInput: document.getElementById('homeLongitude'),
            addrInput: document.getElementById('homeAddress')
        };
    },

    bindEvents() {
        this.els.createBtns.forEach(btn => {
            btn.addEventListener('click', () => this.switchMode('edit'));
        });
        
        if (this.els.editBtn) {
            this.els.editBtn.addEventListener('click', () => {
                this.switchMode('edit');
                const home = this.state.device.home_location;
                if (home) {
                    this.select({
                        lat: home.latitude || home.lat,
                        lng: home.longitude || home.lng,
                        address: home.display_name
                    });
                }
            });
        }
        
        if (this.els.cancelBtn) {
            this.els.cancelBtn.addEventListener('click', () => {
                this.switchMode('read');
                this.resetForm();
                this.render();
            });
        }
        
        if (this.els.form) {
            this.els.form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.save();
            });
        }
        
        if (this.els.search) {
            let timeout = null;
            this.els.search.addEventListener('input', () => {
                clearTimeout(timeout);
                timeout = setTimeout(() => this.search(this.els.search.value), 500);
            });
        }
        
        if (this.els.deleteBtn) {
            this.els.deleteBtn.addEventListener('click', () => {
                if (this.els.deleteModal) {
                    this.els.deleteModal.classList.remove('hidden');
                    this.els.deleteModal.classList.add('flex');
                }
            });
        }
        if (this.els.cancelDeleteBtn) {
            this.els.cancelDeleteBtn.addEventListener('click', () => {
                if (this.els.deleteModal) {
                    this.els.deleteModal.classList.add('hidden');
                    this.els.deleteModal.classList.remove('flex');
                }
            });
        }
        if (this.els.confirmDeleteBtn) {
            this.els.confirmDeleteBtn.addEventListener('click', () => this.deleteLocation());
        }
    },

    switchMode(mode) {
        if (mode === 'edit') {
            this.els.readContainer?.classList.add('hidden');
            this.els.emptyContainer?.classList.add('hidden');
            this.els.editContainer?.classList.remove('hidden');
            
            this.els.editBtn?.classList.add('hidden');
            this.els.editBtn?.classList.remove('inline-flex');
            this.els.deleteBtn?.classList.add('hidden');
            this.els.deleteBtn?.classList.remove('inline-flex');
            this.els.createBtns.forEach(b => { b.classList.add('hidden'); b.classList.remove('inline-flex'); });
        } else {
            this.els.editContainer?.classList.add('hidden');
            this.render();
        }
    },

    render() {
        const home = this.state.device.home_location;
        
        this.els.readContainer?.classList.add('hidden');
        this.els.emptyContainer?.classList.add('hidden');
        this.els.editContainer?.classList.add('hidden');
        
        if (home) {
            this.els.readContainer?.classList.remove('hidden');
            
            this.els.editBtn?.classList.remove('hidden');
            this.els.editBtn?.classList.add('inline-flex');
            this.els.deleteBtn?.classList.remove('hidden');
            this.els.deleteBtn?.classList.add('inline-flex');
            if (this.els.createBtns[0]) {
                this.els.createBtns[0].classList.add('hidden');
                this.els.createBtns[0].classList.remove('inline-flex');
            }
            
            this.setText('homeLocationStatusText', 'Sudah Ditentukan');
            const badge = document.getElementById('homeLocationStatusBadge');
            if (badge) {
                badge.classList.remove('bg-red-50', 'text-red-600');
                badge.classList.add('bg-emerald-50', 'text-emerald-600');
            }
            const dot = document.getElementById('homeLocationStatusDot');
            if (dot) {
                dot.classList.remove('bg-red-500');
                dot.classList.add('bg-emerald-500');
            }
            
            const lat = home.latitude || home.lat;
            const lng = home.longitude || home.lng;
            
            this.setText('homeLocationReadAddress', home.display_name);
            this.setText('homeLocationReadLatitude', Number(lat).toFixed(6));
            this.setText('homeLocationReadLongitude', Number(lng).toFixed(6));
            
            this.setText('homeLocationMarkerStatus', 'Aktif');
            this.setText('homeLocationDragStatus', 'Aktif Saat Edit');
            
            this.setMarker({
                lat: lat,
                lng: lng,
                address: home.display_name
            }, false); 
            
        } else {
            this.els.emptyContainer?.classList.remove('hidden');
            
            this.els.editBtn?.classList.add('hidden');
            this.els.editBtn?.classList.remove('inline-flex');
            this.els.deleteBtn?.classList.add('hidden');
            this.els.deleteBtn?.classList.remove('inline-flex');
            if (this.els.createBtns[0]) {
                this.els.createBtns[0].classList.remove('hidden');
                this.els.createBtns[0].classList.add('inline-flex');
            }
            
            VehicleMap.removeOverlay('home-location');
            this.marker = null;
        }
    },

    async deleteLocation() {
        try {
            const btn = this.els.confirmDeleteBtn;
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = 'Menghapus...';
            }
            
            const response = await VehicleApi.deleteHomeLocation(this.state.device.id);
            
            if (response.success) {
                this.state.device.home_location = null;
                window.Vehicle.updateHomeLocation(null);
                
                if (this.els.deleteModal) {
                    this.els.deleteModal.classList.add('hidden');
                    this.els.deleteModal.classList.remove('flex');
                }
                
                this.render();
                if (window.GPSTracker) GPSTracker.showToast('success', 'Berhasil', response.message);
            } else {
                if (window.GPSTracker) GPSTracker.showToast('error', 'Gagal', response.message);
            }
        } catch (error) {
            console.error(error);
            if (window.GPSTracker) GPSTracker.showToast('error', 'Error', 'Terjadi kesalahan');
        } finally {
            const btn = this.els.confirmDeleteBtn;
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = 'Ya, Hapus Home Location';
            }
        }
    },

    async save() {
        if (!this.selectedLocation) {
            if (window.GPSTracker) GPSTracker.showToast('error', 'Peringatan', 'Pilih lokasi terlebih dahulu');
            return;
        }
        
        try {
            const btn = this.els.saveBtn;
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = 'Menyimpan...';
            }
            
            const payload = {
                device_id: this.state.device.id,
                latitude: this.selectedLocation.lat,
                longitude: this.selectedLocation.lng,
                display_name: this.selectedLocation.address
            };
            
            const response = await VehicleApi.saveHomeLocation(payload);
            
            if (response.success) {
                this.state.device.home_location = {
                    latitude: this.selectedLocation.lat,
                    longitude: this.selectedLocation.lng,
                    display_name: this.selectedLocation.address
                };
                
                window.Vehicle.updateHomeLocation(this.state.device.home_location);
                this.switchMode('read');
                if (window.GPSTracker) GPSTracker.showToast('success', 'Berhasil', response.message || 'Berhasil menyimpan');
            } else {
                if (window.GPSTracker) GPSTracker.showToast('error', 'Gagal', response.message);
            }
        } catch (error) {
            console.error(error);
            if (window.GPSTracker) GPSTracker.showToast('error', 'Error', 'Terjadi kesalahan saat menyimpan');
        } finally {
            const btn = this.els.saveBtn;
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-floppy-disk mr-2"></i> Simpan Home Location';
            }
        }
    },

    async search(keyword) {
        keyword = keyword.trim();
        if (keyword.length < 3) {
            if (this.els.searchResult) {
                this.els.searchResult.innerHTML = '';
                this.els.searchResult.classList.add('hidden');
            }
            return;
        }
        
        try {
            const results = await VehicleApi.searchLocation(keyword);
            this.renderSearchResult(results);
        } catch (error) {
            console.error(error);
        }
    },

    renderSearchResult(results = []) {
        const container = this.els.searchResult;
        if (!container) return;
        
        if (!results.length) {
            container.innerHTML = '<div class="px-4 py-3 text-[13px] text-slate-500">Lokasi tidak ditemukan.</div>';
            container.classList.remove('hidden');
            return;
        }
        
        container.classList.remove('hidden');
        container.innerHTML = results.map(result => `
            <button
                type="button"
                class="block w-full border-b border-slate-100 px-4 py-3 text-left hover:bg-slate-50"
                data-lat="${result.lat}"
                data-lng="${result.lon}"
                data-address="${result.display_name}"
            >
                <div class="text-[13px] font-semibold text-slate-800">${result.display_name}</div>
                <div class="text-[11px] text-slate-500 mt-1">${Number(result.lat).toFixed(6)}, ${Number(result.lon).toFixed(6)}</div>
            </button>
        `).join('');
        
        container.querySelectorAll('button').forEach(btn => {
            btn.addEventListener('click', () => {
                this.select({
                    lat: Number(btn.dataset.lat),
                    lng: Number(btn.dataset.lng),
                    address: btn.dataset.address
                });
            });
        });
    },

    select(location) {
        this.selectedLocation = location;
        this.fillForm(location);
        this.setMarker(location, true); 
        VehicleMap.flyTo(location.lat, location.lng);
        if (this.els.searchResult) {
            this.els.searchResult.classList.add('hidden');
        }
        if (this.els.search) {
            this.els.search.value = location.address;
        }
    },

    fillForm(location) {
        if (this.els.latInput) this.els.latInput.value = Number(location.lat).toFixed(6);
        if (this.els.lngInput) this.els.lngInput.value = Number(location.lng).toFixed(6);
        if (this.els.addrInput) this.els.addrInput.value = location.address;
    },

    resetForm() {
        this.selectedLocation = null;
        if (this.els.search) this.els.search.value = '';
        if (this.els.searchResult) {
            this.els.searchResult.classList.add('hidden');
            this.els.searchResult.innerHTML = '';
        }
        this.fillForm({ lat: '', lng: '', address: '' });
    },

    setMarker(location, draggable = false) {
        if (this.marker) {
            VehicleMap.removeOverlay('home-location');
        }
        
        const icon = L.divIcon({
            className: "",
            iconSize: [40, 40],
            iconAnchor: [20, 40],
            popupAnchor: [0, -40],
            html: `
                <div style="width:40px; height:40px; display:flex; justify-content:center; align-items:center;">
                    <div style="width:32px; height:32px; border-radius:50%; background:${draggable ? '#f59e0b' : '#2563eb'}; border:3px solid white; box-shadow:0 4px 10px rgba(0,0,0,0.3); display:flex; justify-content:center; align-items:center;">
                        <i class="fa-solid fa-house" style="font-size:14px; color:white;"></i>
                    </div>
                </div>
            `
        });
        
        this.marker = L.marker([location.lat, location.lng], {
            icon: icon,
            draggable: draggable,
            zIndexOffset: draggable ? 1000 : 0
        });
        
        if (draggable) {
            this.marker.on('dragend', async () => {
                const pos = this.marker.getLatLng();
                this.selectedLocation = { lat: pos.lat, lng: pos.lng, address: 'Memuat alamat...' };
                this.fillForm(this.selectedLocation);
                
                try {
                    const rev = await VehicleApi.reverseLocation(pos.lat, pos.lng);
                    if (rev && rev.display_name) {
                        this.selectedLocation.address = rev.display_name;
                        this.fillForm(this.selectedLocation);
                    }
                } catch (e) {
                    console.error(e);
                }
            });
            this.marker.bindPopup('<div class="p-2 text-sm font-semibold text-slate-800">Geser marker untuk menyesuaikan</div>').openPopup();
        } else {
            this.marker.bindPopup(`<div class="p-2 min-w-[150px]"><div class="font-bold text-slate-800 mb-1 border-b pb-1">Home Location</div><div class="text-xs text-slate-500">${location.address || ''}</div></div>`);
        }
        
        VehicleMap.addOverlay('home-location', this.marker);
    },

    setText(id, value) {
        const el = document.getElementById(id);
        if (el) el.textContent = value ?? '-';
    },

    destroy() {
        VehicleMap.removeOverlay('home-location');
        this.marker = null;
        this.selectedLocation = null;
    }
};
</script>