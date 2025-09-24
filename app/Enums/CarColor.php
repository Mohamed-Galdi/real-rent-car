<?php

namespace App\Enums;

use Illuminate\Support\Collection;

enum CarColor: string
{
    case WHITE = 'white';
    case BLACK = 'black';
    case SILVER = 'silver';
    case GRAY = 'gray';
    case RED = 'red';
    case BLUE = 'blue';
    case GREEN = 'green';
    case YELLOW = 'yellow';
    case ORANGE = 'orange';
    case BROWN = 'brown';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function toArray(): array
    {
        return [
            self::WHITE->value => [
                'name' => 'White',
                'hex' => '#F9FAFB',
            ],
            self::BLACK->value => [
                'name' => 'Black',
                'hex' => '#1F2937',
            ],
            self::SILVER->value => [
                'name' => 'Silver',
                'hex' => '#E5E7EB',
            ],
            self::GRAY->value => [
                'name' => 'Gray',
                'hex' => '#9CA3AF',
            ],
            self::RED->value => [
                'name' => 'Red',
                'hex' => '#FEE2E2',
            ],
            self::BLUE->value => [
                'name' => 'Blue',
                'hex' => '#DBEAFE',
            ],
            self::GREEN->value => [
                'name' => 'Green',
                'hex' => '#DCFCE7',
            ],
            self::YELLOW->value => [
                'name' => 'Yellow',
                'hex' => '#FEF9C3',
            ],
            self::ORANGE->value => [
                'name' => 'Orange',
                'hex' => '#FFEDD5',
            ],
            self::BROWN->value => [
                'name' => 'Brown',
                'hex' => '#F3E8D2',
            ],
        ];
    }

    public static function forFrontend(): array
    {
        return array_map(
            fn ($color) => [
                'name' => $color['name'],
                'value' => $color['value'] ?? $color['name'],
                'hex' => $color['hex'],
            ],
            array_map(
                fn ($value, $key) => ['value' => $key] + $value,
                self::toArray(),
                array_keys(self::toArray())
            )
        );
    }
}
