<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Enums\PaymentStatus;
use Illuminate\Http\Request;
use App\Models\Payment;
use Inertia\Inertia;
use Inertia\Response;

class PaymentsController extends Controller
{
    public function index(Request $request): Response
    {
        $payments = Payment::query()
            ->with(['user:id,name,email', 'reservation:id,reservation_number'])
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $statuses = collect(PaymentStatus::cases())->mapWithKeys(function ($status) {
            return [
                $status->value => [
                    'label' => $status->label(),
                    'count' => $statusCounts[$status->value] ?? 0,
                    'color' => $status->color(),
                ]
            ];
        })->toArray();

        

        return Inertia::render('Admin/Payments/Index', [
            'payments' => $payments,
            'statuses' => $statuses,
        ]);
    }
}
