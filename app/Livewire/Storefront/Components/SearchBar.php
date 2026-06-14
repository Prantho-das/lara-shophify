<?php

namespace App\Livewire\Storefront\Components;

use Livewire\Component;
use App\Models\Product;

class SearchBar extends Component
{
    public $query = '';
    public $results = [];

    public function updatedQuery()
    {
        if (strlen($this->query) >= 2) {
            $this->results = Product::where('name', 'like', '%' . $this->query . '%')
                ->orWhere('description', 'like', '%' . $this->query . '%')
                ->limit(5)
                ->get()
                ->toArray();
        } else {
            $this->results = [];
        }
    }

    public function submitSearch()
    {
        if (!empty($this->query)) {
            return $this->redirect('/shop?search=' . urlencode($this->query), navigate: true);
        }
    }

    public function render()
    {
        return view('livewire.storefront.components.search-bar');
    }
}
