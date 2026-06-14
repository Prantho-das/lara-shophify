<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('storefront.layouts.app')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('storefront.home', absolute: false), navigate: true);
    }
}; ?>

<div class="max-w-md mx-auto px-4 py-16 transition-colors duration-300">
    <div class="bg-theme-card border border-theme-border rounded-theme p-8 shadow-2xl space-y-6">
        <div class="text-center space-y-2">
            <h1 class="text-2xl font-black text-theme-text">Account Login</h1>
            <p class="text-xs text-theme-muted">Access your premium orders, profile, and updates.</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form wire:submit="login" class="space-y-4">
            <!-- Email Address -->
            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-theme-text" for="email">Email Address</label>
                <input 
                    wire:model="form.email" 
                    id="email" 
                    class="w-full bg-secondary border border-theme-border rounded-theme px-3 py-2.5 text-sm text-theme-text focus:outline-none focus:border-primary" 
                    type="email" 
                    name="email" 
                    required 
                    autofocus 
                    autocomplete="username" 
                />
                @error('form.email') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Password -->
            <div class="space-y-1.5">
                <div class="flex justify-between items-center">
                    <label class="text-xs font-semibold text-theme-text" for="password">Password</label>
                    @if (Route::has('password.request'))
                        <a class="text-xs text-primary hover:underline font-semibold" href="{{ route('password.request') }}" wire:navigate>
                            Forgot?
                        </a>
                    @endif
                </div>
                <input 
                    wire:model="form.password" 
                    id="password" 
                    class="w-full bg-secondary border border-theme-border rounded-theme px-3 py-2.5 text-sm text-theme-text focus:outline-none focus:border-primary"
                    type="password"
                    name="password"
                    required 
                    autocomplete="current-password" 
                />
                @error('form.password') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Remember Me -->
            <div class="flex items-center">
                <input wire:model="form.remember" id="remember" type="checkbox" class="rounded border-theme-border text-primary focus:ring-primary h-4 w-4 bg-secondary" name="remember">
                <label for="remember" class="ms-2 text-xs text-theme-muted cursor-pointer">Remember me</label>
            </div>

            <button 
                type="submit" 
                wire:loading.attr="disabled"
                class="btn-primary w-full py-3 text-sm font-bold tracking-wider mt-4"
            >
                <span wire:loading.remove>Log In</span>
                <span wire:loading class="flex items-center justify-center gap-2">
                    <i class="fa-solid fa-spinner animate-spin"></i> Authenticating...
                </span>
            </button>
        </form>

        <div class="text-center pt-4 border-t border-theme-border text-xs text-theme-muted">
            Don't have an account? 
            <a href="/register" wire:navigate class="text-primary font-bold hover:underline">
                Create one now
            </a>
        </div>
    </div>
</div>
