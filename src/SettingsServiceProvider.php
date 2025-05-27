<?php

declare(strict_types = 1);

namespace MrVaco\Moonshine\Settings;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

final class SettingsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../database/migrations/create_settings_table.php' => $this->getMigrationFileName('create_settings_table.php'),
        ], ['settings', 'plugins']);
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     * Возвращает существующий файл миграции, если он найден, в противном случае используется текущая временная метка.
     */
    public static function getMigrationFileName(string $migrationFileName): string
    {
        $timestamp = date('Y_m_d_His');

        $filesystem = app()->make(Filesystem::class);

        return Collection::make([database_path('migrations/')])
            ->flatMap(fn ($path) => $filesystem->glob($path . '*_' . $migrationFileName))
            ->push(database_path("/migrations/{$timestamp}_{$migrationFileName}"))
            ->first();
    }
}
