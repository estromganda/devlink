<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, ref, watch } from 'vue';
import {
    ArrowRight,
    ArrowUpRight,
    BriefcaseBusiness,
    CheckCircle2,
    Clock3,
    Code2,
    ExternalLink,
    FolderGit2,
    Lightbulb,
    Plus,
    Rocket,
    Search,
    Sparkles,
    Users,
    X,
} from '@lucide/vue';

interface ProjectOwner {
    id: number;
    name: string;
}

interface Project {
    id: number;
    name: string;
    slug: string;
    short_description: string | null;
    description: string;
    status: string;
    visibility: string;
    repository_url: string | null;
    demo_url: string | null;
    cover_path: string | null;
    looking_for_collaborators: boolean;
    members_count: number;
    owner: ProjectOwner;
    created_at: string;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedProjects {
    data: Project[];
    links: PaginationLink[];
    current_page: number;
    last_page: number;
    total: number;
}

const props = defineProps<{
    projects: PaginatedProjects;
    filters: {
        search: string;
    };
}>();

const search = ref(props.filters.search);
let searchTimeout: ReturnType<typeof setTimeout> | undefined;

watch(search, (value) => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }

    searchTimeout = setTimeout(() => {
        router.get(
            '/projects',
            { search: value.trim() || undefined },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            },
        );
    }, 350);
});

onBeforeUnmount(() => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
});

const hasSearch = computed(() => search.value.trim().length > 0);

function projectSummary(project: Project): string {
    return (
        project.short_description ||
        project.description ||
        'Ce projet ne possède pas encore de description.'
    );
}

function projectStatus(status: string): string {
    const statuses: Record<string, string> = {
        planning: 'En préparation',
        in_progress: 'En développement',
        completed: 'Terminé',
        paused: 'En pause',
        active: 'Actif',
    };

    return statuses[status] || status;
}

function projectStatusClass(status: string): string {
    const classes: Record<string, string> = {
        planning:
            'bg-amber-500/10 text-amber-700 ring-amber-500/20 dark:text-amber-400',
        in_progress:
            'bg-blue-500/10 text-blue-700 ring-blue-500/20 dark:text-blue-400',
        completed:
            'bg-emerald-500/10 text-emerald-700 ring-emerald-500/20 dark:text-emerald-400',
        paused:
            'bg-orange-500/10 text-orange-700 ring-orange-500/20 dark:text-orange-400',
        active:
            'bg-primary/10 text-primary ring-primary/20',
    };

    return (
        classes[status] ||
        'bg-muted text-muted-foreground ring-border'
    );
}

function projectInitial(name: string): string {
    return name.trim().charAt(0).toUpperCase() || 'P';
}

function visitPage(url: string | null): void {
    if (url) {
        router.visit(url, {
            preserveScroll: true,
            preserveState: true,
        });
    }
}

function clearSearch(): void {
    search.value = '';
}
</script>

<template>

    <Head title="Projets des développeurs" />

    <main class="min-h-screen">
        <!-- En-tête -->
        <section class="relative isolate overflow-hidden border-b">
            <div
                class="pointer-events-none absolute inset-0 -z-10 bg-linear-to-br from-primary/10 via-background to-background" />

            <div
                class="pointer-events-none absolute -right-32 -top-36 -z-10 size-96 rounded-full bg-primary/10 blur-3xl" />

            <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8 lg:py-20">
                <div class="flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
                    <div class="max-w-3xl">
                        <div
                            class="inline-flex items-center gap-2 rounded-full border bg-background/80 px-3.5 py-2 text-xs font-bold text-primary shadow-sm backdrop-blur sm:text-sm">
                            <Rocket class="size-4" />
                            L'espace projets DevLink
                        </div>

                        <h1 class="mt-6 text-3xl font-black leading-tight tracking-tight sm:text-4xl lg:text-5xl">
                            Des idées qui
                            <span class="text-primary">prennent vie.</span>
                        </h1>

                        <p class="mt-5 max-w-2xl text-sm leading-7 text-muted-foreground sm:text-base sm:leading-8">
                            Découvre les projets de la communauté, explore les
                            technologies utilisées et trouve de nouvelles
                            sources d'inspiration. Chaque projet commence par
                            une idée.
                        </p>
                    </div>

                    <Link href="/projects/create"
                        class="group inline-flex shrink-0 items-center justify-center gap-2.5 rounded-xl bg-primary px-5 py-3.5 text-sm font-bold text-primary-foreground shadow-lg shadow-primary/20 transition duration-200 hover:-translate-y-0.5 hover:shadow-xl hover:shadow-primary/25">
                        <Plus class="size-4" />
                        Présenter un projet
                        <ArrowRight class="size-4 transition-transform group-hover:translate-x-1" />
                    </Link>
                </div>

                <!-- Barre de recherche -->
                <div class="mt-9 max-w-3xl">
                    <label for="project-search" class="mb-2.5 block text-sm font-semibold">
                        Trouver un projet
                    </label>

                    <div class="relative">
                        <Search
                            class="pointer-events-none absolute left-4 top-1/2 size-5 -translate-y-1/2 text-muted-foreground" />

                        <input id="project-search" v-model="search" type="search"
                            placeholder="Nom, description ou idée..." autocomplete="off"
                            class="w-full rounded-2xl border bg-background py-4 pl-12 pr-12 text-sm shadow-sm outline-none transition placeholder:text-muted-foreground/70 focus:border-primary focus:ring-4 focus:ring-primary/10 sm:text-base" />

                        <button v-if="hasSearch" type="button" aria-label="Effacer la recherche"
                            class="absolute right-3 top-1/2 flex size-8 -translate-y-1/2 items-center justify-center rounded-lg text-muted-foreground transition hover:bg-muted hover:text-foreground"
                            @click="clearSearch">
                            <X class="size-4" />
                        </button>
                    </div>

                    <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                        <p class="text-sm text-muted-foreground">
                            <span class="font-bold text-foreground">
                                {{ projects.total }}
                            </span>
                            {{
                                projects.total > 1
                                    ? 'projets publics'
                                    : 'projet public'
                            }}
                            trouvé{{ projects.total > 1 ? 's' : '' }}
                        </p>

                        <div class="inline-flex items-center gap-2 text-xs text-muted-foreground">
                            <Sparkles class="size-4 text-primary" />
                            L'inspiration commence ici
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Catalogue -->
        <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 sm:py-14 lg:px-8">
            <div class="mb-7 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-primary">
                        Découvrir et explorer
                    </p>

                    <h2 class="mt-2 text-2xl font-extrabold tracking-tight">
                        Les projets de la communauté
                    </h2>
                </div>

                <p v-if="projects.last_page > 1" class="text-sm text-muted-foreground">
                    Page {{ projects.current_page }} sur {{ projects.last_page }}
                </p>
            </div>

            <div v-if="projects.data.length" class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                <article v-for="(project, index) in projects.data" :key="project.id"
                    class="group flex min-w-0 flex-col overflow-hidden rounded-2xl border bg-card transition duration-300 hover:-translate-y-1 hover:border-primary/40 hover:shadow-xl hover:shadow-primary/5"
                    :style="{ animationDelay: `${index * 60}ms` }">
                    <!-- Couverture -->
                    <div
                        class="relative flex h-48 items-center justify-center overflow-hidden border-b bg-linear-to-br from-primary/15 via-violet-500/10 to-blue-500/15">
                        <img v-if="project.cover_path" :src="`/storage/${project.cover_path}`"
                            :alt="`Couverture du projet ${project.name}`" loading="lazy"
                            class="absolute inset-0 size-full object-cover transition duration-700 group-hover:scale-105" />

                        <div v-else class="absolute inset-0 flex items-center justify-center">
                            <div class="absolute size-40 rounded-full border border-primary/10" />
                            <div class="absolute size-28 rounded-full border border-primary/15" />

                            <div
                                class="relative flex size-20 items-center justify-center rounded-3xl border border-primary/15 bg-background/80 text-4xl font-black text-primary shadow-lg backdrop-blur transition duration-300 group-hover:rotate-3 group-hover:scale-110">
                                {{ projectInitial(project.name) }}
                            </div>
                        </div>

                        <div class="absolute inset-x-0 bottom-0 h-20 bg-linear-to-t from-black/20 to-transparent" />

                        <span
                            class="absolute left-3 top-3 inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-bold shadow-sm ring-1 ring-inset backdrop-blur-md"
                            :class="projectStatusClass(project.status)">
                            <span class="size-1.5 rounded-full bg-current" />
                            {{ projectStatus(project.status) }}
                        </span>

                        <span v-if="project.looking_for_collaborators"
                            class="absolute right-3 top-3 inline-flex items-center gap-1.5 rounded-full bg-background/95 px-3 py-1.5 text-xs font-bold text-emerald-700 shadow-sm ring-1 ring-emerald-500/20 backdrop-blur dark:text-emerald-400">
                            <Users class="size-3.5" />
                            Recrute
                        </span>
                    </div>

                    <!-- Contenu -->
                    <div class="flex flex-1 flex-col p-5 sm:p-6">
                        <div class="flex items-start gap-3">
                            <div class="min-w-0 flex-1">
                                <h3
                                    class="wrap-break-word text-lg font-extrabold tracking-tight transition group-hover:text-primary">
                                    {{ project.name }}
                                </h3>

                                <p class="mt-1 text-xs text-muted-foreground">
                                    Projet communautaire
                                </p>
                            </div>

                            <div
                                class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-muted text-muted-foreground transition group-hover:bg-primary/10 group-hover:text-primary">
                                <FolderGit2 class="size-4.5" />
                            </div>
                        </div>

                        <p class="mt-4 line-clamp-3 min-h-18 text-sm leading-6 text-muted-foreground">
                            {{ projectSummary(project) }}
                        </p>

                        <!-- Informations -->
                        <div class="mt-5 flex flex-wrap items-center gap-2">
                            <span
                                class="inline-flex items-center gap-1.5 rounded-lg bg-muted px-3 py-2 text-xs font-medium text-muted-foreground">
                                <Users class="size-3.5" />
                                {{ project.members_count }}
                                {{
                                    project.members_count > 1
                                        ? 'membres'
                                        : 'membre'
                                }}
                            </span>

                            <span v-if="project.looking_for_collaborators"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-500/10 px-3 py-2 text-xs font-semibold text-emerald-700 dark:text-emerald-400">
                                <BriefcaseBusiness class="size-3.5" />
                                Collaboration ouverte
                            </span>
                        </div>

                        <!-- Liens externes -->
                        <div v-if="project.repository_url || project.demo_url" class="mt-4 flex flex-wrap gap-2">
                            <a v-if="project.repository_url" :href="project.repository_url" target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-1.5 rounded-lg border px-3 py-2 text-xs font-semibold transition hover:border-primary/30 hover:bg-muted"
                                @click.stop>
                                <Code2 class="size-3.5" />
                                Dépôt
                                <ExternalLink class="size-3" />
                            </a>

                            <a v-if="project.demo_url" :href="project.demo_url" target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-1.5 rounded-lg border px-3 py-2 text-xs font-semibold transition hover:border-primary/30 hover:bg-muted"
                                @click.stop>
                                <Rocket class="size-3.5" />
                                Démo
                                <ExternalLink class="size-3" />
                            </a>
                        </div>

                        <!-- Pied de carte -->
                        <div class="mt-auto flex items-center justify-between gap-3 border-t pt-5"
                            :class="project.repository_url || project.demo_url ? 'mt-5' : 'mt-6'">
                            <Link :href="`/developers/${project.owner.id}`"
                                class="flex min-w-0 items-center gap-2.5 rounded-lg outline-none focus-visible:ring-2 focus-visible:ring-primary">
                                <div
                                    class="flex size-9 shrink-0 items-center justify-center rounded-full bg-primary/10 text-xs font-extrabold text-primary">
                                    {{ projectInitial(project.owner.name) }}
                                </div>

                                <div class="min-w-0">
                                    <p class="text-[11px] text-muted-foreground">
                                        Créé par
                                    </p>
                                    <p class="truncate text-sm font-semibold transition hover:text-primary">
                                        {{ project.owner.name }}
                                    </p>
                                </div>
                            </Link>

                            <Link :href="`/projects/${project.slug}`"
                                class="group/link inline-flex shrink-0 items-center gap-1.5 rounded-lg px-2 py-2 text-sm font-bold text-primary transition hover:bg-primary/5">
                                Découvrir
                                <ArrowRight class="size-4 transition-transform group-hover/link:translate-x-1" />
                            </Link>
                        </div>
                    </div>
                </article>
            </div>

            <!-- État vide -->
            <div v-else class="rounded-3xl border border-dashed bg-muted/10 px-5 py-16 text-center sm:px-10 sm:py-20">
                <div
                    class="mx-auto flex size-16 items-center justify-center rounded-2xl border bg-card text-primary shadow-sm">
                    <Search v-if="hasSearch" class="size-7" />
                    <Rocket v-else class="size-7" />
                </div>

                <h2 class="mt-6 text-xl font-extrabold tracking-tight sm:text-2xl">
                    {{
                        hasSearch
                            ? 'Aucun projet ne correspond à ta recherche'
                            : 'Le prochain projet pourrait être le tien.'
                    }}
                </h2>

                <p class="mx-auto mt-3 max-w-md text-sm leading-7 text-muted-foreground">{{
                    hasSearch
                        ? 'Essaie un autre terme ou efface ta recherche pour retrouver tous les projets publics.' :
                        ('La communauté attend de découvrir tes idées. Présente ton projet et trouve peut-être tes futurs '
                            + 'collaborateurs.')
                }}
                </p>

                <div class="mt-7 flex flex-col justify-center gap-3 sm:flex-row">
                    <button v-if="hasSearch" type="button"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border bg-background px-5 py-3 text-sm font-bold transition hover:bg-muted"
                        @click="clearSearch">
                        <X class="size-4" />
                        Effacer la recherche
                    </button>

                    <Link href="/projects/create"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3 text-sm font-bold text-primary-foreground transition hover:-translate-y-0.5 hover:shadow-lg hover:shadow-primary/20">
                        <Plus class="size-4" />
                        Présenter un projet
                    </Link>
                </div>
            </div>

            <!-- Pagination -->
            <nav v-if="projects.last_page > 1" class="mt-10 flex flex-wrap items-center justify-center gap-2"
                aria-label="Pagination des projets">
                <button v-for="(link, index) in projects.links" :key="`${index}-${link.label}`" type="button"
                    :disabled="!link.url" :aria-current="link.active ? 'page' : undefined"
                    class="min-h-10 rounded-xl border px-3.5 py-2 text-sm font-medium transition disabled:cursor-not-allowed disabled:opacity-40"
                    :class="link.active
                        ? 'border-primary bg-primary text-primary-foreground shadow-sm'
                        : 'bg-background hover:border-primary/30 hover:bg-muted'
                        " @click="visitPage(link.url)" v-html="link.label" />
            </nav>

            <p v-if="projects.last_page > 1" class="mt-4 text-center text-xs text-muted-foreground">
                Page {{ projects.current_page }} sur {{ projects.last_page }}
            </p>
        </section>

        <!-- Bannière de contribution -->
        <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
            <div
                class="relative isolate overflow-hidden rounded-3xl border bg-linear-to-br from-primary/10 via-card to-card p-6 sm:p-9 lg:p-10">
                <div
                    class="pointer-events-none absolute -right-16 -top-20 -z-10 size-64 rounded-full bg-primary/10 blur-3xl" />

                <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                    <div class="flex items-start gap-4">
                        <div
                            class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-primary/10 text-primary sm:size-14">
                            <Lightbulb class="size-6 sm:size-7" />
                        </div>

                        <div class="max-w-2xl">
                            <h2 class="text-xl font-extrabold tracking-tight sm:text-2xl">
                                Tu construis quelque chose d'intéressant ?
                            </h2>

                            <p class="mt-2 text-sm leading-7 text-muted-foreground">
                                Présente ton projet, partage ton parcours et
                                donne à la communauté l'occasion de découvrir
                                ton travail.
                            </p>
                        </div>
                    </div>

                    <Link href="/projects/create"
                        class="group inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3.5 text-sm font-bold text-primary-foreground transition hover:-translate-y-0.5 hover:shadow-lg hover:shadow-primary/20">
                        Présenter mon projet
                        <ArrowUpRight
                            class="size-4 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
                    </Link>
                </div>
            </div>
        </section>
    </main>
</template>

<style scoped>
@keyframes project-appear {
    from {
        opacity: 0;
        transform: translateY(12px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

article {
    animation: project-appear 450ms ease-out both;
}

@media (prefers-reduced-motion: reduce) {
    article {
        animation: none;
    }
}
</style>