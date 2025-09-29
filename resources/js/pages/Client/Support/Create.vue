<script setup lang="ts">
import { Button } from '@/components/ui/button';
import ClientLayout from '@/layouts/ClientLayout.vue';
import { index, store } from '@/routes/client/support';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const form = useForm<{
    subject: string;
    message: string;
}>({
    subject: '',
    message: '',
});

const canSubmit = computed(
    () =>
        form.subject.trim().length > 0 &&
        form.message.trim().length > 0 &&
        !form.processing,
);

const submitTicket = async () => {
    if (!form.subject || form.subject.trim().length === 0) return;
    if (!form.message || form.message.trim().length === 0) return;

    try {
        await form.post(store().url, {
            onSuccess: () => {
                // Redirect will be handled by Inertia after successful creation
            },
            onError: (errors) => {
                console.error('Failed to create ticket:', errors);
            },
        });
    } catch (error) {
        console.error('An error occurred while creating the ticket:', error);
    }
};
</script>

<template>
    <Head title="Create New Ticket" />
    <ClientLayout>
        <div class="p-4">
            <!-- Page Header -->
            <div class="mb-6 w-full rounded-lg bg-white p-6 shadow">
                <div class="flex items-start justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">
                            Create New Support Ticket
                        </h1>
                        <p class="mt-2 text-sm text-gray-600">
                            Need help? Submit a support ticket and our team will
                            get back to you as soon as possible.
                        </p>
                    </div>
                    <div>
                        <Link :href="index().url">
                            <Button variant="outline">Back to Tickets</Button>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Ticket Form -->
            <div class="w-full rounded-lg bg-white p-6 shadow">
                <form @submit.prevent="submitTicket" class="space-y-6">
                    <!-- Subject Field -->
                    <div>
                        <label
                            for="subject"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Subject
                            <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="subject"
                            v-model="form.subject"
                            type="text"
                            class="mt-1 w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            placeholder="Brief description of your issue"
                            required
                            maxlength="255"
                            aria-label="Ticket subject"
                        />
                        <p
                            v-if="form.errors.subject"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.subject }}
                        </p>
                        <p class="mt-1 text-xs text-gray-500">
                            {{ form.subject.length }}/255 characters
                        </p>
                    </div>

                    <!-- Message Field -->
                    <div>
                        <label
                            for="message"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Message
                            <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            id="message"
                            v-model="form.message"
                            rows="8"
                            class="mt-1 w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            placeholder="Provide detailed information about your issue..."
                            required
                            aria-label="Ticket message"
                        ></textarea>
                        <p
                            v-if="form.errors.message"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.message }}
                        </p>
                        <p class="mt-1 text-xs text-gray-500">
                            Please provide as much detail as possible to help us
                            assist you better.
                        </p>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end space-x-3">
                        <Link :href="index().url">
                            <Button
                                type="button"
                                variant="outline"
                                :disabled="form.processing"
                            >
                                Cancel
                            </Button>
                        </Link>
                        <button
                            type="submit"
                            class="rounded-md cursor-pointer bg-slate-600 px-6 py-2 text-white hover:bg-slate-700 focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                            :disabled="!canSubmit"
                        >
                            <span v-if="form.processing"
                                >Creating Ticket...</span
                            >
                            <span v-else>Create Ticket</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Help Text -->
            <div class="mt-6 rounded-lg border border-blue-200 bg-blue-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg
                            class="h-5 w-5 text-blue-400"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">
                            Tips for submitting a ticket
                        </h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc space-y-1 pl-5">
                                <li>
                                    Use a clear and descriptive subject line
                                </li>
                                <li>
                                    Include relevant details such as error
                                    messages or screenshots
                                </li>
                                <li>
                                    Describe the steps to reproduce the issue
                                </li>
                                <li>
                                    Our support team typically responds within
                                    24 hours
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ClientLayout>
</template>
