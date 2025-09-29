<?php

namespace App\Http\Controllers\Client;

use App\Enums\PaymentStatus;
use App\Enums\ReservationStatus;
use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReservationsController extends Controller
{
    public function index(Request $request)
    {

        $reservations = Reservation::where('user_id', auth()->user()->id)
            ->with('car')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return inertia('Client/Reservations/Index', [
            'reservations' => $reservations,
        ]);
    }

    public function show($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->load(['user', 'car', 'payments']);

        return inertia('Client/Reservations/Show', [
            'reservation' => $reservation,
            'statusMeta' => ReservationStatus::getMeta(),
            'paymentStatusMeta' => PaymentStatus::getMeta(),
        ]);
    }

    public function print($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->load(['user', 'car', 'payments']);

        $pdf = Pdf::loadView('admin.reservations.print', [
            'reservation' => $reservation,
            'statusMeta' => ReservationStatus::getMeta(),
            'paymentStatusMeta' => PaymentStatus::getMeta(),
            'currency' => config('app.currency_symbol'),
        ])->setPaper('a4', 'portrait');

        return $pdf->download($reservation->reservation_number . '.pdf');
    }
}
