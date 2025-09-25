<?php

namespace App\Enums;

enum ReservationStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case ACTIVE = 'active';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case NO_SHOW = 'no_show';

    public static function statusColors(): array
    {
        return [
            self::PENDING->value => '#F59E0B',    // Gray-900
            self::CONFIRMED->value => '#10B981',  // Green-500
            self::ACTIVE->value => '#3B82F6',     // Amber-500
            self::COMPLETED->value => '#111827',  // Blue-500
            self::CANCELLED->value => '#EF4444',  // Red-500
            self::NO_SHOW->value => '#6B7280',    // Gray-500
        ];
    }

    public static function getMeta(): array
    {
        return array_map(function ($case) {
            return [
                'value' => $case->value,
                'label' => ucfirst(str_replace('_', ' ', $case->value)),
                'color' => self::statusColors()[$case->value] ?? '#6B7280',
            ];
        }, self::cases());
    }
}
