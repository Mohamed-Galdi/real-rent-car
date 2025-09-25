<?php

namespace App\Enums;

enum CarStatus: string
{
    case AVAILABLE   = 'available';
    case RESERVED    = 'reserved';
    case RENTED      = 'rented';
    case MAINTENANCE = 'maintenance';
    case CLEANING    = 'cleaning';
    case UNAVAILABLE = 'unavailable';
    case RETIRED     = 'retired';

    public function label(): string
    {
        return match ($this) {
            self::AVAILABLE   => 'Available',
            self::RESERVED    => 'Reserved',
            self::RENTED      => 'Rented',
            self::MAINTENANCE => 'Maintenance',
            self::CLEANING    => 'Cleaning',
            self::UNAVAILABLE => 'Unavailable',
            self::RETIRED     => 'Retired',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::AVAILABLE   => 'The car is ready for booking and rental.',
            self::RESERVED    => 'The car is reserved for a customer and awaiting pickup.',
            self::RENTED      => 'The car is currently rented by a customer.',
            self::MAINTENANCE => 'The car is undergoing mechanical service or repair.',
            self::CLEANING    => 'The car is being cleaned/prepared before becoming available again.',
            self::UNAVAILABLE => 'The car is temporarily out of service (administrative or technical reasons).',
            self::RETIRED     => 'The car is permanently removed from the fleet.',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::AVAILABLE   => '#10B981', // Green-500
            self::RESERVED    => '#3B82F6', // Blue-500
            self::RENTED      => '#F59E0B', // Amber-500
            self::MAINTENANCE => '#EF4444', // Red-500
            self::CLEANING    => '#8B5CF6', // Violet-500
            self::UNAVAILABLE => '#6B7280', // Gray-500
            self::RETIRED     => '#4B5563', // Gray-600
        };
    }
}
