<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use App\Models\Setting;

class CmsPage extends Component
{
    public $title = '';
    public $content = '';
    public $seoTitle = '';
    public $seoDescription = '';
    public $settings = [];

    public function mount($slug)
    {
        $this->settings = Setting::pluck('value', 'key')->toArray();

        // 1. Check database dynamic pages
        $page = \App\Models\Page::where('slug', $slug)->where('status', 'active')->first();
        if ($page) {
            $this->title = $page->title;
            $this->content = $page->content;
            $this->seoTitle = $page->seo_title ?? $page->title;
            $this->seoDescription = $page->seo_description ?? '';
            return;
        }

        // 2. Fallback to settings variables
        switch ($slug) {
            case 'privacy-policy':
                $this->title = 'Privacy Policy';
                $this->content = $this->settings['privacy_policy'] ?? 'Privacy Policy content not configured yet.';
                break;
            case 'terms-of-service':
                $this->title = 'Terms of Service';
                $this->content = $this->settings['terms_of_service'] ?? 'Terms of Service content not configured yet.';
                break;
            case 'refund-policy':
                $this->title = 'Refund & Return Policy';
                $this->content = $this->settings['refund_policy'] ?? 'Refund & Return Policy content not configured yet.';
                break;
            default:
                abort(404);
        }
    }

    public function render()
    {
        return view('livewire.storefront.cms-page')
            ->layout('storefront.layouts.app', [
                'title' => !empty($this->seoTitle) ? $this->seoTitle : $this->title,
                'meta_description' => $this->seoDescription
            ]);
    }
}
