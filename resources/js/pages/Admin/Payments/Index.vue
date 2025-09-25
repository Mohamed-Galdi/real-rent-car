<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    payments: {
        data: Array<{
            id: number;
            payment_number: string;
            amount: number | string;
            currency?: string;
            payment_method: string;
            status: string;
            processed_at?: string | null;
            user?: { id: number; name: string; email: string } | null;
            reservation?: { id: number; reservation_number: string } | null;
        }>;
        links: Array<{ url: string | null; label: string; active: boolean }>;
    };
    statuses: Array<{ value: string; label: string; color: string }>;
    currency: { symbol: string; code: string };
}>();

function fmtMoney(n?: number | string) {
    const v = Number(n ?? 0);
    return `${props.currency.symbol}${v.toFixed(2)}`;
}

// Generate status colors based on the colors from the backend
const statusColors = computed(() => {
    const colors: Record<string, { bg: string; text: string; dot: string }> =
        {};

    for (const [status, data] of Object.entries(props.statuses || {})) {
        // Convert hex to RGB for the background with opacity
        const hex = data.color.replace('#', '');
        const r = parseInt(hex.substring(0, 2), 16);
        const g = parseInt(hex.substring(2, 4), 16);
        const b = parseInt(hex.substring(4, 6), 16);

        colors[status] = {
            bg: `rgba(${r}, ${g}, ${b}, 0.1)`,
            text: `text-[${data.color}]`,
            dot: data.color,
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
</script>

<template>
    <Head title="Payments" />
    <AdminLayout>
        <main class="flex-1 space-y-6 p-8">
            <div class="flex items-center justify-between gap-4">
                <h1 class="text-2xl font-semibold">Payments</h1>
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
                                Reservation
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                            >
                                Amount
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                            >
                                Method
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                            >
                                Status
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                            >
                                Processed
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr v-for="p in props.payments.data" :key="p.id">
                            <td class="px-4 py-3">{{ p.payment_number }}</td>
                            <td class="px-4 py-3">
                                <div class="font-medium">
                                    {{ p.user?.name || '—' }}
                                </div>
                                <div class="text-xs text-muted-foreground">
                                    {{ p.user?.email }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <Link
                                    v-if="p.reservation"
                                    :href="`/admin/reservations/${p.reservation.id}`"
                                    class="text-blue-600 hover:underline"
                                >
                                    {{ p.reservation.reservation_number }}
                                </Link>
                                <span v-else>—</span>
                            </td>
                            <td class="px-4 py-3 font-semibold text-green-800">
                                {{ fmtMoney(p.amount) }}
                            </td>
                            <td class="px-4 py-3">{{ p.payment_method }}</td>
                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex items-center gap-2 rounded-full px-2.5 py-1 text-xs font-medium"
                                    :style="{
                                        backgroundColor: getStatusColor(
                                            p.status,
                                        ).bg,
                                        color: getStatusColor(p.status).text,
                                    }"
                                >
                                    <span
                                        class="size-2 rounded-full"
                                        :style="{
                                            backgroundColor: getStatusColor(
                                                p.status,
                                            ).dot,
                                        }"
                                    />
                                    {{ p.status }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                {{
                                    p.processed_at
                                        ? new Date(
                                              p.processed_at,
                                          ).toLocaleString()
                                        : '—'
                                }}
                            </td>
                        </tr>
                        <tr v-if="props.payments.data.length === 0">
                            <td
                                colspan="7"
                                class="px-4 py-6 text-center text-gray-500"
                            >
                                No payments found.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <nav v-if="props.payments.links?.length" class="flex gap-2">
                <Link
                    v-for="(link, i) in props.payments.links"
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
