<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { reactive } from 'vue';
import { Link } from '@inertiajs/vue3';

interface DeveloperTechnology {
    id: number;
    name: string;
    is_primary: boolean;
}

interface Developer {
    id: number;
    name: string;
    headline: string | null;
    location: string | null;
    technologies_count: number;
    technologies: DeveloperTechnology[];
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedDevelopers {
    data: Developer[];
    current_page: number;
    last_page: number;
    links: PaginationLink[];
    total: number;
}

interface TechnologyOption {
    id: number;
    name: string;
}

interface Filters {
    search: string;
    technology: number | null;
}

const props = defineProps<{
    developers: PaginatedDevelopers;
    filters: Filters;
    technologies: TechnologyOption[];
}>();

const form = reactive({
    search: props.filters.search ?? '',
    technology: props.filters.technology
        ? String(props.filters.technology)
        : '',
});

function searchDevelopers() {
    router.get(
        '/developers',
        {
            search: form.search.trim() || undefined,
            technology: form.technology || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
}

function resetFilters() {
    form.search = '';
    form.technology = '';

    router.get(
        '/developers',
        {},
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
}
</script>

<template>
    <div class="mx-auto max-w-7xl px-6 py-12">
        <!-- En-tête -->
        <section class="mb-10">
            <p class="mb-3 text-sm font-semibold uppercase tracking-widest text-primary">
                Communauté DevLink
            </p>

            <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">
                Découvrez les développeurs
            </h1>

            <form @submit.prevent="searchDevelopers"
                class="mb-8 rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-[1fr_1fr_auto]">
                    <!-- Recherche par nom -->
                    <div>
                        <label for="developer-search"
                            class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-200">
                            Nom du développeur
                        </label>

                        <input id="developer-search" v-model="form.search" type="search"
                            placeholder="Ex. : Alexandre, Sophie..."
                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:border-gray-600 dark:bg-gray-800 dark:text-white" />
                    </div>

                    <!-- Filtre par technologie -->
                    <div>
                        <label for="developer-technology"
                            class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-200">
                            Technologie
                        </label>

                        <select id="developer-technology" v-model="form.technology"
                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                            <option value="">Toutes les technologies</option>

                            <option v-for="technology in technologies" :key="technology.id"
                                :value="String(technology.id)">
                                {{ technology.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Boutons -->
                    <div class="flex items-end gap-2">
                        <button type="submit"
                            class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-medium text-white transition hover:bg-indigo-700">
                            Rechercher
                        </button>

                        <button type="button" @click="resetFilters"
                            class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-800">
                            Réinitialiser
                        </button>
                    </div>
                </div>
            </form>

            <p class="mt-4 max-w-2xl text-base text-muted-foreground">
                Explorez les profils des développeurs, découvrez leurs
                spécialités et trouvez des personnes avec lesquelles
                partager vos projets.
            </p>

            <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">
                {{ developers.total }}
                développeur{{ developers.total > 1 ? 's' : '' }}
                trouvé{{ developers.total > 1 ? 's' : '' }}
            </p>
        </section>

        <!-- Liste des développeurs -->
        <section v-if="developers.data.length > 0" aria-label="Liste des développeurs">
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <article v-for="developer in developers.data" :key="developer.id"
                    class="flex flex-col rounded-xl border bg-card p-6 shadow-sm transition hover:shadow-md">
                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-primary/10 text-lg font-bold text-primary"
                        aria-hidden="true">
                        {{ developer.name.charAt(0).toUpperCase() }}
                    </div>

                    <h2 class="text-lg font-semibold">
                        {{ developer.name }}
                    </h2>

                    <p class="mt-2 text-sm text-muted-foreground">
                        {{ developer.headline || 'Développeur' }}
                    </p>

                    <p v-if="developer.location" class="mt-3 text-sm text-muted-foreground">
                        📍 {{ developer.location }}
                    </p>

                    <!-- Technologies -->
                    <div v-if="developer.technologies.length > 0" class="mt-4">
                        <p class="mb-2 text-xs font-medium text-muted-foreground">
                            Technologies
                        </p>

                        <div class="flex flex-wrap gap-2">
                            <span v-for="technology in developer.technologies" :key="technology.id"
                                :title="technology.is_primary ? 'Technologie principale' : technology.name"
                                class="inline-flex items-center gap-1 rounded-full border bg-muted/40 px-3 py-1 text-xs font-medium">
                                <span v-if="technology.is_primary" aria-hidden="true">★</span>
                                {{ technology.name }}
                            </span>

                            <span v-if="developer.technologies_count > developer.technologies.length"
                                class="inline-flex items-center rounded-full border px-3 py-1 text-xs text-muted-foreground">
                                +{{ developer.technologies_count - developer.technologies.length }}
                            </span>
                        </div>
                    </div>

                    <p v-else class="mt-4 text-xs text-muted-foreground">
                        Aucune compétence renseignée
                    </p>
                    <div class="mt-6 grow"></div>

                    <Link :href="`/developers/${developer.id}`"
                        class="mt-5 inline-flex items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground transition hover:opacity-90">
                        Voir le profil
                    </Link>
                </article>
            </div>

            <!-- Pagination -->
            <nav v-if="developers.last_page > 1" class="mt-10 flex flex-wrap items-center justify-center gap-2"
                aria-label="Pagination des développeurs">
                <template v-for="(link, index) in developers.links" :key="index">
                    <Link v-if="link.url" :href="link.url" preserve-scroll
                        :aria-current="link.active ? 'page' : undefined" :class="[
                            'rounded-md border px-3 py-2 text-sm transition',
                            link.active
                                ? 'border-primary bg-primary text-primary-foreground'
                                : 'bg-background hover:bg-muted',
                        ]" v-html="link.label" />

                    <span v-else
                        class="cursor-not-allowed rounded-md border px-3 py-2 text-sm text-muted-foreground opacity-50"
                        v-html="link.label" />
                </template>
            </nav>
        </section>

        <!-- Aucun résultat -->
        <section v-else class="rounded-xl border border-dashed px-6 py-16 text-center">
            <h2 class="text-xl font-semibold">
                Aucun développeur pour le moment
            </h2>

            <p class="mx-auto mt-3 max-w-md text-sm text-muted-foreground">
                Les profils des développeurs apparaîtront ici dès leur création.
                Revenez bientôt pour découvrir la communauté DevLink.
            </p>
        </section>
    </div>
</template>