@props(['subtext' => null])

<div class="w-full max-w-[1400px] h-[calc(100vh-3rem)] max-h-[800px] rounded-[32px] bg-white shadow-2xl border border-slate-200 grid lg:grid-cols-[55%_45%] overflow-hidden">
    <x-auth.left-panel :subtext="$subtext">
        @isset($heading)
            <x-slot:heading>{{ $heading }}</x-slot:heading>
        @endisset
    </x-auth.left-panel>

    <div class="flex items-center justify-center bg-[#F8FAFC] p-4 sm:p-6 min-h-0">
        <div class="w-full h-full overflow-y-auto flex items-center justify-center py-2">
            {{ $slot }}
        </div>
    </div>
</div>
