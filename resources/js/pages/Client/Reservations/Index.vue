<script setup lang="ts">
import ClientLayout from '@/layouts/ClientLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import ReservationsController from '@/actions/App/Http/Controllers/Client/ReservationsController';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Reservation',
        href: ReservationsController.index().url,
    },
];
</script>

<template>
    <Head title="Reservation" />
    <ClientLayout :breadcrumbs="breadcrumbs">
        <!-- Main -->
        <main class="flex-1 p-8">
            <div class="rounded border border-dashed p-8 text-gray-500">
                Empty page for now
            </div>
        </main>
    </ClientLayout>
</template>
