<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { ref, watch } from 'vue';

const props = defineProps<{
  cars: {
    data: Array<{
      id: number
      make: string
      model: string
      year: number
      license_plate: string
      price_per_day: string | number
      available: boolean
      image_url?: string
    }>
    links: Array<{ url: string | null; label: string; active: boolean }>
  }
  filters: { search?: string }
}>()

const search = ref(props.filters?.search || '')

function doSearch() {
  router.get('/admin/cars', { search: search.value }, {
    preserveState: true,
    replace: true,
  })
}

watch(search, (v, ov) => {
  if (v === '' && ov !== '') doSearch()
})

function destroyCar(id: number) {
  if (!confirm('Are you sure you want to delete this car?')) return
  router.delete(`/admin/cars/${id}`, {
    preserveScroll: true,
  })
}
</script>

<template>
    <Head title="Cars" />
    <AdminLayout>
        <!-- Main -->
        <main class="flex-1 p-8 space-y-6">
            <div class="flex items-center justify-between gap-4">
                <h1 class="text-2xl font-semibold">Cars</h1>
                <Link href="/admin/cars/create">
                    <Button >
                        + New Car
                    </Button>
                </Link>
            </div>

            <div class="flex items-center gap-2">
                <Input
                  v-model="search"
                  placeholder="Search make, model, plate..."
                  class="max-w-md"
                  @keyup.enter="doSearch"
                />
                <Button @click="doSearch">Search</Button>
            </div>

            <div class="overflow-x-auto rounded-md border">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Car</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plate</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price/Day</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Available</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="car in props.cars.data" :key="car.id">
                            <td class="px-4 py-3">
                                <img :src="car.image_url" alt="Car" class="h-12 w-16 object-cover rounded" />
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ car.year }} {{ car.make }} {{ car.model }}</div>
                            </td>
                            <td class="px-4 py-3">{{ car.license_plate }}</td>
                            <td class="px-4 py-3">€{{ Number(car.price_per_day).toFixed(2) }}</td>
                            <td class="px-4 py-3">
                                <span
                                  class="inline-flex items-center gap-2 rounded-full px-2.5 py-1 text-xs font-medium"
                                  :class="car.available ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700'"
                                >
                                  <span class="size-2 rounded-full" :class="car.available ? 'bg-green-500' : 'bg-gray-400'" />
                                  {{ car.available ? 'Yes' : 'No' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <Link :href="`/admin/cars/${car.id}/edit`">
                                    <Button variant="outline" size="sm">Edit</Button>
                                </Link>
                                <Button variant="destructive" size="sm" @click="destroyCar(car.id)">Delete</Button>
                            </td>
                        </tr>
                        <tr v-if="props.cars.data.length === 0">
                            <td colspan="6" class="px-4 py-6 text-center text-gray-500">No cars found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <nav v-if="props.cars.links?.length" class="flex gap-2">
                <Link
                    v-for="(link, i) in props.cars.links"
                    :key="i"
                    :href="link.url || ''"
                    :class="[
                        'px-3 py-1 rounded text-sm',
                        link.active ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-700',
                        !link.url && 'pointer-events-none opacity-50'
                    ]"
                >
                  <span v-html="link.label" />
                </Link>
            </nav>
        </main>
    </AdminLayout>
</template>
