<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { AlertTriangle, Trash2, X } from '@lucide/vue';
import { ref } from 'vue';

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

function confirmDeleteProject(project: Project): void {
    projectToDelete.value = project;
}

function cancelDeleteProject(): void {
    if (isDeleting.value) return;

    projectToDelete.value = null;
}

const projectToDelete = ref<Project | null>(null);
const isDeleting = ref(false);

function deleteProject(): void {
    const project = projectToDelete.value;

    if (!project || isDeleting.value) return;

    isDeleting.value = true;

    router.delete(`/projects/${project.slug}`, {
        preserveScroll: true,
        onSuccess: () => {
            projectToDelete.value = null;
        },
        onFinish: () => {
            isDeleting.value = false;
        },
    });
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

    <Head title="Mes projets" />

    <main class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 sm:py-10 lg:px-8 lg:py-12">
        <!-- En-tête -->
        <header class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0">
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-primary sm:text-sm">
                    Espace personnel
                </p>

                <h1 class="mt-2 text-2xl font-extrabold tracking-tight sm:mt-3 sm:text-3xl lg:text-4xl">
                    Mes projets
                </h1>

                <p class="mt-2 max-w-2xl text-sm leading-6 text-muted-foreground sm:mt-3 sm:text-base">
                    Gère tes projets, leur visibilité et leur évolution sur DevColab.
                </p>

                <p v-if="projects.total > 0" class="mt-3 text-sm text-muted-foreground">
                    {{ projects.total }}
                    {{ projects.total === 1 ? 'projet au total' : 'projets au total' }}
                </p>
            </div>

            <Link href="/projects/create"
                class="inline-flex min-h-11 w-full shrink-0 items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-primary-foreground shadow-sm transition hover:opacity-90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 sm:w-auto">
                <span class="text-lg leading-none" aria-hidden="true">+</span>
                Nouveau projet
            </Link>
        </header>

        <!-- Liste des projets -->
        <section v-if="projects.data.length"
            class="mt-7 grid grid-cols-1 gap-4 sm:mt-9 sm:grid-cols-2 sm:gap-5 xl:grid-cols-3"
            aria-label="Liste de mes projets">
            <article v-for="project in projects.data" :key="project.id"
                class="group flex min-w-0 flex-col overflow-hidden rounded-2xl border border-border/70 bg-card shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-lg">
                <!-- Couverture -->
                <Link :href="`/projects/${project.slug}`"
                    class="relative block h-40 overflow-hidden bg-muted/50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-primary sm:h-44"
                    :aria-label="`Voir le projet ${project.name}`">
                    <img v-if="project.cover_path" :src="`/storage/${project.cover_path}`"
                        :alt="`Couverture de ${project.name}`"
                        class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                        loading="lazy" />

                    <div v-else
                        class="flex h-full w-full items-center justify-center bg-linear-to-br from-primary/10 via-muted to-primary/5">
                        <span
                            class="flex h-16 w-16 items-center justify-center rounded-2xl border border-primary/15 bg-background/80 text-3xl font-black text-primary shadow-sm sm:h-20 sm:w-20 sm:text-4xl"
                            aria-hidden="true">
                            {{ project.name.trim().charAt(0).toUpperCase() || '?' }}
                        </span>
                    </div>

                    <div class="absolute inset-x-0 bottom-0 h-16 bg-linear-to-t from-black/30 to-transparent"
                        aria-hidden="true" />

                    <span
                        class="absolute bottom-3 left-3 rounded-full border border-white/20 bg-black/55 px-3 py-1 text-xs font-medium text-white backdrop-blur-sm">
                        {{ project.members_count }}
                        {{ project.members_count === 1 ? 'membre' : 'membres' }}
                    </span>
                </Link>

                <!-- Contenu -->
                <div class="flex flex-1 flex-col p-4 sm:p-5">
                    <div class="flex flex-wrap items-center gap-2">
                        <span
                            class="inline-flex max-w-full items-center rounded-full bg-muted px-3 py-1 text-xs font-medium text-foreground">
                            {{ statusLabel(project.status) }}
                        </span>

                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold" :class="project.visibility === 'public'
                            ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'
                            : 'bg-amber-500/10 text-amber-600 dark:text-amber-400'
                            ">
                            <span class="mr-1.5 h-1.5 w-1.5 rounded-full" :class="project.visibility === 'public'
                                ? 'bg-emerald-500'
                                : 'bg-amber-500'
                                " aria-hidden="true" />
                            {{ project.visibility === 'public' ? 'Public' : 'Privé' }}
                        </span>
                    </div>

                    <h2 class="mt-4 wrap-break-word text-lg font-bold leading-snug sm:text-xl">
                        <Link :href="`/projects/${project.slug}`"
                            class="rounded-sm transition hover:text-primary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary">
                            {{ project.name }}
                        </Link>
                    </h2>

                    <p class="mt-2 line-clamp-3 wrap-break-word text-sm leading-6 text-muted-foreground">
                        {{ project.short_description || project.description || 'Aucune description disponible.' }}
                    </p>

                    <div v-if="project.looking_for_collaborators"
                        class="mt-4 inline-flex w-fit max-w-full items-center gap-2 rounded-lg bg-primary/5 px-3 py-2 text-xs font-medium text-primary">
                        <span class="h-2 w-2 shrink-0 rounded-full bg-primary" aria-hidden="true" />
                        <span>Recherche des collaborateurs</span>
                    </div>

                    <!-- Actions -->
                    <div class="mt-auto grid grid-cols-2 gap-2 border-t border-border/70 pt-4"
                        :class="project.looking_for_collaborators ? 'mt-4' : 'mt-6'">
                        <Link :href="`/projects/${project.slug}`"
                            class="inline-flex min-h-10 items-center justify-center rounded-lg border border-border px-3 py-2 text-sm font-semibold transition hover:bg-muted focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2">
                            Voir
                        </Link>

                        <Link :href="`/projects/${project.slug}/edit`"
                            class="inline-flex min-h-10 items-center justify-center rounded-lg bg-primary px-3 py-2 text-sm font-semibold text-primary-foreground transition hover:opacity-90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2">
                            Modifier
                        </Link>

                        <button type="button"
                            class="col-span-2 inline-flex min-h-10 items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold text-destructive transition hover:bg-destructive/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-destructive focus-visible:ring-offset-2"
                            @click="confirmDeleteProject(project)">
                            <Trash2 class="h-4 w-4" />
                            Supprimer le projet
                        </button>
                    </div>
                </div>
            </article>
        </section>

        <!-- État vide -->
        <section v-else
            class="mt-8 rounded-2xl border border-dashed border-border px-5 py-12 text-center sm:mt-10 sm:px-8 sm:py-16">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-primary/10 text-3xl sm:h-20 sm:w-20 sm:text-4xl"
                aria-hidden="true">
                🚀
            </div>

            <h2 class="mt-5 text-xl font-bold sm:text-2xl">
                Tu n'as pas encore de projet
            </h2>

            <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-muted-foreground sm:text-base">
                Présente ta première réalisation à la communauté DevColab et trouve
                des développeurs avec qui collaborer.
            </p>

            <Link href="/projects/create"
                class="mt-6 inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-primary-foreground transition hover:opacity-90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 sm:w-auto">
                Créer mon premier projet
            </Link>
        </section>

        <!-- Pagination -->
        <nav v-if="projects.last_page > 1" class="mt-8 flex flex-col items-center gap-4 sm:mt-10"
            aria-label="Pagination des projets">
            <p class="text-center text-sm text-muted-foreground">
                Page {{ projects.current_page }} sur {{ projects.last_page }}
            </p>

            <div class="flex max-w-full flex-wrap items-center justify-center gap-1.5 sm:gap-2">
                <template v-for="(link, index) in projects.links" :key="`${index}-${link.label}`">
                    <span v-if="!link.url"
                        class="inline-flex min-h-10 min-w-10 items-center justify-center rounded-lg border border-transparent px-3 text-sm text-muted-foreground/50"
                        v-html="link.label" />

                    <button v-else type="button" :aria-current="link.active ? 'page' : undefined" :aria-label="link.label.includes('Previous')
                        ? 'Page précédente'
                        : link.label.includes('Next')
                            ? 'Page suivante'
                            : `Aller à la page ${link.label}`
                        "
                        class="inline-flex min-h-10 min-w-10 items-center justify-center rounded-lg border px-3 text-sm font-medium transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2"
                        :class="link.active
                            ? 'border-primary bg-primary text-primary-foreground'
                            : 'border-border bg-background hover:bg-muted'
                            " @click="visitPage(link.url)">
                        <span v-html="link.label" />
                    </button>
                </template>
            </div>
        </nav>
    </main>

    <!-- Boîte de dialogue de confirmation -->
    <Teleport to="body">
        <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0"
            enter-to-class="opacity-100" leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="projectToDelete"
                class="fixed inset-0 z-100 flex items-center justify-center overflow-y-auto bg-black/60 p-4 backdrop-blur-sm"
                @click.self="cancelDeleteProject" @keydown.esc="cancelDeleteProject">
                <Transition appear enter-active-class="transition duration-200 ease-out"
                    enter-from-class="translate-y-4 scale-95 opacity-0"
                    enter-to-class="translate-y-0 scale-100 opacity-100"
                    leave-active-class="transition duration-150 ease-in"
                    leave-from-class="translate-y-0 scale-100 opacity-100"
                    leave-to-class="translate-y-2 scale-95 opacity-0">
                    <section v-if="projectToDelete" role="alertdialog" aria-modal="true"
                        aria-labelledby="delete-project-title" aria-describedby="delete-project-description"
                        class="relative my-auto w-full max-w-md overflow-hidden rounded-2xl border border-border bg-card p-5 shadow-2xl sm:p-6">
                        <!-- Bouton de fermeture -->
                        <button type="button" aria-label="Fermer la boîte de dialogue" :disabled="isDeleting"
                            class="absolute right-3 top-3 inline-flex h-9 w-9 items-center justify-center rounded-lg text-muted-foreground transition hover:bg-muted hover:text-foreground disabled:cursor-not-allowed disabled:opacity-50 sm:right-4 sm:top-4"
                            @click="cancelDeleteProject">
                            <X class="h-5 w-5" />
                        </button>

                        <!-- Icône -->
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-destructive/10 text-destructive">
                            <AlertTriangle class="h-6 w-6" />
                        </div>

                        <!-- Texte -->
                        <h2 id="delete-project-title" class="mt-5 pr-8 text-xl font-bold tracking-tight sm:text-2xl">
                            Supprimer ce projet ?
                        </h2>

                        <p id="delete-project-description"
                            class="mt-3 wrap-break-word text-sm leading-6 text-muted-foreground">
                            Tu es sur le point de supprimer le projet
                            <span class="font-semibold text-foreground">
                                « {{ projectToDelete.name }} »
                            </span>.
                            Cette action retirera le projet du catalogue DevColab.
                            Veux-tu vraiment continuer ?
                        </p>

                        <!-- Actions -->
                        <div class="mt-7 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                            <button type="button" :disabled="isDeleting"
                                class="inline-flex min-h-11 items-center justify-center rounded-xl border border-border px-5 py-3 text-sm font-semibold transition hover:bg-muted focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                @click="cancelDeleteProject">
                                Annuler
                            </button>

                            <button type="button" :disabled="isDeleting"
                                class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-destructive px-5 py-3 text-sm font-semibold text-white transition hover:bg-destructive/90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-destructive focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60"
                                @click="deleteProject">
                                <span v-if="isDeleting"
                                    class="h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"
                                    aria-hidden="true" />

                                <Trash2 v-else class="h-4 w-4" />

                                {{ isDeleting ? 'Suppression…' : 'Supprimer définitivement' }}
                            </button>
                        </div>
                    </section>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>
