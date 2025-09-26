<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SupportController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->string('search')->toString();
        $status = $request->string('status')->toString();
        $ticketType = $request->string('type', 'customer')->toString();

        $query = Ticket::query()
            ->when($ticketType === 'customer', function ($q) {
                $q->whereNotNull('user_id')
                  ->with('user:id,name,email');
            }, function ($q) {
                $q->whereNull('user_id');
            })
            ->when($search, function ($q) use ($search, $ticketType) {
                $q->where(function ($w) use ($search, $ticketType) {
                    $w->where('subject', 'like', "%{$search}%");
                    
                    if ($ticketType === 'customer') {
                        $w->orWhereHas('user', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                        });
                    } else {
                        $w->orWhere('guest_name', 'like', "%{$search}%")
                          ->orWhere('guest_email', 'like', "%{$search}%");
                    }
                });
            })
            ->when($status && $status !== 'all', function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->latest();

        $tickets = $query->paginate(10)->withQueryString();

        // Get status counts for both ticket types
        $statusCounts = [
            'customer' => [
                'all' => Ticket::whereNotNull('user_id')->count(),
                ...collect(TicketStatus::cases())->mapWithKeys(fn($status) => [
                    $status->value => Ticket::whereNotNull('user_id')
                        ->where('status', $status->value)
                        ->count()
                ])->toArray()
            ],
            'guest' => [
                'all' => Ticket::whereNull('user_id')->count(),
                ...collect(TicketStatus::cases())->mapWithKeys(fn($status) => [
                    $status->value => Ticket::whereNull('user_id')
                        ->where('status', $status->value)
                        ->count()
                ])->toArray()
            ]
        ];

        $statuses = collect(TicketStatus::cases())->mapWithKeys(function ($status) {
            return [
                $status->value => [
                    'label' => $status->label(),
                    'color' => $status->color(),
                ]
            ];
        })->toArray();

        return Inertia::render('Admin/Support/Index', [
            'tickets' => $tickets,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'type' => $ticketType,
            ],
            'statuses' => $statuses,
            'statusCounts' => $statusCounts,
        ]);
    }

    // In SupportController.php

    public function show(Ticket $ticket)
    {
        // Eager load the messages and user relationship
        $ticket->load(['messages' => function ($query) {
            $query->orderBy('created_at', 'asc');
        }, 'user']);

        return Inertia::render('Admin/Support/Show', [
            'ticket' => $ticket,
            'isGuest' => is_null($ticket->user_id),
        ]);
    }

    public function reply(Request $request, Ticket $ticket)
    {
        $request->validate([
            'message' => 'required|string|min:1',
        ]);

        // Only allow replies to customer tickets
        if (is_null($ticket->user_id)) {
            return back()->with('error', 'Cannot reply to guest tickets');
        }

        $ticket->messages()->create([
            'message' => $request->message,
            'is_admin' => true,
        ]);

        $ticket->update([
            'status' => TicketStatus::IN_PROGRESS,
        ]);


        return back()->with('success', 'Reply sent successfully');
    }

    public function close(Ticket $ticket)
    {
        $ticket->update([
            'status' => TicketStatus::CLOSED,
        ]);

        return redirect()->route('admin.support.index');
    }
}
