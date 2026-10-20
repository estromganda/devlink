<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';

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

const props = defineProps<{
    posts: {
        data: Post[];
        links: Array<{
            url: string | null;
            label: string;
            active: boolean;
        }>;
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

    return new Intl.DateTimeFormat('fr-FR', {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(new Date(date));
}

function getInitial(name: string) {
    return name.trim().charAt(0).toUpperCase() || '?';
}
</script>

<template>

    <Head title="Fil d'actualité" />

    <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6">
        <header class="mb-8 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">
                    Fil d'actualité
                </h1>

                <p class="mt-2 text-muted-foreground">
                    Les idées, questions et projets des développeurs.
                </p>
            </div>

            <Link v-if="auth.user" href="/posts/create"
                class="rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-primary-foreground transition hover:opacity-90">
                + Nouvelle publication
            </Link>

            <Link v-else href="/login"
                class="rounded-lg border px-5 py-2.5 text-sm font-semibold transition hover:bg-muted">
                Se connecter pour publier
            </Link>
        </header>

        <div v-if="posts.data.length" class="space-y-5">
            <article v-for="post in posts.data" :key="post.id" class="rounded-2xl border bg-card p-5 shadow-sm sm:p-6">
                <div class="flex items-start gap-3">
                    <div
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-muted text-sm font-bold">
                        {{ getInitial(post.user.name) }}
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                            <span class="font-semibold">
                                {{ post.user.name }}
                            </span>

                            <span class="text-xs text-muted-foreground">
                                · {{ formatDate(post.published_at ?? post.created_at) }}
                            </span>
                        </div>

                        <Link v-if="post.community" :href="`/communities/${post.community.slug}`"
                            class="mt-1 inline-block text-sm text-primary hover:underline">
                            {{ post.community.name }}
                        </Link>
                    </div>
                </div>

                <Link :href="`/posts/${post.id}`" class="mt-4 block">
                    <h2 v-if="post.title" class="text-xl font-bold leading-snug">
                        {{ post.title }}
                    </h2>

                    <p class="mt-2 whitespace-pre-wrap wrap-break-word text-sm leading-7 text-foreground/90">
                        {{ post.body }}
                    </p>

                    <span class="mt-3 inline-block text-sm font-medium text-primary">
                        Lire la publication →
                    </span>
                </Link>

                <div class="mt-5 flex flex-wrap items-center gap-5 border-t pt-4">
                    <button type="button" class="text-sm font-medium transition hover:text-primary"
                        :class="post.liked_by_me ? 'text-primary' : 'text-muted-foreground'" @click="toggleLike(post)">
                        {{ post.liked_by_me ? '♥ J’aime' : '♡ J’aime' }}
                        · {{ post.likes_count }}
                    </button>

                    <Link :href="`/posts/${post.id}`"
                        class="text-sm text-muted-foreground transition hover:text-primary">
                        Commentaires · {{ post.comments_count }}
                    </Link>
                </div>
            </article>

            <nav v-if="posts.last_page > 1" aria-label="Pagination des publications"
                class="flex flex-wrap justify-center gap-2 pt-4">
                <template v-for="(link, index) in posts.links" :key="index">
                    <Link v-if="link.url" :href="link.url"
                        class="rounded-lg border px-3 py-2 text-sm transition hover:bg-muted" :class="{
                            'bg-primary text-primary-foreground': link.active,
                        }">
                        <span v-html="link.label"></span>
                    </Link>

                    <span v-else class="rounded-lg border px-3 py-2 text-sm text-muted-foreground opacity-50">
                        <span v-html="link.label"></span>
                    </span>
                </template>
            </nav>
        </div>

        <div v-else class="rounded-2xl border border-dashed px-6 py-16 text-center">
            <h2 class="text-xl font-semibold">
                Aucune publication pour le moment
            </h2>

            <p class="mt-2 text-muted-foreground">
                Sois le premier à partager quelque chose avec la communauté.
            </p>

            <Link v-if="auth.user" href="/posts/create"
                class="mt-5 inline-flex rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-primary-foreground transition hover:opacity-90">
                Créer la première publication
            </Link>
        </div>
    </div>
</template>