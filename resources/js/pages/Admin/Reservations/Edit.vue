<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import { index } from '@/routes/admin/reservations';
import { update } from '@/routes/admin/reservations';

const props = defineProps<{
    reservation: any;
    enums: {
        statuses: Array<{ value: string; label: string; color: string }>;
    };
}>();

const statuses = computed(() => props.enums.statuses || []);

const formatDateForInput = (dateString: string | null | undefined): string => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toISOString().split('T')[0];
};

const form = useForm({
    start_date: formatDateForInput(props.reservation?.start_date) || '',
    end_date: formatDateForInput(props.reservation?.end_date) || '',
    pickup_time: props.reservation?.pickup_time || '09:00',
    return_time: props.reservation?.return_time || '18:00',
    pickup_location: props.reservation?.pickup_location || '',
    return_location: props.reservation?.return_location || '',
    discount_amount: props.reservation?.discount_amount || 0,
    notes: props.reservation?.notes || '',
    status: props.reservation?.status || 'pending',
    cancellation_reason: props.reservation?.cancellation_reason || '',
});

function submit() {
    form.put(update(props.reservation.id).url);
}
</script>

<template>
    <Head
        :title="`Edit Reservation ${reservation?.reservation_number || ''}`"
    />
    <AdminLayout>
        <main class="flex-1 space-y-6 p-8">
            <div class="flex items-center justify-between gap-4">
                <h1 class="text-2xl font-semibold">Edit Reservation</h1>
                <Link :href="index().url">
                    <Button variant="outline">Back</Button>
                </Link>
            </div>

            <!-- Summary -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="rounded-md border p-4">
                    <div class="text-sm text-muted-foreground">
                        Reservation #
                    </div>
                    <div class="font-medium">
                        {{ reservation.reservation_number }}
                    </div>
                </div>
                <div class="rounded-md border p-4">
                    <div class="text-sm text-muted-foreground">Client</div>
                    <div class="font-medium">
                        {{ reservation.user?.name }} ({{
                            reservation.user?.email
                        }})
                    </div>
                </div>
                <div class="rounded-md border p-4">
                    <div class="text-sm text-muted-foreground">Car</div>
                    <div class="font-medium">
                        {{
                            reservation.car
                                ? `${reservation.car.year} ${reservation.car.make} ${reservation.car.model}`
                                : '—'
                        }}
                    </div>
                </div>
            </div>

            <form class="space-y-6" @submit.prevent="submit">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- Start Date -->
                    <div>
                        <Label for="start_date">Start Date</Label>
                        <Input
                            id="start_date"
                            v-model="form.start_date"
                            type="date"
                        />
                        <InputError
                            :message="form.errors.start_date"
                            class="mt-1"
                        />
                    </div>

                    <!-- End Date -->
                    <div>
                        <Label for="end_date">End Date</Label>
                        <Input
                            id="end_date"
                            v-model="form.end_date"
                            type="date"
                        />
                        <InputError
                            :message="form.errors.end_date"
                            class="mt-1"
                        />
                    </div>

                    <!-- Pickup Time -->
                    <div>
                        <Label for="pickup_time">Pickup Time</Label>
                        <Input
                            id="pickup_time"
                            v-model="form.pickup_time"
                            type="time"
                        />
                        <InputError
                            :message="form.errors.pickup_time"
                            class="mt-1"
                        />
                    </div>

                    <!-- Return Time -->
                    <div>
                        <Label for="return_time">Return Time</Label>
                        <Input
                            id="return_time"
                            v-model="form.return_time"
                            type="time"
                        />
                        <InputError
                            :message="form.errors.return_time"
                            class="mt-1"
                        />
                    </div>

                    <!-- Pickup Location -->
                    <div>
                        <Label for="pickup_location">Pickup Location</Label>
                        <Input
                            id="pickup_location"
                            v-model="form.pickup_location"
                            placeholder="Main Office"
                        />
                        <InputError
                            :message="form.errors.pickup_location"
                            class="mt-1"
                        />
                    </div>

                    <!-- Return Location -->
                    <div>
                        <Label for="return_location">Return Location</Label>
                        <Input
                            id="return_location"
                            v-model="form.return_location"
                            placeholder="Main Office"
                        />
                        <InputError
                            :message="form.errors.return_location"
                            class="mt-1"
                        />
                    </div>

                    <!-- Discount Amount -->
                    <div>
                        <Label for="discount_amount">Discount</Label>
                        <Input
                            id="discount_amount"
                            v-model="form.discount_amount"
                            type="number"
                            step="0.01"
                            min="0"
                        />
                        <InputError
                            :message="form.errors.discount_amount"
                            class="mt-1"
                        />
                    </div>

                    <!-- Status -->
                    <div>
                        <Label for="status">Status</Label>
                        <select
                            id="status"
                            v-model="form.status"
                            class="mt-1 block w-full rounded-md border border-gray-300 py-2 pr-10 pl-3 text-base focus:border-blue-500 focus:ring-blue-500 focus:outline-none sm:text-sm"
                        >
                            <option
                                v-for="s in statuses"
                                :key="s.value"
                                :value="s.value"
                            >
                                {{ s.label }}
                            </option>
                        </select>
                        <InputError
                            :message="form.errors.status"
                            class="mt-1"
                        />
                    </div>

                    <!-- Notes -->
                    <div class="md:col-span-2">
                        <Label for="notes">Notes</Label>
                        <textarea
                            id="notes"
                            v-model="form.notes"
                            rows="4"
                            class="w-full rounded-md border border-input bg-transparent px-3 py-2"
                            placeholder="Internal notes..."
                        ></textarea>
                        <InputError :message="form.errors.notes" class="mt-1" />
                    </div>

                    <!-- Cancellation Reason -->
                    <div
                        v-if="form.status === 'cancelled'"
                        class="md:col-span-2"
                    >
                        <Label for="cancellation_reason"
                            >Cancellation Reason</Label
                        >
                        <textarea
                            id="cancellation_reason"
                            v-model="form.cancellation_reason"
                            rows="3"
                            class="w-full rounded-md border border-input bg-transparent px-3 py-2"
                            placeholder="Why was this reservation cancelled?"
                        ></textarea>
                        <InputError
                            :message="form.errors.cancellation_reason"
                            class="mt-1"
                        />
                    </div>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Saving...' : 'Save Changes' }}
                    </Button>
                    <Link :href="index().url">
                        <Button type="button" variant="outline">Cancel</Button>
                    </Link>
                </div>
            </form>
        </main>
    </AdminLayout>
</template>
