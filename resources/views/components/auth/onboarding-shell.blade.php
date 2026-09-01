@props(['subtext' => null])

{{-- Mobile/Tablet: stacked card, Desktop (lg+): side-by-side split panel --}}
<div class="w-full max-w-[1400px] rounded-[24px] lg:rounded-[32px] bg-white shadow-2xl border border-slate-200 overflow-hidden
            flex flex-col lg:grid lg:grid-cols-[55%_45%]
            lg:h-[calc(100vh-4rem)] lg:max-h-[860px]">

    {{-- Left panel: hidden on mobile, visible on lg+ --}}
    <x-auth.left-panel :subtext="$subtext">
        @isset($heading)
            <x-slot:heading>{{ $heading }}</x-slot:heading>
        @endisset
    </x-auth.left-panel>

    {{-- Right panel: always visible, scrollable --}}
    <div class="flex flex-col bg-[#F8FAFC] lg:overflow-y-auto">

        {{-- Mobile-only top branding --}}
        <div class="flex lg:hidden items-center gap-3 px-5 pt-5 pb-4 border-b border-slate-100 bg-white">
            <img src="{{ asset('images/logo-gps.png') }}" alt="Trackio" class="h-9 w-9 object-contain" />
            <div>
                <div class="text-xs font-semibold uppercase tracking-widest text-[#2563EB]">Trackio</div>
                <div class="text-[11px] text-slate-500">Platform Pelacakan Kendaraan</div>
            </div>
        </div>

        {{-- Mobile heading --}}
        @isset($heading)
        <div class="lg:hidden px-5 pt-4 pb-1">
            <h1 class="text-[20px] sm:text-[24px] font-extrabold leading-tight text-slate-950 tracking-tight">
                {!! $heading !!}
            </h1>
            @if($subtext)
            <p class="mt-1.5 text-sm text-slate-500 leading-6">{{ $subtext }}</p>
            @endif
        </div>
        @endisset

        {{-- Content slot --}}
        <div class="flex-1 flex items-start lg:items-center justify-center p-4 sm:p-6">
            {{ $slot }}
        </div>

        {{-- Mobile footer --}}
        <div class="lg:hidden text-center pb-5 text-xs text-slate-400">
            &copy; {{ date('Y') }} Trackio. Seluruh hak cipta dilindungi.
        </div>
    </div>
</div>

