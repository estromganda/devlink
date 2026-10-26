<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    ArrowRight,
    Search,
    X,
    Users,
    Code2,
    MapPin,
    Sparkles,
    SlidersHorizontal,
    RotateCcw,
    UserRound,
    ChevronLeft,
    ChevronRight,
    MessageCircle,
    Star,
    Terminal,
} from '@lucide/vue';
import { reactive } from 'vue';

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

function developerInitial(name: string): string {
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

    <Head title="Développeurs" />

    <main class="min-h-screen bg-gray-50/70 py-6 dark:bg-gray-950 sm:py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <!-- En-tête -->
            <header
                class="relative mb-8 overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div
                    class="pointer-events-none absolute -right-16 -top-24 size-72 rounded-full bg-teal-500/10 blur-3xl" />
                <div
                    class="pointer-events-none absolute -bottom-28 right-1/3 size-64 rounded-full bg-cyan-500/10 blur-3xl" />

                <div class="relative p-5 sm:p-8 lg:p-10">
                    <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                        <div class="min-w-0 flex-1">
                            <div
                                class="mb-4 inline-flex items-center gap-2 rounded-full border border-teal-100 bg-teal-50 px-3 py-1.5 text-xs font-semibold uppercase tracking-wider text-teal-700 dark:border-teal-900 dark:bg-teal-950/60 dark:text-teal-300">
                                <Sparkles class="size-3.5" />
                                DevLink · Réseau de développeurs
                            </div>

                            <h1
                                class="max-w-3xl text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl lg:text-5xl">
                                Rencontrez les développeurs.
                                <span class="text-teal-600 dark:text-teal-400">
                                    Construisez ensemble.
                                </span>
                            </h1>

                            <p class="mt-4 max-w-2xl text-sm leading-7 text-gray-600 dark:text-gray-400 sm:text-base">
                                Découvrez les profils, explorez les compétences techniques
                                et trouvez des développeurs avec lesquels partager vos
                                idées et réaliser vos projets.
                            </p>
                        </div>

                        <div class="grid shrink-0 grid-cols-2 gap-3 sm:min-w-64">
                            <div
                                class="rounded-2xl border border-gray-100 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-800/60">
                                <div class="flex items-center gap-2 text-teal-600 dark:text-teal-400">
                                    <Users class="size-5" />
                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">
                                        Membres
                                    </span>
                                </div>

                                <p class="mt-3 text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ developers.total }}
                                </p>

                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    {{ developers.total > 1 ? 'Développeurs trouvés' : 'Développeur trouvé' }}
                                </p>
                            </div>

                            <div
                                class="rounded-2xl border border-gray-100 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-800/60">
                                <div class="flex items-center gap-2 text-teal-600 dark:text-teal-400">
                                    <Code2 class="size-5" />
                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">
                                        Technologies
                                    </span>
                                </div>

                                <p class="mt-3 text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ technologies.length }}
                                </p>

                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    À explorer
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Recherche et filtres -->
            <section
                class="mb-8 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center gap-3 border-b border-gray-100 px-5 py-4 dark:border-gray-800 sm:px-6">
                    <div
                        class="flex size-10 items-center justify-center rounded-xl bg-teal-50 text-teal-700 dark:bg-teal-950 dark:text-teal-300">
                        <SlidersHorizontal class="size-5" />
                    </div>

                    <div>
                        <h2 class="font-semibold text-gray-900 dark:text-white">
                            Trouver un développeur
                        </h2>

                        <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                            Affine les résultats selon tes besoins.
                        </p>
                    </div>
                </div>

                <form class="p-5 sm:p-6" @submit.prevent="searchDevelopers">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-[minmax(0,1fr)_minmax(0,1fr)]">
                        <!-- Recherche par nom -->
                        <div>
                            <label for="developer-search"
                                class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-200">
                                Nom du développeur
                            </label>

                            <div class="relative">
                                <Search
                                    class="pointer-events-none absolute left-4 top-1/2 size-4 -translate-y-1/2 text-gray-400" />

                                <input id="developer-search" v-model="form.search" type="search" autocomplete="off"
                                    placeholder="Ex. : Alexandre, Sophie..."
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 py-3 pl-11 pr-10 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-teal-500 dark:focus:bg-gray-800" />

                                <button v-if="form.search" type="button" aria-label="Effacer le nom"
                                    class="absolute right-3 top-1/2 flex size-7 -translate-y-1/2 items-center justify-center rounded-lg text-gray-400 transition hover:bg-gray-200 hover:text-gray-700 dark:hover:bg-gray-700 dark:hover:text-gray-200"
                                    @click="form.search = ''">
                                    <X class="size-4" />
                                </button>
                            </div>
                        </div>

                        <!-- Filtre par technologie -->
                        <div>
                            <label for="developer-technology"
                                class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-200">
                                Technologie
                            </label>

                            <div class="relative">
                                <Code2
                                    class="pointer-events-none absolute left-4 top-1/2 size-4 -translate-y-1/2 text-gray-400" />

                                <select id="developer-technology" v-model="form.technology"
                                    class="w-full appearance-none rounded-xl border border-gray-200 bg-gray-50 py-3 pl-11 pr-10 text-sm text-gray-900 outline-none transition focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-teal-500 dark:focus:bg-gray-800">
                                    <option value="">
                                        Toutes les technologies
                                    </option>

                                    <option v-for="technology in technologies" :key="technology.id"
                                        :value="String(technology.id)">
                                        {{ technology.name }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div
                        class="mt-5 flex flex-col gap-3 border-t border-gray-100 pt-5 dark:border-gray-800 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-xs leading-5 text-gray-500 dark:text-gray-400">
                            Recherche parmi les profils et leurs spécialités techniques.
                        </p>

                        <div class="flex flex-col gap-2 sm:flex-row">
                            <button type="button"
                                class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200 px-4 py-3 text-sm font-semibold text-gray-600 transition hover:bg-gray-50 hover:text-gray-900 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white"
                                @click="resetFilters">
                                <RotateCcw class="size-4" />
                                Réinitialiser
                            </button>

                            <button type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-teal-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-teal-700 focus:outline-none focus:ring-4 focus:ring-teal-500/20">
                                <Search class="size-4" />
                                Rechercher
                            </button>
                        </div>
                    </div>
                </form>
            </section>

            <!-- En-tête des résultats -->
            <section aria-label="Liste des développeurs">
                <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <h2 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-2xl">
                            {{ form.search || form.technology ? 'Résultats de recherche' : 'Explorer les développeurs'
                            }}
                        </h2>

                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            <template v-if="developers.total > 0">
                                {{ developers.total }}
                                {{ developers.total > 1 ? 'profils correspondent aux résultats' :
                                    'profil correspond aux résultats' }}
                            </template>

                            <template v-else>
                                Aucun profil ne correspond à ces critères.
                            </template>
                        </p>
                    </div>

                    <span v-if="developers.last_page > 1"
                        class="rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs font-medium text-gray-500 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400">
                        Page {{ developers.current_page }} sur {{ developers.last_page }}
                    </span>
                </div>

                <!-- Cartes des développeurs -->
                <div v-if="developers.data.length > 0" class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                    <article v-for="developer in developers.data" :key="developer.id"
                        class="group relative flex min-w-0 flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition duration-200 hover:-translate-y-1 hover:border-teal-200 hover:shadow-lg dark:border-gray-800 dark:bg-gray-900 dark:hover:border-teal-900">
                        <!-- Bandeau décoratif -->
                        <div
                            class="relative h-24 overflow-hidden bg-linear-to-br from-teal-600 via-cyan-600 to-blue-700">
                            <div class="absolute -left-5 -top-12 size-36 rounded-full border border-white/10" />
                            <div class="absolute -bottom-16 right-5 size-40 rounded-full border border-white/10" />
                            <div
                                class="absolute right-5 top-5 flex size-10 items-center justify-center rounded-xl border border-white/20 bg-white/10 text-white backdrop-blur-sm">
                                <Terminal class="size-5" />
                            </div>
                        </div>

                        <div class="flex flex-1 flex-col px-5 pb-5">
                            <!-- Avatar -->
                            <div
                                class="-mt-9 mb-3 flex size-17 items-center justify-center rounded-2xl border-4 border-white bg-linear-to-br from-teal-100 to-cyan-100 text-xl font-bold text-teal-800 shadow-sm dark:border-gray-900 dark:from-teal-950 dark:to-cyan-950 dark:text-teal-300">
                                {{ developerInitial(developer.name) }}
                            </div>

                            <!-- Identité -->
                            <h3 class="truncate text-lg font-bold text-gray-900 dark:text-white"
                                :title="developer.name">
                                <Link :href="`/developers/${developer.id}`"
                                    class="transition hover:text-teal-700 dark:hover:text-teal-400">
                                    {{ developer.name }}
                                </Link>
                            </h3>

                            <p class="mt-1 line-clamp-2 min-h-10 text-sm leading-5 text-gray-500 dark:text-gray-400">
                                {{ developer.headline || 'Développeur de la communauté DevLink' }}
                            </p>

                            <!-- Localisation -->
                            <div v-if="developer.location"
                                class="mt-3 flex min-w-0 items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                                <MapPin class="size-4 shrink-0 text-teal-600 dark:text-teal-400" />
                                <span class="truncate">{{ developer.location }}</span>
                            </div>

                            <div v-else class="mt-3 flex items-center gap-2 text-sm text-gray-400 dark:text-gray-500">
                                <UserRound class="size-4 shrink-0" />
                                <span>Localisation non renseignée</span>
                            </div>

                            <!-- Technologies -->
                            <div class="mt-5 flex-1 border-t border-gray-100 pt-4 dark:border-gray-800">
                                <div class="mb-3 flex items-center justify-between gap-2">
                                    <p
                                        class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        Compétences
                                    </p>

                                    <span class="text-xs text-gray-400 dark:text-gray-500">
                                        {{ developer.technologies_count }}
                                        {{ developer.technologies_count > 1 ? 'technologies' : 'technologie' }}
                                    </span>
                                </div>

                                <div v-if="developer.technologies.length > 0" class="flex flex-wrap gap-2">
                                    <span v-for="technology in developer.technologies" :key="technology.id"
                                        :title="technology.is_primary ? `${technology.name} — Technologie principale` : technology.name"
                                        class="inline-flex max-w-full items-center gap-1.5 rounded-lg border px-2.5 py-1.5 text-xs font-medium"
                                        :class="technology.is_primary
                                            ? 'border-teal-200 bg-teal-50 text-teal-800 dark:border-teal-900 dark:bg-teal-950 dark:text-teal-300'
                                            : 'border-gray-200 bg-gray-50 text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300'
                                            ">
                                        <Star v-if="technology.is_primary" class="size-3 shrink-0 fill-current" />

                                        <Code2 v-else class="size-3 shrink-0" />

                                        <span class="truncate">
                                            {{ technology.name }}
                                        </span>
                                    </span>

                                    <span v-if="developer.technologies_count > developer.technologies.length"
                                        class="inline-flex items-center rounded-lg border border-gray-200 bg-gray-50 px-2.5 py-1.5 text-xs font-medium text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                                        +{{ developer.technologies_count - developer.technologies.length }}
                                    </span>
                                </div>

                                <p v-else
                                    class="rounded-xl border border-dashed border-gray-200 px-3 py-4 text-center text-xs text-gray-400 dark:border-gray-700 dark:text-gray-500">
                                    Aucune compétence renseignée
                                </p>
                            </div>

                            <!-- Action -->
                            <div class="mt-5 border-t border-gray-100 pt-4 dark:border-gray-800">
                                <Link :href="`/developers/${developer.id}`"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gray-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-teal-700 focus:outline-none focus:ring-4 focus:ring-teal-500/20 dark:bg-gray-800 dark:hover:bg-teal-700">
                                    <UserRound class="size-4" />
                                    Voir le profil
                                    <ArrowRight class="ml-auto size-4 transition-transform group-hover:translate-x-1" />
                                </Link>
                            </div>
                        </div>
                    </article>
                </div>

                <!-- Pagination -->
                <nav v-if="developers.last_page > 1" class="mt-8 flex flex-wrap items-center justify-center gap-2"
                    aria-label="Pagination des développeurs">
                    <template v-for="(link, index) in developers.links" :key="`${index}-${link.label}`">
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

                <!-- Aucun résultat -->
                <div v-else
                    class="rounded-2xl border border-dashed border-gray-300 bg-white px-5 py-14 text-center dark:border-gray-700 dark:bg-gray-900 sm:px-10 sm:py-20">
                    <div
                        class="mx-auto flex size-16 items-center justify-center rounded-2xl bg-teal-50 text-teal-700 dark:bg-teal-950 dark:text-teal-300">
                        <Search class="size-8" />
                    </div>

                    <h2 class="mt-5 text-xl font-bold text-gray-900 dark:text-white">
                        Aucun développeur trouvé
                    </h2>

                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-600 dark:text-gray-400">
                        Aucun profil ne correspond à ces critères. Essaie un autre nom
                        ou sélectionne une autre technologie.
                    </p>

                    <button type="button"
                        class="mt-6 inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                        @click="resetFilters">
                        <RotateCcw class="size-4" />
                        Réinitialiser les filtres
                    </button>
                </div>
            </section>

            <!-- Bannière finale -->
            <aside
                class="mt-8 flex flex-col gap-4 rounded-2xl border border-teal-100 bg-teal-50/70 p-5 dark:border-teal-900/60 dark:bg-teal-950/30 sm:flex-row sm:items-center sm:justify-between sm:p-6">
                <div class="flex items-start gap-3">
                    <div
                        class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-white text-teal-700 shadow-sm dark:bg-gray-900 dark:text-teal-300">
                        <MessageCircle class="size-5" />
                    </div>

                    <div>
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                            La prochaine collaboration commence ici.
                        </h3>

                        <p class="mt-1 text-sm leading-6 text-gray-600 dark:text-gray-400">
                            Explore les profils, découvre de nouvelles compétences et
                            rapproche-toi de développeurs qui partagent tes intérêts.
                        </p>
                    </div>
                </div>

                <Link href="/communities"
                    class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl border border-teal-200 bg-white px-4 py-2.5 text-sm font-semibold text-teal-700 transition hover:bg-teal-50 dark:border-teal-900 dark:bg-gray-900 dark:text-teal-300 dark:hover:bg-teal-950">
                    Explorer les communautés
                    <ArrowRight class="size-4" />
                </Link>
            </aside>
        </div>
    </main>
</template>