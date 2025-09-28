<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { computed } from 'vue';
import { index, edit, print } from '@/routes/admin/reservations';

const props = defineProps<{
  reservation: any
  statusMeta: Array<{ value: string; label: string; color: string }>
  paymentStatusMeta: Array<{ value: string; label: string }>
  currency: { symbol: string; code: string }
}>()

const statusMap = computed(() => {
  const map: Record<string, { label: string; color: string }> = {}
  for (const s of props.statusMeta || []) map[s.value] = { label: s.label, color: s.color }
  return map
})

function getStatusStyle(status: string) {
  const meta = statusMap.value[status]
  if (!meta) return { bg: 'rgba(107,114,128,0.1)', text: '#6B7280', dot: '#6B7280', label: status }
  const hex = meta.color.replace('#', '')
  const r = parseInt(hex.slice(0, 2), 16)
  const g = parseInt(hex.slice(2, 4), 16)
  const b = parseInt(hex.slice(4, 6), 16)
  return { bg: `rgba(${r}, ${g}, ${b}, 0.1)`, text: meta.color, dot: meta.color, label: meta.label }
}

function fmtDate(d?: string) {
  return d ? new Date(d).toLocaleDateString() : '—'
}
function fmtMoney(n?: number | string) {
  const v = Number(n ?? 0)
  return `${props.currency.symbol}${v.toFixed(2)}`
}
</script>

<template>
  <Head :title="`Reservation ${reservation?.reservation_number || ''}`" />
  <AdminLayout>
    <main class="flex-1 p-8 space-y-6">
      <div class="flex items-center justify-between gap-4">
        <h1 class="text-2xl font-semibold">
          Reservation {{ reservation?.reservation_number }}
        </h1>
        <div class="flex gap-2">
          <Link :href="index().url">
            <Button variant="outline">Back</Button>
          </Link>
          <Link :href="edit(reservation.id).url">
            <Button variant="outline">Edit</Button>
          </Link>
          <a :href="print(reservation.id).url" target="_blank" rel="noopener">
            <Button variant="secondary">Print</Button>
          </a>
        </div>
      </div>

      <!-- Header ribbon -->
      <div class="rounded-md border p-4 flex items-center justify-between">
        <div class="space-y-1">
          <div class="text-sm text-muted-foreground">Status</div>
          <div>
            <span
              class="inline-flex items-center gap-2 rounded-full px-2.5 py-1 text-xs font-medium"
              :style="{
                backgroundColor: getStatusStyle(reservation.status).bg,
                color: getStatusStyle(reservation.status).text,
              }"
            >
              <span class="size-2 rounded-full" :style="{ backgroundColor: getStatusStyle(reservation.status).dot }" />
              {{ getStatusStyle(reservation.status).label }}
            </span>
          </div>
        </div>
        <div class="text-right">
          <div class="text-sm text-muted-foreground">Total</div>
          <div class="text-xl font-semibold">{{ fmtMoney(reservation.total_amount) }}</div>
        </div>
      </div>

      <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <!-- Client -->
        <div class="rounded-md border">
          <div class="border-b px-4 py-3 font-medium">Client</div>
          <div class="p-4 space-y-1">
            <div class="text-sm">Name</div>
            <div class="font-medium">{{ reservation.user?.name || '—' }}</div>
            <div class="text-sm mt-3">Email</div>
            <div class="font-medium">{{ reservation.user?.email || '—' }}</div>
          </div>
        </div>

        <!-- Car -->
        <div class="rounded-md border">
          <div class="border-b px-4 py-3 font-medium">Car</div>
          <div class="p-4 space-y-1">
            <div class="text-sm">Car</div>
            <div class="font-medium">
              {{ reservation.car ? `${reservation.car.year} ${reservation.car.make} ${reservation.car.model}` : '—' }}
            </div>
            <div class="text-sm mt-3">Plate</div>
            <div class="font-medium">{{ reservation.car?.license_plate || '—' }}</div>
          </div>
        </div>

        <!-- Reservation Details -->
        <div class="rounded-md border md:col-span-2">
          <div class="border-b px-4 py-3 font-medium">Reservation Details</div>
          <div class="p-4 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <div class="text-sm text-muted-foreground">Start Date</div>
              <div class="font-medium">{{ fmtDate(reservation.start_date) }} {{ reservation.pickup_time }}</div>
            </div>
            <div>
              <div class="text-sm text-muted-foreground">End Date</div>
              <div class="font-medium">{{ fmtDate(reservation.end_date) }} {{ reservation.return_time }}</div>
            </div>
            <div>
              <div class="text-sm text-muted-foreground">Duration</div>
              <div class="font-medium">{{ reservation.total_days }} days</div>
            </div>
            <div>
              <div class="text-sm text-muted-foreground">Pickup Location</div>
              <div class="font-medium">{{ reservation.pickup_location || '—' }}</div>
            </div>
            <div>
              <div class="text-sm text-muted-foreground">Return Location</div>
              <div class="font-medium">{{ reservation.return_location || '—' }}</div>
            </div>
            <div v-if="reservation.status === 'cancelled'">
              <div class="text-sm text-muted-foreground">Cancelled At</div>
              <div class="font-medium">{{ reservation.cancelled_at ? new Date(reservation.cancelled_at).toLocaleString() : '—' }}</div>
              <div class="text-sm text-muted-foreground mt-2">Reason</div>
              <div class="font-medium">{{ reservation.cancellation_reason || '—' }}</div>
            </div>
          </div>
        </div>

        <!-- Amounts -->
        <div class="rounded-md border">
          <div class="border-b px-4 py-3 font-medium">Amounts</div>
          <div class="p-4 space-y-2">
            <div class="flex items-center justify-between">
              <div class="text-sm">Daily Rate</div>
              <div class="font-medium">{{ fmtMoney(reservation.daily_rate) }}</div>
            </div>
            <div class="flex items-center justify-between">
              <div class="text-sm">Subtotal</div>
              <div class="font-medium">{{ fmtMoney(reservation.subtotal) }}</div>
            </div>
            <div class="flex items-center justify-between">
              <div class="text-sm">Tax</div>
              <div class="font-medium">{{ fmtMoney(reservation.tax_amount) }}</div>
            </div>
            <div class="flex items-center justify-between">
              <div class="text-sm">Discount</div>
              <div class="font-medium">-{{ fmtMoney(reservation.discount_amount) }}</div>
            </div>
            <div class="border-t pt-2 flex items-center justify-between">
              <div class="text-sm">Total</div>
              <div class="text-lg font-semibold">{{ fmtMoney(reservation.total_amount) }}</div>
            </div>
          </div>
        </div>

        <!-- Payments -->
        <div class="rounded-md border md:col-span-2">
          <div class="border-b px-4 py-3 font-medium">Payments</div>
          <div class="p-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Processed</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="p in (reservation.payments || [])" :key="p.id">
                  <td class="px-4 py-2">{{ p.payment_number }}</td>
                  <td class="px-4 py-2">{{ fmtMoney(p.amount) }}</td>
                  <td class="px-4 py-2">{{ p.payment_method }}</td>
                  <td class="px-4 py-2">{{ p.status }}</td>
                  <td class="px-4 py-2">{{ p.processed_at ? new Date(p.processed_at).toLocaleString() : '—' }}</td>
                </tr>
                <tr v-if="!reservation.payments || reservation.payments.length === 0">
                  <td colspan="5" class="px-4 py-4 text-center text-gray-500">No payments recorded.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </main>
  </AdminLayout>
</template>
