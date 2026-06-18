<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class QuickActions extends Widget
{
    protected string $view = 'filament.widgets.quick-actions';
    
    protected static ?int $sort = -1; // Show first on top

    protected int | string | array $columnSpan = 'full';
}
