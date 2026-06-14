<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfilePage extends Component
{
    // Profile Update Fields
    public $name = '';
    public $email = '';

    // Password Update Fields
    public $current_password = '';
    public $password = '';
    public $password_confirmation = '';

    // Settings
    public $settings = [];

    // Active Tab
    public $activeTab = 'dashboard';

    public function mount()
    {
        $user = auth()->user();
        if (!$user) {
            return $this->redirect('/login', navigate: true);
        }

        $this->name = $user->name;
        $this->email = $user->email;
        $this->settings = Setting::pluck('value', 'key')->toArray();
    }

    public function selectTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function updateProfile()
    {
        $user = auth()->user();
        
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $user->update($validated);

        session()->flash('message', 'Profile updated successfully!');
    }

    public function updatePassword()
    {
        $user = auth()->user();

        $validated = $this->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset(['current_password', 'password', 'password_confirmation']);

        session()->flash('message', 'Password updated successfully!');
    }

    public function getOrdersProperty()
    {
        return Order::where('user_id', auth()->id())
            ->with(['orderItems.product.images', 'orderItems.variant'])
            ->orderBy('id', 'desc')
            ->get();
    }

    public function getStatsProperty()
    {
        $orders = $this->orders;
        $totalSpent = $orders->where('status', '!=', 'cancelled')->sum('total_amount');
        $totalOrders = $orders->count();
        $pendingOrders = $orders->where('status', 'pending')->count();
        $deliveredOrders = $orders->where('status', 'delivered')->count();

        return [
            'total_spent' => $totalSpent,
            'total_orders' => $totalOrders,
            'pending_orders' => $pendingOrders,
            'delivered_orders' => $deliveredOrders,
        ];
    }

    public function render()
    {
        return view('livewire.storefront.profile-page', [
            'orders' => $this->orders,
            'stats' => $this->stats,
        ])->layout('storefront.layouts.app');
    }
}
