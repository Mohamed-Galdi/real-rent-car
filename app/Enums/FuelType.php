<?php

namespace App\Enums;

enum FuelType: string
{
    case GASOLINE = 'Gasoline';
    case DIESEL = 'Diesel';
    case HYBRID = 'Hybrid';
    case ELECTRIC = 'Electric';
    case PLUGIN_HYBRID = 'Plug-in Hybrid';
    case LPG = 'LPG';
    case CNG = 'CNG';
    case HYDROGEN = 'Hydrogen';

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
