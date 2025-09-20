<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Cars',
        href: dashboard().url,
    },
];
</script>

<template>
    <Head title="Cars" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- Main -->
        <main class="flex-1 p-8">
            <div class="rounded border border-dashed p-8 text-gray-500">
                Empty page for now
            </div>
        </main>
    </AppLayout>
</template>
