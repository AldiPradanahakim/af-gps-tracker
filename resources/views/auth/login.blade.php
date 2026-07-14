<x-guest-layout>
    <div class="h-full flex items-center justify-center w-full">
        <div class="w-full max-w-[1500px] min-h-[calc(100vh-48px)] rounded-[32px] bg-white shadow-2xl border border-slate-200 grid lg:grid-cols-[55%_45%] overflow-hidden">
            <div class="hidden lg:flex min-h-0 h-full w-full items-center justify-center bg-[#F8FAFC] p-6">
                <x-auth.left-panel />
            </div>
            <div class="flex min-h-0 h-full items-center justify-center bg-[#F8FAFC] p-6">
                <form method="POST" action="{{ route('login') }}" class="w-full flex items-center justify-center">
                    @csrf
                    <x-auth.login-card />
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
