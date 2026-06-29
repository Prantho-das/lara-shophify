<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class ModuleService
{
    private const SETTING_KEY = 'modules';

    private const ALL_MODULES = [
        'inventory' => 'Inventory Management',
        'reseller' => 'Reseller',
        'accounts' => 'Accounts & Ledger',
        'payroll' => 'Payroll',
    ];

    public function isActive(string $module): bool
    {
        return (bool) ($this->getModules()[$module] ?? false);
    }

    public function getModules(): array
    {
        return Cache::remember('modules_enabled', 3600, function () {
            $raw = Setting::where('key', self::SETTING_KEY)->value('value');
            $stored = $raw ? json_decode($raw, true) : [];

            return array_fill_keys(array_keys(self::ALL_MODULES), false) + $stored;
        });
    }

    public function getLabels(): array
    {
        return self::ALL_MODULES;
    }

    public function enable(string $module): void
    {
        $modules = $this->getModules();
        $modules[$module] = true;
        $this->save($modules);
    }

    public function disable(string $module): void
    {
        $modules = $this->getModules();
        $modules[$module] = false;
        $this->save($modules);
    }

    public function save(array $modules): void
    {
        $validated = array_fill_keys(array_keys(self::ALL_MODULES), false);
        foreach ($modules as $key => $val) {
            if (array_key_exists($key, self::ALL_MODULES)) {
                $validated[$key] = (bool) $val;
            }
        }

        Setting::updateOrCreate(
            ['key' => self::SETTING_KEY],
            ['value' => json_encode($validated)]
        );

        Cache::forget('modules_enabled');
    }
}
