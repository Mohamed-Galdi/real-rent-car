<?php

namespace App\Http\Controllers;

use App\Enums\CarStatus;
use App\Models\Car;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomePagesController extends Controller
{
    public function index()
    {
        $homeCars = Car::where('status', CarStatus::AVAILABLE)
            ->select('id', 'make', 'model', 'year', 'price_per_day', 'description', 'fuel_type')
            ->limit(6)
            ->get();

        return inertia('Welcome', compact('homeCars'));
    }

    public function fleet(Request $request)
    {
        $query = Car::where('status', CarStatus::AVAILABLE)
            ->select('id', 'make', 'model', 'year', 'price_per_day', 'description', 'fuel_type');

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('make', 'like', "%{$searchTerm}%")
                    ->orWhere('model', 'like', "%{$searchTerm}%")
                    ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Make filter
        if ($request->filled('make')) {
            $query->where('make', $request->make);
        }

        // Fuel type filter
        if ($request->filled('fuel_type')) {
            $query->where('fuel_type', $request->fuel_type);
        }

        // Year filter
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        // Price range filter
        if ($request->filled('min_price')) {
            $query->where('price_per_day', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price_per_day', '<=', $request->max_price);
        }

        // Sorting
        $sort = $request->get('sort', 'make_asc');
        switch ($sort) {
            case 'make_desc':
                $query->orderBy('make', 'desc');
                break;
            case 'price_asc':
                $query->orderBy('price_per_day', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price_per_day', 'desc');
                break;
            case 'year_asc':
                $query->orderBy('year', 'asc');
                break;
            case 'year_desc':
                $query->orderBy('year', 'desc');
                break;
            default: // make_asc
                $query->orderBy('make', 'asc');
                break;
        }

        $cars = $query->paginate(9)->withQueryString();

        // Get filter options
        $makes = Car::where('status', CarStatus::AVAILABLE)
            ->distinct()
            ->orderBy('make')
            ->pluck('make')
            ->toArray();

        $fuelTypes = Car::where('status', CarStatus::AVAILABLE)
            ->distinct()
            ->orderBy('fuel_type')
            ->pluck('fuel_type')
            ->toArray();

        $years = Car::where('status', CarStatus::AVAILABLE)
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        $filters = $request->only(['search', 'make', 'fuel_type', 'min_price', 'max_price', 'year', 'sort']);

        return inertia('Fleet', compact('cars', 'makes', 'fuelTypes', 'years', 'filters'));
    }

    public function show(Car $car)
    {
        // Check if car is available for booking
        if ($car->status !== CarStatus::AVAILABLE) {
            return redirect()->route('fleet')->with('error', 'This car is not available for booking.');
        }


        return inertia('Booking', compact('car'));
    }

    // public function store(Request $request, Car $car)
    // {
    //     $request->validate([
    //         'start_date' => ['required', 'date', 'after_or_equal:today'],
    //         'end_date' => ['required', 'date', 'after:start_date'],
    //         'pickup_location' => ['required', 'string', 'max:255'],
    //         'return_location' => ['required', 'string', 'max:255'],
    //         'driver_license' => ['required', 'string', 'max:50'],
    //         'phone' => ['required', 'string', 'max:20'],
    //         'additional_notes' => ['nullable', 'string', 'max:1000'],
    //     ]);

    //     // Check if car is still available
    //     if ($car->status !== CarStatus::AVAILABLE) {
    //         throw ValidationException::withMessages([
    //             'car' => 'This car is no longer available for booking.'
    //         ]);
    //     }

    //     // Check for date conflicts
    //     $conflictingReservation = Reservation::where('car_id', $car->id)
    //         ->whereIn('status', ['confirmed', 'active'])
    //         ->where(function ($query) use ($request) {
    //             $query->whereBetween('start_date', [$request->start_date, $request->end_date])
    //                 ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
    //                 ->orWhere(function ($q) use ($request) {
    //                     $q->where('start_date', '<=', $request->start_date)
    //                         ->where('end_date', '>=', $request->end_date);
    //                 });
    //         })
    //         ->exists();

    //     if ($conflictingReservation) {
    //         throw ValidationException::withMessages([
    //             'dates' => 'The selected dates are not available. Please choose different dates.'
    //         ]);
    //     }

    //     // Calculate total price
    //     $startDate = Carbon::parse($request->start_date);
    //     $endDate = Carbon::parse($request->end_date);
    //     $days = $startDate->diffInDays($endDate);
    //     $totalPrice = $days * $car->price_per_day;

    //     // Create reservation
    //     $reservation = Reservation::create([
    //         'user_id' => auth()->id(),
    //         'car_id' => $car->id,
    //         'start_date' => $request->start_date,
    //         'end_date' => $request->end_date,
    //         'pickup_location' => $request->pickup_location,
    //         'return_location' => $request->return_location,
    //         'driver_license' => $request->driver_license,
    //         'phone' => $request->phone,
    //         'additional_notes' => $request->additional_notes,
    //         'total_price' => $totalPrice,
    //         'status' => 'pending', // Assuming you have a status enum
    //     ]);

    //     return redirect()->route('booking.confirmation', $reservation)
    //         ->with('success', 'Your booking request has been submitted successfully!');
    // }

    public function confirmation(Reservation $reservation)
    {
        // Make sure user can only see their own reservations
        if ($reservation->user_id !== Auth::user()->id) {
            abort(403);
        }

        return inertia('BookingConfirmation', [
            'reservation' => $reservation->load(['car', 'user']),
        ]);
    }

    public function contact()
    {
        return inertia('Contact');
    }
    public function about()
    {
        return inertia('About');
    }
}
