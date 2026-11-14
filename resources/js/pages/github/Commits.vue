<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    GitBranch,
    GitCommit,
    ExternalLink,
    ChevronLeft,
    ChevronRight,
    Clock3,
    UserRound,
    ArrowLeft,
} from '@lucide/vue';

interface Project {
    id: number;
    name: string;
    slug: string;
}

interface Repository {
    id: number;
    full_name: string;
    repository_url: string;
    default_branch: string | null;
}

interface Commit {
    sha: string;
    short_sha: string;
    message: string;
    author_name: string;
    author_login: string | null;
    author_avatar: string | null;
    committed_at: string | null;
    html_url: string | null;
}

interface Pagination {
    current_page: number;
    has_next_page: boolean;
    has_previous_page: boolean;
}

const props = defineProps<{
    project: Project;
    repository: Repository;
    commits: Commit[];
    pagination: Pagination;
}>();

function formatDate(date: string | null): string {
    if (!date) {
        return 'Date inconnue';
    }

    const parsedDate = new Date(date);

    if (Number.isNaN(parsedDate.getTime())) {
        return 'Date inconnue';
    }

    return new Intl.DateTimeFormat('fr-FR', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(parsedDate);
}

function goToPage(page: number) {
    router.get(
        `/projects/${props.project.slug}/repositories/${props.repository.id}/commits`,
        { page },
        {
            preserveScroll: true,
            preserveState: false,
        },
    );
}
</script>

<template>

    <Head :title="`Historique des commits — ${repository.full_name} — DevColab`" />

    <main class="mx-auto min-h-screen max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        <!-- Navigation -->
        <div class="mb-8 flex flex-wrap items-center gap-2 text-sm text-muted-foreground">
            <Link href="/projects" class="transition hover:text-primary">
                Projets
            </Link>

            <span>/</span>

            <Link :href="`/projects/${project.slug}`" class="transition hover:text-primary">
                {{ project.name }}
            </Link>

            <span>/</span>

            <span class="text-foreground">Historique des commits</span>
        </div>

        <!-- En-tête -->
        <section class="rounded-3xl border bg-card p-6 shadow-sm sm:p-8">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
                <div class="flex min-w-0 items-start gap-4">
                    <div
                        class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                        <GitCommit class="h-7 w-7" />
                    </div>

                    <div class="min-w-0">
                        <p class="text-sm font-medium text-muted-foreground">
                            Historique des commits
                        </p>

                        <h1 class="mt-1 wrap-break-word text-2xl font-extrabold tracking-tight sm:text-3xl">
                            {{ repository.full_name }}
                        </h1>

                        <p class="mt-2 text-sm text-muted-foreground">
                            Projet :
                            <Link :href="`/projects/${project.slug}`"
                                class="font-semibold text-primary hover:underline">
                                {{ project.name }}
                            </Link>
                        </p>

                        <div class="mt-3 flex flex-wrap items-center gap-2 text-sm text-muted-foreground">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-muted px-3 py-1">
                                <GitBranch class="h-4 w-4" />
                                {{ repository.default_branch || 'Branche par défaut' }}
                            </span>

                            <span class="rounded-full bg-muted px-3 py-1">
                                Page {{ pagination.current_page }}
                            </span>
                        </div>
                    </div>
                </div>

                <a :href="repository.repository_url" target="_blank" rel="noopener noreferrer"
                    class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl border px-4 py-2.5 text-sm font-semibold transition hover:bg-muted">
                    Ouvrir sur GitHub
                    <ExternalLink class="h-4 w-4" />
                </a>
            </div>
        </section>

        <!-- Historique -->
        <section class="mt-8">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-xl font-bold">
                        Commits récents
                    </h2>

                    <p class="mt-1 text-sm text-muted-foreground">
                        Les modifications enregistrées dans le dépôt.
                    </p>
                </div>

                <span class="text-sm text-muted-foreground">
                    {{ commits.length }}
                    {{ commits.length > 1 ? 'commits affichés' : 'commit affiché' }}
                </span>
            </div>

            <div v-if="commits.length" class="overflow-hidden rounded-2xl border bg-card">
                <article v-for="(commit, index) in commits" :key="commit.sha"
                    class="flex gap-4 p-5 transition hover:bg-muted/40 sm:p-6" :class="{
                        'border-b': index < commits.length - 1,
                    }">
                    <div class="flex flex-col items-center">
                        <img v-if="commit.author_avatar" :src="commit.author_avatar"
                            :alt="commit.author_login || commit.author_name"
                            class="h-10 w-10 rounded-full border object-cover" loading="lazy" />

                        <div v-else
                            class="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10 text-primary">
                            <UserRound class="h-5 w-5" />
                        </div>

                        <div v-if="index < commits.length - 1" class="mt-3 min-h-6 w-px flex-1 bg-border"></div>
                    </div>

                    <div class="min-w-0 flex-1">
                        <a v-if="commit.html_url" :href="commit.html_url" target="_blank" rel="noopener noreferrer"
                            class="block wrap-break-word font-semibold leading-6 text-foreground transition hover:text-primary">
                            {{ commit.message.split('\n')[0] }}
                        </a>

                        <p v-else class="wrap-break-word font-semibold leading-6 text-foreground">
                            {{ commit.message.split('\n')[0] }}
                        </p>

                        <p v-if="commit.message.includes('\n')"
                            class="mt-2 whitespace-pre-line wrap-break-word text-sm leading-6 text-muted-foreground">
                            {{ commit.message.split('\n').slice(1).join('\n').trim() }}
                        </p>

                        <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-muted-foreground">
                            <span class="inline-flex items-center gap-1.5">
                                <UserRound class="h-4 w-4" />

                                <span class="font-medium text-foreground">
                                    {{ commit.author_login ? `@${commit.author_login}` : commit.author_name }}
                                </span>
                            </span>

                            <span class="inline-flex items-center gap-1.5">
                                <Clock3 class="h-4 w-4" />
                                {{ formatDate(commit.committed_at) }}
                            </span>
                        </div>

                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            <code class="rounded-md border bg-muted px-2 py-1 font-mono text-xs text-muted-foreground">
                                {{ commit.short_sha }}
                            </code>

                            <a v-if="commit.html_url" :href="commit.html_url" target="_blank" rel="noopener noreferrer"
                                class="inline-flex items-center gap-1 text-xs font-semibold text-primary hover:underline">
                                Voir le commit
                                <ExternalLink class="h-3.5 w-3.5" />
                            </a>
                        </div>
                    </div>
                </article>
            </div>

            <!-- Aucun commit -->
            <div v-else class="rounded-2xl border border-dashed px-6 py-14 text-center">
                <GitCommit class="mx-auto h-10 w-10 text-muted-foreground" />

                <h3 class="mt-4 text-lg font-bold">
                    Aucun commit à afficher
                </h3>

                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-muted-foreground">
                    Ce dépôt ne contient aucun commit sur la page demandée,
                    ou son historique n'est pas accessible.
                </p>

                <a :href="repository.repository_url" target="_blank" rel="noopener noreferrer"
                    class="mt-5 inline-flex items-center gap-2 rounded-xl border px-4 py-2.5 text-sm font-semibold transition hover:bg-muted">
                    Vérifier sur GitHub
                    <ExternalLink class="h-4 w-4" />
                </a>
            </div>
        </section>

        <!-- Pagination -->
        <nav v-if="commits.length" class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
            aria-label="Pagination de l'historique des commits">
            <button type="button" :disabled="!pagination.has_previous_page"
                class="inline-flex items-center justify-center gap-2 rounded-xl border px-4 py-3 text-sm font-semibold transition hover:bg-muted disabled:cursor-not-allowed disabled:opacity-40"
                @click="goToPage(pagination.current_page - 1)">
                <ChevronLeft class="h-4 w-4" />
                Page précédente
            </button>

            <span class="text-center text-sm text-muted-foreground">
                Page {{ pagination.current_page }}
            </span>

            <button type="button" :disabled="!pagination.has_next_page"
                class="inline-flex items-center justify-center gap-2 rounded-xl border px-4 py-3 text-sm font-semibold transition hover:bg-muted disabled:cursor-not-allowed disabled:opacity-40"
                @click="goToPage(pagination.current_page + 1)">
                Page suivante
                <ChevronRight class="h-4 w-4" />
            </button>
        </nav>

        <!-- Retour -->
        <div class="mt-8">
            <Link :href="`/projects/${project.slug}`"
                class="inline-flex items-center gap-2 text-sm font-semibold text-primary transition hover:underline">
                <ArrowLeft class="h-4 w-4" />
                Retour au projet
            </Link>
        </div>
    </main>
</template>
