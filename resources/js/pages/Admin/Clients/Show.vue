<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { ref, computed } from 'vue';
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import { AlertCircle } from 'lucide-vue-next';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { index } from '@/routes/admin/clients';
import { suspend } from '@/routes/admin/clients';
import { activate } from '@/routes/admin/clients';


const props = defineProps<{
  client: { id: number; name: string; email: string; is_active: boolean; created_at?: string };
  stats: { total_reservations: number; total_payments: number; total_spent: number };
  reservations: {
    data: Array<{
      id: number;
      reservation_number: string;
      start_date: string;
      end_date: string;
      total_days?: number;
      total_amount: number | string;
      status: string;
      car?: { year: number; make: string; model: string; license_plate: string } | null;
    }>;
    links: Array<{ url: string | null; label: string; active: boolean }>;
  };
  payments: {
    data: Array<{
      id: number;
      payment_number: string;
      amount: number | string;
      currency?: string;
      payment_method: string;
      status: string;
      processed_at?: string | null;
      reservation?: { id: number; reservation_number: string } | null;
    }>;
    links: Array<{ url: string | null; label: string; active: boolean }>;
  };
  currency: { symbol: string; code: string }
}>()

const showSuspendDialog = ref(false);
const processingSuspend = ref(false);

const showActivateDialog = ref(false);
const processingActivate = ref(false);

function fmtMoney(n?: number | string) {
  const v = Number(n ?? 0);
  return `${props.currency.symbol}${v.toFixed(2)}`;
}

function suspendClient() {
  processingSuspend.value = true;
  router.patch(suspend(props.client.id), {}, {
    preserveScroll: true,
    onFinish: () => { processingSuspend.value = false; },
    onSuccess: () => { showSuspendDialog.value = false; },
  });
}

function activateClient() {
  processingActivate.value = true;
  router.patch(activate(props.client.id), {}, {
    preserveScroll: true,
    onFinish: () => { processingActivate.value = false; },
    onSuccess: () => { showActivateDialog.value = false; },
  });
}

const statusStyle = computed(() => {
  const active = props.client.is_active;
  const hex = active ? '#10B981' : '#EF4444';
  const toRgb = (h: string) => [parseInt(h.slice(1,3),16), parseInt(h.slice(3,5),16), parseInt(h.slice(5,7),16)];
  const [r,g,b] = toRgb(hex);
  return { bg: `rgba(${r}, ${g}, ${b}, 0.1)`, dot: hex, text: hex, label: active ? 'Active' : 'Suspended' };
});
</script>

<template>
  <Head :title="`Client ${client.name}`" />
  <AdminLayout>
    <main class="flex-1 space-y-6 p-8">
      <div class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-2">
          <div>
            <h1 class="text-2xl font-semibold">{{ client.name }}</h1>
            <div class="text-sm text-muted-foreground">{{ client.email }}</div>
          </div>
           <span
            class="inline-flex items-center gap-2 rounded-full px-2.5 py-1 text-xs font-medium"
            :style="{ backgroundColor: statusStyle.bg, color: statusStyle.text }"
          >
            <span class="size-2 rounded-full" :style="{ backgroundColor: statusStyle.dot }" />
            {{ statusStyle.label }}
          </span>
        </div>
        <div class="flex items-center gap-2">
         
          <Button v-if="client.is_active" variant="destructive" @click="showSuspendDialog = true">Suspend User</Button>
          <Button v-else @click="showActivateDialog = true">Activate User</Button>
          <Link :href="index()">
            <Button variant="outline">Back</Button>
          </Link>
        </div>
      </div>

      <!-- Stats -->
      <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-md border p-4">
          <div class="text-sm text-muted-foreground">Total Spent</div>
          <div class="text-xl font-semibold">{{ fmtMoney(stats.total_spent) }}</div>
        </div>
        <div class="rounded-md border p-4">
          <div class="text-sm text-muted-foreground">Reservations</div>
          <div class="text-xl font-semibold">{{ stats.total_reservations }}</div>
        </div>
        <div class="rounded-md border p-4">
          <div class="text-sm text-muted-foreground">Payments</div>
          <div class="text-xl font-semibold">{{ stats.total_payments }}</div>
        </div>
      </div>

      <!-- Reservations -->
      <div class="rounded-md border">
        <div class="border-b px-4 py-3 font-medium">Past Reservations</div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">#</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Car</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Dates</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Total</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                <th class="px-4 py-3"></th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
              <tr v-for="r in reservations.data" :key="r.id">
                <td class="px-4 py-3">{{ r.reservation_number }}</td>
                <td class="px-4 py-3">
                  <div class="font-medium">
                    {{ r.car ? `${r.car.year} ${r.car.make} ${r.car.model}` : '—' }}
                  </div>
                  <div class="text-xs text-muted-foreground">{{ r.car?.license_plate }}</div>
                </td>
                <td class="px-4 py-3">
                  <div class="font-medium">
                    {{ new Date(r.start_date).toLocaleDateString() }} → {{ new Date(r.end_date).toLocaleDateString() }}
                  </div>
                </td>
                <td class="px-4 py-3">{{ fmtMoney(r.total_amount) }}</td>
                <td class="px-4 py-3">{{ r.status }}</td>
                <td class="px-4 py-3 text-right">
                  <Link :href="`/admin/reservations/${r.id}`">
                    <Button variant="outline" size="sm">View</Button>
                  </Link>
                </td>
              </tr>
              <tr v-if="reservations.data.length === 0">
                <td colspan="6" class="px-4 py-6 text-center text-gray-500">No reservations.</td>
              </tr>
            </tbody>
          </table>
        </div>
        <nav v-if="reservations.links?.length" class="flex gap-2 px-4 py-3">
          <Link
            v-for="(link, i) in reservations.links"
            :key="i"
            :href="link.url || ''"
            :class="[
              'rounded px-3 py-1 text-sm',
              link.active ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-700',
              !link.url && 'pointer-events-none opacity-50',
            ]"
          >
            <span v-html="link.label" />
          </Link>
        </nav>
      </div>

      <!-- Payments -->
      <div class="rounded-md border">
        <div class="border-b px-4 py-3 font-medium">Payments</div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">#</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Reservation</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Amount</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Method</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Processed</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
              <tr v-for="p in payments.data" :key="p.id">
                <td class="px-4 py-3">{{ p.payment_number }}</td>
                <td class="px-4 py-3">
                  <div class="font-medium">{{ p.reservation?.reservation_number || '—' }}</div>
                </td>
                <td class="px-4 py-3">{{ fmtMoney(p.amount) }}</td>
                <td class="px-4 py-3">{{ p.payment_method }}</td>
                <td class="px-4 py-3">{{ p.status }}</td>
                <td class="px-4 py-3">{{ p.processed_at ? new Date(p.processed_at).toLocaleString() : '—' }}</td>
              </tr>
              <tr v-if="payments.data.length === 0">
                <td colspan="6" class="px-4 py-6 text-center text-gray-500">No payments.</td>
              </tr>
            </tbody>
          </table>
        </div>
        <nav v-if="payments.links?.length" class="flex gap-2 px-4 py-3">
          <Link
            v-for="(link, i) in payments.links"
            :key="i"
            :href="link.url || ''"
            :class="[
              'rounded px-3 py-1 text-sm',
              link.active ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-700',
              !link.url && 'pointer-events-none opacity-50',
            ]"
          >
            <span v-html="link.label" />
          </Link>
        </nav>
      </div>
    </main>

    <!-- Suspend Confirmation Dialog -->
    <Dialog v-model:open="showSuspendDialog">
      <DialogContent class="sm:max-w-[425px]">
        <DialogHeader>
          <DialogTitle class="flex items-center gap-2">
            <AlertCircle class="h-5 w-5 text-destructive" />
            Suspend User
          </DialogTitle>
          <DialogDescription>
            Are you sure you want to suspend this user? They will not be able to log in until re-activated.
          </DialogDescription>
        </DialogHeader>
        <Alert variant="destructive" class="mt-4">
          <AlertCircle class="h-4 w-4" />
          <AlertDescription>
            This action can be reverted later by an admin, but the user will be blocked immediately.
          </AlertDescription>
        </Alert>
        <DialogFooter class="mt-4">
          <DialogClose as-child>
            <Button variant="outline">Cancel</Button>
          </DialogClose>
          <Button type="button" variant="destructive" :disabled="processingSuspend" @click="suspendClient">
            {{ processingSuspend ? 'Suspending...' : 'Suspend User' }}
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>

    <!-- Activate Confirmation Dialog -->
    <Dialog v-model:open="showActivateDialog">
      <DialogContent class="sm:max-w-[425px]">
        <DialogHeader>
          <DialogTitle class="flex items-center gap-2">
            <AlertCircle class="h-5 w-5 text-destructive" />
            Activate User
          </DialogTitle>
          <DialogDescription>
            Are you sure you want to activate this user? They will be able to log in again.
          </DialogDescription>
        </DialogHeader>
        <DialogFooter class="mt-4">
          <DialogClose as-child>
            <Button variant="outline">Cancel</Button>
          </DialogClose>
          <Button type="button" variant="destructive" :disabled="processingActivate" @click="activateClient">
            {{ processingActivate ? 'Activating...' : 'Activate User' }}
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </AdminLayout>
</template>
