<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed, nextTick, onMounted, ref, watch } from 'vue';
import { index } from '@/routes/admin/support';
import { reply } from '@/routes/admin/support';
import { close } from '@/routes/admin/support';

// Types
interface Message {
    id: number;
    message: string;
    is_admin: boolean;
    created_at: string;
}

interface Ticket {
    id: number;
    subject: string;
    status: TicketStatusType;
    created_at: string;
    guest_name?: string;
    guest_email?: string;
    message?: string;
    messages: Message[];
    user?: {
        name: string;
    };
}

// Define TicketStatus enum locally since it's not available in @/types
const TicketStatus = {
    OPEN: 'open',
    IN_PROGRESS: 'in_progress',
    CLOSED: 'closed',
} as const;

type TicketStatusType = (typeof TicketStatus)[keyof typeof TicketStatus];

const props = defineProps<{
    ticket: Ticket;
    isGuest?: boolean;
}>();

const form = useForm<{
    message: string;
}>({
    message: '',
});

const statusColors: Record<TicketStatusType, string> = {
    [TicketStatus.OPEN]: 'bg-blue-100 text-blue-800',
    [TicketStatus.IN_PROGRESS]: 'bg-yellow-100 text-yellow-800',
    [TicketStatus.CLOSED]: 'bg-gray-100 text-gray-800',
} as const;

const statusLabels: Record<TicketStatusType, string> = {
    [TicketStatus.OPEN]: 'Open',
    [TicketStatus.IN_PROGRESS]: 'In Progress',
    [TicketStatus.CLOSED]: 'Closed',
} as const;

const canSend = computed(
    () => form.message.trim().length > 0 && !form.processing,
);

const messagesEndRef = ref<HTMLElement | null>(null);
const scrollToBottom = async () => {
    await nextTick();
    messagesEndRef.value?.scrollIntoView({ behavior: 'smooth', block: 'end' });
};

const submitReply = async () => {
    if (props.isGuest) return;
    if (!form.message || form.message.trim().length === 0) return;

    try {
        await form.post(reply(props.ticket.id).url, {
            preserveScroll: true,
            onSuccess: () => {
                form.reset('message');
                // Refresh the ticket data to get the latest messages
                router.reload({ only: ['ticket'] });
                scrollToBottom();
            },
            onError: (errors) => {
                console.error('Failed to send message:', errors);
            },
        });
    } catch (error) {
        console.error('An error occurred while sending the message:', error);
    }
};

const formatDate = (dateString: string): string => {
    return new Date(dateString).toLocaleString();
};

onMounted(() => {
    scrollToBottom();
});

watch(
    () => props.ticket.messages?.length,
    () => scrollToBottom(),
);

const btnProcessing = ref(false);
function closeTicket(){
    btnProcessing.value = true;
    router.post(close(props.ticket.id).url);
}
</script>

<template>
    <Head :title="`Ticket #${ticket.id}`" />
    <AdminLayout>
        <div class="p-4">
            <!-- Ticket Header -->
            <div class="mb-6 w-full rounded-lg bg-white p-6 shadow">
                <div class="mb-4 flex items-start justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">
                            {{ ticket.subject }}
                        </h1>
                        <div class="mt-2 flex items-center">
                            <span
                                class="rounded-full px-3 py-1 text-xs font-medium"
                                :class="
                                    statusColors[
                                        ticket.status as TicketStatusType
                                    ] || 'bg-gray-100 text-gray-800'
                                "
                            >
                                {{
                                    statusLabels[
                                        ticket.status as TicketStatusType
                                    ] || ticket.status
                                }}
                            </span>
                            <span class="ml-2 text-sm text-gray-500">
                                #{{ ticket.id }} •
                                {{ formatDate(ticket.created_at) }}
                            </span>
                        </div>
                    </div>
                    <div class="mt-2">
                        <Link :href="index().url">
                            <Button variant="outline">Back</Button>
                        </Link>
                        <Button
                            v-if="!isGuest && ticket.status !== 'closed'"
                            @click="closeTicket"
                            variant="secondary"
                            class="ml-2"
                            :disabled="btnProcessing"
                        >
                            {{ btnProcessing ? 'Closing...' : 'Close Ticket' }}
                        </Button>
                    </div>
                </div>

                <!-- Guest Ticket Details -->
                <div v-if="isGuest" class="mt-4 border-t pt-4">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <p class="text-sm font-medium text-gray-500">
                                Name
                            </p>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ ticket.guest_name }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">
                                Email
                            </p>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ ticket.guest_email }}
                            </p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-sm font-medium text-gray-500">
                                Message
                            </p>
                            <div class="mt-1 rounded-md bg-gray-50 p-3">
                                <p
                                    class="text-sm whitespace-pre-line text-gray-800"
                                >
                                    {{ ticket.messages[0].message }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chat Interface (for client tickets) -->
            <div
                v-if="!isGuest"
                class="h-2/3 space-y-4 overflow-scroll p-2 border-2 rounded-md"
            >
                <!-- Messages -->
                <div class="space-y-4">
                    <div
                        v-if="!ticket.messages || ticket.messages.length === 0"
                        class="rounded-md border border-dashed p-6 text-center text-sm text-gray-500"
                    >
                        No messages yet. Start the conversation below.
                    </div>
                    <div
                        v-for="message in ticket.messages"
                        :key="message.id"
                        :class="[
                            'flex',
                            message.is_admin ? 'justify-end' : 'justify-start',
                        ]"
                    >
                        <div
                            :class="[
                                'max-w-3xl rounded-lg px-4 py-2',
                                message.is_admin
                                    ? 'rounded-tr-none bg-blue-500 text-white'
                                    : 'rounded-tl-none bg-gray-200 text-gray-800',
                            ]"
                        >
                            <p class="whitespace-pre-line">
                                {{ message.message }}
                            </p>
                            <p class="mt-1 text-right text-xs opacity-75">
                                {{ formatDate(message.created_at) }}
                                <span v-if="message.is_admin" class="ml-1"
                                    >• Admin(you)</span
                                >
                                <span v-else class="ml-1"
                                    >• {{ ticket.user?.name || 'Client' }}</span
                                >
                            </p>
                        </div>
                    </div>
                    <div ref="messagesEndRef"></div>
                </div>
            </div>
            <!-- Reply Form -->
            <form v-if="!isGuest && ticket.status !== 'closed'" @submit.prevent="submitReply" class="mt-6">
                <div class="flex space-x-2">
                    <div class="flex-1">
                        <label for="message" class="sr-only"
                            >Reply to ticket</label
                        >
                        <textarea
                            id="message"
                            v-model="form.message"
                            rows="3"
                            class="w-full rounded-lg border-1 border-gray-300 p-2"
                            placeholder="Type your reply here... (Ctrl+Enter to send)"
                            required
                            aria-label="Type your reply here"
                            @keydown.ctrl.enter.prevent="submitReply"
                        ></textarea>
                        <p
                            v-if="form.errors.message"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.message }}
                        </p>
                    </div>
                    <button
                        type="submit"
                        class="mb-2 w-20 cursor-pointer self-end rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                        :disabled="!canSend"
                    >
                        <span v-if="form.processing">Sending...</span>
                        <span v-else>Send</span>
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
