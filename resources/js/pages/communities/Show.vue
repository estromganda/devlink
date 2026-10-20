<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';

interface CommunityMember {
    id: number;
    name: string;
}

interface Community {
    id: number;
    creator_id: number,
    name: string;
    slug: string;
    description: string | null;
    avatar_path: string | null;
    cover_path: string | null;
    created_at: string;
    creator: {
        id: number;
        name: string;
    };
    members_count: number;
    members: CommunityMember[];
}

const props = defineProps<{
    community: Community;
    isMember: boolean;
}>();

const joinForm = useForm({});
const leaveForm = useForm({});

function joinCommunity() {
    joinForm.post(`/communities/${props.community.slug}/join`, {
        preserveScroll: true,
    });
}

function leaveCommunity() {
    leaveForm.delete(`/communities/${props.community.slug}/leave`, {
        preserveScroll: true,
    });
}

function formatDate(date: string): string {
    return new Intl.DateTimeFormat('fr-FR', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    }).format(new Date(date));
}

function initial(name: string): string {
    return name.trim().charAt(0).toUpperCase() || '?';
}
</script>

<template>

    <Head :title="community.name" />

    <div class="min-h-screen bg-gray-50 text-gray-900 dark:bg-gray-950 dark:text-white">
        <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
            <Link href="/communities" class="text-sm font-medium text-indigo-600 hover:underline dark:text-indigo-400">
                ← Retour aux communautés
            </Link>

            <!-- Bannière -->
            <section
                class="mt-6 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="relative h-48 bg-linear-to-br from-indigo-500 via-violet-500 to-purple-600 sm:h-64">
                    <img v-if="community.cover_path" :src="`/storage/${community.cover_path}`"
                        :alt="`Bannière de ${community.name}`" class="h-full w-full object-cover" />
                </div>

                <div class="p-6 sm:p-8">
                    <div class="flex flex-col gap-5 sm:flex-row sm:items-start">
                        <div
                            class="-mt-16 flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-2xl border-4 border-white bg-indigo-100 text-3xl font-bold text-indigo-700 dark:border-gray-900 dark:bg-indigo-950 dark:text-indigo-300">
                            <img v-if="community.avatar_path" :src="`/storage/${community.avatar_path}`"
                                :alt="`Avatar de ${community.name}`" class="h-full w-full object-cover" />

                            <span v-else>
                                {{ initial(community.name) }}
                            </span>
                        </div>

                        <div class="min-w-0 flex-1">
                            <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">
                                {{ community.name }}
                            </h1>

                            <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">
                                Créée par
                                <span class="font-medium text-gray-900 dark:text-white">
                                    {{ community.creator.name }}
                                </span>
                                <span class="mx-2">·</span>
                                {{ formatDate(community.created_at) }}
                            </p>
                        </div>

                        <div class="rounded-xl bg-indigo-50 px-4 py-3 text-center dark:bg-indigo-950">
                            <p class="text-2xl font-bold text-indigo-700 dark:text-indigo-300">
                                {{ community.members_count }}
                            </p>
                            <p class="text-sm text-indigo-700 dark:text-indigo-300">
                                membre{{ community.members_count > 1 ? 's' : '' }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-8 border-t border-gray-100 pt-6 dark:border-gray-800">
                        <h2 class="text-lg font-semibold">
                            À propos de cette communauté
                        </h2>

                        <p class="mt-3 whitespace-pre-line leading-7 text-gray-600 dark:text-gray-300">
                            {{
                                community.description ||
                                'Cette communauté n’a pas encore de description.'
                            }}
                        </p>
                    </div>
                    <!-- Nombre des membres -->
                    <span class="text-sm text-muted-foreground">
                        {{ community.members_count }}
                        {{ community.members_count > 1 ? 'membres' : 'membre' }}
                    </span>
                    <!-- Joindre ou quitter -->
                    <div class="mt-6 flex flex-wrap items-center gap-3">
                        <button v-if="!isMember" type="button" :disabled="joinForm.processing"
                            class="inline-flex items-center justify-center rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-primary-foreground transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-50"
                            @click="joinCommunity">
                            {{ joinForm.processing ? 'Inscription en cours...' : 'Rejoindre la communauté' }}
                        </button>

                        <button v-else type="button"
                            :disabled="leaveForm.processing || community.creator_id === $page.props.auth.user?.id"
                            class="inline-flex items-center justify-center rounded-lg border px-5 py-2.5 text-sm font-semibold transition hover:bg-muted disabled:cursor-not-allowed disabled:opacity-50"
                            @click="leaveCommunity">
                            {{
                                community.creator_id === $page.props.auth.user?.id
                                    ? 'Créateur de la communauté'
                                    : leaveForm.processing
                                        ? 'Départ en cours...'
                                        : 'Quitter la communauté'
                            }}
                        </button>
                    </div>
                </div>
            </section>

            <!-- Membres -->
            <section
                class="mt-8 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900 sm:p-8">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold">
                            Membres
                        </h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Les développeurs de cette communauté.
                        </p>
                    </div>

                    <span class="rounded-full bg-gray-100 px-3 py-1 text-sm font-medium dark:bg-gray-800">
                        {{ community.members_count }}
                    </span>
                </div>

                <div v-if="community.members.length" class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    <div v-for="member in community.members" :key="member.id"
                        class="flex items-center gap-3 rounded-xl border border-gray-100 p-4 dark:border-gray-800">
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-100 font-semibold text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">
                            {{ initial(member.name) }}
                        </div>

                        <p class="truncate font-medium">
                            {{ member.name }}
                        </p>
                    </div>
                </div>

                <p v-else
                    class="mt-6 rounded-xl bg-gray-50 p-6 text-center text-sm text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                    Cette communauté n'a pas encore de membres.
                </p>
            </section>
        </div>
    </div>
</template>