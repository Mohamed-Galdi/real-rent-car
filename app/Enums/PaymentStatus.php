<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';
    case PARTIALLY_REFUNDED = 'partially_refunded';

    public static function getMeta(): array
    {
        return array_map(function ($case) {
            return [
                'value' => $case->value,
                'label' => ucfirst(str_replace('_', ' ', $case->value)),
            ];
        }, self::cases());
    }

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::COMPLETED => 'Completed',
            self::FAILED => 'Failed',
            self::CANCELLED => 'Cancelled',
            self::REFUNDED => 'Refunded',
            self::PARTIALLY_REFUNDED => 'Partially Refunded',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => '#FFC107', // yellow
            self::COMPLETED => '#28A745', // green
            self::FAILED => '#DC3545', // red
            self::CANCELLED => '#6C757D', // gray
            self::REFUNDED => '#007bff', // blue
            self::PARTIALLY_REFUNDED => '#fd7e14', // orange
        };
    }

    
}
