<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { router, usePage } from '@inertiajs/vue3';
import { Chart, registerables } from 'chart.js';
import { computed, onMounted, ref, watch } from 'vue';

Chart.register(...registerables);

// Types
interface KPI {
    value: number;
    formatted: string;
    label: string;
}

interface CarState {
    value: number;
    formatted: string;
    label: string;
    color: string;
}

interface ChartData {
    labels: string[];
    datasets: Array<{
        label: string;
        data: number[];
        backgroundColor: string;
        borderColor: string;
        borderWidth: number;
    }>;
    dailyTotals: number[];
    statusColors: Record<string, string>;
    statusLabels: Record<string, string>;
    dateRange: {
        start: string;
        end: string;
    };
}

interface CarPerformance {
    id: number;
    car_name: string;
    license_plate: string;
    status: string;
    status_color: string;
    total_reservations: number;
    total_revenue: number;
    formatted_revenue: string;
    total_days: number;
    utilization_rate: number;
    average_per_reservation: number;
}

interface PeriodOption {
    value: string;
    label: string;
}

interface PageProps {
    kpis: {
        totalRevenue: KPI;
        platformVisits: KPI;
        activeReservations: KPI;
        newClients: KPI;
    };
    carsState: {
        totalCars: CarState;
        availableCars: CarState;
        rentedCars: CarState;
        unavailableCars: CarState;
    };
    reservationsChart: ChartData;
    carsPerformance: CarPerformance[];
    currentPeriod: string;
    periodOptions: PeriodOption[];
}

const page = usePage<PageProps>();
const selectedPeriod = ref(page.props.currentPeriod);
const reservationChart = ref<Chart | null>(null);
const chartCanvas = ref<HTMLCanvasElement | null>(null);

// Table sorting
const sortField = ref<keyof CarPerformance>('total_revenue');
const sortDirection = ref<'asc' | 'desc'>('desc');

// Computed properties for easier access
const kpis = computed(() => page.props.kpis);
const carsState = computed(() => page.props.carsState);
const chartData = computed(() => page.props.reservationsChart);
const periodOptions = computed(() => page.props.periodOptions);

// Sorted and limited cars performance
const sortedCarsPerformance = computed(() => {
    const sorted = [...page.props.carsPerformance].sort((a, b) => {
        const aValue = a[sortField.value];
        const bValue = b[sortField.value];

        if (typeof aValue === 'string' && typeof bValue === 'string') {
            return sortDirection.value === 'asc'
                ? aValue.localeCompare(bValue)
                : bValue.localeCompare(aValue);
        }

        const numA = Number(aValue);
        const numB = Number(bValue);

        return sortDirection.value === 'asc' ? numA - numB : numB - numA;
    });

    return sorted.slice(0, 10); // Limit to 10 cars
});

// Handle period change
const handlePeriodChange = () => {
    router.get(
        '/admin/reports',
        { period: selectedPeriod.value },
        {
            preserveState: false,
            preserveScroll: false,
            only: [
                'kpis',
                'carsState',
                'reservationsChart',
                'carsPerformance',
                'currentPeriod',
            ],
        },
    );
};

// Handle table sorting
const sortTable = (field: keyof CarPerformance) => {
    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortField.value = field;
        sortDirection.value = 'desc';
    }
};

// Create reservations chart as stacked bar chart
const createReservationsChart = () => {
    if (!chartCanvas.value || !chartData.value) return;

    // Destroy existing chart
    if (reservationChart.value) {
        reservationChart.value.destroy();
    }

    const ctx = chartCanvas.value.getContext('2d');
    if (!ctx) return;

    reservationChart.value = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartData.value.labels,
            datasets: chartData.value.datasets,
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        pointStyle: 'rect',
                    },
                },

                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: 'white',
                    bodyColor: 'white',
                    borderColor: 'rgba(255, 255, 255, 0.2)',
                    borderWidth: 1,
                    callbacks: {
                        title: function (tooltipItems) {
                            return `Date: ${tooltipItems[0].label}`;
                        },
                        afterBody: function (tooltipItems) {
                            const dayIndex = tooltipItems[0].dataIndex;
                            const total = chartData.value.dailyTotals[dayIndex];
                            return [``, `Total Reservations: ${total}`];
                        },
                        label: function (context) {
                            const label = context.dataset.label || '';
                            const value = context.parsed.y;
                            return `${label}: ${value}`;
                        },
                    },
                },
            },
            scales: {
                x: {
                    stacked: true,
                    grid: {
                        display: false,
                    },
                    ticks: {
                        maxRotation: 45,
                        minRotation: 0,
                    },
                },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        callback: function (value) {
                            if (Number.isInteger(value)) {
                                return value;
                            }
                            return '';
                        },
                    },
                    title: {
                        display: true,
                        text: 'Number of Reservations',
                        font: {
                            size: 12,
                            weight: 'bold',
                        },
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)',
                    },
                },
            },
            interaction: {
                intersect: false,
                mode: 'index',
            },
            animation: {
                duration: 1000,
                easing: 'easeInOutQuart',
            },
            elements: {
                bar: {
                    borderRadius: 2,
                    borderSkipped: false,
                },
            },
        },
    });
};

// Watch for data changes to recreate chart
watch(() => [chartData.value, selectedPeriod.value], createReservationsChart, {
    deep: true,
});

// Watch for period changes in props
watch(
    () => page.props.currentPeriod,
    (newPeriod) => {
        selectedPeriod.value = newPeriod;
    },
);

onMounted(() => {
    createReservationsChart();
});
</script>

<template>
    <AdminLayout>
        <div class="space-y-6 px-8 py-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-900">
                    Reports & Analytics
                </h2>

                <!-- Period Selector -->
                <div class="flex items-center space-x-2">
                    <label
                        for="period"
                        class="text-sm font-medium text-gray-700"
                    >
                        Period:
                    </label>
                    <select
                        id="period"
                        v-model="selectedPeriod"
                        @change="handlePeriodChange"
                        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    >
                        <option
                            v-for="option in periodOptions"
                            :key="option.value"
                            :value="option.value"
                        >
                            {{ option.label }}
                        </option>
                    </select>
                </div>
            </div>

            <!-- High-level KPIs -->
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Total Revenue -->
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-md bg-green-500"
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
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"
                                        ></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt
                                        class="truncate text-sm font-medium text-gray-500"
                                    >
                                        {{ kpis.totalRevenue.label }}
                                    </dt>
                                    <dd
                                        class="text-lg font-medium text-gray-900"
                                    >
                                        {{ kpis.totalRevenue.formatted }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Platform Visits -->
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-md bg-blue-500"
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
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                        ></path>
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                        ></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt
                                        class="truncate text-sm font-medium text-gray-500"
                                    >
                                        {{ kpis.platformVisits.label }}
                                    </dt>
                                    <dd
                                        class="text-lg font-medium text-gray-900"
                                    >
                                        {{ kpis.platformVisits.formatted }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Reservations -->
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-md bg-yellow-500"
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
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                        ></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt
                                        class="truncate text-sm font-medium text-gray-500"
                                    >
                                        {{ kpis.activeReservations.label }}
                                    </dt>
                                    <dd
                                        class="text-lg font-medium text-gray-900"
                                    >
                                        {{ kpis.activeReservations.formatted }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- New Clients -->
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-md bg-purple-500"
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
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                                        ></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt
                                        class="truncate text-sm font-medium text-gray-500"
                                    >
                                        {{ kpis.newClients.label }}
                                    </dt>
                                    <dd
                                        class="text-lg font-medium text-gray-900"
                                    >
                                        {{ kpis.newClients.formatted }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cars State -->
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Total Cars -->
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-md"
                                    :style="{
                                        backgroundColor:
                                            carsState.totalCars.color,
                                    }"
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
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                        ></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt
                                        class="truncate text-sm font-medium text-gray-500"
                                    >
                                        {{ carsState.totalCars.label }}
                                    </dt>
                                    <dd
                                        class="text-lg font-medium text-gray-900"
                                    >
                                        {{ carsState.totalCars.formatted }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Available Cars -->
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-md"
                                    :style="{
                                        backgroundColor:
                                            carsState.availableCars.color,
                                    }"
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
                                            d="M5 13l4 4L19 7"
                                        ></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt
                                        class="truncate text-sm font-medium text-gray-500"
                                    >
                                        {{ carsState.availableCars.label }}
                                    </dt>
                                    <dd
                                        class="text-lg font-medium text-gray-900"
                                    >
                                        {{ carsState.availableCars.formatted }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rented Cars -->
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-md"
                                    :style="{
                                        backgroundColor:
                                            carsState.rentedCars.color,
                                    }"
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
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                                        ></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt
                                        class="truncate text-sm font-medium text-gray-500"
                                    >
                                        {{ carsState.rentedCars.label }}
                                    </dt>
                                    <dd
                                        class="text-lg font-medium text-gray-900"
                                    >
                                        {{ carsState.rentedCars.formatted }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Unavailable Cars -->
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-md"
                                    :style="{
                                        backgroundColor:
                                            carsState.unavailableCars.color,
                                    }"
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
                                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L5.636 5.636"
                                        ></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt
                                        class="truncate text-sm font-medium text-gray-500"
                                    >
                                        {{ carsState.unavailableCars.label }}
                                    </dt>
                                    <dd
                                        class="text-lg font-medium text-gray-900"
                                    >
                                        {{
                                            carsState.unavailableCars.formatted
                                        }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reservations Chart -->
            <div class="rounded-lg bg-white shadow">
                <div class="p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">
                            Daily Reservations Created
                        </h3>
                        <div class="text-sm text-gray-500">
                            {{ chartData.dateRange.start }} to
                            {{ chartData.dateRange.end }}
                        </div>
                    </div>

                    <!-- Chart container -->
                    <div class="relative h-96">
                        <canvas ref="chartCanvas"></canvas>
                    </div>
                </div>
            </div>

            <!-- Cars Performance Table -->
            <div class="rounded-lg bg-white shadow">
                <div class="p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">
                            Top 10 Cars Performance
                        </h3>
                        <div class="text-sm text-gray-500">
                            Click column headers to sort
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        scope="col"
                                        class="cursor-pointer px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase hover:bg-gray-100"
                                        @click="sortTable('car_name')"
                                    >
                                        <div
                                            class="flex items-center space-x-1"
                                        >
                                            <span>Car</span>
                                            <svg
                                                v-if="sortField === 'car_name'"
                                                class="h-4 w-4"
                                                :class="
                                                    sortDirection === 'asc'
                                                        ? 'rotate-180 transform'
                                                        : ''
                                                "
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
                                        </div>
                                    </th>
                                    <th
                                        scope="col"
                                        class="cursor-pointer px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase hover:bg-gray-100"
                                        @click="sortTable('status')"
                                    >
                                        <div
                                            class="flex items-center space-x-1"
                                        >
                                            <span>Status</span>
                                            <svg
                                                v-if="sortField === 'status'"
                                                class="h-4 w-4"
                                                :class="
                                                    sortDirection === 'asc'
                                                        ? 'rotate-180 transform'
                                                        : ''
                                                "
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
                                        </div>
                                    </th>
                                    <th
                                        scope="col"
                                        class="cursor-pointer px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase hover:bg-gray-100"
                                        @click="sortTable('total_reservations')"
                                    >
                                        <div
                                            class="flex items-center space-x-1"
                                        >
                                            <span>Reservations</span>
                                            <svg
                                                v-if="
                                                    sortField ===
                                                    'total_reservations'
                                                "
                                                class="h-4 w-4"
                                                :class="
                                                    sortDirection === 'asc'
                                                        ? 'rotate-180 transform'
                                                        : ''
                                                "
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
                                        </div>
                                    </th>
                                    <th
                                        scope="col"
                                        class="cursor-pointer px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase hover:bg-gray-100"
                                        @click="sortTable('total_revenue')"
                                    >
                                        <div
                                            class="flex items-center space-x-1"
                                        >
                                            <span>Total Revenue</span>
                                            <svg
                                                v-if="
                                                    sortField ===
                                                    'total_revenue'
                                                "
                                                class="h-4 w-4"
                                                :class="
                                                    sortDirection === 'asc'
                                                        ? 'rotate-180 transform'
                                                        : ''
                                                "
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
                                        </div>
                                    </th>
                                    <th
                                        scope="col"
                                        class="cursor-pointer px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase hover:bg-gray-100"
                                        @click="sortTable('utilization_rate')"
                                    >
                                        <div
                                            class="flex items-center space-x-1"
                                        >
                                            <span>Utilization Rate</span>
                                            <svg
                                                v-if="
                                                    sortField ===
                                                    'utilization_rate'
                                                "
                                                class="h-4 w-4"
                                                :class="
                                                    sortDirection === 'asc'
                                                        ? 'rotate-180 transform'
                                                        : ''
                                                "
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
                                        </div>
                                    </th>
                                    <th
                                        scope="col"
                                        class="cursor-pointer px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase hover:bg-gray-100"
                                        @click="
                                            sortTable('average_per_reservation')
                                        "
                                    >
                                        <div
                                            class="flex items-center space-x-1"
                                        >
                                            <span>Avg per Reservation</span>
                                            <svg
                                                v-if="
                                                    sortField ===
                                                    'average_per_reservation'
                                                "
                                                class="h-4 w-4"
                                                :class="
                                                    sortDirection === 'asc'
                                                        ? 'rotate-180 transform'
                                                        : ''
                                                "
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
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr
                                    v-for="car in sortedCarsPerformance"
                                    :key="car.id"
                                    class="hover:bg-gray-50"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div
                                            class="text-sm font-medium text-gray-900"
                                        >
                                            {{ car.car_name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ car.license_plate }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex rounded-full px-2 py-1 text-xs font-semibold text-white"
                                            :style="{
                                                backgroundColor:
                                                    car.status_color,
                                            }"
                                        >
                                            {{ car.status }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-6 py-4 text-sm whitespace-nowrap text-gray-900"
                                    >
                                        {{ car.total_reservations }}
                                    </td>
                                    <td
                                        class="px-6 py-4 text-sm whitespace-nowrap text-gray-900"
                                    >
                                        {{ car.formatted_revenue }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                class="mr-2 h-2 flex-1 rounded-full bg-gray-200"
                                            >
                                                <div
                                                    class="h-2 rounded-full bg-blue-500"
                                                    :style="{
                                                        width: `${Math.min(car.utilization_rate, 100)}%`,
                                                    }"
                                                ></div>
                                            </div>
                                            <span
                                                class="min-w-0 text-sm text-gray-900"
                                            >
                                                {{ car.utilization_rate }}%
                                            </span>
                                        </div>
                                    </td>
                                    <td
                                        class="px-6 py-4 text-sm whitespace-nowrap text-gray-900"
                                    >
                                        {{
                                            car.average_per_reservation > 0
                                                ? `$${car.average_per_reservation.toFixed(2)}`
                                                : '$0.00'
                                        }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
