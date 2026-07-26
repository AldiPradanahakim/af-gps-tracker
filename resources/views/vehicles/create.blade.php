<x-guest-layout>

    <div class="min-h-screen bg-[#F8FAFC] flex items-center justify-center p-6">

        <div
            class="mx-auto
                    w-full
                    max-w-[1600px]
                    rounded-[32px]
                    bg-white
                    border border-[#E5E7EB]
                    shadow-xl
                    overflow-hidden">

            <div class="grid h-full lg:grid-cols-[60%_40%]">

                {{-- LEFT PANEL --}}
                <div class="hidden lg:flex bg-[#F8FAFC] p-8 xl:p-10">

                    <div class="flex h-full w-full flex-col">

                        {{-- HEADER --}}
                        <div class="max-w-[670px]">

                            <div class="flex items-center gap-5">

                                <img
                                    src="{{ asset('images/LOGO GPS.png') }}"
                                    alt="GPS TRACKER"
                                    class="h-16 w-16 object-contain">

                                <div>

                                    <div class="text-sm font-semibold uppercase tracking-[0.38em] text-[#2563EB]">
                                        GPS TRACKER
                                    </div>

                                    <div class="mt-1 text-sm text-slate-500">
                                        Monitoring System
                                    </div>

                                </div>

                            </div>

                            <div class="mt-10">

                                <h1 class="text-[64px] leading-[1.02] font-extrabold tracking-[-0.04em] text-slate-950">

                                    Informasi

                                    <span class="text-[#2563EB]">
                                        Kendaraan
                                    </span>

                                </h1>

                                <p class="mt-8 max-w-[640px] text-[22px] leading-10 text-slate-600">

                                    Lengkapi data kendaraan yang akan dihubungkan
                                    dengan perangkat GPS Tracker Anda untuk mulai
                                    melakukan monitoring kendaraan secara real-time.

                                </p>

                            </div>

                        </div>

                        {{-- ILLUSTRATION --}}
                        <div class="mt-auto flex w-full flex-col items-center">

                            <div class="w-full max-w-[820px] rounded-[30px] bg-white p-6 shadow-sm">

                                <img
                                    src="{{ asset('images/illustrator login.png') }}"
                                    alt="Illustration"
                                    class="mx-auto w-[115%] max-w-none object-contain">

                            </div>

                            <p class="mt-6 text-sm text-slate-500">

                                © 2026 GPS Tracking System. All Rights Reserved.

                            </p>

                        </div>

                    </div>

                </div>

                {{-- RIGHT PANEL --}}
                <div class="flex items-center justify-center bg-[#F8FAFC] p-8">

                    <x-vehicle.vehicle-card />

                </div>

            </div>

        </div>

    </div>

</x-guest-layout>