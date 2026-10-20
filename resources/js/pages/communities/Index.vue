<script setup lang="ts">
import { Link, Head } from '@inertiajs/vue3';

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
</script>

<template>

    <Head title="Communautés" />

    <div class="min-h-screen bg-gray-50 dark:bg-gray-950">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <!-- En-tête -->
            <header class="mb-8">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p
                            class="mb-2 text-sm font-semibold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">
                            DevLink · Communauté
                        </p>

                        <div class="flex space-x-2 justify-between w-full flex-col sm:flex-row">
                            <h1
                                class="flex-1 text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                                Découvrez les communautés ou
                            </h1>
                            <Link href="/communities/create"
                                class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700">
                                + Créer une communauté
                            </Link>
                        </div>
                        <p class="mt-3 max-w-2xl text-gray-600 dark:text-gray-400">
                            Rejoignez des développeurs qui partagent vos
                            technologies, vos intérêts et vos projets.
                        </p>
                    </div>

                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ communities.total }}
                        communauté{{ communities.total > 1 ? 's' : '' }}
                    </p>
                </div>
            </header>

            <!-- Liste des communautés -->
            <section v-if="communities.data.length > 0" aria-label="Liste des communautés">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <article v-for="community in communities.data" :key="community.id"
                        class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-md dark:border-gray-800 dark:bg-gray-900">
                        <!-- Bannière -->
                        <div
                            class="relative h-32 overflow-hidden bg-linear-to-br from-indigo-500 via-violet-500 to-purple-600">
                            <img v-if="community.cover_path" :src="`/storage/${community.cover_path}`"
                                :alt="`Bannière de ${community.name}`" class="h-full w-full object-cover" />

                            <div v-else class="flex h-full items-center justify-center">
                                <span class="text-5xl font-bold text-white/30" aria-hidden="true">
                                    {{ communityInitial(community.name) }}
                                </span>
                            </div>

                            <!-- Avatar -->
                            <div
                                class="absolute -bottom-1 left-5 flex h-16 w-16 items-center justify-center overflow-hidden rounded-2xl border-4 border-white bg-indigo-100 text-xl font-bold text-indigo-700 dark:border-gray-900 dark:bg-indigo-950 dark:text-indigo-300">
                                <img v-if="community.avatar_path" :src="`/storage/${community.avatar_path}`"
                                    :alt="`Avatar de ${community.name}`" class="h-full w-full object-cover" />

                                <span v-else>
                                    {{ communityInitial(community.name) }}
                                </span>
                            </div>
                        </div>

                        <!-- Informations -->
                        <div class="p-5 pt-7">
                            <h2 class="truncate text-lg font-semibold text-gray-900 dark:text-white"
                                :title="community.name">
                                {{ community.name }}
                            </h2>

                            <p class="mt-2 min-h-12 text-sm leading-6 text-gray-600 dark:text-gray-400">
                                {{
                                    community.description ||
                                    'Cette communauté n’a pas encore de description.'
                                }}
                            </p>

                            <!-- Créateur et membres -->
                            <div
                                class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 pt-4 text-sm dark:border-gray-800">
                                <div class="min-w-0">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Créée par
                                    </p>

                                    <p class="truncate font-medium text-gray-800 dark:text-gray-200">
                                        {{ community.creator.name }}
                                    </p>
                                </div>

                                <div
                                    class="shrink-0 rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">
                                    {{ community.members_count }}
                                    membre{{ community.members_count > 1 ? 's' : '' }}
                                </div>
                            </div>

                            <!-- Action temporaire -->
                            <div class="mt-5">
                                <Link :href="`/communities/${community.slug}`"
                                    class="block w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-center text-sm font-semibold text-white transition hover:bg-indigo-700">
                                    Découvrir la communauté
                                </Link>
                            </div>
                        </div>
                    </article>
                </div>

                <!-- Pagination -->
                <nav v-if="communities.last_page > 1" class="mt-10 flex flex-wrap items-center justify-center gap-2"
                    aria-label="Pagination des communautés">
                    <template v-for="(link, index) in communities.links" :key="`${index}-${link.label}`">
                        <Link v-if="link.url" :href="link.url" preserve-scroll
                            class="rounded-lg border px-3 py-2 text-sm transition" :class="link.active
                                ? 'border-indigo-600 bg-indigo-600 text-white'
                                : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800'
                                ">
                            <span v-if="link.label.includes('Previous') || link.label.includes('précédent')">
                                Précédent
                            </span>

                            <span v-else-if="link.label.includes('Next') || link.label.includes('suivant')">
                                Suivant
                            </span>

                            <span v-else v-html="link.label" />
                        </Link>

                        <span v-else
                            class="cursor-not-allowed rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-400 dark:border-gray-800"
                            v-html="link.label.includes('Previous') ||
                                link.label.includes('précédent')
                                ? 'Précédent'
                                : link.label.includes('Next') ||
                                    link.label.includes('suivant')
                                    ? 'Suivant'
                                    : link.label
                                " />
                    </template>
                </nav>
            </section>

            <!-- État vide -->
            <section v-else
                class="rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-16 text-center dark:border-gray-700 dark:bg-gray-900">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50 text-3xl dark:bg-indigo-950"
                    aria-hidden="true">
                    👥
                </div>

                <h2 class="mt-5 text-xl font-semibold text-gray-900 dark:text-white">
                    Aucune communauté pour le moment
                </h2>

                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-600 dark:text-gray-400">
                    Les communautés publiques apparaîtront ici dès qu'elles
                    auront été créées.
                </p>
            </section>
        </div>
    </div>
</template>