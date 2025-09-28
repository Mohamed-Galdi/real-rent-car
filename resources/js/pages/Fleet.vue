<script setup lang="ts">
import CarCard from '@/components/CarCard.vue';
import HomeLayout from '@/layouts/HomeLayout.vue';
import { router, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const $page = usePage<PageProps>();
const cars = computed(() => $page.props.cars);
const filters = computed(() => $page.props.filters);
const makes = computed(() => $page.props.makes);
const fuelTypes = computed(() => $page.props.fuelTypes);
const years = computed(() => $page.props.years);

// Filter state
const searchQuery = ref(filters.value.search || '');
const selectedMake = ref(filters.value.make || '');
const selectedFuelType = ref(filters.value.fuel_type || '');
const minPrice = ref(filters.value.min_price || '');
const maxPrice = ref(filters.value.max_price || '');
const selectedYear = ref(filters.value.year || '');
const sortBy = ref(filters.value.sort || 'make_asc');

// Show/hide filters on mobile
const showFilters = ref(false);
const isLoading = ref(false);

const applyFilters = () => {
    isLoading.value = true;
    const params: Record<string, any> = {};

    if (searchQuery.value.trim()) params.search = searchQuery.value.trim();
    if (selectedMake.value) params.make = selectedMake.value;
    if (selectedFuelType.value) params.fuel_type = selectedFuelType.value;
    if (minPrice.value) params.min_price = minPrice.value;
    if (maxPrice.value) params.max_price = maxPrice.value;
    if (selectedYear.value) params.year = selectedYear.value;
    if (sortBy.value && sortBy.value !== 'make_asc') params.sort = sortBy.value;

    router.get('/fleet', params, {
        preserveState: true,
        preserveScroll: true,
        onFinish: () => {
            isLoading.value = false;
        },
    });
};

const clearFilters = () => {
    searchQuery.value = '';
    selectedMake.value = '';
    selectedFuelType.value = '';
    minPrice.value = '';
    maxPrice.value = '';
    selectedYear.value = '';
    sortBy.value = 'make_asc';

    isLoading.value = true;
    router.get(
        '/fleet',
        {},
        {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => {
                isLoading.value = false;
            },
        },
    );
};

const handleSearch = (event?: Event) => {
    if (event) {
        event.preventDefault();
    }
    applyFilters();
};

// Watch only for sort changes (immediate feedback)
watch(sortBy, () => {
    applyFilters();
});

const goToPage = (url: string) => {
    if (url) {
        isLoading.value = true;
        router.visit(url, {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => {
                isLoading.value = false;
            },
        });
    }
};

const hasActiveFilters = computed(() => {
    return (
        searchQuery.value.trim() ||
        selectedMake.value ||
        selectedFuelType.value ||
        minPrice.value ||
        maxPrice.value ||
        selectedYear.value ||
        (sortBy.value && sortBy.value !== 'make_asc')
    );
});
</script>

<template>
    <HomeLayout>
        <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
            <!-- Loading Overlay -->
            <div
                v-if="isLoading"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm"
            >
                <div
                    class="flex items-center space-x-4 rounded-2xl bg-white p-8 shadow-2xl"
                >
                    <div class="relative">
                        <div
                            class="h-8 w-8 animate-spin rounded-full border-4 border-orange-200 border-t-orange-500"
                        ></div>
                    </div>
                    <span class="text-lg font-medium text-gray-700"
                        >Loading premium vehicles...</span
                    >
                </div>
            </div>

            <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                

                <div class="flex flex-col gap-8 lg:flex-row">
                    <!--  Filters Sidebar -->
                    <div class="lg:w-1/4">
                        <!-- Mobile Filter Toggle -->
                        <div class="mb-6 lg:hidden">
                            <button
                                @click="showFilters = !showFilters"
                                class="group flex w-full items-center justify-between rounded-2xl border border-gray-200 bg-white px-6 py-4 text-left font-semibold text-gray-700 shadow-sm transition-all duration-200 hover:border-orange-200 hover:shadow-md"
                            >
                                <span class="flex items-center">
                                    <div
                                        class="mr-3 rounded-lg bg-orange-100 p-2 transition-colors group-hover:bg-orange-200"
                                    >
                                        <svg
                                            class="h-5 w-5 text-orange-600"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 2v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"
                                            ></path>
                                        </svg>
                                    </div>
                                    Filters & Search
                                    <span
                                        v-if="hasActiveFilters"
                                        class="ml-2 rounded-full bg-orange-500 px-2 py-1 text-xs text-white"
                                        >{{
                                            Object.values({
                                                searchQuery: searchQuery.trim(),
                                                selectedMake,
                                                selectedFuelType,
                                                minPrice,
                                                maxPrice,
                                                selectedYear,
                                            }).filter(Boolean).length
                                        }}</span
                                    >
                                </span>
                                <svg
                                    class="h-5 w-5 transition-transform duration-200"
                                    :class="{ 'rotate-180': showFilters }"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 9l-7 7-7-7"
                                    ></path>
                                </svg>
                            </button>
                        </div>

                        <!--  Filters Panel -->
                        <div
                            :class="{ hidden: !showFilters }"
                            class="sticky top-16 space-y-6 rounded-2xl border border-gray-200 bg-white p-4 shadow-lg lg:block"
                        >
                            <!-- Search Form -->
                            <div>
                                
                                <form @submit="handleSearch" class="space-y-3">
                                    <div class="relative">
                                        <input
                                            v-model="searchQuery"
                                            type="text"
                                            placeholder="Search by make, model, or features..."
                                            class="w-full rounded-xl border border-gray-300 py-2 pr-4 pl-12 text-gray-900 placeholder-gray-500 transition-all duration-200 focus:border-orange-500 focus:ring-4 focus:ring-orange-100"
                                            @keydown.enter="handleSearch"
                                        />
                                        <svg
                                            class="absolute top-1/2 left-4 h-5 w-5 -translate-y-1/2 transform text-gray-400"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                            ></path>
                                        </svg>
                                    </div>
                                    <button
                                        type="submit"
                                        class="w-full rounded-xl bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-2 font-semibold text-white shadow-lg transition-all duration-200 hover:from-orange-600 hover:to-orange-700 hover:shadow-xl focus:ring-4 focus:ring-orange-200"
                                    >
                                        Search Fleet
                                    </button>
                                </form>
                            </div>

                            <div class="border-t border-gray-200 pt-4">
                                <div class="space-y-3">
                                    <!-- Make Filter -->
                                    <div>
                                        <label
                                            class="mb-2 block text-sm font-medium text-gray-700"
                                            >Vehicle Make</label
                                        >
                                        <select
                                            v-model="selectedMake"
                                            class="w-full rounded-xl border border-gray-300 px-4 py-2 text-gray-900 transition-all duration-200 focus:border-orange-500 focus:ring-4 focus:ring-orange-100"
                                        >
                                            <option value="">All Makes</option>
                                            <option
                                                v-for="make in makes"
                                                :key="make"
                                                :value="make"
                                            >
                                                {{ make }}
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Fuel Type Filter -->
                                    <div>
                                        <label
                                            class="mb-2 block text-sm font-medium text-gray-700"
                                            >Fuel Type</label
                                        >
                                        <select
                                            v-model="selectedFuelType"
                                            class="w-full rounded-xl border border-gray-300 px-4 py-2 text-gray-900 transition-all duration-200 focus:border-orange-500 focus:ring-4 focus:ring-orange-100"
                                        >
                                            <option value="">
                                                All Fuel Types
                                            </option>
                                            <option
                                                v-for="fuelType in fuelTypes"
                                                :key="fuelType"
                                                :value="fuelType"
                                            >
                                                {{
                                                    fuelType
                                                        .charAt(0)
                                                        .toUpperCase() +
                                                    fuelType.slice(1)
                                                }}
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Year Filter -->
                                    <div>
                                        <label
                                            class="mb-2 block text-sm font-medium text-gray-700"
                                            >Model Year</label
                                        >
                                        <select
                                            v-model="selectedYear"
                                            class="w-full rounded-xl border border-gray-300 px-4 py-2 text-gray-900 transition-all duration-200 focus:border-orange-500 focus:ring-4 focus:ring-orange-100"
                                        >
                                            <option value="">All Years</option>
                                            <option
                                                v-for="year in years"
                                                :key="year"
                                                :value="year"
                                            >
                                                {{ year }}
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Price Range -->
                                    <div>
                                        <label
                                            class="mb-2 block text-sm font-medium text-gray-700"
                                            >Daily Rate Range</label
                                        >
                                        <div class="grid grid-cols-2 gap-3">
                                            <div class="relative">
                                                <span
                                                    class="absolute top-1/2 left-3 -translate-y-1/2 text-gray-500"
                                                    >$</span
                                                >
                                                <input
                                                    v-model="minPrice"
                                                    type="number"
                                                    placeholder="Min"
                                                    class="w-full rounded-xl border border-gray-300 py-2 pr-4 pl-8 text-gray-900 transition-all duration-200 focus:border-orange-500 focus:ring-4 focus:ring-orange-100"
                                                />
                                            </div>
                                            <div class="relative">
                                                <span
                                                    class="absolute top-1/2 left-3 -translate-y-1/2 text-gray-500"
                                                    >$</span
                                                >
                                                <input
                                                    v-model="maxPrice"
                                                    type="number"
                                                    placeholder="Max"
                                                    class="w-full rounded-xl border border-gray-300 py-2 pr-4 pl-8 text-gray-900 transition-all duration-200 focus:border-orange-500 focus:ring-4 focus:ring-orange-100"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div
                                class="space-y-3 border-t border-gray-200 pt-6"
                            >
                                <button
                                    @click="applyFilters"
                                    class="w-full rounded-xl bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-2 font-semibold text-white shadow-lg transition-all duration-200 hover:from-orange-600 hover:to-orange-700 hover:shadow-xl focus:ring-4 focus:ring-orange-200"
                                >
                                    Apply Filters
                                </button>

                                <button
                                    @click="clearFilters"
                                    class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2 font-medium text-gray-700 transition-all duration-200 hover:border-gray-400 hover:bg-gray-50"
                                >
                                    Clear All Filters
                                </button>
                            </div>

                        </div>
                    </div>

                    <!--  Cars Grid -->
                    <div class="lg:w-3/4">
                        <!--  Results Summary -->
                        <div
                            class="mb-8 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm"
                        >
                            <div
                                class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center"
                            >
                                <div>
                                    <h2
                                        class="text-xl font-semibold text-gray-900"
                                    >
                                        {{ cars.total }} Premium Vehicles
                                        Available
                                    </h2>
                                    <p class="text-sm text-gray-600">
                                        Showing {{ cars.from }} -
                                        {{ cars.to }} results
                                    </p>
                                </div>
                                <div
                                    class="flex items-center space-x-2 text-sm text-gray-500"
                                >
                                    <span
                                        >Page {{ cars.current_page }} of
                                        {{ cars.last_page }}</span
                                    >
                                    <div class="h-4 w-px bg-gray-300"></div>
                                    <span
                                        class="rounded-full bg-orange-100 px-3 py-1 font-medium text-orange-700"
                                    >
                                        {{ cars.data.length }} shown
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Cars Grid -->
                        <div
                            v-if="cars.data.length > 0"
                            class="grid gap-8 md:grid-cols-1 xl:grid-cols-2"
                        >
                            <CarCard
                                v-for="car in cars.data"
                                :key="car.id"
                                :car="car"
                            />
                        </div>

                        <!--  No Results -->
                        <div
                            v-else
                            class="rounded-2xl border border-gray-200 bg-white p-16 text-center shadow-sm"
                        >
                            <div
                                class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-gray-100"
                            >
                                <svg
                                    class="h-10 w-10 text-gray-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="1.5"
                                        d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.058 0-3.9.785-5.293 2.071A8.003 8.003 0 014 12C4 7.582 7.582 4 12 4s8 3.582 8 8c0 1.996-.732 3.82-1.945 5.224L16 19l-4-4z"
                                    ></path>
                                </svg>
                            </div>
                            <h3
                                class="mb-3 text-2xl font-semibold text-gray-900"
                            >
                                No vehicles found
                            </h3>
                            <p
                                class="mx-auto mb-8 max-w-md leading-relaxed text-gray-600"
                            >
                                We couldn't find any vehicles matching your
                                criteria. Try adjusting your filters or search
                                terms to discover more options.
                            </p>
                            <button
                                @click="clearFilters"
                                class="rounded-xl bg-gradient-to-r from-orange-500 to-orange-600 px-8 py-3 font-semibold text-white shadow-lg transition-all duration-200 hover:from-orange-600 hover:to-orange-700"
                            >
                                View All Vehicles
                            </button>
                        </div>

                        <!--  Pagination -->
                        <div
                            v-if="cars.data.length > 0 && cars.last_page > 1"
                            class="mt-12 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm"
                        >
                            <div
                                class="flex flex-col items-center justify-between gap-6 sm:flex-row"
                            >
                                <!-- Mobile pagination -->
                                <div
                                    class="flex w-full justify-between sm:hidden"
                                >
                                    <button
                                        v-if="cars.current_page > 1"
                                        @click="goToPage(cars.links[0].url)"
                                        class="rounded-xl bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-3 font-semibold text-white shadow-lg transition-all duration-200 hover:from-orange-600 hover:to-orange-700"
                                    >
                                        Previous
                                    </button>
                                    <span
                                        class="flex items-center rounded-xl bg-gray-100 px-4 py-3 text-sm font-medium text-gray-700"
                                    >
                                        Page {{ cars.current_page }} of
                                        {{ cars.last_page }}
                                    </span>
                                    <button
                                        v-if="
                                            cars.current_page < cars.last_page
                                        "
                                        @click="
                                            goToPage(
                                                cars.links[
                                                    cars.links.length - 1
                                                ].url,
                                            )
                                        "
                                        class="rounded-xl bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-3 font-semibold text-white shadow-lg transition-all duration-200 hover:from-orange-600 hover:to-orange-700"
                                    >
                                        Next
                                    </button>
                                </div>

                                <!-- Desktop pagination -->
                                <div
                                    class="hidden items-center space-x-2 sm:flex"
                                >
                                    <button
                                        v-for="(link, index) in cars.links"
                                        :key="index"
                                        @click="goToPage(link.url)"
                                        :disabled="!link.url"
                                        :class="{
                                            'bg-gradient-to-r from-orange-500 to-orange-600 text-white shadow-lg':
                                                link.active,
                                            'border-gray-300 bg-white text-gray-700 hover:bg-gray-50':
                                                !link.active && link.url,
                                            'cursor-not-allowed border-gray-200 bg-gray-100 text-gray-400':
                                                !link.url,
                                        }"
                                        class="rounded-xl border px-4 py-2 text-sm font-medium transition-all duration-200"
                                        v-html="link.label"
                                    ></button>
                                </div>

                                <!-- Results info -->
                                <div class="text-sm text-gray-600">
                                    Showing
                                    <span class="font-semibold text-gray-900">{{
                                        cars.from
                                    }}</span>
                                    to
                                    <span class="font-semibold text-gray-900">{{
                                        cars.to
                                    }}</span>
                                    of
                                    <span class="font-semibold text-gray-900">{{
                                        cars.total
                                    }}</span>
                                    results
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </HomeLayout>
</template>
<style scoped>
    button:hover {
        cursor: pointer;
    }
</style>
