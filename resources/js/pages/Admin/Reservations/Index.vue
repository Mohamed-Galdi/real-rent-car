<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { show } from '@/routes/admin/reservations';
import { index } from '@/routes/admin/reservations';

const props = defineProps<{
    reservations: {
        data: Array<{
            id: number;
            reservation_number: string;
            user: { id: number; name: string; email: string } | null;
            car: {
                id: number;
                make: string;
                model: string;
                year: number;
                license_plate: string;
            } | null;
            start_date: string;
            end_date: string;
            total_days: number;
            total_amount: number | string;
            status: string;
        }>;
        links: Array<{ url: string | null; label: string; active: boolean }>;
    };
    filters: {
        search?: string;
        status?: string;
    };
    statuses: Record<string, { label: string; count: number; color: string }>;
    currency: { symbol: string; code: string }
}>();

// Generate status colors based on the colors from the backend (mirrors Cars)
const statusColors = computed(() => {
    const colors: Record<string, { bg: string; text: string; dot: string }> =
        {};
    for (const [status, data] of Object.entries(props.statuses || {})) {
        const hex = (data as any).color?.replace('#', '') || '6B7280';
        const r = parseInt(hex.substring(0, 2), 16);
        const g = parseInt(hex.substring(2, 4), 16);
        const b = parseInt(hex.substring(4, 6), 16);
        colors[status] = {
            bg: `rgba(${r}, ${g}, ${b}, 0.1)`,
            text: `text-[${(data as any).color}]`,
            dot: (data as any).color,
        };
    }
    return colors;
});

const getStatusColor = (status: string) => {
    return (
        statusColors.value[status] || {
            bg: 'rgba(107, 114, 128, 0.1)',
            text: 'text-gray-500',
            dot: '#6B7280',
        }
    );
};

const search = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status || 'all');

const navigateToReservation = (id: number) => {
    router.visit(show(id).url);
};

function doSearch() {
    router.get(
        index().url,
        {
            search: search.value,
            status: statusFilter.value === 'all' ? null : statusFilter.value,
        },
        {
            preserveState: true,
            replace: true,
        },
    );
}

watch(search, (v, ov) => {
    if (v === '' && ov !== '') doSearch();
});
</script>

<template>
    <Head title="Reservations" />
    <AdminLayout>
        <!-- Main -->
        <main class="flex-1 space-y-6 p-8">
            <div class="flex items-center justify-between gap-4">
                <h1 class="text-2xl font-semibold">Reservations</h1>
            </div>

            <div class="flex flex-col gap-4">
                <div class="flex items-center gap-2">
                    <Input
                        v-model="search"
                        placeholder="Search reservation #, client, car, plate..."
                        class="max-w-md"
                        @keyup.enter="doSearch"
                    />
                    <Button @click="doSearch">Search</Button>
                </div>

                <!-- Status Filter -->
                <div class="flex flex-wrap items-center gap-2">
                    <label class="inline-flex items-center">
                        <input
                            type="radio"
                            class="hidden"
                            v-model="statusFilter"
                            value="all"
                            @change="doSearch"
                        />
                        <span
                            class="cursor-pointer rounded-full px-3 py-1.5 text-sm transition-colors"
                            :class="{
                                'bg-primary text-primary-foreground':
                                    statusFilter === 'all',
                                'bg-muted text-muted-foreground hover:bg-muted/80':
                                    statusFilter !== 'all',
                            }"
                        >
                            All ({{
                                Object.values(statuses).reduce(
                                    (acc: number, curr: any) =>
                                        acc + (curr as any).count,
                                    0,
                                )
                            }})
                        </span>
                    </label>

                    <template v-for="(status, key) in statuses" :key="key">
                        <label class="inline-flex items-center">
                            <input
                                type="radio"
                                class="hidden"
                                v-model="statusFilter"
                                :value="key"
                                @change="doSearch"
                            />
                            <span
                                class="flex cursor-pointer items-center gap-1.5 rounded-full px-3 py-1.5 text-sm transition-colors"
                                :class="{
                                    'bg-primary text-primary-foreground':
                                        statusFilter === key,
                                    'bg-muted text-muted-foreground hover:bg-muted/80':
                                        statusFilter !== key,
                                }"
                            >
                                <span
                                    class="h-2 w-2 rounded-full"
                                    :style="{
                                        backgroundColor: (status as any).color,
                                    }"
                                ></span>
                                {{ (status as any).label }} ({{
                                    (status as any).count
                                }})
                            </span>
                        </label>
                    </template>
                </div>
            </div>

            <div class="overflow-x-auto rounded-md border">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                            >
                                #
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                            >
                                Client
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                            >
                                Car
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                            >
                                Dates
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                            >
                                Total
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                            >
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr
                            v-for="res in props.reservations.data"
                            :key="res.id"
                            @click="navigateToReservation(res.id)"
                            class="cursor-pointer transition-colors hover:bg-gray-50"
                        >
                            <td class="px-4 py-3">
                                <div class="font-medium">
                                    {{ res.reservation_number }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium">
                                    {{ res.user?.name || '—' }}
                                </div>
                                <div class="text-xs text-muted-foreground">
                                    {{ res.user?.email }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium">
                                    {{
                                        res.car
                                            ? `${res.car.year} ${res.car.make} ${res.car.model}`
                                            : '—'
                                    }}
                                </div>
                                <div class="text-xs text-muted-foreground">
                                    {{ res.car?.license_plate }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium">
                                    {{
                                        new Date(
                                            res.start_date,
                                        ).toLocaleDateString()
                                    }}
                                    →
                                    {{
                                        new Date(
                                            res.end_date,
                                        ).toLocaleDateString()
                                    }}
                                </div>
                                <!-- duration in days-->
                                <div class="text-xs text-muted-foreground">
                                    {{ res.total_days }} days
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                {{ props.currency.symbol }} {{ Number(res.total_amount).toFixed(2) }}
                            </td>
                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex items-center gap-2 rounded-full px-2.5 py-1 text-xs font-medium"
                                    :style="{
                                        backgroundColor: getStatusColor(
                                            res.status,
                                        ).bg,
                                        color: getStatusColor(res.status).text,
                                    }"
                                >
                                    <span
                                        class="size-2 rounded-full"
                                        :style="{
                                            backgroundColor: getStatusColor(
                                                res.status,
                                            ).dot,
                                        }"
                                    />
                                    {{
                                        statuses[res.status]?.label ||
                                        res.status
                                    }}
                                </span>
                            </td>
                        </tr>
                        <tr v-if="props.reservations.data.length === 0">
                            <td
                                colspan="7"
                                class="px-4 py-6 text-center text-gray-500"
                            >
                                No reservations found.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <nav v-if="props.reservations.links?.length" class="flex gap-2">
                <Link
                    v-for="(link, i) in props.reservations.links"
                    :key="i"
                    :href="link.url || ''"
                    :class="[
                        'rounded px-3 py-1 text-sm',
                        link.active
                            ? 'bg-gray-900 text-white'
                            : 'bg-gray-100 text-gray-700',
                        !link.url && 'pointer-events-none opacity-50',
                    ]"
                >
                    <span v-html="link.label" />
                </Link>
            </nav>
        </main>
    </AdminLayout>
</template>
