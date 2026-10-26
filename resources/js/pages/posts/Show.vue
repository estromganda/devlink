<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import {
    ArrowLeft,
    ArrowRight,
    ArrowUpRight,
    Heart,
    MessageCircle,
    Send,
    Users,
    Clock3,
    Sparkles,
    Code2,
    MessageSquare,
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
    if (!commentForm.body.trim() || commentForm.processing) return;

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

function formatDate(date: string | null) {
    if (!date) return '';

    const parsedDate = new Date(date);

    if (Number.isNaN(parsedDate.getTime())) return '';

    return new Intl.DateTimeFormat('fr-FR', {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(parsedDate);
}

function getInitial(name: string) {
    return name.trim().charAt(0).toUpperCase() || '?';
}

const postDate = computed(() =>
    formatDate(props.post.published_at ?? props.post.created_at),
);
</script>

<template>

    <Head :title="post.title || `Publication de ${post.user.name}`" />

    <main class="min-h-screen bg-background">
        <div class="mx-auto max-w-6xl px-4 py-6 sm:px-6 sm:py-10">
            <!-- Navigation -->
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                <Link href="/posts"
                    class="inline-flex items-center gap-2 rounded-xl border bg-card px-3.5 py-2.5 text-sm font-medium text-muted-foreground transition hover:border-primary/30 hover:bg-muted hover:text-foreground">
                    <ArrowLeft class="size-4" />
                    Retour au fil d’actualité
                </Link>

                <div class="inline-flex items-center gap-2 text-xs font-medium text-muted-foreground">
                    <Code2 class="size-4 text-primary" />
                    <span>Communauté DevLink</span>
                </div>
            </div>

            <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-[minmax(0,1fr)_280px]">
                <!-- Publication et commentaires -->
                <div class="min-w-0">
                    <article class="overflow-hidden rounded-3xl border bg-card shadow-sm">
                        <!-- Bandeau -->
                        <div
                            class="relative overflow-hidden border-b bg-linear-to-br from-primary/10 via-card to-violet-500/10 px-5 py-6 sm:px-8 sm:py-8">
                            <div
                                class="pointer-events-none absolute -right-10 -top-16 size-48 rounded-full bg-primary/10 blur-3xl" />

                            <div class="relative">
                                <div
                                    class="inline-flex items-center gap-2 rounded-full border bg-background/80 px-3 py-1.5 text-xs font-semibold text-primary">
                                    <Sparkles class="size-3.5" />
                                    Publication DevLink
                                </div>

                                <h1 v-if="post.title"
                                    class="mt-5 wrap-break-word text-2xl font-bold leading-tight tracking-tight sm:text-3xl">
                                    {{ post.title }}
                                </h1>

                                <p v-else class="mt-5 text-xl font-bold tracking-tight sm:text-2xl">
                                    Une idée à partager
                                </p>
                            </div>
                        </div>

                        <div class="p-5 sm:p-8">
                            <!-- Auteur -->
                            <div class="flex items-start gap-3">
                                <div
                                    class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-primary/10 text-base font-bold text-primary ring-1 ring-primary/10">
                                    {{ getInitial(post.user.name) }}
                                </div>

                                <div class="min-w-0 flex-1">
                                    <p class="wrap-break-word text-sm font-bold">
                                        {{ post.user.name }}
                                    </p>

                                    <p
                                        class="mt-1 inline-flex flex-wrap items-center gap-1.5 text-xs text-muted-foreground">
                                        <Clock3 class="size-3.5" />
                                        {{ postDate }}
                                    </p>

                                    <Link v-if="post.community" :href="`/communities/${post.community.slug}`"
                                        class="mt-2 inline-flex max-w-full items-center gap-1.5 rounded-lg bg-primary/5 px-2.5 py-1.5 text-xs font-semibold text-primary transition hover:bg-primary/10">
                                        <Users class="size-3.5 shrink-0" />
                                        <span class="truncate">
                                            {{ post.community.name }}
                                        </span>
                                        <ArrowUpRight class="size-3.5 shrink-0" />
                                    </Link>
                                </div>
                            </div>

                            <!-- Corps de la publication -->
                            <div class="mt-7">
                                <p
                                    class="whitespace-pre-wrap wrap-break-word text-sm leading-8 text-foreground/90 sm:text-base">
                                    {{ post.body }}
                                </p>
                            </div>

                            <!-- Actions -->
                            <div class="mt-8 flex flex-wrap items-center justify-between gap-3 border-t pt-5">
                                <div class="flex flex-wrap items-center gap-2">
                                    <button type="button" :aria-label="post.liked_by_me
                                        ? 'Retirer le J’aime'
                                        : 'Aimer la publication'
                                        " :aria-pressed="post.liked_by_me"
                                        class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                                        :class="post.liked_by_me
                                            ? 'bg-rose-500/10 text-rose-500'
                                            : 'bg-muted/60 text-muted-foreground hover:bg-rose-500/10 hover:text-rose-500'
                                            " @click="toggleLike">
                                        <Heart class="size-4" :class="post.liked_by_me ? 'fill-current' : ''" />
                                        <span>
                                            {{ post.liked_by_me ? 'J’aime' : 'J’aime' }}
                                        </span>
                                        <span>{{ post.likes_count }}</span>
                                    </button>

                                    <a href="#comment-form"
                                        class="inline-flex items-center gap-2 rounded-xl bg-muted/60 px-4 py-2.5 text-sm font-semibold text-muted-foreground transition hover:bg-primary/10 hover:text-primary">
                                        <MessageCircle class="size-4" />
                                        Commenter
                                    </a>
                                </div>

                                <span class="inline-flex items-center gap-1.5 text-xs text-muted-foreground">
                                    <MessageSquare class="size-4" />
                                    {{ post.comments_count }}
                                    commentaire{{ post.comments_count > 1 ? 's' : '' }}
                                </span>
                            </div>
                        </div>
                    </article>

                    <!-- Commentaires -->
                    <section id="comments" class="mt-8">
                        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <h2 class="text-xl font-bold tracking-tight">
                                    La discussion
                                </h2>
                                <p class="mt-1 text-sm text-muted-foreground">
                                    Partage ton avis et échange avec la communauté.
                                </p>
                            </div>

                            <div
                                class="inline-flex items-center gap-2 rounded-xl border bg-card px-3 py-2 text-sm font-semibold">
                                <MessageCircle class="size-4 text-primary" />
                                {{ post.comments_count }}
                            </div>
                        </div>

                        <!-- Formulaire de commentaire -->
                        <form v-if="auth.user" id="comment-form" class="rounded-2xl border bg-card p-5 shadow-sm sm:p-6"
                            @submit.prevent="submitComment">
                            <div class="flex items-start gap-3">
                                <div
                                    class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-sm font-bold text-primary">
                                    {{ getInitial(auth.user.name) }}
                                </div>

                                <div class="min-w-0 flex-1">
                                    <label for="comment-body" class="block text-sm font-semibold">
                                        Ajouter un commentaire
                                    </label>

                                    <p class="mt-1 text-xs leading-5 text-muted-foreground">
                                        Une idée, une question ou un conseil ? La discussion
                                        commence ici.
                                    </p>
                                </div>
                            </div>

                            <textarea id="comment-body" v-model="commentForm.body" rows="4" maxlength="5000" required
                                placeholder="Écris ton commentaire ici…"
                                class="mt-4 w-full resize-y rounded-xl border bg-background px-4 py-3 text-sm leading-6 outline-none transition placeholder:text-muted-foreground/70 focus:border-primary/50 focus:ring-2 focus:ring-primary/20"
                                :aria-invalid="!!commentForm.errors.body" />

                            <p v-if="commentForm.errors.body" class="mt-2 text-sm text-destructive">
                                {{ commentForm.errors.body }}
                            </p>

                            <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
                                <span class="text-xs text-muted-foreground">
                                    {{ commentForm.body.length }} / 5000 caractères
                                </span>

                                <button type="submit" :disabled="commentForm.processing ||
                                    !commentForm.body.trim()
                                    "
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-primary-foreground shadow-sm transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-50">
                                    <span>
                                        {{
                                            commentForm.processing
                                                ? 'Envoi en cours…'
                                                : 'Publier le commentaire'
                                        }}
                                    </span>
                                    <Send v-if="!commentForm.processing" class="size-4" />
                                </button>
                            </div>
                        </form>

                        <!-- Invitation à se connecter -->
                        <div v-else class="rounded-2xl border border-dashed bg-card p-5 sm:p-6">
                            <div class="flex items-start gap-3">
                                <div
                                    class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary">
                                    <MessageCircle class="size-5" />
                                </div>

                                <div class="min-w-0 flex-1">
                                    <h3 class="font-semibold">
                                        Rejoins la discussion
                                    </h3>

                                    <p class="mt-1 text-sm leading-6 text-muted-foreground">
                                        Connecte-toi pour commenter cette publication
                                        et échanger avec les autres développeurs.
                                    </p>

                                    <Link href="/login"
                                        class="mt-4 inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-primary-foreground transition hover:opacity-90">
                                        Se connecter
                                        <ArrowRight class="size-4" />
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <!-- Liste des commentaires -->
                        <div v-if="post.comments.length" class="mt-6 space-y-4">
                            <article v-for="comment in post.comments" :key="comment.id"
                                class="rounded-2xl border bg-card p-4 transition hover:border-primary/20 sm:p-5">
                                <div class="flex items-start gap-3">
                                    <div
                                        class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-muted text-sm font-bold">
                                        {{ getInitial(comment.user.name) }}
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                                            <span class="wrap-break-word text-sm font-bold">
                                                {{ comment.user.name }}
                                            </span>

                                            <span class="inline-flex items-center gap-1 text-xs text-muted-foreground">
                                                <Clock3 class="size-3" />
                                                {{ formatDate(comment.created_at) }}
                                            </span>
                                        </div>

                                        <p
                                            class="mt-3 whitespace-pre-wrap wrap-break-word text-sm leading-7 text-foreground/85">
                                            {{ comment.body }}
                                        </p>
                                    </div>
                                </div>
                            </article>
                        </div>

                        <!-- Aucun commentaire -->
                        <div v-else
                            class="mt-6 rounded-2xl border border-dashed bg-card px-5 py-12 text-center sm:px-8">
                            <div
                                class="mx-auto flex size-14 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                                <MessageCircle class="size-7" />
                            </div>

                            <h3 class="mt-4 font-bold">
                                La discussion commence avec toi
                            </h3>

                            <p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-muted-foreground">
                                Aucun commentaire pour le moment. Partage ton point de vue
                                et aide cette communauté à grandir.
                            </p>

                            <a v-if="auth.user" href="#comment-form"
                                class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-primary hover:underline">
                                Écrire le premier commentaire
                                <ArrowRight class="size-4" />
                            </a>
                        </div>
                    </section>
                </div>

                <!-- Colonne latérale -->
                <aside class="space-y-5">
                    <div class="rounded-2xl border bg-card p-5 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="flex size-10 items-center justify-center rounded-xl bg-primary/10 text-primary">
                                <Users class="size-5" />
                            </div>

                            <div>
                                <h2 class="font-bold">À propos</h2>
                                <p class="text-xs text-muted-foreground">
                                    La communauté DevLink
                                </p>
                            </div>
                        </div>

                        <p class="mt-4 text-sm leading-6 text-muted-foreground">
                            DevLink est un espace où les développeurs partagent leurs
                            connaissances, découvrent des projets et s'entraident.
                        </p>

                        <div class="mt-4 space-y-3 border-t pt-4">
                            <div class="flex items-center gap-2.5 text-sm">
                                <Code2 class="size-4 shrink-0 text-primary" />
                                <span>Partage de connaissances</span>
                            </div>

                            <div class="flex items-center gap-2.5 text-sm">
                                <MessageCircle class="size-4 shrink-0 text-primary" />
                                <span>Discussions techniques</span>
                            </div>

                            <div class="flex items-center gap-2.5 text-sm">
                                <Users class="size-4 shrink-0 text-primary" />
                                <span>Entraide entre développeurs</span>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border bg-card p-5 shadow-sm">
                        <div
                            class="flex size-10 items-center justify-center rounded-xl bg-violet-500/10 text-violet-500">
                            <Sparkles class="size-5" />
                        </div>

                        <h2 class="mt-4 font-bold">Continue à explorer</h2>

                        <p class="mt-2 text-sm leading-6 text-muted-foreground">
                            Découvre d'autres idées et participe aux échanges de la communauté.
                        </p>

                        <Link href="/posts"
                            class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl border px-4 py-2.5 text-sm font-semibold transition hover:border-primary/40 hover:bg-primary/5 hover:text-primary">
                            Voir le fil d’actualité
                            <ArrowRight class="size-4" />
                        </Link>
                    </div>
                </aside>
            </div>
        </div>
    </main>
</template>