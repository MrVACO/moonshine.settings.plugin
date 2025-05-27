<?php

declare(strict_types = 1);

namespace MrVaco\Moonshine\Settings;

use MrVaco\Moonshine\Settings\Casts\SettingsTypeEnum;
use MrVaco\Moonshine\Settings\Models\Settings;

class SettingsService
{
    public function get($key)
    {
        return Settings::query()
            ->where('key', $key)
            ->firstOrFail();
    }

    public function set($key, mixed $value): Settings
    {
        $column = match (true)
        {
            is_int($value) => 'integer',
            is_bool($value) => 'boolean',
            is_array($value), is_object($value) => 'json',
            default => 'string'
        };

        $type = match (true)
        {
            is_int($value) => SettingsTypeEnum::Integer,
            is_bool($value) => SettingsTypeEnum::Bool,
            is_array($value), is_object($value) => SettingsTypeEnum::Array,
            default => SettingsTypeEnum::String,
        };

        return Settings::updateOrCreate(
            ['key' => $key],
            [$column => $value, 'type' => $type]
        );
    }
}
