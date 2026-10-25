<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

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
            { search: value || undefined },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            },
        );
    }, 350);
});

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
</script>

<template>

    <Head title="Projets des développeurs" />

    <div class="min-h-screen">
        <!-- En-tête -->
        <section class="border-b bg-muted/20">
            <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
                <p class="text-sm font-semibold uppercase tracking-widest text-primary">
                    L'espace projets DevLink
                </p>

                <div class="mt-4 flex flex-col justify-between gap-6 md:flex-row md:items-end">
                    <div>
                        <h1 class="text-3xl font-extrabold tracking-tight sm:text-4xl">
                            Des idées qui prennent vie.
                        </h1>

                        <p class="mt-4 max-w-2xl leading-7 text-muted-foreground">
                            Découvre les projets de la communauté, explore les
                            technologies utilisées et trouve de nouvelles
                            sources d'inspiration.
                        </p>
                    </div>

                    <Link href="/projects/create"
                        class="inline-flex shrink-0 items-center justify-center rounded-xl bg-primary px-5 py-3 font-semibold text-primary-foreground transition hover:-translate-y-0.5 hover:opacity-90">
                        + Présenter un projet
                    </Link>
                </div>

                <!-- Recherche -->
                <div class="mt-8 max-w-2xl">
                    <label for="project-search" class="mb-2 block text-sm font-medium">
                        Rechercher un projet
                    </label>

                    <div class="relative">
                        <svg class="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-muted-foreground"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <circle cx="11" cy="11" r="7" />
                            <path d="m16 16 4 4" />
                        </svg>

                        <input id="project-search" v-model="search" type="search"
                            placeholder="Nom, description ou idée..."
                            class="w-full rounded-xl border bg-background py-3.5 pl-12 pr-4 outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20" />
                    </div>
                </div>

                <p class="mt-4 text-sm text-muted-foreground">
                    {{ projects.total }}
                    {{ projects.total > 1 ? 'projets publics' : 'projet public' }}
                    trouvé{{ projects.total > 1 ? 's' : '' }}
                </p>
            </div>
        </section>

        <!-- Catalogue -->
        <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div v-if="projects.data.length" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <article v-for="(project, index) in projects.data" :key="project.id"
                    class="group flex flex-col overflow-hidden rounded-2xl border bg-card transition duration-300 hover:-translate-y-1 hover:border-primary/50 hover:shadow-xl"
                    :style="{ animationDelay: `${index * 70}ms` }">
                    <!-- Image de couverture ou initiale -->
                    <div
                        class="relative flex h-44 items-center justify-center overflow-hidden bg-linear-to-br from-indigo-500/15 via-primary/10 to-violet-500/20">
                        <img v-if="project.cover_path" :src="`/storage/${project.cover_path}`"
                            :alt="`Couverture du projet ${project.name}`"
                            class="h-full w-full object-cover transition duration-500 group-hover:scale-105" />

                        <span v-else
                            class="text-6xl font-black text-primary/60 transition duration-300 group-hover:scale-110">
                            {{ projectInitial(project.name) }}
                        </span>

                        <span
                            class="absolute right-3 top-3 rounded-full border bg-background/90 px-3 py-1 text-xs font-semibold backdrop-blur">
                            {{ projectStatus(project.status) }}
                        </span>
                    </div>

                    <div class="flex flex-1 flex-col p-5">
                        <div class="flex items-start justify-between gap-3">
                            <h2 class="text-lg font-bold transition group-hover:text-primary">
                                {{ project.name }}
                            </h2>
                        </div>

                        <p class="mt-3 line-clamp-3 min-h-18 text-sm leading-6 text-muted-foreground">
                            {{ projectSummary(project) }}
                        </p>

                        <div class="mt-5 flex flex-wrap items-center gap-2">
                            <span class="rounded-lg bg-muted px-3 py-1.5 text-xs text-muted-foreground">
                                {{ project.members_count }}
                                {{
                                    project.members_count > 1
                                        ? 'membres'
                                        : 'membre'
                                }}
                            </span>

                            <span v-if="project.looking_for_collaborators"
                                class="rounded-lg bg-emerald-500/10 px-3 py-1.5 text-xs font-medium text-emerald-600 dark:text-emerald-400">
                                Recherche des collaborateurs
                            </span>
                        </div>

                        <div class="mt-6 flex items-center justify-between gap-3 border-t pt-4">
                            <Link :href="`/developers/${project.owner.id}`"
                                class="truncate text-sm text-muted-foreground transition hover:text-primary">
                                Par {{ project.owner.name }}
                            </Link>

                            <Link :href="`/projects/${project.slug}`"
                                class="shrink-0 text-sm font-semibold text-primary transition hover:translate-x-1">
                                Découvrir →
                            </Link>
                        </div>
                    </div>
                </article>
            </div>

            <!-- Aucun résultat -->
            <div v-else class="rounded-2xl border border-dashed px-6 py-16 text-center">
                <div class="text-4xl" aria-hidden="true">🚀</div>

                <h2 class="mt-4 text-xl font-bold">
                    Aucun projet trouvé
                </h2>

                <p class="mx-auto mt-2 max-w-md text-muted-foreground">
                    {{
                        search
                            ? 'Essaie un autre terme de recherche.'
                            : 'La communauté attend ses premiers projets publics.'
                    }}
                </p>

                <button v-if="search" type="button" class="mt-5 font-semibold text-primary hover:underline"
                    @click="search = ''">
                    Effacer la recherche
                </button>
            </div>

            <!-- Pagination -->
            <nav v-if="projects.last_page > 1" class="mt-10 flex flex-wrap justify-center gap-2"
                aria-label="Pagination des projets">
                <button v-for="(link, index) in projects.links" :key="`${index}-${link.label}`" type="button"
                    :disabled="!link.url" :aria-current="link.active ? 'page' : undefined"
                    class="rounded-lg border px-3 py-2 text-sm transition disabled:cursor-not-allowed disabled:opacity-40"
                    :class="link.active
                        ? 'border-primary bg-primary text-primary-foreground'
                        : 'bg-background hover:bg-muted'
                        " @click="visitPage(link.url)" v-html="link.label" />
            </nav>
        </section>
    </div>
</template>

<style scoped>
@keyframes project-appear {
    from {
        opacity: 0;
        transform: translateY(14px);
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
