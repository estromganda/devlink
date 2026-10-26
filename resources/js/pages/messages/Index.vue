<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import {
    MessageCircle,
    MessageSquarePlus,
    Search,
    Users,
    ChevronRight,
    Inbox,
} from '@lucide/vue';

interface User {
    id: number;
    name: string;
}

interface ConversationMember {
    id: number;
    user: User;
}

interface Message {
    id: number;
    body: string | null;
    type: string;
    created_at: string;
    sender_id: number;
}

interface Conversation {
    id: number;
    type: string;
    title: string | null;
    members: ConversationMember[];
    messages: Message[];
    unread_count: number;
    updated_at: string;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

const props = defineProps<{
    conversations: {
        data: Conversation[];
        links: PaginationLink[];
        current_page: number;
        last_page: number;
        total: number;
    };
}>();

const hasConversations = computed(
    () => props.conversations.data.length > 0,
);

const unreadTotal = computed(() =>
    props.conversations.data.reduce(
        (total, conversation) => total + conversation.unread_count,
        0,
    ),
);

function conversationName(conversation: Conversation): string {
    if (conversation.title) {
        return conversation.title;
    }

    const names = conversation.members
        .map((member) => member.user?.name)
        .filter(Boolean);

    return names.join(', ') || 'Conversation';
}

function conversationInitials(conversation: Conversation): string {
    const name = conversationName(conversation).trim();

    if (!name) {
        return 'C';
    }

    const parts = name.split(/\s+/).filter(Boolean);

    if (parts.length === 1) {
        return parts[0].slice(0, 2).toUpperCase();
    }

    return `${parts[0][0]}${parts[1][0]}`.toUpperCase();
}

function lastMessage(conversation: Conversation): string {
    const message = conversation.messages?.[0];

    if (!message) {
        return 'Aucun message pour le moment';
    }

    if (message.body?.trim()) {
        return message.body;
    }

    switch (message.type) {
        case 'image':
            return '📷 Image';
        case 'file':
        case 'attachment':
            return '📎 Pièce jointe';
        case 'audio':
            return '🎤 Message vocal';
        case 'video':
            return '🎬 Vidéo';
        default:
            return 'Nouveau message';
    }
}

function formatDate(date: string): string {
    const value = new Date(date);

    if (Number.isNaN(value.getTime())) {
        return '';
    }

    const now = new Date();
    const isToday = value.toDateString() === now.toDateString();

    const yesterday = new Date(now);
    yesterday.setDate(now.getDate() - 1);

    const isYesterday = value.toDateString() === yesterday.toDateString();

    if (isToday) {
        return value.toLocaleTimeString('fr-FR', {
            hour: '2-digit',
            minute: '2-digit',
        });
    }

    if (isYesterday) {
        return 'Hier';
    }

    const sameYear = value.getFullYear() === now.getFullYear();

    return value.toLocaleDateString(
        'fr-FR',
        sameYear
            ? { day: 'numeric', month: 'short' }
            : { day: 'numeric', month: 'short', year: '2-digit' },
    );
}
</script>

<template>

    <Head title="Messagerie" />

    <main class="min-h-[calc(100vh-8rem)] bg-gray-50/70 dark:bg-gray-950">
        <div class="mx-auto max-w-5xl px-4 py-6 sm:px-6 sm:py-10 lg:px-8">

            <!-- En-tête -->
            <header class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-start gap-4">
                    <div
                        class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-teal-100 text-teal-700 dark:bg-teal-950 dark:text-teal-300 sm:size-14">
                        <MessageCircle class="size-7" />
                    </div>

                    <div class="min-w-0">
                        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100 sm:text-3xl">
                            Messagerie
                        </h1>

                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 sm:text-base">
                            Échange et collabore avec les développeurs.
                        </p>
                    </div>
                </div>

                <Link href="/messages/new"
                    class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-teal-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-teal-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-2 sm:w-auto dark:focus-visible:ring-offset-gray-950">
                    <MessageSquarePlus class="size-5" />
                    Nouvelle conversation
                </Link>
            </header>

            <!-- Résumé -->
            <section class="mt-7 grid grid-cols-2 gap-3 sm:mt-9 sm:gap-4" aria-label="Résumé de la messagerie">
                <div
                    class="flex items-center gap-3 rounded-2xl border border-gray-200/80 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900 sm:p-5">
                    <div
                        class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-950 dark:text-blue-300">
                        <Users class="size-5" />
                    </div>

                    <div class="min-w-0">
                        <p class="text-xl font-bold text-gray-900 dark:text-gray-100">
                            {{ conversations.total }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 sm:text-sm">
                            Conversations
                        </p>
                    </div>
                </div>

                <div
                    class="flex items-center gap-3 rounded-2xl border border-gray-200/80 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900 sm:p-5">
                    <div
                        class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-teal-50 text-teal-700 dark:bg-teal-950 dark:text-teal-300">
                        <MessageCircle class="size-5" />
                    </div>

                    <div class="min-w-0">
                        <p class="text-xl font-bold text-gray-900 dark:text-gray-100">
                            {{ unreadTotal }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 sm:text-sm">
                            Messages non lus
                        </p>
                    </div>
                </div>
            </section>

            <!-- Liste des conversations -->
            <section class="mt-7 sm:mt-9">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                            Vos conversations
                        </h2>

                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Retrouvez vos derniers échanges.
                        </p>
                    </div>

                    <span v-if="conversations.total > 0"
                        class="shrink-0 rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                        {{ conversations.total }}
                    </span>
                </div>

                <!-- État vide -->
                <div v-if="!hasConversations"
                    class="rounded-3xl border border-dashed border-gray-300 bg-white px-5 py-12 text-center dark:border-gray-700 dark:bg-gray-900 sm:px-10 sm:py-16">
                    <div
                        class="mx-auto flex size-16 items-center justify-center rounded-2xl bg-teal-50 text-teal-700 dark:bg-teal-950 dark:text-teal-300">
                        <Inbox class="size-8" />
                    </div>

                    <h3 class="mt-5 text-lg font-bold text-gray-900 dark:text-gray-100">
                        Aucune conversation pour le moment
                    </h3>

                    <p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                        Entrez en contact avec d'autres développeurs pour partager
                        vos idées et collaborer sur des projets.
                    </p>

                    <Link href="/messages/new"
                        class="mt-6 inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-teal-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-teal-700">
                        <MessageSquarePlus class="size-5" />
                        Démarrer une conversation
                    </Link>
                </div>

                <!-- Liste -->
                <div v-else
                    class="overflow-hidden rounded-2xl border border-gray-200/80 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <Link v-for="conversation in conversations.data" :key="conversation.id"
                        :href="`/messages/${conversation.id}`"
                        class="group relative flex items-center gap-3 border-b border-gray-100 p-4 transition-colors last:border-b-0 hover:bg-gray-50 focus-visible:z-10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-teal-500 sm:gap-4 sm:px-5 sm:py-5 dark:border-gray-800 dark:hover:bg-gray-800/70"
                        :class="conversation.unread_count > 0
                            ? 'bg-teal-50/40 dark:bg-teal-950/10'
                            : ''">
                        <!-- Avatar -->
                        <div class="relative shrink-0">
                            <div
                                class="flex size-12 items-center justify-center rounded-2xl bg-linear-to-br from-teal-500 to-cyan-600 text-sm font-bold text-white shadow-sm sm:size-14 sm:text-base">
                                {{ conversationInitials(conversation) }}
                            </div>

                            <span v-if="conversation.unread_count > 0"
                                class="absolute -right-1 -top-1 size-3 rounded-full border-2 border-white bg-teal-500 dark:border-gray-900"
                                aria-label="Messages non lus" />
                        </div>

                        <!-- Contenu -->
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-2 sm:items-center">
                                <h3 class="min-w-0 truncate text-sm font-semibold text-gray-900 group-hover:text-teal-700 dark:text-gray-100 dark:group-hover:text-teal-300 sm:text-base"
                                    :class="conversation.unread_count > 0 ? 'font-bold' : ''">
                                    {{ conversationName(conversation) }}
                                </h3>

                                <time :datetime="conversation.updated_at"
                                    class="shrink-0 pt-0.5 text-[11px] text-gray-400 sm:pt-0 sm:text-xs dark:text-gray-500">
                                    {{ formatDate(conversation.updated_at) }}
                                </time>
                            </div>

                            <p class="mt-1 line-clamp-2 wrap-break-word text-sm leading-5 sm:line-clamp-1" :class="conversation.unread_count > 0
                                ? 'font-medium text-gray-700 dark:text-gray-200'
                                : 'text-gray-500 dark:text-gray-400'">
                                {{ lastMessage(conversation) }}
                            </p>

                            <div v-if="conversation.type === 'group'"
                                class="mt-2 inline-flex items-center gap-1 rounded-md bg-gray-100 px-2 py-1 text-[11px] font-medium text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                                <Users class="size-3" />
                                Groupe
                            </div>
                        </div>

                        <!-- Non lus et navigation -->
                        <div class="flex shrink-0 items-center gap-2">
                            <span v-if="conversation.unread_count > 0"
                                class="flex min-h-6 min-w-6 items-center justify-center rounded-full bg-teal-600 px-2 text-xs font-bold text-white"
                                :aria-label="`${conversation.unread_count} messages non lus`">
                                {{ conversation.unread_count > 99
                                    ? '99+'
                                    : conversation.unread_count }}
                            </span>

                            <ChevronRight
                                class="hidden size-4 text-gray-300 transition group-hover:translate-x-0.5 group-hover:text-teal-600 sm:block dark:text-gray-600" />
                        </div>
                    </Link>
                </div>
            </section>

            <!-- Pagination -->
            <nav v-if="conversations.last_page > 1" class="mt-6 flex flex-wrap items-center justify-center gap-2"
                aria-label="Pagination des conversations">
                <template v-for="(link, index) in conversations.links" :key="index">
                    <Link v-if="link.url" :href="link.url"
                        class="inline-flex min-h-10 min-w-10 items-center justify-center rounded-xl border px-3 py-2 text-sm font-medium transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal-500"
                        :class="link.active
                            ? 'border-teal-600 bg-teal-600 text-white shadow-sm'
                            : 'border-gray-200 bg-white text-gray-600 hover:border-teal-300 hover:bg-teal-50 hover:text-teal-700 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-teal-700 dark:hover:bg-teal-950/40 dark:hover:text-teal-300'"
                        v-html="link.label" />

                    <span v-else
                        class="inline-flex min-h-10 min-w-10 items-center justify-center rounded-xl border border-gray-100 px-3 py-2 text-sm text-gray-300 dark:border-gray-800 dark:text-gray-600"
                        v-html="link.label" />
                </template>
            </nav>

            <p v-if="conversations.last_page > 1" class="mt-3 text-center text-xs text-gray-400 dark:text-gray-500">
                Page {{ conversations.current_page }} sur {{ conversations.last_page }}
            </p>
        </div>
    </main>
</template>