<script setup lang="ts">
import HomeLayout from '@/layouts/HomeLayout.vue';
import { usePage } from '@inertiajs/vue3';
import { fleet } from '@/routes';
import { index } from '@/routes/client/reservations';

interface Reservation {
    id: number;
    reservation_number: string;
    start_date: string;
    end_date: string;
    pickup_location: string;
    return_location: string;
    driver_license: string;
    phone: string;
    additional_notes?: string;
    total_amount: string;
    status: string;
    created_at: string;
    car: {
        make: string;
        model: string;
        year: number;
        image_url: string;
        description: string;
        fuel_type: string;
    };
    user: {
        name: string;
        email: string;
    };
}

interface PageProps {
    reservation: Reservation;
}

const $page = usePage<PageProps>();
const reservation = $page.props.reservation;
</script>

<template>
    <HomeLayout>
        <div class="min-h-screen bg-white py-12">
            <div class="mx-auto max-w-7xl px-6">
                <!-- Clean success header with minimal styling -->
                <div class="mb-12 text-center">
                    <div
                        class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-green-100"
                    >
                        <svg
                            class="h-8 w-8 text-green-600"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M5 13l4 4L19 7"
                            ></path>
                        </svg>
                    </div>
                    <h1 class="mb-2 text-3xl font-bold text-gray-900">
                        Booking Confirmed
                    </h1>
                    <p class="text-gray-600">
                        Reservation #{{ reservation.reservation_number }}
                    </p>
                </div>

                <!-- Clean two-column layout with proper alignment -->
                <div class="grid gap-8 lg:grid-cols-3">
                    <!-- Main Details -->
                    <div class="space-y-8 lg:col-span-2">
                        <!-- Car Information -->
                        <div class="rounded-lg border border-gray-200 p-6">
                            <h2
                                class="mb-6 text-xl font-semibold text-gray-900"
                            >
                                Vehicle Details
                            </h2>
                            <div class="flex items-start space-x-6">
                                <img
                                    :src="reservation.car.image_url"
                                    :alt="`${reservation.car.make} ${reservation.car.model}`"
                                    class="h-24 w-32 rounded-lg object-cover"
                                />
                                <div class="space-y-2">
                                    <h3
                                        class="text-lg font-medium text-gray-900"
                                    >
                                        {{ reservation.car.make }}
                                        {{ reservation.car.model }} - {{ reservation.car.year }}
                                    </h3>
                                    <p class="bg-gray-100 px-2 rounded w-fit">
                                        {{ reservation.car.fuel_type }}
                                    </p>
                                    <p class="text-gray-600">
                                        {{ reservation.car.description }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Rental Details -->
                        <div class="rounded-lg border border-gray-200 p-6">
                            <h2
                                class="mb-6 text-xl font-semibold text-gray-900"
                            >
                                Rental Information
                            </h2>
                            <div class="grid gap-8 md:grid-cols-2">
                                <div>
                                    <h3 class="mb-4 font-medium text-gray-900">
                                        Dates
                                    </h3>
                                    <div class="space-y-3">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600"
                                                >Pickup:</span
                                            >
                                            <span class="font-medium">{{
                                                new Date(
                                                    reservation.start_date,
                                                ).toLocaleDateString()
                                            }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600"
                                                >Return:</span
                                            >
                                            <span class="font-medium">{{
                                                new Date(
                                                    reservation.end_date,
                                                ).toLocaleDateString()
                                            }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <h3 class="mb-4 font-medium text-gray-900">
                                        Locations
                                    </h3>
                                    <div class="space-y-3">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600"
                                                >Pickup:</span
                                            >
                                            <span class="font-medium">{{
                                                reservation.pickup_location
                                            }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600"
                                                >Return:</span
                                            >
                                            <span class="font-medium">{{
                                                reservation.return_location
                                            }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="rounded-lg border border-gray-200 p-6">
                            <h2
                                class="mb-6 text-xl font-semibold text-gray-900"
                            >
                                Contact Details
                            </h2>
                            <div class="grid gap-8 md:grid-cols-2">
                                    <div class="flex gap-2">
                                        <span class="text-gray-600">Name:</span>
                                        <span class="font-medium">{{
                                            reservation.user.name
                                        }}</span>
                                    </div>
                                    <div class="flex gap-2">
                                        <span class="text-gray-600"
                                            >Email:</span
                                        >
                                        <span class="font-medium">{{
                                            reservation.user.email
                                        }}</span>
                                    </div>
                            </div>
                        </div>
                    </div>

                    <!-- Clean sidebar with price summary and next steps -->
                    <div class="space-y-6">
                        <!-- Price Summary -->
                        <div class="rounded-lg border border-gray-200 p-6">
                            <h2
                                class="mb-4 text-xl font-semibold text-gray-900"
                            >
                                Summary
                            </h2>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Status:</span>
                                    <span
                                        class="rounded-full bg-yellow-100 px-3 py-1 text-sm text-yellow-800 capitalize"
                                    >
                                        {{ reservation.status }}
                                    </span>
                                </div>
                                <div class="border-t pt-3">
                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <span
                                            class="text-lg font-semibold text-gray-900"
                                            >Total:</span
                                        >
                                        <span
                                            class="text-2xl font-bold text-orange-500"
                                        >
                                            ${{
                                                parseFloat(
                                                    reservation.total_amount,
                                                ).toFixed(2)
                                            }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Next Steps -->
                        <div class="rounded-lg border border-gray-200 p-6">
                            <h2
                                class="mb-4 text-xl font-semibold text-gray-900"
                            >
                                Next Steps
                            </h2>
                            <div class="space-y-4 text-sm text-gray-700">
                                <div class="flex items-start space-x-3">
                                    <span
                                        class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-gray-100 text-xs font-medium"
                                        >1</span
                                    >
                                    <span
                                        >We'll review your booking within 24
                                        hours</span
                                    >
                                </div>
                                <div class="flex items-start space-x-3">
                                    <span
                                        class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-gray-100 text-xs font-medium"
                                        >2</span
                                    >
                                    <span
                                        >You'll receive confirmation email with
                                        payment details</span
                                    >
                                </div>
                                <div class="flex items-start space-x-3">
                                    <span
                                        class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-gray-100 text-xs font-medium"
                                        >3</span
                                    >
                                    <span
                                        >Bring your license and confirmation on
                                        pickup day</span
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-3">
                            <a
                                :href="index.url()"
                                class="block w-full rounded-lg bg-black px-6 py-3 text-center font-medium text-white transition-colors duration-200 hover:bg-gray-800"
                            >
                                View My Bookings
                            </a>
                            <a
                                :href="fleet.url()"
                                class="block w-full rounded-lg border border-gray-300 bg-white px-6 py-3 text-center font-medium text-gray-900 transition-colors duration-200 hover:bg-gray-50"
                            >
                                Browse More Cars
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </HomeLayout>
</template>
