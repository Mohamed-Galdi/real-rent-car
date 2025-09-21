<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import FileUpload from '@/components/ViltFilePond/FileUpload.vue';
import { computed, ref, watch } from 'vue';

const props = defineProps<{
  car: any | null
  imageFiles: Array<{ id: number; url: string }>
}>()

const isEdit = computed(() => !!props.car)

// Form state
const form = useForm({
  make: props.car?.make ?? '',
  model: props.car?.model ?? '',
  year: props.car?.year ?? '',
  license_plate: props.car?.license_plate ?? '',
  color: props.car?.color ?? '',
  price_per_day: props.car?.price_per_day ?? '',
  mileage: props.car?.mileage ?? '',
  transmission: props.car?.transmission ?? 'automatic',
  seats: props.car?.seats ?? '',
  fuel_type: props.car?.fuel_type ?? '',
  description: props.car?.description ?? '',
  available: props.car?.available ?? true,
  // FilePond fields
  image: [] as string[],
  image_temp_folders: [] as string[],
  image_removed_files: [] as number[],
})

// Single image upload handling
const fileUploadRef = ref<InstanceType<typeof FileUpload> | null>(null)
const tempFolders = ref<string[]>([])
const removedFileIds = ref<number[]>([])

// Sync temp folders with form for edit
watch(tempFolders, (value) => {
  form.image_temp_folders = [...value]
}, { deep: true })

function handleFileRemoved(data: { type: string; fileId?: number }) {
  if (data.type === 'existing' && data.fileId) {
    removedFileIds.value.push(data.fileId)
    form.image_removed_files = [...removedFileIds.value]
  }
}

function submit() {
  if (isEdit.value) {
    form.put(`/admin/cars/${props.car.id}`)
  } else {
    // for create, pass image temp folders via `image`
    form.image = [...tempFolders.value]
    form.post('/admin/cars', {
      onSuccess: () => {
        form.reset()
        tempFolders.value = []
        fileUploadRef.value?.resetFiles()
      }
    })
  }
}

</script>

<template>
    <Head :title="isEdit ? 'Edit Car' : 'Create Car'" />
    <AdminLayout>
        <!-- Main -->
        <main class="flex-1 p-8 space-y-6">
            <div class="flex items-center justify-between gap-4">
                <h1 class="text-2xl font-semibold">{{ isEdit ? 'Edit Car' : 'Create Car' }}</h1>
                <Link href="/admin/cars">
                    <Button variant="outline">Back</Button>
                </Link>
            </div>

            <form class="space-y-6" @submit.prevent="submit">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <Label for="make">Make</Label>
                        <Input id="make" v-model="form.make" />
                        <InputError :message="form.errors.make" class="mt-1" />
                    </div>
                    <div>
                        <Label for="model">Model</Label>
                        <Input id="model" v-model="form.model" />
                        <InputError :message="form.errors.model" class="mt-1" />
                    </div>

                    <div>
                        <Label for="year">Year</Label>
                        <Input id="year" v-model="form.year" type="number" />
                        <InputError :message="form.errors.year" class="mt-1" />
                    </div>
                    <div>
                        <Label for="license_plate">License Plate</Label>
                        <Input id="license_plate" v-model="form.license_plate" />
                        <InputError :message="form.errors.license_plate" class="mt-1" />
                    </div>

                    <div>
                        <Label for="color">Color</Label>
                        <Input id="color" v-model="form.color" />
                        <InputError :message="form.errors.color" class="mt-1" />
                    </div>
                    <div>
                        <Label for="price_per_day">Price Per Day</Label>
                        <Input id="price_per_day" v-model="form.price_per_day" type="number" step="0.01" />
                        <InputError :message="form.errors.price_per_day" class="mt-1" />
                    </div>

                    <div>
                        <Label for="mileage">Mileage</Label>
                        <Input id="mileage" v-model="form.mileage" type="number" />
                        <InputError :message="form.errors.mileage" class="mt-1" />
                    </div>
                    <div>
                        <Label for="transmission">Transmission</Label>
                        <select id="transmission" v-model="form.transmission" class="border-input dark:bg-input/30 w-full rounded-md border bg-transparent px-3 py-2">
                            <option value="automatic">Automatic</option>
                            <option value="manual">Manual</option>
                        </select>
                        <InputError :message="form.errors.transmission" class="mt-1" />
                    </div>

                    <div>
                        <Label for="seats">Seats</Label>
                        <Input id="seats" v-model="form.seats" type="number" />
                        <InputError :message="form.errors.seats" class="mt-1" />
                    </div>
                    <div>
                        <Label for="fuel_type">Fuel Type</Label>
                        <Input id="fuel_type" v-model="form.fuel_type" />
                        <InputError :message="form.errors.fuel_type" class="mt-1" />
                    </div>

                    <div class="md:col-span-2">
                        <Label for="description">Description</Label>
                        <textarea id="description" v-model="form.description" rows="4" class="border-input dark:bg-input/30 w-full rounded-md border bg-transparent px-3 py-2"></textarea>
                        <InputError :message="form.errors.description" class="mt-1" />
                    </div>
                </div>

                <div>
                    <Label>Image</Label>
                    <div class="mt-2">
                        <FileUpload
                          ref="fileUploadRef"
                          v-model="tempFolders"
                          :initial-files="imageFiles || []"
                          :allow-multiple="false"
                          :max-files="1"
                          collection="image"
                          theme="light"
                          width="24rem"
                          @file-removed="handleFileRemoved"
                        />
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Only one image is allowed.</p>
                </div>

                <div class="flex items-center gap-2">
                    <Checkbox id="available" v-model:checked="form.available" />
                    <Label for="available">Available</Label>
                </div>

                <div class="flex items-center gap-3">
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? (isEdit ? 'Saving...' : 'Creating...') : (isEdit ? 'Save Changes' : 'Create Car') }}
                    </Button>
                    <Link href="/admin/cars">
                        <Button type="button" variant="outline">Cancel</Button>
                    </Link>
                </div>
            </form>
        </main>
    </AdminLayout>
</template>
