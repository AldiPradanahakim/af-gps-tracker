<script>
document.addEventListener('DOMContentLoaded', () => {
    
    const iconMap = {
        'motor': [
            { value: 'motorcycle', text: '🏍 Motor' }
        ],
        'mobil': [
            { value: 'car', text: '🚗 Mobil Biasa' },
            { value: 'pickup', text: '🛻 Pickup' },
            { value: 'van', text: '🚐 Van' },
            { value: 'taxi', text: '🚕 Taxi' },
            { value: 'police', text: '🚓 Polisi' },
            { value: 'ambulance', text: '🚑 Ambulance' }
        ],
        'kendaraan_besar': [
            { value: 'truck', text: '🚚 Truk' },
            { value: 'bus', text: '🚌 Bus' }
        ]
    };

    function updateIcons(formContext) {
        const typeSelect = formContext.querySelector('.dynamic-vehicle-type');
        const iconSelect = formContext.querySelector('.dynamic-marker-icon');
        
        if (!typeSelect || !iconSelect) return;
        
        const type = typeSelect.value;
        const options = iconMap[type] || [];
        
        // Get the currently selected icon from dataset or value
        const currentIcon = iconSelect.dataset.selectedIcon || iconSelect.value;
        
        iconSelect.innerHTML = '';
        
        let found = false;
        options.forEach(opt => {
            const optionEl = document.createElement('option');
            optionEl.value = opt.value;
            optionEl.textContent = opt.text;
            if (opt.value === currentIcon) {
                optionEl.selected = true;
                found = true;
            }
            iconSelect.appendChild(optionEl);
        });

        // If the previously selected icon is not in the new options, select the first one
        if (!found && options.length > 0) {
            iconSelect.selectedIndex = 0;
            iconSelect.dataset.selectedIcon = options[0].value;
        }
    }

    // Initialize all existing forms
    const forms = document.querySelectorAll('form, .grid'); // Include grid for the detail page layout
    forms.forEach(form => {
        if(form.querySelector('.dynamic-vehicle-type')) {
            updateIcons(form);
        }
    });

    // Handle change events globally using event delegation
    document.addEventListener('change', function(e) {
        if (e.target && e.target.classList.contains('dynamic-vehicle-type')) {
            // Find the closest wrapper that contains both selects (form, or parent section for details page)
            const wrapper = e.target.closest('form') || e.target.closest('section') || document;
            updateIcons(wrapper);
        }
    });

});
</script>
