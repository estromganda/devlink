<script setup lang="ts">
import { computed, onMounted, onUnmounted, nextTick, ref, watch } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';

defineOptions({
    layout: PublicLayout,
});

interface User {
    id: number;
    name: string;
}

interface ConversationMember {
    id: number;
    user: User;
}

interface ReplyMessage {
    id: number;
    body: string | null;
    sender: User;
}

interface Message {
    id: number;
    conversation_id: number;
    sender_id: number;
    body: string | null;
    type: string;
    reply_to_id: number | null;
    reply_to: ReplyMessage | null;
    sender: User;
    created_at: string;
}

interface Conversation {
    id: number;
    type: string;
    title: string | null;
    members: ConversationMember[];
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

const realtimeMessages = ref<Message[]>([]);

const allMessages = computed(() => [
    ...props.messages.data,
    ...realtimeMessages.value,
]);

onMounted(() => {
    window.Echo
        .private(`conversation.${props.conversation.id}`)
        .listen('.message.sent', (event: { message: Message }) => {
            const incoming = event.message;

            const alreadyLoaded = props.messages.data.some(
                (message: Message) => message.id === incoming.id,
            );

            const alreadyReceived = realtimeMessages.value.some(
                (message) => message.id === incoming.id,
            );

            if (!alreadyLoaded && !alreadyReceived) {
                realtimeMessages.value.push(incoming);
            }
        });
});

onUnmounted(() => {
    window.Echo.leave(`conversation.${props.conversation.id}`);
});

const props = defineProps<{
    conversation: Conversation;
    messages: {
        data: Message[];
        links: PaginationLink[];
        current_page: number;
        last_page: number;
        total: number;
    };
    currentUserId: number;
}>();

props.messages.data = props.messages.data.reverse()

const form = useForm({
    body: '',
    reply_to_id: null as number | null,
});

const messageContainer = ref<HTMLElement | null>(null);


const conversationTitle = computed(() => {
    if (props.conversation.title) {
        return props.conversation.title;
    }

    const otherMembers = props.conversation.members
        .filter((member) => member.user.id !== props.currentUserId)
        .map((member) => member.user.name);

    return otherMembers.join(', ') || 'Conversation';
});

const replyTarget = computed(() =>
    props.messages.data.find(
        (message) => message.id === form.reply_to_id,
    ) ?? null,
);

function sendMessage() {
    if (!form.body.trim()) {
        return;
    }

    form.transform((data) => ({
        ...data,
        body: data.body.trim(),
    })).post(`/messages/${props.conversation.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            form.reply_to_id = null;
        },
    });
}

function replyTo(message: Message) {
    form.reply_to_id = message.id;

    nextTick(() => {
        document.getElementById('message-body')?.focus();
    });
}

function cancelReply() {
    form.reply_to_id = null;
}

function isOwnMessage(message: Message): boolean {
    return message.sender_id === props.currentUserId;
}

function formatTime(date: string): string {
    return new Date(date).toLocaleTimeString('fr-FR', {
        hour: '2-digit',
        minute: '2-digit',
    });
}

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
}

watch(
    () => props.messages.data.length,
    async () => {
        await nextTick();

        if (messageContainer.value && props.messages.current_page === 1) {
            messageContainer.value.scrollTop =
                messageContainer.value.scrollHeight;
        }
    },
    { immediate: true },
);
</script>

<template>

    <Head :title="conversationTitle" />

    <main class="mx-auto max-w-5xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <header class="flex items-center gap-4 border-b border-gray-200 p-5">
                <Link href="/messages" aria-label="Retour à la messagerie"
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200">
                    ←
                </Link>

                <div
                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-teal-100 font-bold text-teal-800">
                    {{ conversationTitle.charAt(0).toUpperCase() }}
                </div>

                <div class="min-w-0">
                    <h1 class="truncate text-lg font-bold text-gray-900">
                        {{ conversationTitle }}
                    </h1>

                    <p class="text-sm text-gray-500">
                        Conversation privée
                    </p>
                </div>
            </header>

            <div ref="messageContainer" class="max-h-[65vh] min-h-87.5 space-y-5 overflow-y-auto bg-gray-50 p-4 sm:p-6">
                <div v-if="messages.data.length === 0"
                    class="flex min-h-75 flex-col items-center justify-center text-center">
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-white text-2xl shadow-sm">
                        ✉
                    </div>

                    <h2 class="mt-4 font-semibold text-gray-900">
                        Début de la conversation
                    </h2>

                    <p class="mt-2 text-sm text-gray-500">
                        Envoie ton premier message.
                    </p>
                </div>

                <div v-else class="space-y-5">
                    <div v-if="messages.current_page < messages.last_page" class="text-center">
                        <Link :href="messages.links[messages.links.length - 1]?.url ?? '#'"
                            class="text-sm font-medium text-teal-700 hover:text-teal-900">
                            Charger les messages précédents
                        </Link>
                    </div>

                    <div v-for="message in allMessages" :key="message.id" class="flex"
                        :class="isOwnMessage(message) ? 'justify-end' : 'justify-start'">
                        <div class="max-w-[85%] rounded-2xl px-4 py-3 shadow-sm sm:max-w-[75%]" :class="isOwnMessage(message)
                            ? 'rounded-br-md bg-teal-600 text-white'
                            : 'rounded-bl-md border border-gray-200 bg-white text-gray-900'
                            ">
                            <p v-if="!isOwnMessage(message)" class="mb-1 text-xs font-semibold text-teal-700">
                                {{ message.sender.name }}
                            </p>

                            <div v-if="message.reply_to" class="mb-2 rounded-lg border-l-2 px-3 py-2 text-xs" :class="isOwnMessage(message)
                                ? 'border-teal-200 bg-teal-700/40 text-teal-50'
                                : 'border-teal-500 bg-gray-50 text-gray-600'
                                ">
                                <p class="font-semibold">
                                    {{ message.reply_to.sender.name }}
                                </p>

                                <p class="mt-1 line-clamp-2">
                                    {{ message.reply_to.body || 'Pièce jointe' }}
                                </p>
                            </div>

                            <p class="whitespace-pre-wrap wrap-break-word text-sm leading-6">
                                {{ message.body || 'Pièce jointe' }}
                            </p>

                            <div class="mt-2 flex items-center justify-end gap-3">
                                <span class="text-xs" :class="isOwnMessage(message)
                                    ? 'text-teal-100'
                                    : 'text-gray-400'
                                    ">
                                    {{ formatTime(message.created_at) }}
                                </span>

                                <button type="button" class="text-xs underline underline-offset-2" :class="isOwnMessage(message)
                                    ? 'text-teal-100 hover:text-white'
                                    : 'text-gray-500 hover:text-teal-700'
                                    " @click="replyTo(message)">
                                    Répondre
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <footer class="border-t border-gray-200 bg-white p-4 sm:p-5">
                <div v-if="replyTarget" class="mb-3 flex items-start justify-between gap-3 rounded-xl bg-gray-50 p-3">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-teal-700">
                            Réponse à {{ replyTarget.sender.name }}
                        </p>

                        <p class="mt-1 truncate text-sm text-gray-600">
                            {{ replyTarget.body }}
                        </p>
                    </div>

                    <button type="button" class="text-sm text-gray-500 hover:text-gray-900" @click="cancelReply">
                        Annuler
                    </button>
                </div>

                <form class="flex items-end gap-3" @submit.prevent="sendMessage">
                    <div class="min-w-0 flex-1">
                        <label for="message-body" class="sr-only">
                            Ton message
                        </label>

                        <textarea id="message-body" v-model="form.body" rows="2" maxlength="10000" required
                            placeholder="Écris ton message..."
                            class="block w-full resize-y rounded-xl dark:text-gray-950 border border-gray-300 px-4 py-3 text-sm outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-100"
                            @keydown.enter.exact.prevent="sendMessage" />

                        <p v-if="form.errors.body" class="mt-1 text-sm text-red-600">
                            {{ form.errors.body }}
                        </p>

                        <p v-if="form.errors.reply_to_id" class="mt-1 text-sm text-red-600">
                            {{ form.errors.reply_to_id }}
                        </p>
                    </div>

                    <button type="submit" :disabled="form.processing || !form.body.trim()"
                        class="shrink-0 rounded-xl bg-teal-600 px-5 py-3 font-semibold text-white transition hover:bg-teal-700 disabled:cursor-not-allowed disabled:opacity-50">
                        Envoyer
                    </button>
                </form>

                <p class="mt-2 text-xs text-gray-400">
                    Entrée pour envoyer · Maj + Entrée pour passer à la ligne
                </p>
            </footer>
        </div>
    </main>
</template>
