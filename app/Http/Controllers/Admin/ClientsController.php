<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Enums\PaymentStatus;
use App\Enums\UserRole;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClientsController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->string('search')->toString();
        $status = $request->string('status')->toString();

        $query = User::query()
            ->where('role', UserRole::CLIENT)
            ->when($search, function ($q) use ($search) {
                $q->where(function ($w) use ($search) {
                    $w->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($status, function ($q) use ($status) {
                if ($status === 'active') {
                    $q->where('is_active', true);
                } elseif ($status === 'suspended') {
                    $q->where('is_active', false);
                }
            })
            ->withCount(['reservations', 'payments'])
            ->orderBy('name');

        $clients = $query->paginate(10)->withQueryString();

        $statusCounts = [
            'active' => User::where('role', UserRole::CLIENT)->where('is_active', true)->count(),
            'suspended' => User::where('role', UserRole::CLIENT)->where('is_active', false)->count(),
        ];

        $statuses = [
            'active' => ['label' => 'Active', 'count' => $statusCounts['active'], 'color' => '#10B981'],
            'suspended' => ['label' => 'Suspended', 'count' => $statusCounts['suspended'], 'color' => '#EF4444'],
        ];

        return Inertia::render('Admin/Clients/Index', [
            'clients' => $clients,
            'filters' => [
                'search' => $search,
                'status' => $status,
            ],
            'statuses' => $statuses,
        ]);
    }

    public function show(User $client): Response
    {
        $totalSpent = Payment::where('user_id', $client->id)
            ->where('status', PaymentStatus::COMPLETED)
            ->sum('amount');

        $reservations = $client->reservations()
            ->with(['car'])
            ->orderByDesc('created_at')
            ->paginate(10, ['*'], 'reservations_page')
            ->withQueryString();

        $payments = $client->payments()
            ->with(['reservation'])
            ->orderByDesc('created_at')
            ->paginate(10, ['*'], 'payments_page')
            ->withQueryString();

        return Inertia::render('Admin/Clients/Show', [
            'client' => [
                'id' => $client->id,
                'name' => $client->name,
                'email' => $client->email,
                'is_active' => (bool) $client->is_active,
                'created_at' => $client->created_at,
            ],
            'stats' => [
                'total_reservations' => $client->reservations()->count(),
                'total_payments' => $client->payments()->count(),
                'total_spent' => (float) $totalSpent,
            ],
            'reservations' => $reservations,
            'payments' => $payments,
        ]);
    }

    public function suspend(User $client)
    {
        // Restrict this action
        return redirect()
            ->back()
            ->with('restricted_action', 'This is a demo version. For security reasons, create, update, and delete actions are disabled.');

        $client->is_active = false;
        $client->save();

        return redirect()
            ->route('admin.clients.show', $client)
            ->with('success', 'Client suspended successfully.');
    }

    public function activate(User $client)
    {
        // Restrict this action
        return redirect()
            ->back()
            ->with('restricted_action', 'This is a demo version. For security reasons, create, update, and delete actions are disabled.');

        $client->is_active = true;
        $client->save();

        return redirect()
            ->route('admin.clients.show', $client)
            ->with('success', 'Client activated successfully.');
    }
}
