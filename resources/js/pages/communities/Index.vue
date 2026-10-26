<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowRight,
    Users,
    Plus,
    Compass,
    MessageCircle,
    Code2,
    Sparkles,
    UserRound,
    ChevronLeft,
    ChevronRight,
    Globe,
} from '@lucide/vue';

interface CommunityCreator {
    id: number;
    name: string;
}

interface Community {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    avatar_path: string | null;
    cover_path: string | null;
    created_at: string;
    creator: CommunityCreator;
    members_count: number;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedCommunities {
    data: Community[];
    current_page: number;
    last_page: number;
    links: PaginationLink[];
    total: number;
}

defineProps<{
    communities: PaginatedCommunities;
}>();

function communityInitial(name: string): string {
    return name.trim().charAt(0).toUpperCase() || '?';
}

function creatorInitial(name: string): string {
    return name.trim().charAt(0).toUpperCase() || '?';
}

function paginationLabel(label: string): string {
    const normalized = label.toLowerCase();

    if (
        normalized.includes('previous') ||
        normalized.includes('précédent')
    ) {
        return 'Précédent';
    }

    if (
        normalized.includes('next') ||
        normalized.includes('suivant')
    ) {
        return 'Suivant';
    }

    return label;
}
</script>

<template>

    <Head title="Communautés" />

    <main class="min-h-screen bg-gray-50/70 py-6 dark:bg-gray-950 sm:py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <!-- En-tête -->
            <header
                class="relative mb-8 overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div
                    class="pointer-events-none absolute -right-16 -top-24 size-72 rounded-full bg-teal-500/10 blur-3xl" />
                <div
                    class="pointer-events-none absolute -bottom-28 right-1/3 size-64 rounded-full bg-cyan-500/10 blur-3xl" />

                <div
                    class="relative flex flex-col gap-6 p-5 sm:p-8 lg:flex-row lg:items-center lg:justify-between lg:p-10">
                    <div class="min-w-0 flex-1">
                        <div
                            class="mb-4 inline-flex items-center gap-2 rounded-full border border-teal-100 bg-teal-50 px-3 py-1.5 text-xs font-semibold uppercase tracking-wider text-teal-700 dark:border-teal-900 dark:bg-teal-950/60 dark:text-teal-300">
                            <Sparkles class="size-3.5" />
                            DevLink · Communauté
                        </div>

                        <h1
                            class="max-w-2xl text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl lg:text-5xl">
                            Trouve ta communauté.
                            <span class="text-teal-600 dark:text-teal-400">
                                Construis ensemble.
                            </span>
                        </h1>

                        <p class="mt-4 max-w-2xl text-sm leading-7 text-gray-600 dark:text-gray-400 sm:text-base">
                            Rencontre des développeurs qui partagent tes technologies,
                            échange des connaissances et donne vie à tes projets avec
                            la communauté DevLink.
                        </p>

                        <div
                            class="mt-6 flex flex-wrap items-center gap-x-5 gap-y-3 text-sm text-gray-500 dark:text-gray-400">
                            <div class="inline-flex items-center gap-2">
                                <Users class="size-4 text-teal-600 dark:text-teal-400" />
                                <span>
                                    <strong class="font-semibold text-gray-900 dark:text-white">
                                        {{ communities.total }}
                                    </strong>
                                    {{ communities.total > 1 ? 'communautés' : 'communauté' }}
                                </span>
                            </div>

                            <div class="inline-flex items-center gap-2">
                                <MessageCircle class="size-4 text-teal-600 dark:text-teal-400" />
                                <span>Partage et entraide</span>
                            </div>

                            <div class="inline-flex items-center gap-2">
                                <Code2 class="size-4 text-teal-600 dark:text-teal-400" />
                                <span>Pour les développeurs</span>
                            </div>
                        </div>
                    </div>

                    <div class="shrink-0">
                        <Link href="/communities/create"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-teal-600 px-5 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-teal-700 focus:outline-none focus:ring-4 focus:ring-teal-500/20 sm:w-auto">
                            <Plus class="size-5" />
                            Créer une communauté
                        </Link>

                        <p class="mt-2 text-center text-xs text-gray-400 dark:text-gray-500">
                            Rassemble des développeurs autour d'une idée.
                        </p>
                    </div>
                </div>
            </header>

            <!-- Titre de section -->
            <section aria-label="Liste des communautés">
                <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <h2 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-2xl">
                            Explorer les communautés
                        </h2>

                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Découvre les espaces de discussion de DevLink.
                        </p>
                    </div>

                    <div v-if="communities.total > 0"
                        class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-3.5 py-2 text-sm text-gray-600 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                        <Globe class="size-4 text-teal-600 dark:text-teal-400" />
                        {{ communities.total }}
                        {{ communities.total > 1 ? 'résultats' : 'résultat' }}
                    </div>
                </div>

                <!-- Liste des communautés -->
                <div v-if="communities.data.length > 0" class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3">
                    <article v-for="community in communities.data" :key="community.id"
                        class="group flex min-w-0 flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition duration-200 hover:-translate-y-1 hover:border-teal-200 hover:shadow-lg dark:border-gray-800 dark:bg-gray-900 dark:hover:border-teal-900">
                        <!-- Bannière -->
                        <Link :href="`/communities/${community.slug}`"
                            class="relative block h-36 overflow-hidden bg-linear-to-br from-teal-600 via-cyan-600 to-blue-700"
                            :aria-label="`Découvrir ${community.name}`">
                            <img v-if="community.cover_path" :src="`/storage/${community.cover_path}`"
                                :alt="`Bannière de ${community.name}`"
                                class="size-full object-cover transition duration-500 group-hover:scale-105" />

                            <div v-else class="absolute inset-0 flex items-center justify-center">
                                <div class="absolute -left-8 -top-12 size-40 rounded-full border border-white/10" />
                                <div class="absolute -bottom-20 right-5 size-52 rounded-full border border-white/10" />

                                <span
                                    class="relative text-6xl font-bold text-white/30 transition duration-300 group-hover:scale-110">
                                    {{ communityInitial(community.name) }}
                                </span>
                            </div>

                            <div class="absolute inset-0 bg-linear-to-t from-black/20 to-transparent" />

                            <div
                                class="absolute right-3 top-3 inline-flex items-center gap-1.5 rounded-full border border-white/20 bg-black/25 px-2.5 py-1 text-xs font-medium text-white backdrop-blur-sm">
                                <Users class="size-3.5" />
                                {{ community.members_count }}
                            </div>

                            <!-- Avatar -->
                            <div
                                class="absolute -bottom-1 left-5 flex size-17 items-center justify-center overflow-hidden rounded-2xl border-4 border-white bg-teal-50 text-xl font-bold text-teal-700 shadow-sm dark:border-gray-900 dark:bg-teal-950 dark:text-teal-300">
                                <img v-if="community.avatar_path" :src="`/storage/${community.avatar_path}`"
                                    :alt="`Avatar de ${community.name}`" class="size-full object-cover" />

                                <span v-else>
                                    {{ communityInitial(community.name) }}
                                </span>
                            </div>
                        </Link>

                        <!-- Informations -->
                        <div class="flex flex-1 flex-col p-5 pt-6">
                            <h3 class="truncate text-lg font-bold text-gray-900 dark:text-white"
                                :title="community.name">
                                <Link :href="`/communities/${community.slug}`"
                                    class="transition hover:text-teal-700 dark:hover:text-teal-400">
                                    {{ community.name }}
                                </Link>
                            </h3>

                            <p class="mt-2 line-clamp-3 min-h-18 text-sm leading-6 text-gray-600 dark:text-gray-400">
                                {{
                                    community.description ||
                                    'Cette communauté n’a pas encore de description. Découvre-la et participe aux échanges.'
                                }}
                            </p>

                            <!-- Créateur -->
                            <div
                                class="mt-4 flex min-w-0 items-center gap-2.5 border-t border-gray-100 pt-4 dark:border-gray-800">
                                <div
                                    class="flex size-9 shrink-0 items-center justify-center rounded-full bg-gray-100 text-sm font-semibold text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                                    {{ creatorInitial(community.creator.name) }}
                                </div>

                                <div class="min-w-0 flex-1">
                                    <p class="text-xs text-gray-400 dark:text-gray-500">
                                        Créée par
                                    </p>

                                    <p class="truncate text-sm font-medium text-gray-800 dark:text-gray-200">
                                        {{ community.creator.name }}
                                    </p>
                                </div>

                                <div
                                    class="inline-flex shrink-0 items-center gap-1.5 rounded-lg bg-teal-50 px-2.5 py-1.5 text-xs font-semibold text-teal-700 dark:bg-teal-950 dark:text-teal-300">
                                    <Users class="size-3.5" />
                                    {{ community.members_count }}
                                </div>
                            </div>

                            <!-- Action -->
                            <div class="mt-5">
                                <Link :href="`/communities/${community.slug}`"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gray-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-teal-700 focus:outline-none focus:ring-4 focus:ring-teal-500/20 dark:bg-gray-800 dark:hover:bg-teal-700">
                                    <Compass class="size-4" />
                                    Découvrir la communauté
                                    <ArrowRight class="ml-auto size-4 transition-transform group-hover:translate-x-1" />
                                </Link>
                            </div>
                        </div>
                    </article>
                </div>

                <!-- Pagination -->
                <nav v-if="communities.last_page > 1" class="mt-8 flex flex-wrap items-center justify-center gap-2"
                    aria-label="Pagination des communautés">
                    <template v-for="(link, index) in communities.links" :key="`${index}-${link.label}`">
                        <Link v-if="link.url" :href="link.url" preserve-scroll
                            :aria-current="link.active ? 'page' : undefined"
                            class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl border px-3.5 py-2 text-sm font-medium transition"
                            :class="link.active
                                ? 'border-teal-600 bg-teal-600 text-white shadow-sm'
                                : 'border-gray-200 bg-white text-gray-600 hover:border-teal-300 hover:bg-teal-50 hover:text-teal-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-teal-900 dark:hover:bg-teal-950 dark:hover:text-teal-300'
                                ">
                            <ChevronLeft v-if="paginationLabel(link.label) === 'Précédent'" class="size-4" />

                            <ChevronRight v-if="paginationLabel(link.label) === 'Suivant'" class="size-4" />

                            <span
                                v-if="paginationLabel(link.label) !== 'Précédent' && paginationLabel(link.label) !== 'Suivant'"
                                v-html="link.label" />

                            <span v-else>
                                {{ paginationLabel(link.label) }}
                            </span>
                        </Link>

                        <span v-else
                            class="inline-flex min-h-10 cursor-not-allowed items-center justify-center gap-2 rounded-xl border border-gray-200 px-3.5 py-2 text-sm text-gray-400 dark:border-gray-800 dark:text-gray-600">
                            <ChevronLeft v-if="paginationLabel(link.label) === 'Précédent'" class="size-4" />

                            <ChevronRight v-if="paginationLabel(link.label) === 'Suivant'" class="size-4" />

                            <span
                                v-if="paginationLabel(link.label) !== 'Précédent' && paginationLabel(link.label) !== 'Suivant'"
                                v-html="link.label" />

                            <span v-else>
                                {{ paginationLabel(link.label) }}
                            </span>
                        </span>
                    </template>
                </nav>

                <!-- État vide -->
                <div v-else
                    class="rounded-2xl border border-dashed border-gray-300 bg-white px-5 py-14 text-center dark:border-gray-700 dark:bg-gray-900 sm:px-10 sm:py-20">
                    <div
                        class="mx-auto flex size-16 items-center justify-center rounded-2xl bg-teal-50 text-teal-700 dark:bg-teal-950 dark:text-teal-300">
                        <Users class="size-8" />
                    </div>

                    <h2 class="mt-5 text-xl font-bold text-gray-900 dark:text-white">
                        Aucune communauté pour le moment
                    </h2>

                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-600 dark:text-gray-400">
                        Sois parmi les premiers à créer un espace dédié à une technologie,
                        un centre d'intérêt ou un projet.
                    </p>

                    <Link href="/communities/create"
                        class="mt-6 inline-flex items-center justify-center gap-2 rounded-xl bg-teal-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-teal-700 focus:outline-none focus:ring-4 focus:ring-teal-500/20">
                        <Plus class="size-4" />
                        Créer la première communauté
                    </Link>
                </div>
            </section>

            <!-- Bannière finale -->
            <aside
                class="mt-8 flex flex-col gap-4 rounded-2xl border border-teal-100 bg-teal-50/70 p-5 dark:border-teal-900/60 dark:bg-teal-950/30 sm:flex-row sm:items-center sm:justify-between sm:p-6">
                <div class="flex items-start gap-3">
                    <div
                        class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-white text-teal-700 shadow-sm dark:bg-gray-900 dark:text-teal-300">
                        <Code2 class="size-5" />
                    </div>

                    <div>
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                            Tu ne trouves pas ta communauté ?
                        </h3>

                        <p class="mt-1 text-sm leading-6 text-gray-600 dark:text-gray-400">
                            Crée ton propre espace et rassemble des développeurs autour de tes idées.
                        </p>
                    </div>
                </div>

                <Link href="/communities/create"
                    class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl border border-teal-200 bg-white px-4 py-2.5 text-sm font-semibold text-teal-700 transition hover:bg-teal-50 dark:border-teal-900 dark:bg-gray-900 dark:text-teal-300 dark:hover:bg-teal-950">
                    Créer une communauté
                    <ArrowRight class="size-4" />
                </Link>
            </aside>
        </div>
    </main>
</template>