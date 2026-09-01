@extends('admin.layouts.app')

@section('title', 'Dashboard Admin')
@section('page_title', 'Statistik Sistem')
@section('page_description', 'Ringkasan penggunaan perangkat dan akun.')

@section('admin_content')

<div class="space-y-6">

    {{-- Welcome Card --}}
    <div class="relative overflow-hidden rounded-2xl bg-white shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 flex flex-col md:flex-row items-center justify-between">
        <div class="p-8 max-w-xl z-10">
            <h2 class="text-sm font-bold text-slate-400 uppercase tracking-[0.2em] mb-2">Welcome Back</h2>
            <h1 class="text-3xl font-black text-blue-600 tracking-tight mb-4">{{ auth()->user()->name }}!</h1>
            <p class="text-sm text-slate-600 leading-relaxed max-w-md">
                Ini adalah dashboard admin untuk mengelola perangkat pelacak GPS. Anda dapat memantau aktivitas perangkat, mendaftarkan perangkat baru, serta mengelola pengguna yang terdaftar di dalam sistem secara real-time.
            </p>
        </div>
        <div class="hidden md:block w-72 h-auto absolute right-0 top-0 bottom-0 pointer-events-none opacity-90">
            {{-- Aesthetic SVG Background Illustration --}}
            <svg viewBox="0 0 400 300" preserveAspectRatio="none" class="w-full h-full" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M400 0H200C250 50 300 150 200 300H400V0Z" fill="#EFF6FF"/>
                <path d="M400 50C350 100 380 200 300 300H400V50Z" fill="#DBEAFE"/>
                <circle cx="280" cy="80" r="15" fill="#BFDBFE"/>
                <circle cx="350" cy="180" r="30" fill="#93C5FD"/>
                <circle cx="320" cy="140" r="8" fill="#60A5FA"/>
            </svg>
        </div>
    </div>

    {{-- 4 Stat Cards with Circular Progress --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        {{-- Card 1: Total Pengguna --}}
        <div class="rounded-2xl bg-white p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 flex items-center gap-4">
            <div class="relative h-14 w-14 shrink-0">
                <svg class="h-full w-full -rotate-90" viewBox="0 0 36 36" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="18" cy="18" r="16" fill="none" class="stroke-slate-100" stroke-width="3"></circle>
                    <circle cx="18" cy="18" r="16" fill="none" class="stroke-blue-500" stroke-width="3" stroke-dasharray="100, 100" stroke-dashoffset="0"></circle>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-xs font-bold text-slate-700">100%</span>
                </div>
            </div>
            <div>
                <p class="text-2xl font-black text-slate-800">{{ $totalUsers }}</p>
                <p class="text-sm font-semibold text-slate-500">Pengguna</p>
            </div>
        </div>

        {{-- Card 2: Total Perangkat --}}
        <div class="rounded-2xl bg-white p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 flex items-center gap-4">
            <div class="relative h-14 w-14 shrink-0">
                <svg class="h-full w-full -rotate-90" viewBox="0 0 36 36" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="18" cy="18" r="16" fill="none" class="stroke-slate-100" stroke-width="3"></circle>
                    <circle cx="18" cy="18" r="16" fill="none" class="stroke-indigo-500" stroke-width="3" stroke-dasharray="100, 100" stroke-dashoffset="0"></circle>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-xs font-bold text-slate-700">100%</span>
                </div>
            </div>
            <div>
                <p class="text-2xl font-black text-slate-800">{{ $totalDevices }}</p>
                <p class="text-sm font-semibold text-slate-500">Perangkat</p>
            </div>
        </div>

        {{-- Card 3: Perangkat Dipakai --}}
        <div class="rounded-2xl bg-white p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 flex items-center gap-4">
            @php $usedPercent = $totalDevices > 0 ? round(($usedDevices / $totalDevices) * 100) : 0; @endphp
            <div class="relative h-14 w-14 shrink-0">
                <svg class="h-full w-full -rotate-90" viewBox="0 0 36 36" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="18" cy="18" r="16" fill="none" class="stroke-slate-100" stroke-width="3"></circle>
                    <circle cx="18" cy="18" r="16" fill="none" class="stroke-emerald-500" stroke-width="3" stroke-dasharray="{{ $usedPercent }}, 100" stroke-dashoffset="0"></circle>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-xs font-bold text-slate-700">{{ $usedPercent }}%</span>
                </div>
            </div>
            <div>
                <p class="text-2xl font-black text-slate-800">{{ $usedDevices }}</p>
                <p class="text-sm font-semibold text-slate-500">Digunakan</p>
            </div>
        </div>

        {{-- Card 4: Perangkat Tersedia --}}
        <div class="rounded-2xl bg-white p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 flex items-center gap-4">
            @php $availablePercent = $totalDevices > 0 ? round(($availableDevices / $totalDevices) * 100) : 0; @endphp
            <div class="relative h-14 w-14 shrink-0">
                <svg class="h-full w-full -rotate-90" viewBox="0 0 36 36" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="18" cy="18" r="16" fill="none" class="stroke-slate-100" stroke-width="3"></circle>
                    <circle cx="18" cy="18" r="16" fill="none" class="stroke-amber-500" stroke-width="3" stroke-dasharray="{{ $availablePercent }}, 100" stroke-dashoffset="0"></circle>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-xs font-bold text-slate-700">{{ $availablePercent }}%</span>
                </div>
            </div>
            <div>
                <p class="text-2xl font-black text-slate-800">{{ $availableDevices }}</p>
                <p class="text-sm font-semibold text-slate-500">Tersedia</p>
            </div>
        </div>

    </div>

    {{-- Charts Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Bar Chart (Activity) --}}
        <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
            <h3 class="text-lg font-bold text-slate-900 mb-6">Aktivitas Perangkat</h3>
            <div class="h-72 w-full">
                <canvas id="deviceActivityChart"></canvas>
            </div>
        </div>
        
        {{-- Half Doughnut (Network Status) --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] flex flex-col items-center">
            <h3 class="text-lg font-bold text-slate-900 mb-2 self-start">Status Jaringan Perangkat</h3>
            <div class="relative h-64 w-full flex items-center justify-center mt-4">
                <canvas id="deviceNetworkChart"></canvas>
                <div class="absolute flex flex-col items-center" style="top: 60%">
                    <span class="text-3xl font-black text-slate-800">{{ $onlineDevicesCount }}</span>
                    <span class="text-xs font-semibold text-slate-500 mt-1">Perangkat Online</span>
                </div>
            </div>
        </div>

    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // Bar Chart (Activity)
        const ctxActivity = document.getElementById('deviceActivityChart').getContext('2d');
        new Chart(ctxActivity, {
            type: 'bar',
            data: {
                labels: ['Digunakan', 'Tersedia'],
                datasets: [
                    {
                        label: 'Status Perangkat',
                        data: [{{ $usedDevices }}, {{ $availableDevices }}],
                        backgroundColor: ['#3B82F6', '#F43F5E'],
                        borderRadius: 4,
                        barPercentage: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    y: { duration: 1000, easing: 'easeOutQuart' }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, font: { family: "'Figtree', sans-serif" } },
                        grid: { borderDash: [4, 4] }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: "'Figtree', sans-serif" } }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });

        // Half Doughnut (Network Status)
        const ctxNetwork = document.getElementById('deviceNetworkChart').getContext('2d');
        new Chart(ctxNetwork, {
            type: 'doughnut',
            data: {
                labels: ['Online', 'Offline'],
                datasets: [{
                    data: [{{ $onlineDevicesCount }}, {{ $offlineDevicesCount }}],
                    backgroundColor: ['#10B981', '#F1F5F9'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                circumference: 180,
                rotation: -90,
                animation: {
                    animateScale: true,
                    animateRotate: true
                },
                cutout: '80%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ' ' + context.label + ': ' + context.raw + ' Perangkat';
                            }
                        }
                    }
                }
            }
        });

    });
</script>
@endpush

@endsection
