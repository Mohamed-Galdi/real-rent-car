<script setup lang="ts">
import { login, register } from '@/routes';
import { index as carsIndex } from '@/routes/admin/cars/index';
import { index as reservationsIndex } from '@/routes/client/reservations/index';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div>
        <header
            class="sticky top-0 z-50 border-b border-gray-100 bg-white/95 shadow-sm backdrop-blur-md"
        >
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <nav class="flex h-20 items-center justify-between">
                    <!-- Enhanced Logo -->
                    <div class="flex items-center space-x-2">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 shadow-lg"
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
                                    d="M13 10V3L4 14h7v7l9-11h-7z"
                                ></path>
                            </svg>
                        </div>
                        <div>
                            <h1
                                class="text-2xl font-bold tracking-tight text-gray-900"
                            >
                                REAL<span class="text-orange-500">RENT</span>
                            </h1>
                            <p class="text-xs font-medium text-gray-500">
                                PREMIUM CARS
                            </p>
                        </div>
                    </div>

                    <!-- Enhanced Navigation -->
                    <div class="hidden items-center space-x-8 md:flex">
                        <a
                            href="#fleet"
                            class="font-medium text-gray-700 transition-colors hover:text-orange-500"
                            >Fleet</a
                        >
                        <a
                            href="#services"
                            class="font-medium text-gray-700 transition-colors hover:text-orange-500"
                            >Services</a
                        >
                        <a
                            href="#about"
                            class="font-medium text-gray-700 transition-colors hover:text-orange-500"
                            >About</a
                        >
                        <a
                            href="#contact"
                            class="font-medium text-gray-700 transition-colors hover:text-orange-500"
                            >Contact</a
                        >
                    </div>

                    <!-- Auth Buttons -->
                    <div class="flex items-center space-x-3">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="
                                $page.props.auth.user.role === 'admin'
                                    ? carsIndex.url
                                    : reservationsIndex.url
                            "
                            class="inline-flex items-center rounded-xl bg-gray-50 px-6 py-2.5 text-sm font-semibold text-gray-700 transition-all duration-200 hover:bg-gray-100 hover:shadow-md"
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
                                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"
                                ></path>
                            </svg>
                            Dashboard
                        </Link>
                        <template v-else>
                            <Link
                                :href="login()"
                                class="inline-flex items-center px-6 py-2.5 text-sm font-semibold text-gray-700 transition-colors duration-200 hover:text-orange-600"
                            >
                                Sign In
                            </Link>
                            <Link
                                :href="register()"
                                class="inline-flex items-center rounded-xl bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-2.5 text-sm font-semibold text-white shadow-lg transition-all duration-200 hover:scale-105 hover:from-orange-600 hover:to-orange-700 hover:shadow-xl"
                            >
                                Get Started
                            </Link>
                        </template>
                    </div>
                </nav>
            </div>
        </header>

        <slot />

        <!-- Enhanced Footer -->
        <footer class="bg-gray-900 py-16 text-white">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid gap-12 md:grid-cols-4">
                    <div class="space-y-6">
                        <div class="flex items-center space-x-2">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-orange-500 to-orange-600"
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
                                        d="M13 10V3L4 14h7v7l9-11h-7z"
                                    ></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold">
                                    REAL<span class="text-orange-500"
                                        >RENT</span
                                    >
                                </h3>
                                <p class="text-xs font-medium text-gray-400">
                                    PREMIUM CARS
                                </p>
                            </div>
                        </div>
                        <p class="leading-relaxed text-gray-400">
                            Premium car rental service providing luxury and
                            reliable vehicles for all your transportation needs
                            with exceptional customer service.
                        </p>
                    </div>

                    <div class="space-y-6">
                        <h4 class="text-lg font-semibold">Services</h4>
                        <ul class="space-y-3 text-gray-400">
                            <li>
                                <a
                                    href="#"
                                    class="transition-colors hover:text-orange-500"
                                    >Luxury Car Rental</a
                                >
                            </li>
                            <li>
                                <a
                                    href="#"
                                    class="transition-colors hover:text-orange-500"
                                    >Long Term Rental</a
                                >
                            </li>
                            <li>
                                <a
                                    href="#"
                                    class="transition-colors hover:text-orange-500"
                                    >Corporate Solutions</a
                                >
                            </li>
                            <li>
                                <a
                                    href="#"
                                    class="transition-colors hover:text-orange-500"
                                    >Airport Transfers</a
                                >
                            </li>
                        </ul>
                    </div>

                    <div class="space-y-6">
                        <h4 class="text-lg font-semibold">Support</h4>
                        <ul class="space-y-3 text-gray-400">
                            <li>
                                <a
                                    href="#"
                                    class="transition-colors hover:text-orange-500"
                                    >Contact Us</a
                                >
                            </li>
                            <li>
                                <a
                                    href="#"
                                    class="transition-colors hover:text-orange-500"
                                    >Help Center</a
                                >
                            </li>
                            <li>
                                <a
                                    href="#"
                                    class="transition-colors hover:text-orange-500"
                                    >Terms & Conditions</a
                                >
                            </li>
                            <li>
                                <a
                                    href="#"
                                    class="transition-colors hover:text-orange-500"
                                    >Privacy Policy</a
                                >
                            </li>
                        </ul>
                    </div>

                    <div class="space-y-6">
                        <h4 class="text-lg font-semibold">Contact Info</h4>
                        <div class="space-y-3 text-gray-400">
                            <div class="flex items-center space-x-3">
                                <svg
                                    class="h-5 w-5 text-orange-500"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                                    ></path>
                                </svg>
                                <span>+1 (555) 123-4567</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <svg
                                    class="h-5 w-5 text-orange-500"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                    ></path>
                                </svg>
                                <span>hello@realrent.com</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <svg
                                    class="h-5 w-5 text-orange-500"
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
                                <span>123 Business Ave, City</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-12 border-t border-gray-800 pt-8">
                    <div
                        class="flex flex-col items-center justify-between space-y-4 md:flex-row md:space-y-0"
                    >
                        <p class="text-gray-400">
                            &copy; 2025 RealRent. All rights reserved.
                        </p>
                        <div class="flex space-x-6">
                            <a
                                href="#"
                                class="text-gray-400 transition-colors hover:text-orange-500"
                            >
                                <svg
                                    class="h-6 w-6"
                                    fill="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"
                                    />
                                </svg>
                            </a>
                            <a
                                href="#"
                                class="text-gray-400 transition-colors hover:text-orange-500"
                            >
                                <svg
                                    class="h-6 w-6"
                                    fill="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"
                                    />
                                </svg>
                            </a>
                            <a
                                href="#"
                                class="text-gray-400 transition-colors hover:text-orange-500"
                            >
                                <svg
                                    class="h-6 w-6"
                                    fill="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.402.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24.009 12.017 24.009c6.624 0 11.99-5.367 11.99-11.988C24.007 5.367 18.641.001.012.001z"
                                    />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>
