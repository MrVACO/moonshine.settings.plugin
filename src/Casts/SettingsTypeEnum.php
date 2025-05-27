<?php

declare(strict_types = 1);

namespace MrVaco\Moonshine\Settings\Casts;

enum SettingsTypeEnum: string
{
    case Bool = 'boolean';
    case Integer = 'integer';
    case Array = 'array';
    case Json = 'json';
    case String = 'string';
}
