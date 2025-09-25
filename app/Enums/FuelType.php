<?php

namespace App\Enums;

enum FuelType: string
{
    case GASOLINE = 'gasoline';
    case DIESEL = 'diesel';
    case HYBRID = 'hybrid';
    case ELECTRIC = 'electric';
    case PLUGIN_HYBRID = 'plug-in hybrid';
    case LPG = 'lpg';
    case CNG = 'cng';
    case HYDROGEN = 'hydrogen';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function toArray(): array
    {
        return [
            self::GASOLINE->value,
            self::DIESEL->value,
            self::HYBRID->value,
            self::ELECTRIC->value,
            self::PLUGIN_HYBRID->value,
            self::LPG->value,
            self::CNG->value,
            self::HYDROGEN->value,
        ];
    }

    public static function forFrontend(): array
    {
        return self::toArray();
    }
}
