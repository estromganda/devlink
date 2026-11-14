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

    <main class="min-h-screen bg-gray-50/70 py-6 dark:bg-card sm:py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <!-- En-tête -->
            <header
                class="relative mb-8 overflow-hidden rounded-3xl border border-gray-200 bg-card shadow-sm dark:border-gray-800">
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
                                DevColab · Réseau de développeurs
                            </div>

                            <h1
                                class="max-w-3xl text-3xl font-bold tracking-tight text-foreground  sm:text-4xl lg:text-5xl">
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

                                <p class="mt-3 text-2xl font-bold text-foreground ">
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

                                <p class="mt-3 text-2xl font-bold text-foreground ">
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
                class="mb-8 overflow-hidden rounded-2xl border border-gray-200 bg-card shadow-sm dark:border-gray-800">
                <div class="flex items-center gap-3 border-b p-5 sm:px-6">
                    <div class="flex size-10 items-center justify-center rounded-xl bg-primary/10 text-primary">
                        <SlidersHorizontal class="size-5" />
                    </div>

                    <div>
                        <h2 class="font-semibold">Trouver un développeur</h2>
                        <p class="mt-0.5 text-xs text-muted-foreground">
                            Affine les résultats selon tes besoins.
                        </p>
                    </div>
                </div>

                <form class="p-5 sm:p-6" @submit.prevent="searchDevelopers">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label for="developer-search" class="mb-2 block text-sm font-medium">
                                Nom du développeur
                            </label>

                            <div class="relative">
                                <Search
                                    class="pointer-events-none absolute left-4 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />

                                <input id="developer-search" v-model="form.search" type="search" autocomplete="off"
                                    placeholder="Ex. : Alexandre, Sophie..."
                                    class="w-full rounded-xl border border-input bg-background py-3 pl-11 pr-10 text-sm outline-none transition placeholder:text-muted-foreground focus:border-primary focus:ring-4 focus:ring-primary/10" />

                                <button v-if="form.search" type="button" aria-label="Effacer le nom"
                                    class="absolute right-3 top-1/2 flex size-7 -translate-y-1/2 items-center justify-center rounded-lg text-muted-foreground transition hover:bg-muted hover:text-foreground"
                                    @click="form.search = ''">
                                    <X class="size-4" />
                                </button>
                            </div>
                        </div>

                        <div>
                            <label for="developer-technology" class="mb-2 block text-sm font-medium">
                                Technologie
                            </label>

                            <div class="relative">
                                <Code2
                                    class="pointer-events-none absolute left-4 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />

                                <select id="developer-technology" v-model="form.technology"
                                    class="w-full appearance-none rounded-xl border border-input bg-background py-3 pl-11 pr-10 text-sm outline-none transition focus:border-primary focus:ring-4 focus:ring-primary/10">
                                    <option value="">Toutes les technologies</option>

                                    <option v-for="technology in technologies" :key="technology.id"
                                        :value="String(technology.id)">
                                        {{ technology.name }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 flex flex-col gap-3 border-t pt-5 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-xs leading-5 text-muted-foreground">
                            Recherche parmi les profils et leurs spécialités techniques.
                        </p>

                        <div class="flex flex-col gap-2 sm:flex-row">
                            <button type="button"
                                class="inline-flex items-center justify-center gap-2 rounded-xl border bg-background px-4 py-3 text-sm font-semibold transition hover:border-primary/30 hover:bg-muted"
                                @click="resetFilters">
                                <RotateCcw class="size-4" />
                                Réinitialiser
                            </button>

                            <button type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-primary-foreground shadow-sm shadow-primary/20 transition hover:-translate-y-0.5 hover:shadow-md">
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
                        <h2 class="text-xl font-bold tracking-tight text-foreground  sm:text-2xl">
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
                        class="rounded-xl border border-gray-200 bg-card px-3 py-2 text-xs font-medium text-gray-500 dark:border-gray-800 dark:text-gray-400">
                        Page {{ developers.current_page }} sur {{ developers.last_page }}
                    </span>
                </div>

                <!-- Cartes des développeurs -->
                <div v-if="developers.data.length > 0" class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                    <article v-for="developer in developers.data" :key="developer.id"
                        class="group relative flex min-w-0 flex-col overflow-hidden rounded-2xl border bg-card transition duration-200 hover:-translate-y-1 hover:border-primary/40 hover:shadow-xl hover:shadow-primary/5">
                        <!-- Bandeau décoratif -->
                        <div
                            class="relative h-24 overflow-hidden border-b bg-linear-to-br from-primary/15 via-primary/5 to-background">
                            <div class="absolute -left-5 -top-12 size-36 rounded-full border border-primary/10" />
                            <div class="absolute -bottom-16 right-5 size-40 rounded-full border border-primary/10" />

                            <div
                                class="absolute right-5 top-5 flex size-10 items-center justify-center rounded-xl border bg-background/70 text-primary backdrop-blur-sm">
                                <Terminal class="size-5" />
                            </div>
                        </div>

                        <div class="flex flex-1 flex-col px-5 pb-5">
                            <!-- Avatar -->
                            <div
                                class="-mt-9 mb-3 flex size-17 items-center justify-center rounded-2xl border-4 border-card bg-primary/10 text-xl font-bold text-primary shadow-sm">
                                {{ developerInitial(developer.name) }}
                            </div>

                            <!-- Identité -->
                            <h3 class="truncate text-lg font-bold" :title="developer.name">
                                <Link :href="`/developers/${developer.id}`" class="transition hover:text-primary">
                                    {{ developer.name }}
                                </Link>
                            </h3>

                            <p class="mt-1 line-clamp-2 min-h-10 text-sm leading-5 text-muted-foreground">
                                {{ developer.headline || 'Développeur de la communauté DevColab' }}
                            </p>

                            <!-- Localisation -->
                            <div v-if="developer.location"
                                class="mt-3 flex min-w-0 items-center gap-2 text-sm text-muted-foreground">
                                <MapPin class="size-4 shrink-0 text-primary" />
                                <span class="truncate">{{ developer.location }}</span>
                            </div>

                            <div v-else class="mt-3 flex items-center gap-2 text-sm text-muted-foreground">
                                <UserRound class="size-4 shrink-0" />
                                <span>Localisation non renseignée</span>
                            </div>

                            <!-- Technologies -->
                            <div class="mt-5 flex-1 border-t pt-4">
                                <div class="mb-3 flex items-center justify-between gap-2">
                                    <p class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                                        Compétences
                                    </p>

                                    <span class="text-xs text-muted-foreground">
                                        {{ developer.technologies_count }}
                                        {{ developer.technologies_count > 1 ? 'technologies' : 'technologie' }}
                                    </span>
                                </div>

                                <div v-if="developer.technologies.length > 0" class="flex flex-wrap gap-2">
                                    <span v-for="technology in developer.technologies" :key="technology.id" :title="technology.is_primary
                                        ? `${technology.name} — Technologie principale`
                                        : technology.name"
                                        class="inline-flex max-w-full items-center gap-1.5 rounded-lg border px-2.5 py-1.5 text-xs font-medium"
                                        :class="technology.is_primary
                                            ? 'border-primary/20 bg-primary/10 text-primary'
                                            : 'bg-muted/50 text-muted-foreground'">
                                        <Star v-if="technology.is_primary" class="size-3 shrink-0 fill-current" />

                                        <Code2 v-else class="size-3 shrink-0" />

                                        <span class="truncate">{{ technology.name }}</span>
                                    </span>

                                    <span v-if="developer.technologies_count > developer.technologies.length"
                                        class="inline-flex items-center rounded-lg border bg-muted/50 px-2.5 py-1.5 text-xs font-medium text-muted-foreground">
                                        +{{ developer.technologies_count - developer.technologies.length }}
                                    </span>
                                </div>

                                <p v-else
                                    class="rounded-xl border border-dashed px-3 py-4 text-center text-xs text-muted-foreground">
                                    Aucune compétence renseignée
                                </p>
                            </div>

                            <!-- Action -->
                            <div class="mt-5 border-t pt-4">
                                <Link :href="`/developers/${developer.id}`"
                                    class="group/button inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-4 py-3 text-sm font-semibold text-primary-foreground transition hover:-translate-y-0.5 hover:shadow-md hover:shadow-primary/20">
                                    <UserRound class="size-4" />
                                    Voir le profil

                                    <ArrowRight
                                        class="ml-auto size-4 transition-transform group-hover/button:translate-x-1" />
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
                                ? 'border-primary bg-primary text-primary-foreground shadow-sm'
                                : 'bg-background hover:border-primary/40 hover:bg-muted'">
                            <ChevronLeft v-if="paginationLabel(link.label) === 'Précédent'" class="size-4" />

                            <ChevronRight v-if="paginationLabel(link.label) === 'Suivant'" class="size-4" />

                            <span v-if="!['Précédent', 'Suivant'].includes(paginationLabel(link.label))"
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
                    class="rounded-2xl border border-dashed border-gray-300 bg-card px-5 py-14 text-center dark:border-gray-700 sm:px-10 sm:py-20">
                    <div
                        class="mx-auto flex size-16 items-center justify-center rounded-2xl bg-teal-50 text-teal-700 dark:bg-teal-950 dark:text-teal-300">
                        <Search class="size-8" />
                    </div>

                    <h2 class="mt-5 text-xl font-bold text-foreground">
                        Aucun développeur trouvé
                    </h2>

                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-600 dark:text-gray-400">
                        Aucun profil ne correspond à ces critères. Essaie un autre nom
                        ou sélectionne une autre technologie.
                    </p>

                    <button type="button"
                        class="mt-6 inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200 bg-card px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
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
                        class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-card text-teal-700 shadow-sm dark:text-teal-300">
                        <MessageCircle class="size-5" />
                    </div>

                    <div>
                        <h3 class="text-sm font-bold text-foreground ">
                            La prochaine collaboration commence ici.
                        </h3>

                        <p class="mt-1 text-sm leading-6 text-gray-600 dark:text-gray-400">
                            Explore les profils, découvre de nouvelles compétences et
                            rapproche-toi de développeurs qui partagent tes intérêts.
                        </p>
                    </div>
                </div>

                <Link href="/communities"
                    class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl border border-teal-200 bg-card px-4 py-2.5 text-sm font-semibold text-teal-700 transition hover:bg-teal-50 dark:border-teal-900 dark:text-teal-300 dark:hover:bg-teal-950">
                    Explorer les communautés
                    <ArrowRight class="size-4" />
                </Link>
            </aside>
        </div>
    </main>
</template>