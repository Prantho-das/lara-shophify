<?php

$dir = __DIR__ . '/app/Filament/Resources';

$resources = [
    'Orders/OrderResource.php' => ['group' => 'Shop', 'icon' => 'heroicon-o-shopping-bag', 'sort' => 1],
    'Users/UserResource.php' => ['group' => 'Shop', 'icon' => 'heroicon-o-users', 'sort' => 2],
    'Products/ProductResource.php' => ['group' => 'Catalog', 'icon' => 'heroicon-o-squares-2x2', 'sort' => 1],
    'Categories/CategoryResource.php' => ['group' => 'Catalog', 'icon' => 'heroicon-o-tag', 'sort' => 2],
    'Brands/BrandResource.php' => ['group' => 'Catalog', 'icon' => 'heroicon-o-star', 'sort' => 3],
    'Coupons/CouponResource.php' => ['group' => 'Marketing', 'icon' => 'heroicon-o-ticket', 'sort' => 1],
    'Campaigns/CampaignResource.php' => ['group' => 'Marketing', 'icon' => 'heroicon-o-receipt-percent', 'sort' => 2],
    'Banners/BannerResource.php' => ['group' => 'Storefront', 'icon' => 'heroicon-o-photo', 'sort' => 1],
    'Pages/PageResource.php' => ['group' => 'Storefront', 'icon' => 'heroicon-o-document-text', 'sort' => 2],
    'ShippingZones/ShippingZoneResource.php' => ['group' => 'Settings', 'icon' => 'heroicon-o-truck', 'sort' => 1],
    'Settings/SettingResource.php' => ['group' => 'Settings', 'icon' => 'heroicon-o-cog-6-tooth', 'sort' => 2],
];

foreach ($resources as $file => $config) {
    $path = $dir . '/' . $file;
    if (file_exists($path)) {
        $content = file_get_contents($path);
        
        // Remove ALL navigation properties first
        $content = preg_replace('/^\s*protected static \?(string|int) \$(navigationGroup|navigationIcon|navigationSort).*?;\r?\n/m', '', $content);
        $content = preg_replace('/^\s*protected static string\|BackedEnum\|null \$navigationIcon.*?;\r?\n/m', '', $content);
        
        $newProps = "\n    protected static ?string \$navigationGroup = '{$config['group']}';\n" .
                    "    protected static ?string \$navigationIcon = '{$config['icon']}';\n" .
                    "    protected static ?int \$navigationSort = {$config['sort']};\n";

        // Insert new properties after $model
        $content = preg_replace('/(protected static \?string \$model = [a-zA-Z0-9_\\\\]+::class;)/', "$1\n" . $newProps, $content);

        file_put_contents($path, $content);
        echo "Fixed $file\n";
    }
}
