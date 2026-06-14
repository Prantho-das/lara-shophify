<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('storefront.layouts.app')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('storefront.home', absolute: false), navigate: true);
    }
}; ?>

<div class="max-w-md mx-auto px-4 py-16 transition-colors duration-300">
    <div class="bg-theme-card border border-theme-border rounded-theme p-8 shadow-2xl space-y-6">
        <div class="text-center space-y-2">
            <h1 class="text-2xl font-black text-theme-text">Create Account</h1>
            <p class="text-xs text-theme-muted">Register to save order history and track deliveries.</p>
        </div>

        <form wire:submit="register" class="space-y-4">
            <!-- Name -->
            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-theme-text" for="name">Full Name</label>
                <input 
                    wire:model="name" 
                    id="name" 
                    class="w-full bg-secondary border border-theme-border rounded-theme px-3 py-2.5 text-sm text-theme-text focus:outline-none focus:border-primary" 
                    type="text" 
                    name="name" 
                    required 
                    autofocus 
                    autocomplete="name" 
                />
                @error('name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Email Address -->
            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-theme-text" for="email">Email Address</label>
                <input 
                    wire:model="email" 
                    id="email" 
                    class="w-full bg-secondary border border-theme-border rounded-theme px-3 py-2.5 text-sm text-theme-text focus:outline-none focus:border-primary" 
                    type="email" 
                    name="email" 
                    required 
                    autocomplete="username" 
                />
                @error('email') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Password -->
            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-theme-text" for="password">Password</label>
                <input 
                    wire:model="password" 
                    id="password" 
                    class="w-full bg-secondary border border-theme-border rounded-theme px-3 py-2.5 text-sm text-theme-text focus:outline-none focus:border-primary"
                    type="password"
                    name="password"
                    required 
                    autocomplete="new-password" 
                />
                @error('password') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Confirm Password -->
            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-theme-text" for="password_confirmation">Confirm Password</label>
                <input 
                    wire:model="password_confirmation" 
                    id="password_confirmation" 
                    class="w-full bg-secondary border border-theme-border rounded-theme px-3 py-2.5 text-sm text-theme-text focus:outline-none focus:border-primary"
                    type="password"
                    name="password_confirmation" 
                    required 
                    autocomplete="new-password" 
                />
                @error('password_confirmation') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <button 
                type="submit" 
                wire:loading.attr="disabled"
                class="btn-primary w-full py-3 text-sm font-bold tracking-wider mt-6"
            >
                <span wire:loading.remove>Sign Up</span>
                <span wire:loading class="flex items-center justify-center gap-2">
                    <i class="fa-solid fa-spinner animate-spin"></i> Creating Account...
                </span>
            </button>
        </form>

        <div class="text-center pt-4 border-t border-theme-border text-xs text-theme-muted">
            Already registered? 
            <a href="/login" wire:navigate class="text-primary font-bold hover:underline">
                Login here
            </a>
        </div>
    </div>
</div>
