<script setup lang="ts">
import {
    computed,
    onMounted,
    onUnmounted,
    nextTick,
    ref,
    watch,
} from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    ArrowLeft,
    Send,
    Reply,
    X,
    Users,
    MessageCircle,
    Paperclip,
    Check,
    Clock,
    ChevronUp,
} from '@lucide/vue';

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

const realtimeMessages = ref<Message[]>([]);
const messageContainer = ref<HTMLElement | null>(null);
const textarea = ref<HTMLTextAreaElement | null>(null);

const form = useForm({
    body: '',
    reply_to_id: null as number | null,
});

const allMessages = computed(() => {
    const messages = [
        ...props.messages.data,
        ...realtimeMessages.value,
    ];

    const uniqueMessages = new Map<number, Message>();

    for (const message of messages) {
        uniqueMessages.set(message.id, message);
    }

    return [...uniqueMessages.values()].sort(
        (a, b) =>
            new Date(a.created_at).getTime() -
            new Date(b.created_at).getTime(),
    );
});

const conversationTitle = computed(() => {
    if (props.conversation.title) {
        return props.conversation.title;
    }

    const otherMembers = props.conversation.members
        .filter((member) => member.user.id !== props.currentUserId)
        .map((member) => member.user.name);

    return otherMembers.join(', ') || 'Conversation';
});

const conversationInitials = computed(() => {
    const parts = conversationTitle.value.trim().split(/\s+/).filter(Boolean);

    if (parts.length === 0) {
        return 'C';
    }

    if (parts.length === 1) {
        return parts[0].slice(0, 2).toUpperCase();
    }

    return `${parts[0][0]}${parts[1][0]}`.toUpperCase();
});

const replyTarget = computed(() =>
    allMessages.value.find(
        (message) => message.id === form.reply_to_id,
    ) ?? null,
);

const otherMembersCount = computed(
    () => props.conversation.members.filter(
        (member) => member.user.id !== props.currentUserId,
    ).length,
);

function isOwnMessage(message: Message): boolean {
    return message.sender_id === props.currentUserId;
}

function formatTime(date: string): string {
    const value = new Date(date);

    if (Number.isNaN(value.getTime())) {
        return '';
    }

    return value.toLocaleTimeString('fr-FR', {
        hour: '2-digit',
        minute: '2-digit',
    });
}

function formatDate(date: string): string {
    const value = new Date(date);

    if (Number.isNaN(value.getTime())) {
        return '';
    }

    return value.toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
}

function isSameDay(first: string, second: string): boolean {
    return new Date(first).toDateString() === new Date(second).toDateString();
}

function shouldShowDate(index: number): boolean {
    if (index === 0) {
        return true;
    }

    return !isSameDay(
        allMessages.value[index - 1].created_at,
        allMessages.value[index].created_at,
    );
}

function messagePreview(message: Message | ReplyMessage): string {
    if (message.body?.trim()) {
        return message.body;
    }

    return 'Pièce jointe';
}

function sendMessage() {
    const body = form.body.trim();

    if (!body || form.processing) {
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
            resizeTextarea();

            nextTick(scrollToBottom);
        },
    });
}

function replyTo(message: Message) {
    form.reply_to_id = message.id;

    nextTick(() => {
        textarea.value?.focus();
    });
}

function cancelReply() {
    form.reply_to_id = null;
    textarea.value?.focus();
}

function resizeTextarea() {
    if (!textarea.value) {
        return;
    }

    textarea.value.style.height = 'auto';
    textarea.value.style.height = `${Math.min(textarea.value.scrollHeight, 160)}px`;
}

async function scrollToBottom() {
    await nextTick();

    if (messageContainer.value) {
        messageContainer.value.scrollTop =
            messageContainer.value.scrollHeight;
    }
}

function handleIncomingMessage(event: { message: Message }) {
    const incoming = event.message;

    const alreadyLoaded = props.messages.data.some(
        (message) => message.id === incoming.id,
    );

    const alreadyReceived = realtimeMessages.value.some(
        (message) => message.id === incoming.id,
    );

    if (!alreadyLoaded && !alreadyReceived) {
        realtimeMessages.value.push(incoming);
        scrollToBottom();
    }
}

const channelName = `conversation.${props.conversation.id}`;

onMounted(() => {
    window.Echo
        .private(channelName)
        .listen('.message.sent', handleIncomingMessage);

    scrollToBottom();
});

onUnmounted(() => {
    window.Echo.leave(channelName);
});

watch(
    () => props.messages.data.length,
    async () => {
        await nextTick();

        if (props.messages.current_page === 1) {
            scrollToBottom();
        }
    },
);

watch(
    () => form.body,
    () => {
        resizeTextarea();
    },
);
</script>

<template>

    <Head :title="conversationTitle" />

    <main class="min-h-[calc(100vh-8rem)] bg-gray-50/70 dark:bg-gray-950">
        <div class="mx-auto max-w-5xl px-3 py-4 sm:px-6 sm:py-8 lg:px-8">

            <!-- Retour -->
            <div class="mb-4">
                <Link href="/messages"
                    class="inline-flex items-center gap-2 rounded-lg px-2 py-2 text-sm font-medium text-gray-500 transition hover:bg-white hover:text-teal-700 sm:px-3 dark:text-gray-400 dark:hover:bg-gray-900 dark:hover:text-teal-300">
                    <ArrowLeft class="size-4" />
                    Retour aux conversations
                </Link>
            </div>

            <!-- Fenêtre de conversation -->
            <section
                class="flex h-[calc(100dvh-11rem)] min-h-105 flex-col overflow-hidden rounded-2xl border border-gray-200/80 bg-white shadow-sm sm:h-[min(760px,calc(100dvh-10rem))] dark:border-gray-800 dark:bg-gray-900"
                aria-label="Conversation">
                <!-- En-tête -->
                <header
                    class="flex shrink-0 items-center gap-3 border-b border-gray-100 px-4 py-3 sm:gap-4 sm:px-6 sm:py-4 dark:border-gray-800">
                    <div
                        class="flex size-11 shrink-0 items-center justify-center rounded-2xl bg-linear-to-br from-teal-500 to-cyan-600 text-sm font-bold text-white shadow-sm sm:size-12">
                        {{ conversationInitials }}
                    </div>

                    <div class="min-w-0 flex-1">
                        <h1 class="truncate text-base font-bold text-gray-900 sm:text-lg dark:text-gray-100">
                            {{ conversationTitle }}
                        </h1>

                        <div class="mt-1 flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                            <Users v-if="conversation.type === 'group'" class="size-3.5" />

                            <MessageCircle v-else class="size-3.5" />

                            <span>
                                {{ conversation.type === 'group'
                                    ? `${props.conversation.members.length} membres`
                                    : 'Conversation privée' }}
                            </span>

                            <span v-if="otherMembersCount > 0 && conversation.type !== 'group'"
                                class="text-gray-300 dark:text-gray-700">
                                ·
                            </span>

                            <span v-if="otherMembersCount > 0 && conversation.type !== 'group'" class="truncate">
                                {{ otherMembersCount }} interlocuteur{{ otherMembersCount > 1 ? 's' : '' }}
                            </span>
                        </div>
                    </div>

                    <div
                        class="hidden shrink-0 items-center gap-2 rounded-full bg-teal-50 px-3 py-1.5 text-xs font-medium text-teal-700 sm:inline-flex dark:bg-teal-950 dark:text-teal-300">
                        <span class="size-1.5 rounded-full bg-teal-500" />
                        Messagerie
                    </div>
                </header>

                <!-- Messages -->
                <div ref="messageContainer"
                    class="min-h-0 flex-1 overflow-y-auto bg-gray-50/80 px-3 py-5 sm:px-6 sm:py-6 dark:bg-gray-950/60">
                    <!-- Aucun message -->
                    <div v-if="allMessages.length === 0"
                        class="flex min-h-full flex-col items-center justify-center px-4 py-12 text-center">
                        <div
                            class="flex size-16 items-center justify-center rounded-2xl bg-white text-teal-600 shadow-sm dark:bg-gray-900 dark:text-teal-300">
                            <MessageCircle class="size-8" />
                        </div>

                        <h2 class="mt-5 text-lg font-bold text-gray-900 dark:text-gray-100">
                            Le début d'une belle conversation
                        </h2>

                        <p class="mt-2 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                            Présente-toi, partage une idée ou commence à collaborer
                            avec ton interlocuteur.
                        </p>
                    </div>

                    <div v-else class="mx-auto max-w-3xl space-y-5">

                        <!-- Messages précédents -->
                        <div v-if="messages.current_page < messages.last_page" class="flex justify-center pb-2">
                            <Link :href="messages.links[messages.links.length - 1]?.url ?? '#'"
                                class="inline-flex items-center gap-2 rounded-full border border-gray-200 bg-white px-4 py-2 text-xs font-semibold text-gray-600 shadow-sm transition hover:border-teal-300 hover:text-teal-700 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-teal-700 dark:hover:text-teal-300">
                                <ChevronUp class="size-4" />
                                Charger les messages précédents
                            </Link>
                        </div>

                        <template v-for="(message, index) in allMessages" :key="message.id">
                            <!-- Séparateur de date -->
                            <div v-if="shouldShowDate(index)" class="flex items-center justify-center py-2">
                                <span
                                    class="rounded-full border border-gray-200/80 bg-white px-3 py-1.5 text-[11px] font-medium text-gray-500 shadow-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400">
                                    {{ formatDate(message.created_at) }}
                                </span>
                            </div>

                            <!-- Bulle -->
                            <div class="group flex items-end gap-2.5" :class="isOwnMessage(message)
                                ? 'justify-end'
                                : 'justify-start'">
                                <div v-if="!isOwnMessage(message)"
                                    class="mb-1 flex size-8 shrink-0 items-center justify-center rounded-xl bg-gray-200 text-[10px] font-bold text-gray-600 dark:bg-gray-800 dark:text-gray-300"
                                    :aria-label="message.sender.name" :title="message.sender.name">
                                    {{ message.sender.name.slice(0, 2).toUpperCase() }}
                                </div>

                                <div class="min-w-0 max-w-[88%] sm:max-w-[75%]">
                                    <p v-if="!isOwnMessage(message)"
                                        class="mb-1.5 ml-1 text-xs font-semibold text-gray-500 dark:text-gray-400">
                                        {{ message.sender.name }}
                                    </p>

                                    <div class="rounded-2xl px-3.5 py-3 shadow-sm sm:px-4"
                                        :class="isOwnMessage(message)
                                            ? 'rounded-br-md bg-teal-600 text-white'
                                            : 'rounded-bl-md border border-gray-200/80 bg-white text-gray-800 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-100'">
                                        <!-- Référence à un message -->
                                        <div v-if="message.reply_to"
                                            class="mb-2.5 rounded-lg border-l-2 px-3 py-2.5 text-xs"
                                            :class="isOwnMessage(message)
                                                ? 'border-teal-200/80 bg-teal-700/50 text-teal-50'
                                                : 'border-teal-500 bg-gray-50 text-gray-600 dark:bg-gray-800 dark:text-gray-300'">
                                            <p class="font-semibold">
                                                {{ message.reply_to.sender.name }}
                                            </p>

                                            <p class="mt-1 line-clamp-2 wrap-break-word opacity-90">
                                                {{ messagePreview(message.reply_to) }}
                                            </p>
                                        </div>

                                        <!-- Contenu -->
                                        <p class="whitespace-pre-wrap wrap-break-word text-sm leading-6">
                                            {{ message.body || '📎 Pièce jointe' }}
                                        </p>

                                        <!-- Heure et actions -->
                                        <div class="mt-2 flex items-center justify-end gap-2.5">
                                            <time :datetime="message.created_at" class="text-[10px]" :class="isOwnMessage(message)
                                                ? 'text-teal-100'
                                                : 'text-gray-400 dark:text-gray-500'">
                                                {{ formatTime(message.created_at) }}
                                            </time>

                                            <button type="button"
                                                class="inline-flex items-center gap-1 rounded-md text-[11px] font-medium transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal-400"
                                                :class="isOwnMessage(message)
                                                    ? 'text-teal-100 hover:text-white'
                                                    : 'text-gray-400 hover:text-teal-700 dark:hover:text-teal-300'"
                                                :aria-label="`Répondre au message de ${message.sender.name}`"
                                                @click="replyTo(message)">
                                                <Reply class="size-3.5" />
                                                <span>Répondre</span>
                                            </button>

                                            <Check v-if="isOwnMessage(message)" class="size-3.5 text-teal-100"
                                                aria-label="Message envoyé" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Zone de réponse et de saisie -->
                <footer
                    class="shrink-0 border-t border-gray-100 bg-white p-3 sm:p-5 dark:border-gray-800 dark:bg-gray-900">
                    <!-- Réponse en cours -->
                    <div v-if="replyTarget"
                        class="mb-3 flex items-start gap-3 rounded-xl border border-teal-100 bg-teal-50/70 p-3 dark:border-teal-900 dark:bg-teal-950/40">
                        <div
                            class="mt-0.5 flex size-8 shrink-0 items-center justify-center rounded-lg bg-teal-100 text-teal-700 dark:bg-teal-900 dark:text-teal-300">
                            <Reply class="size-4" />
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold text-teal-800 dark:text-teal-300">
                                Réponse à {{ replyTarget.sender.name }}
                            </p>

                            <p class="mt-1 line-clamp-2 wrap-break-word text-sm text-gray-600 dark:text-gray-300">
                                {{ messagePreview(replyTarget) }}
                            </p>
                        </div>

                        <button type="button"
                            class="flex size-8 shrink-0 items-center justify-center rounded-lg text-gray-500 transition hover:bg-teal-100 hover:text-gray-900 dark:hover:bg-teal-900 dark:hover:text-white"
                            aria-label="Annuler la réponse" @click="cancelReply">
                            <X class="size-4" />
                        </button>
                    </div>

                    <form class="mx-auto flex max-w-3xl items-end gap-2 sm:gap-3" @submit.prevent="sendMessage">
                        <div
                            class="flex min-h-12 min-w-0 flex-1 items-end rounded-2xl border border-gray-200 bg-gray-50 px-3 transition focus-within:border-teal-400 focus-within:bg-white focus-within:ring-2 focus-within:ring-teal-100 dark:border-gray-700 dark:bg-gray-950 dark:focus-within:border-teal-700 dark:focus-within:bg-gray-950 dark:focus-within:ring-teal-950">
                            <!-- <div class="flex size-8 shrink-0 items-center justify-center self-end pb-3 text-gray-400"
                                aria-hidden="true">
                                <Paperclip class="size-4" />
                            </div> -->

                            <label for="message-body" class="sr-only">
                                Ton message
                            </label>

                            <textarea id="message-body" ref="textarea" v-model="form.body" rows="1" maxlength="10000"
                                required placeholder="Écris ton message..."
                                class="block max-h-40 min-h-12 min-w-0 flex-1 resize-none border-0 bg-transparent px-1 py-3 text-sm leading-6 text-gray-900 outline-none placeholder:text-gray-400 focus:ring-0 dark:text-gray-100 dark:placeholder:text-gray-500"
                                @keydown.enter.exact.prevent="sendMessage" @keydown.shift.enter.stop />
                        </div>

                        <button type="submit" :disabled="form.processing || !form.body.trim()"
                            class="inline-flex size-12 shrink-0 items-center justify-center rounded-2xl bg-teal-600 text-white shadow-sm transition hover:bg-teal-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-40 dark:focus-visible:ring-offset-gray-900"
                            aria-label="Envoyer le message">
                            <span v-if="form.processing"
                                class="size-5 animate-spin rounded-full border-2 border-white/40 border-t-white" />

                            <Send v-else class="size-5" />
                        </button>
                    </form>

                    <!-- Erreurs -->
                    <div v-if="form.errors.body || form.errors.reply_to_id" class="mx-auto mt-2 max-w-3xl" role="alert">
                        <p v-if="form.errors.body" class="text-xs text-red-600 dark:text-red-400">
                            {{ form.errors.body }}
                        </p>

                        <p v-if="form.errors.reply_to_id" class="mt-1 text-xs text-red-600 dark:text-red-400">
                            {{ form.errors.reply_to_id }}
                        </p>
                    </div>

                    <p class="mt-2 hidden text-center text-[11px] text-gray-400 sm:block dark:text-gray-500">
                        Entrée pour envoyer · Maj + Entrée pour passer à la ligne
                    </p>
                </footer>
            </section>
        </div>
    </main>
</template>