<?php

namespace App\Enums;

enum TicketStatus: string
{
    case NEW = "new";
    case IN_PROGRESS = "in_progress";
    case CLOSED = "closed";

    public function label(): string
    {
        return match ($this) {
            TicketStatus::NEW => 'New',
            TicketStatus::IN_PROGRESS => 'In Progress',
            TicketStatus::CLOSED => 'Closed',
        };
    }

    public function color(): string
    {
        return match ($this) {
            TicketStatus::NEW => '#007bff', // blue
            TicketStatus::IN_PROGRESS => '#FFC107', // yellow
            TicketStatus::CLOSED => '#28A745', // green
        };
    }
}
