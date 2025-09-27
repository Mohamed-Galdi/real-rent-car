<template>
    <HomeLayout>
        <div
            class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8"
        >
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Enhanced Header -->
                <div class="mb-8">
                    <nav
                        class="mb-6 flex items-center space-x-2 text-sm text-gray-500"
                    >
                        <a
                            href="/fleet"
                            class="font-medium transition-colors duration-200 hover:text-orange-500"
                            >Fleet</a
                        >
                        <svg
                            class="h-4 w-4 text-gray-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 5l7 7-7 7"
                            ></path>
                        </svg>
                        <span class="font-medium text-gray-900"
                            >{{ car.make }} {{ car.model }}</span
                        >
                    </nav>
                    <div class="flex items-center space-x-4">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-orange-500 to-orange-600"
                        >
                            <svg
                                class="h-6 w-6 text-white"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                ></path>
                            </svg>
                        </div>
                        <div>
                            <h1
                                class="text-4xl leading-tight font-bold text-gray-900"
                            >
                                Book {{ car.make }} {{ car.model }}
                            </h1>
                            <p class="mt-1 text-gray-600">
                                Complete your reservation in just a few steps
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid gap-8 lg:grid-cols-3">
                    <!-- Enhanced Car Details Section -->
                    <div class="space-y-8 lg:col-span-2">
                        <!-- Enhanced Car Images -->
                        <div
                            class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-lg"
                        >
                            <div
                                class="relative h-72 bg-gradient-to-br from-gray-100 to-gray-200 sm:h-96"
                            >
                                <img
                                    :src="images[selectedImage]?.url"
                                    :alt="images[selectedImage]?.alt"
                                    class="h-full w-full object-cover transition-all duration-500"
                                />
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"
                                ></div>

                                <!-- Enhanced Image Navigation -->
                                <div
                                    v-if="images.length > 1"
                                    class="absolute bottom-6 left-1/2 flex -translate-x-1/2 transform space-x-3"
                                >
                                    <button
                                        v-for="(image, index) in images"
                                        :key="index"
                                        @click="selectedImage = index"
                                        :class="{
                                            'scale-110 bg-orange-500':
                                                selectedImage === index,
                                            'bg-white/70 hover:bg-white/90':
                                                selectedImage !== index,
                                        }"
                                        class="h-4 w-4 rounded-full shadow-lg transition-all duration-300"
                                    />
                                </div>
                            </div>

                            <!-- Enhanced Car Info -->
                            <div class="p-8">
                                <div
                                    class="mb-6 flex items-start justify-between"
                                >
                                    <div>
                                        <h2
                                            class="mb-2 text-3xl font-bold text-gray-900"
                                        >
                                            {{ car.make }} {{ car.model }}
                                        </h2>
                                        <div
                                            class="flex items-center space-x-6 text-sm text-gray-500"
                                        >
                                            <span
                                                class="flex items-center rounded-full bg-gray-100 px-3 py-1"
                                            >
                                                <svg
                                                    class="mr-2 h-4 w-4"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                                    ></path>
                                                </svg>
                                                {{ car.year }}
                                            </span>
                                            <span
                                                class="flex items-center rounded-full bg-gray-100 px-3 py-1 capitalize"
                                            >
                                                <svg
                                                    class="mr-2 h-4 w-4"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M13 10V3L4 14h7v7l9-11h-7z"
                                                    ></path>
                                                </svg>
                                                {{ car.fuel_type }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div
                                            class="rounded-xl bg-gradient-to-r from-orange-500 to-orange-600 px-4 py-2 text-white"
                                        >
                                            <span class="text-3xl font-bold"
                                                >${{ car.price_per_day }}</span
                                            >
                                            <span
                                                class="block text-sm text-orange-100"
                                                >per day</span
                                            >
                                        </div>
                                    </div>
                                </div>

                                <p class="leading-relaxed text-gray-600">
                                    {{ car.description }}
                                </p>
                            </div>
                        </div>

                        <!-- Enhanced Booking Form -->
                        <div
                            class="rounded-2xl border border-gray-100 bg-white p-8 shadow-lg"
                        >
                            <div class="mb-8 flex items-center space-x-3">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-orange-500 to-orange-600"
                                >
                                    <svg
                                        class="h-5 w-5 text-white"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                                        ></path>
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900">
                                    Booking Details
                                </h3>
                            </div>

                            <form
                                @submit.prevent="submitBooking"
                                class="space-y-8"
                            >
                                <!-- Enhanced Rental Dates -->
                                <div class="space-y-4">
                                    <h4
                                        class="flex items-center text-lg font-semibold text-gray-900"
                                    >
                                        <svg
                                            class="mr-2 h-5 w-5 text-orange-500"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                            ></path>
                                        </svg>
                                        Rental Period
                                    </h4>
                                    <div class="grid gap-6 md:grid-cols-2">
                                        <div class="space-y-2">
                                            <label
                                                class="block text-sm font-semibold text-gray-700"
                                            >
                                                Pickup Date *
                                            </label>
                                            <input
                                                v-model="form.start_date"
                                                type="date"
                                                :min="$page.props.minDate"
                                                :max="$page.props.maxDate"
                                                required
                                                class="w-full rounded-xl border-2 border-gray-200 px-4 py-4 text-lg transition-all duration-200 focus:border-orange-500 focus:ring-2 focus:ring-orange-500"
                                                :class="{
                                                    'border-red-500 focus:border-red-500 focus:ring-red-500':
                                                        form.errors.start_date,
                                                }"
                                            />
                                            <span
                                                v-if="form.errors.start_date"
                                                class="text-sm font-medium text-red-500"
                                            >
                                                {{ form.errors.start_date }}
                                            </span>
                                        </div>

                                        <div class="space-y-2">
                                            <label
                                                class="block text-sm font-semibold text-gray-700"
                                            >
                                                Return Date *
                                            </label>
                                            <input
                                                v-model="form.end_date"
                                                type="date"
                                                :min="
                                                    form.start_date ||
                                                    $page.props.minDate
                                                "
                                                :max="$page.props.maxDate"
                                                required
                                                class="w-full rounded-xl border-2 border-gray-200 px-4 py-4 text-lg transition-all duration-200 focus:border-orange-500 focus:ring-2 focus:ring-orange-500"
                                                :class="{
                                                    'border-red-500 focus:border-red-500 focus:ring-red-500':
                                                        form.errors.end_date,
                                                }"
                                            />
                                            <span
                                                v-if="form.errors.end_date"
                                                class="text-sm font-medium text-red-500"
                                            >
                                                {{ form.errors.end_date }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Enhanced Date Conflict Warning -->
                                <div
                                    v-if="hasDateConflict"
                                    class="rounded-xl border-2 border-red-200 bg-red-50 p-6"
                                >
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <svg
                                                class="h-6 w-6 text-red-500"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.314 16.5c-.77.833.192 2.5 1.732 2.5z"
                                                ></path>
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h4
                                                class="font-semibold text-red-800"
                                            >
                                                Date Conflict Detected
                                            </h4>
                                            <p
                                                class="mt-1 text-sm text-red-700"
                                            >
                                                The selected dates conflict with
                                                existing reservations. Please
                                                choose different dates.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Enhanced Locations -->
                                <div class="space-y-4">
                                    <h4
                                        class="flex items-center text-lg font-semibold text-gray-900"
                                    >
                                        <svg
                                            class="mr-2 h-5 w-5 text-orange-500"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                                            ></path>
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                                            ></path>
                                        </svg>
                                        Pickup & Return Locations
                                    </h4>
                                    <div class="grid gap-6 md:grid-cols-2">
                                        <div class="space-y-2">
                                            <label
                                                class="block text-sm font-semibold text-gray-700"
                                            >
                                                Pickup Location *
                                            </label>
                                            <select
                                                v-model="form.pickup_location"
                                                required
                                                class="w-full rounded-xl border-2 border-gray-200 bg-white px-4 py-4 text-lg transition-all duration-200 focus:border-orange-500 focus:ring-2 focus:ring-orange-500"
                                                :class="{
                                                    'border-red-500 focus:border-red-500 focus:ring-red-500':
                                                        form.errors
                                                            .pickup_location,
                                                }"
                                            >
                                                <option value="">
                                                    Select pickup location
                                                </option>
                                                <option
                                                    v-for="location in commonLocations"
                                                    :key="location"
                                                    :value="location"
                                                >
                                                    {{ location }}
                                                </option>
                                            </select>
                                            <span
                                                v-if="
                                                    form.errors.pickup_location
                                                "
                                                class="text-sm font-medium text-red-500"
                                            >
                                                {{
                                                    form.errors.pickup_location
                                                }}
                                            </span>
                                        </div>

                                        <div class="space-y-2">
                                            <label
                                                class="block text-sm font-semibold text-gray-700"
                                            >
                                                Return Location *
                                            </label>
                                            <select
                                                v-model="form.return_location"
                                                required
                                                class="w-full rounded-xl border-2 border-gray-200 bg-white px-4 py-4 text-lg transition-all duration-200 focus:border-orange-500 focus:ring-2 focus:ring-orange-500"
                                                :class="{
                                                    'border-red-500 focus:border-red-500 focus:ring-red-500':
                                                        form.errors
                                                            .return_location,
                                                }"
                                            >
                                                <option value="">
                                                    Select return location
                                                </option>
                                                <option
                                                    v-for="location in commonLocations"
                                                    :key="location"
                                                    :value="location"
                                                >
                                                    {{ location }}
                                                </option>
                                            </select>
                                            <span
                                                v-if="
                                                    form.errors.return_location
                                                "
                                                class="text-sm font-medium text-red-500"
                                            >
                                                {{
                                                    form.errors.return_location
                                                }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Enhanced Driver Info -->
                                <div class="space-y-4">
                                    <h4
                                        class="flex items-center text-lg font-semibold text-gray-900"
                                    >
                                        <svg
                                            class="mr-2 h-5 w-5 text-orange-500"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                            ></path>
                                        </svg>
                                        Driver Information
                                    </h4>
                                    <div class="grid gap-6 md:grid-cols-2">
                                        <div class="space-y-2">
                                            <label
                                                class="block text-sm font-semibold text-gray-700"
                                            >
                                                Driver License Number *
                                            </label>
                                            <input
                                                v-model="form.driver_license"
                                                type="text"
                                                required
                                                placeholder="Enter license number"
                                                class="w-full rounded-xl border-2 border-gray-200 px-4 py-4 text-lg transition-all duration-200 focus:border-orange-500 focus:ring-2 focus:ring-orange-500"
                                                :class="{
                                                    'border-red-500 focus:border-red-500 focus:ring-red-500':
                                                        form.errors
                                                            .driver_license,
                                                }"
                                            />
                                            <span
                                                v-if="
                                                    form.errors.driver_license
                                                "
                                                class="text-sm font-medium text-red-500"
                                            >
                                                {{ form.errors.driver_license }}
                                            </span>
                                        </div>

                                        <div class="space-y-2">
                                            <label
                                                class="block text-sm font-semibold text-gray-700"
                                            >
                                                Phone Number *
                                            </label>
                                            <input
                                                v-model="form.phone"
                                                type="tel"
                                                required
                                                placeholder="Enter phone number"
                                                class="w-full rounded-xl border-2 border-gray-200 px-4 py-4 text-lg transition-all duration-200 focus:border-orange-500 focus:ring-2 focus:ring-orange-500"
                                                :class="{
                                                    'border-red-500 focus:border-red-500 focus:ring-red-500':
                                                        form.errors.phone,
                                                }"
                                            />
                                            <span
                                                v-if="form.errors.phone"
                                                class="text-sm font-medium text-red-500"
                                            >
                                                {{ form.errors.phone }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Enhanced Additional Notes -->
                                <div class="space-y-2">
                                    <label
                                        class="block text-sm font-semibold text-gray-700"
                                    >
                                        Additional Notes
                                    </label>
                                    <textarea
                                        v-model="form.additional_notes"
                                        rows="4"
                                        placeholder="Any special requirements or notes..."
                                        class="w-full resize-none rounded-xl border-2 border-gray-200 px-4 py-4 transition-all duration-200 focus:border-orange-500 focus:ring-2 focus:ring-orange-500"
                                        :class="{
                                            'border-red-500 focus:border-red-500 focus:ring-red-500':
                                                form.errors.additional_notes,
                                        }"
                                    ></textarea>
                                    <span
                                        v-if="form.errors.additional_notes"
                                        class="text-sm font-medium text-red-500"
                                    >
                                        {{ form.errors.additional_notes }}
                                    </span>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Enhanced Price Summary Sidebar -->
                    <div class="lg:col-span-1">
                        <div
                            class="sticky top-4 rounded-2xl border border-gray-100 bg-white p-8 shadow-lg"
                        >
                            <div class="mb-6 flex items-center space-x-3">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-orange-500 to-orange-600"
                                >
                                    <svg
                                        class="h-5 w-5 text-white"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"
                                        ></path>
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900">
                                    Booking Summary
                                </h3>
                            </div>

                            <div class="mb-8 space-y-6">
                                <div
                                    class="space-y-4 rounded-xl bg-gray-50 p-4"
                                >
                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <span class="font-medium text-gray-600"
                                            >Rental Period</span
                                        >
                                        <span class="font-bold text-gray-900">
                                            {{
                                                rentalDays > 0
                                                    ? `${rentalDays} day${rentalDays > 1 ? 's' : ''}`
                                                    : '-'
                                            }}
                                        </span>
                                    </div>

                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <span class="font-medium text-gray-600"
                                            >Daily Rate</span
                                        >
                                        <span class="font-bold text-gray-900"
                                            >${{ car.price_per_day }}</span
                                        >
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <div
                                        class="flex items-center justify-between py-2"
                                    >
                                        <span class="font-medium text-gray-600"
                                            >Subtotal</span
                                        >
                                        <span
                                            class="text-lg font-bold text-gray-900"
                                        >
                                            ${{
                                                rentalDays > 0
                                                    ? subtotal.toFixed(2)
                                                    : '0.00'
                                            }}
                                        </span>
                                    </div>

                                    <div
                                        class="flex items-center justify-between py-2"
                                    >
                                        <span class="font-medium text-gray-600"
                                            >Tax (10%)</span
                                        >
                                        <span
                                            class="text-lg font-bold text-gray-900"
                                        >
                                            ${{
                                                rentalDays > 0
                                                    ? tax.toFixed(2)
                                                    : '0.00'
                                            }}
                                        </span>
                                    </div>

                                    <div
                                        class="border-t-2 border-gray-200 pt-4"
                                    >
                                        <div
                                            class="flex items-center justify-between"
                                        >
                                            <span
                                                class="text-xl font-bold text-gray-900"
                                                >Total</span
                                            >
                                            <span
                                                class="text-2xl font-bold text-orange-500"
                                            >
                                                ${{
                                                    rentalDays > 0
                                                        ? total.toFixed(2)
                                                        : '0.00'
                                                }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Enhanced Book Now Button -->
                            <button
                                @click="submitBooking"
                                :disabled="!canSubmit || form.processing"
                                :class="{
                                    'transform bg-gradient-to-r from-orange-500 to-orange-600 text-white shadow-lg hover:scale-105 hover:from-orange-600 hover:to-orange-700 hover:shadow-xl':
                                        canSubmit && !form.processing,
                                    'cursor-not-allowed bg-gray-300 text-gray-500':
                                        !canSubmit || form.processing,
                                }"
                                class="w-full rounded-xl px-6 py-5 text-lg font-bold transition-all duration-300"
                            >
                                <span
                                    v-if="form.processing"
                                    class="flex items-center justify-center"
                                >
                                    <svg
                                        class="mr-3 -ml-1 h-6 w-6 animate-spin text-white"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                    >
                                        <circle
                                            class="opacity-25"
                                            cx="12"
                                            cy="12"
                                            r="10"
                                            stroke="currentColor"
                                            stroke-width="4"
                                        ></circle>
                                        <path
                                            class="opacity-75"
                                            fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                        ></path>
                                    </svg>
                                    Processing...
                                </span>
                                <span
                                    v-else-if="!$page.props.auth.user"
                                    class="flex items-center justify-center"
                                >
                                    <svg
                                        class="mr-2 h-5 w-5"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"
                                        ></path>
                                    </svg>
                                    Login to Book
                                </span>
                                <span
                                    v-else
                                    class="flex items-center justify-center"
                                >
                                    <svg
                                        class="mr-2 h-5 w-5"
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
                                    Book Now
                                </span>
                            </button>

                            <div class="mt-6 rounded-xl bg-blue-50 p-4">
                                <div class="flex items-start">
                                    <svg
                                        class="mt-0.5 mr-2 h-5 w-5 flex-shrink-0 text-blue-500"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                        ></path>
                                    </svg>
                                    <p
                                        class="text-sm font-medium text-blue-700"
                                    >
                                        Your booking will be confirmed within 24
                                        hours. You'll receive an email
                                        confirmation shortly.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </HomeLayout>
</template>

<script setup lang="ts">
import HomeLayout from '@/layouts/HomeLayout.vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

interface Car {
    id: number;
    make: string;
    model: string;
    year: number;
    price_per_day: string;
    description: string;
    fuel_type: string;
    image_url: string;
    images?: Array<{ url: string; alt: string }>;
}

interface UnavailableDate {
    start: string;
    end: string;
}

interface PageProps {
    car: Car;
    unavailableDates: UnavailableDate[];
    minDate: string;
    maxDate: string;
    auth: {
        user?: {
            id: number;
            name: string;
            email: string;
        };
    };
}

const $page = usePage<PageProps>();
const car = computed(() => $page.props.car);
const unavailableDates = computed(() => $page.props.unavailableDates);

const form = useForm({
    start_date: '',
    end_date: '',
    pickup_location: '',
    return_location: '',
    driver_license: '',
    phone: '',
    additional_notes: '',
});

const selectedImage = ref(0);
const showPriceBreakdown = ref(false);

// Calculate rental details
const rentalDays = computed(() => {
    if (!form.start_date || !form.end_date) return 0;
    const start = new Date(form.start_date);
    const end = new Date(form.end_date);
    const diffTime = end.getTime() - start.getTime();
    return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
});

const subtotal = computed(() => {
    return rentalDays.value * parseFloat(car.value.price_per_day);
});

const tax = computed(() => {
    return subtotal.value * 0.1; // 10% tax
});

const total = computed(() => {
    return subtotal.value + tax.value;
});

// Check if selected dates conflict with unavailable dates
const hasDateConflict = computed(() => {
    if (!form.start_date || !form.end_date) return false;

    const start = new Date(form.start_date);
    const end = new Date(form.end_date);

    return unavailableDates.value.some((unavailable) => {
        const unavailableStart = new Date(unavailable.start);
        const unavailableEnd = new Date(unavailable.end);

        return start <= unavailableEnd && end >= unavailableStart;
    });
});

const canSubmit = computed(() => {
    return (
        form.start_date &&
        form.end_date &&
        form.pickup_location &&
        form.return_location &&
        form.driver_license &&
        form.phone &&
        !hasDateConflict.value &&
        rentalDays.value > 0
    );
});

const submitBooking = () => {
    if (!$page.props.auth.user) {
        // Redirect to login
        router.visit('/login', {
            data: { redirect: window.location.pathname },
        });
        return;
    }

    form.post(`/booking/${car.value.id}`, {
        preserveScroll: true,
    });
};

// Auto-populate return location when pickup is selected
watch(
    () => form.pickup_location,
    (newLocation) => {
        if (newLocation && !form.return_location) {
            form.return_location = newLocation;
        }
    },
);

const images = computed(() => {
    if (car.value.images && car.value.images.length > 0) {
        return car.value.images;
    }
    return [
        {
            url: car.value.image_url,
            alt: `${car.value.make} ${car.value.model}`,
        },
    ];
});

const commonLocations = [
    'Downtown Office',
    'Airport Terminal 1',
    'Airport Terminal 2',
    'Central Station',
    'Mall Plaza',
    'Hotel District',
    'Business District',
];
</script>
