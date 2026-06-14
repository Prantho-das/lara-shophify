<?php

namespace App\Livewire\Storefront\Components;

use Livewire\Component;
use App\Models\Menu;
use App\Models\Setting;

class Footer extends Component
{
    public $settings = [];
    public $footerSections = [];

    public function mount()
    {
        $this->settings = Setting::pluck('value', 'key')->toArray();
        $this->footerSections = json_decode($this->settings['footer_sections'] ?? '[]', true);
    }

    public function render()
    {
        return view('livewire.storefront.components.footer');
    }
}
