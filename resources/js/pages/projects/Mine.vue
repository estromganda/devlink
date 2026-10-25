<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';

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
    cover_path: string | null;
    looking_for_collaborators: boolean;
    members_count: number;
    owner: ProjectOwner;
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

defineProps<{
    projects: PaginatedProjects;
}>();

function statusLabel(status: string): string {
    const labels: Record<string, string> = {
        planning: 'En préparation',
        in_progress: 'En développement',
        completed: 'Terminé',
        paused: 'En pause',
    };

    return labels[status] || status;
}

function deleteProject(project: Project): void {
    const confirmed = window.confirm(
        `Supprimer le projet « ${project.name} » ? Cette action le retirera du catalogue.`,
    );

    if (confirmed) {
        router.delete(`/projects/${project.slug}`);
    }
}

function visitPage(url: string | null): void {
    if (url) {
        router.visit(url, { preserveScroll: true });
    }
}
</script>

<template>

    <Head title="Mes projets" />

    <main class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
            <div>
                <p class="text-sm font-semibold uppercase tracking-widest text-primary">
                    Espace personnel
                </p>

                <h1 class="mt-3 text-3xl font-extrabold tracking-tight">
                    Mes projets
                </h1>

                <p class="mt-3 text-muted-foreground">
                    Gère tes projets et leur visibilité sur DevLink.
                </p>
            </div>

            <Link href="/projects/create"
                class="inline-flex items-center justify-center rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-primary-foreground transition hover:opacity-90">
                + Nouveau projet
            </Link>
        </div>

        <div v-if="projects.data.length" class="mt-9 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <article v-for="project in projects.data" :key="project.id"
                class="overflow-hidden rounded-2xl border bg-card">
                <div class="flex h-36 items-center justify-center overflow-hidden bg-muted/50">
                    <img v-if="project.cover_path" :src="`/storage/${project.cover_path}`"
                        :alt="`Couverture de ${project.name}`" class="h-full w-full object-cover" />

                    <span v-else class="text-5xl font-black text-primary/50">
                        {{ project.name.trim().charAt(0).toUpperCase() }}
                    </span>
                </div>

                <div class="p-5">
                    <div class="flex flex-wrap gap-2">
                        <span class="rounded-full bg-muted px-3 py-1 text-xs">
                            {{ statusLabel(project.status) }}
                        </span>

                        <span class="rounded-full px-3 py-1 text-xs font-semibold" :class="project.visibility === 'public'
                            ? 'bg-emerald-500/10 text-emerald-600'
                            : 'bg-amber-500/10 text-amber-600'
                            ">
                            {{ project.visibility === 'public' ? 'Public' : 'Privé' }}
                        </span>
                    </div>

                    <h2 class="mt-4 text-lg font-bold">
                        {{ project.name }}
                    </h2>

                    <p class="mt-2 line-clamp-3 text-sm leading-6 text-muted-foreground">
                        {{
                            project.short_description ||
                            project.description
                        }}
                    </p>

                    <p class="mt-4 text-xs text-muted-foreground">
                        {{ project.members_count }}
                        {{ project.members_count === 1 ? 'membre' : 'membres' }}
                    </p>

                    <div class="mt-5 flex flex-wrap gap-2 border-t pt-4">
                        <Link :href="`/projects/${project.slug}`"
                            class="rounded-lg border px-3 py-2 text-sm font-semibold hover:bg-muted">
                            Voir
                        </Link>

                        <Link :href="`/projects/${project.slug}/edit`"
                            class="rounded-lg bg-primary px-3 py-2 text-sm font-semibold text-primary-foreground hover:opacity-90">
                            Modifier
                        </Link>

                        <button type="button"
                            class="rounded-lg px-3 py-2 text-sm font-semibold text-destructive hover:bg-destructive/10"
                            @click="deleteProject(project)">
                            Supprimer
                        </button>
                    </div>
                </div>
            </article>
        </div>

        <div v-else class="mt-9 rounded-2xl border border-dashed px-6 py-16 text-center">
            <div class="text-4xl" aria-hidden="true">🚀</div>

            <h2 class="mt-4 text-xl font-bold">
                Tu n'as pas encore de projet
            </h2>

            <p class="mt-2 text-muted-foreground">
                Présente ta première réalisation à la communauté.
            </p>

            <Link href="/projects/create"
                class="mt-6 inline-flex rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-primary-foreground hover:opacity-90">
                Créer mon premier projet
            </Link>
        </div>

        <nav v-if="projects.last_page > 1" class="mt-8 flex flex-wrap justify-center gap-2" aria-label="Pagination">
            <button v-for="(link, index) in projects.links" :key="`${index}-${link.label}`" type="button"
                :disabled="!link.url" class="rounded-lg border px-3 py-2 text-sm disabled:opacity-40"
                :class="link.active ? 'bg-primary text-primary-foreground' : 'bg-background hover:bg-muted'"
                @click="visitPage(link.url)" v-html="link.label" />
        </nav>
    </main>
</template>
