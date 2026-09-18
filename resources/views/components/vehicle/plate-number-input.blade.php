@props([
    'name' => null,
    'id' => 'plateNumber',
    'value' => '',
    'inputClass' => 'h-[3.25rem] rounded-xl border border-slate-300 bg-white text-center text-sm font-semibold uppercase text-slate-700 outline-none transition focus:border-[#2563EB] focus:ring-2 focus:ring-[#2563EB]/20',
    'wrapperId' => null,
])

@php
    $rawParts = preg_split('/\s+/', trim((string) $value));
    $rawParts = array_values(array_filter($rawParts, fn($part) => $part !== ''));

    [$partRegion, $partNumber, $partSeries] = count($rawParts) === 3
        ? $rawParts
        : ['', '', ''];
@endphp

<div id="{{ $wrapperId ?? $id . 'Boxes' }}" class="flex items-center gap-2">

    <input
        type="text"
        id="{{ $id }}Region"
        maxlength="2"
        placeholder="B"
        autocomplete="off"
        value="{{ $partRegion }}"
        class="{{ $inputClass }} w-14 px-2"
    >

    <span class="text-sm font-semibold text-slate-300">-</span>

    <input
        type="text"
        id="{{ $id }}Number"
        maxlength="4"
        inputmode="numeric"
        placeholder="1234"
        autocomplete="off"
        value="{{ $partNumber }}"
        class="{{ $inputClass }} w-20 px-2"
    >

    <span class="text-sm font-semibold text-slate-300">-</span>

    <input
        type="text"
        id="{{ $id }}Series"
        maxlength="3"
        placeholder="ABC"
        autocomplete="off"
        value="{{ $partSeries }}"
        class="{{ $inputClass }} w-16 px-2"
    >

    <input
        type="hidden"
        id="{{ $id }}"
        @if($name) name="{{ $name }}" @endif
        value="{{ $value }}"
    >

</div>

<p class="mt-1.5 text-xs text-slate-400">Contoh: B 1234 ABC (Huruf-Angka-Huruf)</p>

<script>
(function () {

    const region = document.getElementById('{{ $id }}Region');
    const number = document.getElementById('{{ $id }}Number');
    const series = document.getElementById('{{ $id }}Series');
    const combined = document.getElementById('{{ $id }}');

    if (!region || !number || !series || !combined) {
        return;
    }

    function sync() {
        const parts = [region.value.trim(), number.value.trim(), series.value.trim()]
            .filter(part => part.length > 0);
        combined.value = parts.join(' ');
    }

    function setValue(value) {
        const parts = String(value || '').trim().split(/\s+/).filter(Boolean);
        region.value = parts[0] ?? '';
        number.value = parts[1] ?? '';
        series.value = parts[2] ?? '';
        sync();
    }

    function bindBox(input, { numeric = false, next = null, prev = null } = {}) {

        input.addEventListener('input', () => {
            let v = input.value.toUpperCase();
            v = numeric ? v.replace(/[^0-9]/g, '') : v.replace(/[^A-Z]/g, '');
            input.value = v;
            sync();
            if (next && v.length >= input.maxLength) {
                next.focus();
            }
        });

        input.addEventListener('keydown', (event) => {
            if (event.key === 'Backspace' && input.value === '' && prev) {
                prev.focus();
            }
        });

        input.addEventListener('blur', sync);

    }

    bindBox(region, { next: number });
    bindBox(number, { numeric: true, next: series, prev: region });
    bindBox(series, { prev: number });

    sync();

    // Diekspos supaya halaman (mis. mode edit dengan tombol Batal) bisa
    // mereset ketiga kotak sekaligus dari satu string gabungan.
    window.PlateNumberInputs = window.PlateNumberInputs || {};
    window.PlateNumberInputs['{{ $id }}'] = { setValue, sync };

})();
</script>
