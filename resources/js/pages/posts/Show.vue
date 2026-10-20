<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';

type User = {
    id: number;
    name: string;
};

type Community = {
    id: number;
    name: string;
    slug: string;
};

type Comment = {
    id: number;
    body: string;
    created_at: string;
    user: User;
};

type Post = {
    id: number;
    title: string | null;
    body: string;
    created_at: string;
    published_at: string | null;
    user: User;
    community: Community | null;
    comments: Comment[];
    comments_count: number;
    likes_count: number;
    liked_by_me: boolean;
};

const props = defineProps<{
    post: Post;
}>();

const page = usePage();

const auth = computed(() => page.props.auth as {
    user: User | null;
});

const commentForm = useForm({
    body: '',
});

function submitComment() {
    commentForm.post(`/posts/${props.post.id}/comments`, {
        preserveScroll: true,
        onSuccess: () => commentForm.reset(),
    });
}

function toggleLike() {
    if (!auth.value.user) {
        router.visit('/login');
        return;
    }

    if (props.post.liked_by_me) {
        router.delete(`/posts/${props.post.id}/like`, {
            preserveScroll: true,
        });
    } else {
        router.post(`/posts/${props.post.id}/like`, {}, {
            preserveScroll: true,
        });
    }
}

function formatDate(date: string) {
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

    <Head :title="post.title || `Publication de ${post.user.name}`" />

    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6">
        <Link href="/posts" class="text-sm text-muted-foreground transition hover:text-foreground">
            ← Retour au fil d'actualité
        </Link>

        <article class="mt-6 rounded-2xl border bg-card p-5 shadow-sm sm:p-7">
            <div class="flex items-start gap-3">
                <div
                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-muted text-lg font-bold">
                    {{ getInitial(post.user.name) }}
                </div>

                <div class="min-w-0 flex-1">
                    <p class="font-semibold">
                        {{ post.user.name }}
                    </p>

                    <p class="mt-1 text-xs text-muted-foreground">
                        {{ formatDate(post.published_at ?? post.created_at) }}
                    </p>

                    <Link v-if="post.community" :href="`/communities/${post.community.slug}`"
                        class="mt-2 inline-block text-sm text-primary hover:underline">
                        {{ post.community.name }}
                    </Link>
                </div>
            </div>

            <h1 v-if="post.title" class="mt-6 text-2xl font-bold leading-tight tracking-tight sm:text-3xl">
                {{ post.title }}
            </h1>

            <p class="mt-5 whitespace-pre-wrap wrap-break-word text-base leading-8">
                {{ post.body }}
            </p>

            <div class="mt-6 flex items-center gap-5 border-t pt-4">
                <button type="button" class="text-sm font-medium transition hover:text-primary"
                    :class="post.liked_by_me ? 'text-primary' : 'text-muted-foreground'" @click="toggleLike">
                    {{ post.liked_by_me ? '♥ J’aime' : '♡ J’aime' }}
                    · {{ post.likes_count }}
                </button>

                <span class="text-sm text-muted-foreground">
                    {{ post.comments_count }}
                    {{ post.comments_count > 1 ? 'commentaires' : 'commentaire' }}
                </span>
            </div>
        </article>

        <section class="mt-8">
            <h2 class="text-xl font-bold">
                Commentaires
                <span class="text-muted-foreground">
                    ({{ post.comments_count }})
                </span>
            </h2>

            <form v-if="auth.user" class="mt-5 rounded-2xl border bg-card p-5" @submit.prevent="submitComment">
                <label for="comment-body" class="mb-2 block text-sm font-medium">
                    Ajouter un commentaire
                </label>

                <textarea id="comment-body" v-model="commentForm.body" rows="4" maxlength="5000" required
                    placeholder="Partage ton avis ou aide à répondre à la question…"
                    class="w-full resize-y rounded-lg border bg-background px-4 py-3 outline-none transition focus:ring-2 focus:ring-ring"></textarea>

                <p v-if="commentForm.errors.body" class="mt-2 text-sm text-destructive">
                    {{ commentForm.errors.body }}
                </p>

                <div class="mt-3 flex justify-end">
                    <button type="submit" :disabled="commentForm.processing || !commentForm.body.trim()"
                        class="rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-primary-foreground transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-50">
                        {{
                            commentForm.processing
                                ? 'Envoi en cours…'
                                : 'Commenter'
                        }}
                    </button>
                </div>
            </form>

            <div v-else class="mt-5 rounded-xl border border-dashed p-5 text-sm text-muted-foreground">
                <Link href="/login" class="font-semibold text-primary hover:underline">
                    Connecte-toi
                </Link>
                pour participer à la discussion.
            </div>

            <div v-if="post.comments.length" class="mt-6 space-y-4">
                <article v-for="comment in post.comments" :key="comment.id"
                    class="rounded-xl border bg-card p-4 sm:p-5">
                    <div class="flex items-start gap-3">
                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-muted text-sm font-bold">
                            {{ getInitial(comment.user.name) }}
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                                <span class="text-sm font-semibold">
                                    {{ comment.user.name }}
                                </span>

                                <span class="text-xs text-muted-foreground">
                                    {{ formatDate(comment.created_at) }}
                                </span>
                            </div>

                            <p class="mt-2 whitespace-pre-wrap wrap-break-word text-sm leading-7">
                                {{ comment.body }}
                            </p>
                        </div>
                    </div>
                </article>
            </div>

            <p v-else class="mt-6 rounded-xl border border-dashed p-8 text-center text-sm text-muted-foreground">
                Aucun commentaire pour le moment. Lance la discussion !
            </p>
        </section>
    </div>
</template>