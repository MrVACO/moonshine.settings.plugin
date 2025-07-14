<?php

declare(strict_types = 1);

namespace MrVaco\Moonshine\Settings;

use Illuminate\Support\ServiceProvider;
use MrVaco\Helpers\Migration;

final class SettingsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../database/migrations/create_settings_table.php' => Migration::get('create_settings_table.php'),
        ], ['settings', 'plugins']);
    }
}
