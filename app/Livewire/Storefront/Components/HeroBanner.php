<?php

namespace App\Livewire\Storefront\Components;

use Livewire\Component;
use App\Models\Banner;

class HeroBanner extends Component
{
    public $banners = [];
    public $bannerIds = [];

    public function mount($bannerIds = [])
    {
        $this->bannerIds = $bannerIds;
        $query = Banner::where('status', 'active');
        if (!empty($this->bannerIds)) {
            $query->whereIn('id', $this->bannerIds);
        }
        $this->banners = $query->get()->toArray();
    }

    public function render()
    {
        return view('livewire.storefront.components.hero-banner');
    }
}
