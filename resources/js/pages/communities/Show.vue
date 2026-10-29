<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';

interface CommunityMember {
    id: number;
    name: string;
}

interface Community {
    id: number;
    creator_id: number;
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

    <div class="min-h-screen bg-background text-foreground">
        <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Retour aux communautés -->
            <Link href="/communities"
                class="inline-flex items-center gap-2 text-sm font-medium text-muted-foreground transition-colors hover:text-primary">
                <span aria-hidden="true">←</span>
                Retour aux communautés
            </Link>

            <!-- Informations de la communauté -->
            <section class="mt-6 overflow-hidden rounded-2xl border bg-card shadow-sm">
                <!-- Bannière -->
                <div
                    class="relative h-48 overflow-hidden bg-linear-to-br from-primary/20 via-primary/10 to-muted sm:h-64">
                    <img v-if="community.cover_path" :src="`/storage/${community.cover_path}`"
                        :alt="`Bannière de ${community.name}`" class="h-full w-full object-cover" />

                    <div v-else class="absolute inset-0 flex items-center justify-center" aria-hidden="true">
                        <span class="text-7xl font-bold text-primary/20 sm:text-8xl">
                            {{ initial(community.name) }}
                        </span>
                    </div>

                    <div
                        class="pointer-events-none absolute inset-0 bg-linear-to-t from-background/20 to-transparent" />
                </div>

                <div class="p-6 sm:p-8">
                    <!-- Identité et statistiques -->
                    <div class="flex flex-col gap-5 sm:flex-row sm:items-start">
                        <!-- Avatar -->
                        <div
                            class="-mt-16 flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-2xl border-4 border-card bg-primary/10 text-3xl font-bold text-primary shadow-sm">
                            <img v-if="community.avatar_path" :src="`/storage/${community.avatar_path}`"
                                :alt="`Avatar de ${community.name}`" class="h-full w-full object-cover" />

                            <span v-else>
                                {{ initial(community.name) }}
                            </span>
                        </div>

                        <!-- Nom et créateur -->
                        <div class="min-w-0 flex-1">
                            <h1 class="wrap-break-word text-3xl font-bold tracking-tight sm:text-4xl">
                                {{ community.name }}
                            </h1>

                            <p class="mt-3 flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-muted-foreground">
                                <span>Créée par</span>

                                <span class="font-medium text-foreground">
                                    {{ community.creator.name }}
                                </span>

                                <span aria-hidden="true">·</span>

                                <span>
                                    {{ formatDate(community.created_at) }}
                                </span>
                            </p>
                        </div>

                        <!-- Nombre de membres -->
                        <div
                            class="flex shrink-0 items-center gap-3 rounded-xl border bg-muted/50 px-4 py-3 sm:flex-col sm:gap-1 sm:text-center">
                            <p class="text-2xl font-bold text-primary">
                                {{ community.members_count }}
                            </p>

                            <p class="text-sm text-muted-foreground">
                                membre{{ community.members_count > 1 ? 's' : '' }}
                            </p>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mt-8 border-t pt-6">
                        <h2 class="text-lg font-semibold">
                            À propos de cette communauté
                        </h2>

                        <p class="mt-3 whitespace-pre-line leading-7 text-muted-foreground">
                            {{
                                community.description ||
                                'Cette communauté n’a pas encore de description.'
                            }}
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="mt-6 flex flex-wrap items-center gap-3">
                        <button v-if="!isMember" type="button" :disabled="joinForm.processing"
                            class="inline-flex items-center justify-center rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-primary-foreground transition-opacity hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-50"
                            @click="joinCommunity">
                            {{
                                joinForm.processing
                                    ? 'Inscription en cours...'
                                    : 'Rejoindre la communauté'
                            }}
                        </button>

                        <button v-else type="button" :disabled="leaveForm.processing ||
                            community.creator_id === $page.props.auth.user?.id
                            "
                            class="inline-flex items-center justify-center rounded-lg border bg-background px-5 py-2.5 text-sm font-semibold transition-colors hover:bg-muted disabled:cursor-not-allowed disabled:opacity-50"
                            @click="leaveCommunity">
                            {{
                                community.creator_id === $page.props.auth.user?.id
                                    ? 'Créateur de la communauté'
                                    : leaveForm.processing
                                        ? 'Départ en cours...'
                                        : 'Quitter la communauté'
                            }}
                        </button>
                        <Link v-if="community.creator_id === $page.props.auth.user?.id"
                            :href="`/communities/${community.slug}/edit`"
                            class="inline-flex items-center justify-center gap-2 rounded-lg border bg-background px-5 py-2.5 text-sm font-semibold transition-colors hover:bg-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="1.8" class="h-4 w-4" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m16 4 4 4M4 20l4-.8L19 8a2.12 2.12 0 0 0-3-3L5 16l-1 4Z" />
                            </svg>
                            Modifier la communauté
                        </Link>
                    </div>
                </div>
            </section>

            <!-- Liste des membres -->
            <section class="mt-8 rounded-2xl border bg-card p-6 shadow-sm sm:p-8">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold">
                            Membres
                        </h2>

                        <p class="mt-1 text-sm text-muted-foreground">
                            Les développeurs de cette communauté.
                        </p>
                    </div>

                    <span
                        class="inline-flex min-w-10 items-center justify-center rounded-full bg-primary/10 px-3 py-1 text-sm font-semibold text-primary">
                        {{ community.members_count }}
                    </span>
                </div>

                <!-- Membres présents -->
                <div v-if="community.members.length" class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    <Link v-for="member in community.members" :key="member.id" :href="`/developers/${member.id}`"
                        class="group flex min-w-0 items-center gap-3 rounded-xl border p-4 transition-colors hover:border-primary/30 hover:bg-muted/50">
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary/10 font-semibold text-primary transition-colors group-hover:bg-primary group-hover:text-primary-foreground">
                            {{ initial(member.name) }}
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="truncate font-medium transition-colors group-hover:text-primary">
                                {{ member.name }}
                            </p>

                            <p class="mt-1 text-xs text-muted-foreground">
                                Voir le profil
                            </p>
                        </div>

                        <span class="text-muted-foreground transition-colors group-hover:text-primary"
                            aria-hidden="true">
                            →
                        </span>
                    </Link>
                </div>

                <!-- Aucun membre -->
                <div v-else class="mt-6 rounded-xl border border-dashed p-8 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-primary/10 text-primary"
                        aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.7" class="h-6 w-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="10" cy="7" r="4" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M20 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                    </div>

                    <p class="mt-4 font-medium">
                        Aucun membre pour le moment
                    </p>

                    <p class="mt-1 text-sm text-muted-foreground">
                        Rejoignez cette communauté pour commencer à échanger avec d'autres développeurs.
                    </p>
                </div>
            </section>
        </div>
    </div>
</template>