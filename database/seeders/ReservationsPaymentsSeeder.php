<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Car;
use App\Models\Reservation;
use App\Models\Payment;
use App\Enums\UserRole;
use App\Enums\ReservationStatus;
use App\Enums\PaymentStatus;
use App\Enums\PaymentMethod;
use App\Enums\CarStatus;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ReservationsPaymentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('role', UserRole::CLIENT)->get();
        $cars = Car::where('status', 'available')->take(25)->get(); // Increased to handle more reservations

        if ($users->isEmpty() || $cars->isEmpty()) {
            $this->command->error('Please ensure you have client users and available cars in the database first.');
            return;
        }

        $reservations = $this->getReservationData();
        $usedCars = []; // Track which cars are assigned to which reservations

        foreach ($reservations as $reservationData) {
            $user = $users->random();

            // For same-day reservations, try to reuse cars if possible
            if ($this->isSameDayReservation($reservationData, $reservations, $usedCars)) {
                $car = $this->findAvailableCarForSameDay($cars, $usedCars, $reservationData);
            } else {
                $car = $cars->random();
            }

            // Calculate amounts
            $dailyRate = $car->price_per_day;
            $totalDays = Carbon::parse($reservationData['start_date'])->diffInDays(Carbon::parse($reservationData['end_date'])) + 1;
            $subtotal = $dailyRate * $totalDays;
            $taxAmount = $subtotal * 0.07; // 7% tax
            $discountAmount = $reservationData['discount_amount'] ?? 0;
            $totalAmount = $subtotal + $taxAmount - $discountAmount;

            $reservation = Reservation::create([
                'user_id' => $user->id,
                'car_id' => $car->id,
                'start_date' => $reservationData['start_date'],
                'end_date' => $reservationData['end_date'],
                'pickup_time' => $reservationData['pickup_time'] ?? '09:00',
                'return_time' => $reservationData['return_time'] ?? '18:00',
                'pickup_location' => $reservationData['pickup_location'] ?? 'Main Office',
                'return_location' => $reservationData['return_location'] ?? 'Main Office',
                'total_days' => $totalDays,
                'daily_rate' => $dailyRate,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'status' => $reservationData['status'],
                'notes' => $reservationData['notes'] ?? null,
                'cancellation_reason' => $reservationData['cancellation_reason'] ?? null,
                'cancelled_at' => $reservationData['cancelled_at'] ?? null,
                'created_at' => $reservationData['created_at'],
                'updated_at' => $reservationData['updated_at'],
            ]);

            // Track the car usage
            $usedCars[] = [
                'car_id' => $car->id,
                'reservation_id' => $reservation->id,
                'start_date' => $reservationData['start_date'],
                'end_date' => $reservationData['end_date'],
                'status' => $reservationData['status'],
            ];

            // Update car status based on reservation status
            $this->updateCarStatus($car, $reservationData['status'], $reservationData);

            // Create payments based on reservation status
            $this->createPaymentsForReservation($reservation, $reservationData);
        }

        $this->command->info('Created ' . count($reservations) . ' reservations with payments and updated car statuses.');
    }

    /**
     * Update car status based on reservation status and dates
     */
    private function updateCarStatus(Car $car, ReservationStatus $reservationStatus, array $reservationData): void
    {
        $now = Carbon::now();
        $startDate = Carbon::parse($reservationData['start_date']);
        $endDate = Carbon::parse($reservationData['end_date']);

        $newStatus = match ($reservationStatus) {
            ReservationStatus::PENDING => CarStatus::AVAILABLE, // Keep available until confirmed
            ReservationStatus::CONFIRMED => $startDate->isFuture() ? CarStatus::RESERVED : CarStatus::RENTED,
            ReservationStatus::ACTIVE => CarStatus::RENTED,
            ReservationStatus::COMPLETED => CarStatus::CLEANING, // Car needs cleaning after rental
            ReservationStatus::CANCELLED => CarStatus::AVAILABLE,
            ReservationStatus::NO_SHOW => CarStatus::AVAILABLE,
        };

        // Additional logic for timing
        if ($reservationStatus === ReservationStatus::CONFIRMED) {
            if ($startDate->isToday()) {
                $newStatus = CarStatus::RESERVED; // Reserved for today's pickup
            } elseif ($startDate->isPast() && $endDate->isFuture()) {
                $newStatus = CarStatus::RENTED; // Currently being rented
            } elseif ($endDate->isPast()) {
                $newStatus = CarStatus::CLEANING; // Should be cleaning after return
            }
        }

        $car->update(['status' => $newStatus]);

        $this->command->info("Updated car ID {$car->id} status to: {$newStatus->value}");
    }

    /**
     * Check if this is a same-day reservation scenario
     */
    private function isSameDayReservation(array $currentReservation, array $allReservations, array $usedCars): bool
    {
        $currentDate = Carbon::parse($currentReservation['start_date'])->format('Y-m-d');

        foreach ($usedCars as $usedCar) {
            $usedDate = Carbon::parse($usedCar['start_date'])->format('Y-m-d');
            if ($currentDate === $usedDate) {
                return true;
            }
        }

        return false;
    }

    /**
     * Find an available car for same-day reservations (different times)
     */
    private function findAvailableCarForSameDay( $cars, array $usedCars, array $reservationData): Car
    {
        $requestedDate = Carbon::parse($reservationData['start_date'])->format('Y-m-d');
        $requestedPickupTime = $reservationData['pickup_time'] ?? '09:00';
        $requestedReturnTime = $reservationData['return_time'] ?? '18:00';

        // Find cars that are available or have different time slots on the same day
        foreach ($usedCars as $usedCar) {
            $usedDate = Carbon::parse($usedCar['start_date'])->format('Y-m-d');

            if ($requestedDate === $usedDate) {
                // Check if times don't overlap (simplified check)
                $car = $cars->find($usedCar['car_id']);
                if ($car && $this->timeSlotsAvailable($requestedPickupTime, $requestedReturnTime)) {
                    return $car;
                }
            }
        }

        // If no same-day car found, return a random available car
        return $cars->random();
    }

    /**
     * Simple check for available time slots (simplified for demo)
     */
    private function timeSlotsAvailable(string $pickupTime, string $returnTime): bool
    {
        // Simplified logic - in real world, you'd check actual conflicts
        return rand(0, 1) === 1; // 50% chance of availability
    }

    /**
     * Get reservation test data covering various scenarios including same-day reservations
     */
    private function getReservationData(): array
    {
        $now = Carbon::now();

        return [
            // 1. Completed reservation with successful payment
            [
                'start_date' => $now->copy()->subDays(30),
                'end_date' => $now->copy()->subDays(25),
                'status' => ReservationStatus::COMPLETED,
                'pickup_time' => '10:00',
                'return_time' => '17:00',
                'pickup_location' => 'Airport Terminal 1',
                'return_location' => 'Airport Terminal 1',
                'notes' => 'Customer was very satisfied with the service.',
                'created_at' => $now->copy()->subDays(35),
                'updated_at' => $now->copy()->subDays(25),
                'payment_scenarios' => [
                    [
                        'status' => PaymentStatus::COMPLETED,
                        'method' => PaymentMethod::CREDIT_CARD,
                        'processed_at' => $now->copy()->subDays(35),
                    ]
                ]
            ],

            // 2. Active reservation with payment completed
            [
                'start_date' => $now->copy()->subDays(2),
                'end_date' => $now->copy()->addDays(3),
                'status' => ReservationStatus::ACTIVE,
                'pickup_location' => 'Downtown Office',
                'return_location' => 'Downtown Office',
                'notes' => 'Customer requested GPS navigation system.',
                'created_at' => $now->copy()->subDays(10),
                'updated_at' => $now->copy()->subDays(2),
                'payment_scenarios' => [
                    [
                        'status' => PaymentStatus::COMPLETED,
                        'method' => PaymentMethod::PAYPAL,
                        'processed_at' => $now->copy()->subDays(10),
                    ]
                ]
            ],

            // 3. Confirmed future reservation
            [
                'start_date' => $now->copy()->addDays(7),
                'end_date' => $now->copy()->addDays(10),
                'status' => ReservationStatus::CONFIRMED,
                'pickup_location' => 'Hotel Pickup',
                'return_location' => 'Airport Terminal 2',
                'created_at' => $now->copy()->subDays(5),
                'updated_at' => $now->copy()->subDays(5),
                'payment_scenarios' => [
                    [
                        'status' => PaymentStatus::COMPLETED,
                        'method' => PaymentMethod::STRIPE,
                        'processed_at' => $now->copy()->subDays(5),
                    ]
                ]
            ],

            // 4. Same-day reservation #1 - Morning slot
            [
                'start_date' => $now->copy(),
                'end_date' => $now->copy(),
                'status' => ReservationStatus::ACTIVE,
                'pickup_time' => '08:00',
                'return_time' => '12:00',
                'pickup_location' => 'Main Office',
                'return_location' => 'Main Office',
                'notes' => 'Same-day morning rental.',
                'created_at' => $now->copy()->subHours(2),
                'updated_at' => $now->copy()->subHours(1),
                'payment_scenarios' => [
                    [
                        'status' => PaymentStatus::COMPLETED,
                        'method' => PaymentMethod::CREDIT_CARD,
                        'processed_at' => $now->copy()->subHours(2),
                    ]
                ]
            ],

            // 5. Same-day reservation #2 - Afternoon slot
            [
                'start_date' => $now->copy(),
                'end_date' => $now->copy(),
                'status' => ReservationStatus::CONFIRMED,
                'pickup_time' => '14:00',
                'return_time' => '18:00',
                'pickup_location' => 'Main Office',
                'return_location' => 'Main Office',
                'notes' => 'Same-day afternoon rental.',
                'created_at' => $now->copy()->subHours(4),
                'updated_at' => $now->copy()->subHours(3),
                'payment_scenarios' => [
                    [
                        'status' => PaymentStatus::COMPLETED,
                        'method' => PaymentMethod::PAYPAL,
                        'processed_at' => $now->copy()->subHours(4),
                    ]
                ]
            ],

            // 6. Same-day reservation #3 - Evening slot
            [
                'start_date' => $now->copy(),
                'end_date' => $now->copy(),
                'status' => ReservationStatus::CONFIRMED,
                'pickup_time' => '19:00',
                'return_time' => '23:00',
                'pickup_location' => 'Downtown Office',
                'return_location' => 'Downtown Office',
                'notes' => 'Same-day evening rental.',
                'created_at' => $now->copy()->subHours(6),
                'updated_at' => $now->copy()->subHours(5),
                'payment_scenarios' => [
                    [
                        'status' => PaymentStatus::COMPLETED,
                        'method' => PaymentMethod::STRIPE,
                        'processed_at' => $now->copy()->subHours(6),
                    ]
                ]
            ],

            // 7. Pending reservation with pending payment
            [
                'start_date' => $now->copy()->addDays(15),
                'end_date' => $now->copy()->addDays(18),
                'status' => ReservationStatus::PENDING,
                'notes' => 'Waiting for payment confirmation.',
                'created_at' => $now->copy()->subDays(1),
                'updated_at' => $now->copy()->subDays(1),
                'payment_scenarios' => [
                    [
                        'status' => PaymentStatus::PENDING,
                        'method' => PaymentMethod::BANK_TRANSFER,
                    ]
                ]
            ],

            // 8. Cancelled reservation with refunded payment
            [
                'start_date' => $now->copy()->addDays(20),
                'end_date' => $now->copy()->addDays(25),
                'status' => ReservationStatus::CANCELLED,
                'cancellation_reason' => 'Customer changed travel plans due to emergency.',
                'cancelled_at' => $now->copy()->subDays(2),
                'discount_amount' => 50.00,
                'created_at' => $now->copy()->subDays(8),
                'updated_at' => $now->copy()->subDays(2),
                'payment_scenarios' => [
                    [
                        'status' => PaymentStatus::REFUNDED,
                        'method' => PaymentMethod::CREDIT_CARD,
                        'processed_at' => $now->copy()->subDays(8),
                        'refund_amount' => 'full',
                    ]
                ]
            ],

            // 9. No-show reservation
            [
                'start_date' => $now->copy()->subDays(5),
                'end_date' => $now->copy()->subDays(2),
                'status' => ReservationStatus::NO_SHOW,
                'notes' => 'Customer did not show up for pickup.',
                'created_at' => $now->copy()->subDays(15),
                'updated_at' => $now->copy()->subDays(5),
                'payment_scenarios' => [
                    [
                        'status' => PaymentStatus::COMPLETED,
                        'method' => PaymentMethod::DEBIT_CARD,
                        'processed_at' => $now->copy()->subDays(15),
                    ]
                ]
            ],

            // 10. Failed payment scenario
            [
                'start_date' => $now->copy()->addDays(5),
                'end_date' => $now->copy()->addDays(8),
                'status' => ReservationStatus::PENDING,
                'notes' => 'Payment failed, customer needs to retry.',
                'created_at' => $now->copy()->subHours(12),
                'updated_at' => $now->copy()->subHours(12),
                'payment_scenarios' => [
                    [
                        'status' => PaymentStatus::FAILED,
                        'method' => PaymentMethod::CREDIT_CARD,
                        'gateway_response' => 'Insufficient funds',
                    ],
                    [
                        'status' => PaymentStatus::PENDING,
                        'method' => PaymentMethod::PAYPAL,
                        'created_hours_after' => 2,
                    ]
                ]
            ],

            // 11. Partially refunded payment
            [
                'start_date' => $now->copy()->subDays(12),
                'end_date' => $now->copy()->subDays(8),
                'status' => ReservationStatus::COMPLETED,
                'notes' => 'Car had minor issue, partial refund issued.',
                'created_at' => $now->copy()->subDays(20),
                'updated_at' => $now->copy()->subDays(8),
                'payment_scenarios' => [
                    [
                        'status' => PaymentStatus::PARTIALLY_REFUNDED,
                        'method' => PaymentMethod::STRIPE,
                        'processed_at' => $now->copy()->subDays(20),
                        'refund_amount' => 'partial',
                    ]
                ]
            ],

            // 12. Long-term rental (completed)
            [
                'start_date' => $now->copy()->subDays(45),
                'end_date' => $now->copy()->subDays(15),
                'status' => ReservationStatus::COMPLETED,
                'pickup_location' => 'Corporate Office',
                'return_location' => 'Corporate Office',
                'notes' => 'Business rental for 30 days. Excellent customer.',
                'discount_amount' => 200.00,
                'created_at' => $now->copy()->subDays(60),
                'updated_at' => $now->copy()->subDays(15),
                'payment_scenarios' => [
                    [
                        'status' => PaymentStatus::COMPLETED,
                        'method' => PaymentMethod::BANK_TRANSFER,
                        'processed_at' => $now->copy()->subDays(60),
                    ]
                ]
            ],

            // 13. Weekend rental (completed)
            [
                'start_date' => $now->copy()->subDays(8),
                'end_date' => $now->copy()->subDays(6),
                'status' => ReservationStatus::COMPLETED,
                'pickup_time' => '08:00',
                'return_time' => '20:00',
                'pickup_location' => 'City Center',
                'return_location' => 'City Center',
                'notes' => 'Weekend getaway rental.',
                'created_at' => $now->copy()->subDays(14),
                'updated_at' => $now->copy()->subDays(6),
                'payment_scenarios' => [
                    [
                        'status' => PaymentStatus::COMPLETED,
                        'method' => PaymentMethod::CREDIT_CARD,
                        'processed_at' => $now->copy()->subDays(14),
                    ]
                ]
            ],
        ];
    }

    /**
     * Create payments for a reservation based on scenarios
     */
    private function createPaymentsForReservation(Reservation $reservation, array $reservationData): void
    {
        $paymentScenarios = $reservationData['payment_scenarios'] ?? [];

        foreach ($paymentScenarios as $scenario) {
            $paymentAmount = $reservation->total_amount;
            $refundedAmount = 0;

            // Calculate refund amount if needed
            if (isset($scenario['refund_amount'])) {
                if ($scenario['refund_amount'] === 'full') {
                    $refundedAmount = $paymentAmount;
                } elseif ($scenario['refund_amount'] === 'partial') {
                    $refundedAmount = $paymentAmount * 0.3; // 30% refund
                }
            }

            $createdAt = $reservation->created_at;
            if (isset($scenario['created_hours_after'])) {
                $createdAt = $reservation->created_at->copy()->addHours($scenario['created_hours_after']);
            }

            Payment::create([
                'reservation_id' => $reservation->id,
                'user_id' => $reservation->user_id,
                'amount' => $paymentAmount,
                'currency' => config('app.currency_code', 'USD'),
                'payment_method' => $scenario['method'],
                'status' => $scenario['status'],
                'transaction_id' => $this->generateTransactionId(),
                'gateway_response' => $scenario['gateway_response'] ?? null,
                'gateway_data' => $this->generateGatewayData($scenario['method']),
                'notes' => $scenario['notes'] ?? null,
                'processed_at' => $scenario['processed_at'] ?? null,
                'refunded_amount' => $refundedAmount,
                'created_at' => $createdAt,
                'updated_at' => $scenario['processed_at'] ?? $createdAt,
            ]);
        }
    }

    /**
     * Generate a mock transaction ID
     */
    private function generateTransactionId(): string
    {
        return 'TXN_' . strtoupper(uniqid());
    }

    /**
     * Generate mock gateway data based on payment method
     */
    private function generateGatewayData(PaymentMethod $method): array
    {
        $baseData = [
            'gateway' => $method->value,
            'processed_at' => now()->toISOString(),
        ];

        return match ($method) {
            PaymentMethod::CREDIT_CARD, PaymentMethod::DEBIT_CARD => array_merge($baseData, [
                'card_last_four' => rand(1000, 9999),
                'card_type' => collect(['visa', 'mastercard', 'amex'])->random(),
                'authorization_code' => 'AUTH_' . rand(100000, 999999),
            ]),
            PaymentMethod::PAYPAL => array_merge($baseData, [
                'paypal_transaction_id' => 'PP_' . uniqid(),
                'payer_email' => 'customer@example.com',
            ]),
            PaymentMethod::STRIPE => array_merge($baseData, [
                'stripe_charge_id' => 'ch_' . uniqid(),
                'stripe_customer_id' => 'cus_' . uniqid(),
            ]),
            PaymentMethod::BANK_TRANSFER => array_merge($baseData, [
                'bank_reference' => 'BANK_' . rand(1000000, 9999999),
                'bank_name' => collect(['Bank of Example', 'Example National Bank', 'First Example Bank'])->random(),
            ]),
            PaymentMethod::CASH => array_merge($baseData, [
                'receipt_number' => 'CASH_' . rand(10000, 99999),
                'cashier_id' => rand(1, 10),
            ]),
        };
    }
}
