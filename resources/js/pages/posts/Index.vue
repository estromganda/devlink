<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import {
    ArrowRight,
    ArrowUpRight,
    Heart,
    MessageCircle,
    Plus,
    Newspaper,
    Users,
    Sparkles,
    Code2,
    Clock3,
    ChevronLeft,
    ChevronRight,
} from '@lucide/vue';

type User = {
    id: number;
    name: string;
};

type Community = {
    id: number;
    name: string;
    slug: string;
};

type Post = {
    id: number;
    title: string | null;
    body: string;
    created_at: string;
    published_at: string | null;
    user: User;
    community: Community | null;
    comments_count: number;
    likes_count: number;
    liked_by_me: boolean;
};

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

const props = defineProps<{
    posts: {
        data: Post[];
        links: PaginationLink[];
        current_page: number;
        last_page: number;
        total: number;
    };
}>();

const page = usePage();

const auth = computed(() => page.props.auth as {
    user: User | null;
});

function toggleLike(post: Post) {
    if (!auth.value.user) {
        router.visit('/login');
        return;
    }

    if (post.liked_by_me) {
        router.delete(`/posts/${post.id}/like`, {
            preserveScroll: true,
        });
    } else {
        router.post(`/posts/${post.id}/like`, {}, {
            preserveScroll: true,
        });
    }
}

function formatDate(date: string | null) {
    if (!date) return '';

    const parsedDate = new Date(date);

    if (Number.isNaN(parsedDate.getTime())) return '';

    return new Intl.DateTimeFormat('fr-FR', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(parsedDate);
}

function getInitial(name: string) {
    return name.trim().charAt(0).toUpperCase() || '?';
}

function paginationLabel(label: string) {
    const normalized = label.toLowerCase();

    if (normalized.includes('previous') || normalized.includes('précédent')) {
        return 'Précédent';
    }

    if (normalized.includes('next') || normalized.includes('suivant')) {
        return 'Suivant';
    }

    return label.replace(/&laquo;|&raquo;|«|»/g, '').trim();
}
</script>

<template>

    <Head title="Fil d’actualité" />

    <main class="min-h-screen bg-background">
        <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:py-10">
            <!-- En-tête -->
            <header class="relative mb-8 overflow-hidden rounded-3xl border bg-card p-6 shadow-sm sm:p-8">
                <div
                    class="pointer-events-none absolute -right-12 -top-16 size-56 rounded-full bg-primary/10 blur-3xl" />
                <div
                    class="pointer-events-none absolute -bottom-20 right-1/4 size-48 rounded-full bg-violet-500/10 blur-3xl" />

                <div class="relative flex flex-col justify-between gap-6 sm:flex-row sm:items-center">
                    <div class="min-w-0">
                        <div
                            class="mb-4 inline-flex items-center gap-2 rounded-full border bg-background/80 px-3 py-1.5 text-xs font-semibold text-primary">
                            <Sparkles class="size-3.5" />
                            <span>La communauté DevColab</span>
                        </div>

                        <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">
                            Fil d’actualité<span class="text-primary">.</span>
                        </h1>

                        <p class="mt-3 max-w-xl text-sm leading-6 text-muted-foreground sm:text-base">
                            Les idées, questions et projets des développeurs.
                            Partage tes connaissances et découvre ce que la communauté construit.
                        </p>

                        <div class="mt-5 flex flex-wrap items-center gap-4 text-sm text-muted-foreground">
                            <div class="flex items-center gap-2">
                                <Newspaper class="size-4 text-primary" />
                                <span>
                                    {{ posts.total }}
                                    publication{{ posts.total > 1 ? 's' : '' }}
                                </span>
                            </div>

                            <div class="flex items-center gap-2">
                                <Users class="size-4 text-primary" />
                                <span>Une communauté de développeurs</span>
                            </div>
                        </div>
                    </div>

                    <div class="relative shrink-0">
                        <Link v-if="auth.user" href="/posts/create"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-primary-foreground shadow-md shadow-primary/20 transition hover:-translate-y-0.5 hover:opacity-90 sm:w-auto">
                            <Plus class="size-4" />
                            Nouvelle publication
                        </Link>

                        <Link v-else href="/login"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl border bg-background px-5 py-3 text-sm font-semibold transition hover:bg-muted sm:w-auto">
                            Se connecter pour publier
                            <ArrowRight class="size-4" />
                        </Link>
                    </div>
                </div>
            </header>

            <!-- Fil de publications -->
            <section class="grid grid-cols-1 items-start gap-6 lg:grid-cols-[minmax(0,1fr)_280px]">
                <div class="min-w-0">
                    <div class="mb-5 flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-bold tracking-tight">
                                Les dernières publications
                            </h2>
                            <p class="mt-1 text-sm text-muted-foreground">
                                Les dernières actualités de la communauté.
                            </p>
                        </div>

                        <div v-if="posts.data.length"
                            class="shrink-0 rounded-lg border bg-card px-3 py-1.5 text-xs font-medium text-muted-foreground">
                            Page {{ posts.current_page }} / {{ posts.last_page }}
                        </div>
                    </div>

                    <div v-if="posts.data.length" class="space-y-5">
                        <article v-for="post in posts.data" :key="post.id"
                            class="group overflow-hidden rounded-2xl border bg-card shadow-sm transition duration-200 hover:border-primary/30 hover:shadow-md">
                            <div class="p-5 sm:p-6">
                                <!-- Auteur -->
                                <div class="flex items-start gap-3">
                                    <div
                                        class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-primary/10 text-base font-bold text-primary ring-1 ring-primary/10">
                                        {{ getInitial(post.user.name) }}
                                    </div>

                                    <div class="min-w-0 flex-1 pt-0.5">
                                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                                            <span class="truncate text-sm font-semibold">
                                                {{ post.user.name }}
                                            </span>

                                            <span class="inline-flex items-center gap-1 text-xs text-muted-foreground">
                                                <Clock3 class="size-3" />
                                                {{ formatDate(post.published_at ?? post.created_at) }}
                                            </span>
                                        </div>

                                        <Link v-if="post.community" :href="`/communities/${post.community.slug}`"
                                            class="mt-1 inline-flex max-w-full items-center gap-1.5 text-xs font-medium text-primary transition hover:underline">
                                            <Users class="size-3.5 shrink-0" />
                                            <span class="truncate">
                                                {{ post.community.name }}
                                            </span>
                                        </Link>

                                        <span v-else
                                            class="mt-1 inline-flex items-center gap-1.5 text-xs text-muted-foreground">
                                            <Code2 class="size-3.5" />
                                            Communauté DevColab
                                        </span>
                                    </div>
                                </div>

                                <!-- Contenu -->
                                <Link :href="`/posts/${post.id}`"
                                    class="mt-5 block rounded-lg outline-none focus-visible:ring-2 focus-visible:ring-primary">
                                    <h3 v-if="post.title"
                                        class="text-lg font-bold leading-snug tracking-tight transition group-hover:text-primary sm:text-xl">
                                        {{ post.title }}
                                    </h3>

                                    <p :class="post.title ? 'mt-2' : ''"
                                        class="whitespace-pre-wrap wrap-break-word text-sm leading-7 text-foreground/85">
                                        {{ post.body }}
                                    </p>

                                    <span
                                        class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-primary">
                                        Lire la publication
                                        <ArrowUpRight
                                            class="size-4 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
                                    </span>
                                </Link>

                                <!-- Actions -->
                                <div class="mt-5 flex flex-wrap items-center gap-3 border-t pt-4 sm:gap-5">
                                    <button type="button"
                                        :aria-label="post.liked_by_me ? 'Retirer le J’aime' : 'Aimer la publication'"
                                        :aria-pressed="post.liked_by_me"
                                        class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-medium transition hover:bg-rose-500/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                                        :class="post.liked_by_me
                                            ? 'bg-rose-500/10 text-rose-500'
                                            : 'text-muted-foreground hover:text-rose-500'
                                            " @click="toggleLike(post)">
                                        <Heart class="size-4 transition-transform"
                                            :class="post.liked_by_me ? 'fill-current scale-110' : ''" />
                                        <span>{{ post.likes_count }}</span>
                                        <span class="hidden sm:inline">
                                            {{ post.liked_by_me ? 'J’aime' : 'J’aime' }}
                                        </span>
                                    </button>

                                    <Link :href="`/posts/${post.id}`"
                                        class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-medium text-muted-foreground transition hover:bg-primary/10 hover:text-primary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary">
                                        <MessageCircle class="size-4" />
                                        <span>{{ post.comments_count }}</span>
                                        <span>Commentaire{{ post.comments_count > 1 ? 's' : '' }}</span>
                                    </Link>
                                </div>
                            </div>
                        </article>

                        <!-- Pagination -->
                        <nav v-if="posts.last_page > 1" aria-label="Pagination des publications"
                            class="flex flex-wrap items-center justify-center gap-2 pt-3">
                            <template v-for="(link, index) in posts.links" :key="`${index}-${link.label}`">
                                <Link v-if="link.url" :href="link.url" :aria-current="link.active ? 'page' : undefined"
                                    class="inline-flex min-h-10 items-center justify-center rounded-xl border px-3.5 text-sm font-medium transition hover:border-primary/40 hover:bg-muted"
                                    :class="link.active
                                        ? 'border-primary bg-primary text-primary-foreground shadow-sm hover:bg-primary hover:text-primary-foreground'
                                        : 'bg-card'
                                        ">
                                    {{ paginationLabel(link.label) }}
                                </Link>

                                <span v-else
                                    class="inline-flex min-h-10 cursor-not-allowed items-center justify-center rounded-xl border bg-muted/40 px-3.5 text-sm text-muted-foreground opacity-60">
                                    {{ paginationLabel(link.label) }}
                                </span>
                            </template>
                        </nav>
                    </div>

                    <!-- État vide -->
                    <div v-else class="rounded-3xl border border-dashed bg-card px-6 py-16 text-center sm:px-10">
                        <div
                            class="mx-auto flex size-16 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                            <Newspaper class="size-8" />
                        </div>

                        <h2 class="mt-5 text-xl font-bold tracking-tight">
                            Le fil est encore calme
                        </h2>

                        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-muted-foreground">
                            Aucune publication pour le moment. Lance la conversation
                            et partage une idée, une découverte ou un projet avec les autres développeurs.
                        </p>

                        <Link v-if="auth.user" href="/posts/create"
                            class="mt-6 inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-primary-foreground shadow-sm transition hover:opacity-90">
                            <Plus class="size-4" />
                            Créer la première publication
                        </Link>

                        <Link v-else href="/login"
                            class="mt-6 inline-flex items-center gap-2 rounded-xl border px-5 py-3 text-sm font-semibold transition hover:bg-muted">
                            Se connecter
                            <ArrowRight class="size-4" />
                        </Link>
                    </div>
                </div>

                <!-- Colonne latérale -->
                <aside class="space-y-5">
                    <div class="rounded-2xl border bg-card p-5 shadow-sm">
                        <div class="flex items-center gap-2">
                            <div class="flex size-9 items-center justify-center rounded-xl bg-primary/10 text-primary">
                                <Sparkles class="size-4" />
                            </div>
                            <h2 class="font-bold">L’esprit DevColab</h2>
                        </div>

                        <p class="mt-4 text-sm leading-6 text-muted-foreground">
                            Un espace conçu par et pour les développeurs afin de partager
                            leurs connaissances, présenter leurs projets et progresser ensemble.
                        </p>

                        <div class="mt-4 space-y-3 border-t pt-4">
                            <div class="flex items-start gap-2.5 text-sm">
                                <Code2 class="mt-0.5 size-4 shrink-0 text-primary" />
                                <span>Partage tes expériences techniques.</span>
                            </div>

                            <div class="flex items-start gap-2.5 text-sm">
                                <MessageCircle class="mt-0.5 size-4 shrink-0 text-primary" />
                                <span>Échange des idées et des conseils.</span>
                            </div>

                            <div class="flex items-start gap-2.5 text-sm">
                                <Users class="mt-0.5 size-4 shrink-0 text-primary" />
                                <span>Connecte-toi à la communauté.</span>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border bg-card p-5 shadow-sm">
                        <div
                            class="flex size-10 items-center justify-center rounded-xl bg-violet-500/10 text-violet-500">
                            <Newspaper class="size-5" />
                        </div>

                        <h2 class="mt-4 font-bold">Une idée à partager ?</h2>

                        <p class="mt-2 text-sm leading-6 text-muted-foreground">
                            Chaque publication peut déclencher une discussion,
                            apporter une solution ou faire naître une collaboration.
                        </p>

                        <Link v-if="auth.user" href="/posts/create"
                            class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl border px-4 py-2.5 text-sm font-semibold transition hover:border-primary/40 hover:bg-primary/5 hover:text-primary">
                            Écrire une publication
                            <ArrowRight class="size-4" />
                        </Link>

                        <Link v-else href="/login"
                            class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl border px-4 py-2.5 text-sm font-semibold transition hover:border-primary/40 hover:bg-primary/5 hover:text-primary">
                            Rejoindre DevColab
                            <ArrowRight class="size-4" />
                        </Link>
                    </div>
                </aside>
            </section>
        </div>
    </main>
</template>