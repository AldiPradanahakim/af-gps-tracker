<x-guest-layout>
    <x-auth.onboarding-shell>
        <form method="POST" action="{{ route('login') }}" class="w-full flex items-center justify-center">
            @csrf
            <x-auth.login-card />
        </form>
    </x-auth.onboarding-shell>
</x-guest-layout>
