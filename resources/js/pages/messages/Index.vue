<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
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

function conversationName(conversation: Conversation): string {
    if (conversation.title) {
        return conversation.title;
    }

    const names = conversation.members.map(
        (member) => member.user?.name,
    ).filter(Boolean);

    return names.join(', ') || 'Conversation';
}

function lastMessage(conversation: Conversation): string {
    const message = conversation.messages?.[0];

    if (!message) {
        return 'Aucun message pour le moment';
    }

    return message.body || 'Pièce jointe';
}

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: '2-digit',
    });
}
</script>

<template>

    <Head title="Messagerie" />

    <main class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">
                    Messagerie
                </h1>

                <p class="mt-2 text-gray-600">
                    Échange avec les développeurs de la communauté.
                </p>
            </div>

            <Link href="/messages/new"
                class="inline-flex items-center justify-center rounded-xl bg-teal-600 px-5 py-3 font-semibold text-white transition hover:bg-teal-700">
                Nouvelle conversation
            </Link>
        </div>

        <div v-if="!hasConversations"
            class="mt-8 rounded-2xl border border-dashed border-gray-300 bg-white p-10 text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-teal-50 text-2xl">
                ✉
            </div>

            <h2 class="mt-4 text-lg font-semibold text-gray-900">
                Aucune conversation
            </h2>

            <p class="mt-2 text-gray-600">
                Démarre une conversation avec un développeur.
            </p>

            <Link href="/messages/new"
                class="mt-5 inline-flex rounded-xl bg-teal-600 px-5 py-3 font-semibold text-white hover:bg-teal-700">
                Trouver un développeur
            </Link>
        </div>

        <div v-else class="mt-8 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <Link v-for="conversation in conversations.data" :key="conversation.id"
                :href="`/messages/${conversation.id}`"
                class="flex items-center gap-4 border-b border-gray-100 p-5 transition last:border-b-0 hover:bg-gray-50">
                <div
                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-teal-100 font-bold text-teal-800">
                    {{ conversationName(conversation).charAt(0).toUpperCase() }}
                </div>

                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="truncate font-semibold text-gray-900">
                            {{ conversationName(conversation) }}
                        </h2>

                        <span class="shrink-0 text-xs text-gray-400">
                            {{ formatDate(conversation.updated_at) }}
                        </span>
                    </div>

                    <p class="mt-1 truncate text-sm text-gray-500">
                        {{ lastMessage(conversation) }}
                    </p>
                </div>

                <span v-if="conversation.unread_count > 0"
                    class="flex h-6 min-w-6 shrink-0 items-center justify-center rounded-full bg-teal-600 px-2 text-xs font-bold text-white">
                    {{ conversation.unread_count }}
                </span>
            </Link>
        </div>

        <nav v-if="conversations.last_page > 1" class="mt-6 flex flex-wrap justify-center gap-2"
            aria-label="Pagination">
            <template v-for="(link, index) in conversations.links" :key="index">
                <Link v-if="link.url" :href="link.url" class="rounded-lg border px-3 py-2 text-sm" :class="link.active
                        ? 'border-teal-600 bg-teal-600 text-white'
                        : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50'
                    " v-html="link.label" />

                <span v-else class="rounded-lg border border-gray-100 px-3 py-2 text-sm text-gray-300"
                    v-html="link.label" />
            </template>
        </nav>
    </main>
</template>
