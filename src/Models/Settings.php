<?php

namespace MrVaco\Moonshine\Settings\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use MrVaco\Moonshine\Settings\Casts\SettingsTypeEnum;

class Settings extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'key';

    protected $fillable = [
        'key',
        'type',
        'string',
        'boolean',
        'integer',
        'json',
    ];

    protected function casts(): array
    {
        return [
            'key' => 'string',
            'type' => SettingsTypeEnum::class,
            'string' => 'string',
            'boolean' => 'bool',
            'integer' => 'int',
            'json' => 'array',
        ];
    }

    protected function value(): Attribute
    {
        return Attribute::make(
            get: fn () => match ($this->type)
            {
                SettingsTypeEnum::Bool => $this->boolean,
                SettingsTypeEnum::Integer => $this->integer,
                SettingsTypeEnum::Array, SettingsTypeEnum::Json => $this->json,
                default => $this->string,
            },
        );
    }
}
