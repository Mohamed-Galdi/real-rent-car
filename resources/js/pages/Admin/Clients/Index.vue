<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { ref, watch, computed } from 'vue';
import { show , index } from '@/routes/admin/clients';

const props = defineProps<{
  clients: {
    data: Array<{
      id: number
      name: string
      email: string
      is_active: boolean
      reservations_count: number
      payments_count: number
      created_at?: string
    }>
    links: Array<{ url: string | null; label: string; active: boolean }>
  }
  filters: {
    search?: string
    status?: string
  }
  statuses: Record<string, { label: string; count: number; color: string }>
}>()

const statusColors = computed(() => {
  const colors: Record<string, { bg: string; text: string; dot: string }> = {};
  for (const [status, data] of Object.entries(props.statuses || {})) {
    const hex = (data as any).color?.replace('#', '') || '6B7280';
    const r = parseInt(hex.substring(0, 2), 16);
    const g = parseInt(hex.substring(2, 4), 16);
    const b = parseInt(hex.substring(4, 6), 16);
    colors[status] = {
      bg: `rgba(${r}, ${g}, ${b}, 0.1)`,
      text: (data as any).color,
      dot: (data as any).color,
    };
  }
  return colors;
});

const getStatusColor = (status: string) => {
  return statusColors.value[status] || {
    bg: 'rgba(107, 114, 128, 0.1)',
    text: '#6B7280',
    dot: '#6B7280',
  };
};

const search = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status || 'all');

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

const navigateToClient = (id: number) => {
  router.visit(show(id).url);
};
</script>

<template>
  <Head title="Clients" />
  <AdminLayout>
    <main class="flex-1 p-8 space-y-6">
      <div class="flex items-center justify-between gap-4">
        <h1 class="text-2xl font-semibold">Clients</h1>
      </div>

      <div class="flex flex-col gap-4">
        <div class="flex items-center gap-2">
          <Input
            v-model="search"
            placeholder="Search name or email..."
            class="max-w-md"
            @keyup.enter="doSearch"
          />
          <Button @click="doSearch">Search</Button>
        </div>

        <!-- Status Filter -->
        <div class="flex flex-wrap items-center gap-2">
          <label class="inline-flex items-center">
            <input type="radio" class="hidden" v-model="statusFilter" value="all" @change="doSearch" />
            <span
              class="px-3 py-1.5 text-sm rounded-full cursor-pointer transition-colors"
              :class="{
                'bg-primary text-primary-foreground': statusFilter === 'all',
                'bg-muted text-muted-foreground hover:bg-muted/80': statusFilter !== 'all',
              }"
            >
              All ({{ Object.values(statuses).reduce((acc: number, curr: any) => acc + (curr as any).count, 0) }})
            </span>
          </label>

          <template v-for="(status, key) in statuses" :key="key">
            <label class="inline-flex items-center">
              <input type="radio" class="hidden" v-model="statusFilter" :value="key" @change="doSearch" />
              <span
                class="px-3 py-1.5 text-sm rounded-full cursor-pointer transition-colors flex items-center gap-1.5"
                :class="{
                  'bg-primary text-primary-foreground': statusFilter === key,
                  'bg-muted text-muted-foreground hover:bg-muted/80': statusFilter !== key,
                }"
              >
                <span class="w-2 h-2 rounded-full" :style="{ backgroundColor: (status as any).color }"></span>
                {{ (status as any).label }} ({{ (status as any).count }})
              </span>
            </label>
          </template>
        </div>
      </div>

      <div class="overflow-x-auto rounded-md border">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reservations</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payments</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-4 py-3"></th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr
              v-for="c in props.clients.data"
              :key="c.id"
              class="cursor-pointer hover:bg-gray-50 transition-colors"
              @click="navigateToClient(c.id)"
            >
              <td class="px-4 py-3">
                <div class="font-medium">{{ c.name }}</div>
                <div class="text-xs text-muted-foreground">{{ c.email }}</div>
              </td>
              <td class="px-4 py-3">{{ c.reservations_count }}</td>
              <td class="px-4 py-3">{{ c.payments_count }}</td>
              <td class="px-4 py-3">
                <span
                  class="inline-flex items-center gap-2 rounded-full px-2.5 py-1 text-xs font-medium"
                  :style="{
                    backgroundColor: getStatusColor(c.is_active ? 'active' : 'suspended').bg,
                    color: getStatusColor(c.is_active ? 'active' : 'suspended').text,
                  }"
                >
                  <span class="size-2 rounded-full" :style="{ backgroundColor: getStatusColor(c.is_active ? 'active' : 'suspended').dot }" />
                  {{ c.is_active ? 'Active' : 'Suspended' }}
                </span>
              </td>
              <td class="px-4 py-3 text-right">
                <Link :href="`/admin/clients/${c.id}`">
                  <Button variant="outline" size="sm">View</Button>
                </Link>
              </td>
            </tr>
            <tr v-if="props.clients.data.length === 0">
              <td colspan="5" class="px-4 py-6 text-center text-gray-500">No clients found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <nav v-if="props.clients.links?.length" class="flex gap-2">
        <Link
          v-for="(link, i) in props.clients.links"
          :key="i"
          :href="link.url || ''"
          :class="[
            'px-3 py-1 rounded text-sm',
            link.active ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-700',
            !link.url && 'pointer-events-none opacity-50',
          ]"
        >
          <span v-html="link.label" />
        </Link>
      </nav>
    </main>
  </AdminLayout>
</template>
