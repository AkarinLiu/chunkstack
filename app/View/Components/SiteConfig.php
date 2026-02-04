<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Services\SiteConfigService;

class SiteConfig extends Component
{
    public string $siteName;
    public string $siteDescription;
    public string $siteUrl;
    public bool $enableSitemap;

    public function __construct()
    {
        $this->siteName = SiteConfigService::siteName();
        $this->siteDescription = SiteConfigService::siteDescription();
        $this->siteUrl = SiteConfigService::siteUrl();
        $this->enableSitemap = SiteConfigService::enableSitemap();
    }

    public function render(): View|Closure|string
    {
        return view('components.site-config');
    }
}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.site-config');
    }
}
