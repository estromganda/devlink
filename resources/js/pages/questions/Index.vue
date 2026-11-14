<script setup lang="ts">
import { computed, reactive } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import {
    ArrowRight,
    ArrowUpRight,
    Search,
    SlidersHorizontal,
    RotateCcw,
    Plus,
    MessageCircle,
    Heart,
    Clock3,
    Tag as TagIcon,
    Sparkles,
    CircleHelp,
    CheckCircle2,
    Code2,
    Users,
    X,
    SearchX,
} from '@lucide/vue'

interface User {
    id: number
    name: string
}

interface Tag {
    id: number
    name: string
    slug: string
}

interface Question {
    id: number
    title: string
    body: string
    created_at: string
    user: User
    tags: Tag[]
    answers_count: number
    likes_count: number
}

interface PaginationLink {
    url: string | null
    label: string
    active: boolean
}

interface PaginatedQuestions {
    data: Question[]
    links: PaginationLink[]
}

interface Props {
    questions: PaginatedQuestions
    tags: Tag[]
    filters: {
        search?: string
        tag?: string
        filter?: string
    }
}

const props = defineProps<Props>()

const form = reactive({
    search: props.filters.search ?? '',
    tag: props.filters.tag ?? '',
    filter: props.filters.filter ?? 'latest',
})

const hasActiveFilters = computed(
    () =>
        !!form.search.trim() ||
        !!form.tag ||
        form.filter !== 'latest',
)

const selectedTag = computed(() =>
    props.tags.find((tag) => tag.slug === form.tag),
)

function searchQuestions() {
    router.get(
        '/questions',
        {
            search: form.search.trim() || undefined,
            tag: form.tag || undefined,
            filter: form.filter || 'latest',
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    )
}

function clearFilters() {
    form.search = ''
    form.tag = ''
    form.filter = 'latest'
    searchQuestions()
}

function formatDate(date: string) {
    const parsedDate = new Date(date)

    if (Number.isNaN(parsedDate.getTime())) {
        return ''
    }

    return new Intl.DateTimeFormat('fr-FR', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    }).format(parsedDate)
}

function getInitial(name: string) {
    return name.trim().charAt(0).toUpperCase() || '?'
}

function paginationLabel(label: string) {
    const normalized = label.toLowerCase()

    if (
        normalized.includes('previous') ||
        normalized.includes('précédent')
    ) {
        return 'Précédent'
    }

    if (
        normalized.includes('next') ||
        normalized.includes('suivant')
    ) {
        return 'Suivant'
    }

    return label.replace(/&laquo;|&raquo;|«|»/g, '').trim()
}
</script>

<template>

    <Head title="Questions & Réponses" />

    <main class="min-h-screen bg-background">
        <div class="mx-auto max-w-6xl px-4 py-6 sm:px-6 sm:py-10">
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
                            Entraide entre développeurs
                        </div>

                        <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">
                            Questions & Réponses<span class="text-primary">.</span>
                        </h1>

                        <p class="mt-3 max-w-2xl text-sm leading-6 text-muted-foreground sm:text-base">
                            Pose tes questions, partage tes connaissances et
                            trouve des solutions avec la communauté DevColab.
                        </p>

                        <div class="mt-5 flex flex-wrap items-center gap-4 text-sm text-muted-foreground">
                            <div class="flex items-center gap-2">
                                <CircleHelp class="size-4 text-primary" />
                                <span>
                                    {{ questions.data.length }}
                                    question{{ questions.data.length > 1 ? 's' : '' }}
                                    sur cette page
                                </span>
                            </div>

                            <div class="flex items-center gap-2">
                                <Code2 class="size-4 text-primary" />
                                <span>Développement et technologie</span>
                            </div>
                        </div>
                    </div>

                    <Link href="/questions/create"
                        class="relative inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-primary-foreground shadow-md shadow-primary/20 transition hover:-translate-y-0.5 hover:opacity-90">
                        <Plus class="size-4" />
                        Poser une question
                    </Link>
                </div>
            </header>

            <!-- Recherche et filtres -->
            <section class="mb-8 overflow-hidden rounded-2xl border bg-card shadow-sm">
                <div class="flex items-center gap-3 border-b px-5 py-4 sm:px-6">
                    <div class="flex size-10 items-center justify-center rounded-xl bg-primary/10 text-primary">
                        <SlidersHorizontal class="size-5" />
                    </div>

                    <div class="min-w-0 flex-1">
                        <h2 class="font-bold">Trouver une question</h2>
                        <p class="mt-0.5 text-xs text-muted-foreground">
                            Recherche et filtres pour affiner les résultats.
                        </p>
                    </div>

                    <button v-if="hasActiveFilters" type="button"
                        class="inline-flex items-center gap-1.5 rounded-lg px-2 py-1.5 text-xs font-semibold text-muted-foreground transition hover:bg-muted hover:text-foreground"
                        @click="clearFilters">
                        <X class="size-3.5" />
                        Effacer
                    </button>
                </div>

                <form class="grid grid-cols-1 gap-5 p-5 sm:p-6 md:grid-cols-2 lg:grid-cols-4"
                    @submit.prevent="searchQuestions">
                    <!-- Recherche -->
                    <div class="md:col-span-2">
                        <label for="search" class="mb-2 block text-sm font-semibold">
                            Recherche
                        </label>

                        <div class="relative">
                            <Search
                                class="pointer-events-none absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />

                            <input id="search" v-model="form.search" type="search" placeholder="Titre, contenu..."
                                class="w-full rounded-xl border bg-background py-3 pl-10 pr-4 text-sm outline-none transition placeholder:text-muted-foreground/70 focus:border-primary/50 focus:ring-2 focus:ring-primary/20" />
                        </div>
                    </div>

                    <!-- Technologie -->
                    <div>
                        <label for="tag" class="mb-2 block text-sm font-semibold">
                            Technologie
                        </label>

                        <div class="relative">
                            <TagIcon
                                class="pointer-events-none absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />

                            <select id="tag" v-model="form.tag"
                                class="w-full appearance-none rounded-xl border bg-background py-3 pl-10 pr-8 text-sm outline-none transition focus:border-primary/50 focus:ring-2 focus:ring-primary/20">
                                <option value="">
                                    Toutes les technologies
                                </option>

                                <option v-for="tag in tags" :key="tag.id" :value="tag.slug">
                                    {{ tag.name }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Tri -->
                    <div>
                        <label for="filter" class="mb-2 block text-sm font-semibold">
                            Trier par
                        </label>

                        <select id="filter" v-model="form.filter"
                            class="w-full rounded-xl border bg-background px-3.5 py-3 text-sm outline-none transition focus:border-primary/50 focus:ring-2 focus:ring-primary/20">
                            <option value="latest">Plus récentes</option>
                            <option value="oldest">Plus anciennes</option>
                            <option value="votes">Les plus appréciées</option>
                            <option value="unanswered">Sans réponse</option>
                        </select>
                    </div>

                    <!-- Actions de recherche -->
                    <div class="flex flex-wrap items-center gap-3 md:col-span-2 lg:col-span-4">
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-primary-foreground transition hover:opacity-90">
                            <Search class="size-4" />
                            Rechercher
                        </button>

                        <button type="button"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border px-5 py-2.5 text-sm font-semibold transition hover:bg-muted"
                            @click="clearFilters">
                            <RotateCcw class="size-4" />
                            Réinitialiser
                        </button>
                    </div>

                    <!-- Filtres actifs -->
                    <div v-if="hasActiveFilters"
                        class="flex flex-wrap items-center gap-2 border-t pt-4 md:col-span-2 lg:col-span-4">
                        <span class="mr-1 text-xs font-medium text-muted-foreground">
                            Filtres actifs :
                        </span>

                        <span v-if="form.search.trim()"
                            class="inline-flex max-w-full items-center gap-1.5 rounded-lg bg-primary/10 px-2.5 py-1.5 text-xs font-semibold text-primary">
                            <Search class="size-3" />
                            <span class="max-w-48 truncate">
                                {{ form.search }}
                            </span>
                        </span>

                        <span v-if="selectedTag"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-violet-500/10 px-2.5 py-1.5 text-xs font-semibold text-violet-600 dark:text-violet-400">
                            <TagIcon class="size-3" />
                            {{ selectedTag.name }}
                        </span>

                        <span v-if="form.filter !== 'latest'"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-muted px-2.5 py-1.5 text-xs font-semibold text-muted-foreground">
                            {{ form.filter === 'oldest'
                                ? 'Plus anciennes'
                                : form.filter === 'votes'
                                    ? 'Les plus appréciées'
                                    : 'Sans réponse'
                            }}
                        </span>
                    </div>
                </form>
            </section>

            <!-- Liste et panneau latéral -->
            <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-[minmax(0,1fr)_280px]">
                <section class="min-w-0">
                    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-bold tracking-tight">
                                Explorer les questions
                            </h2>
                            <p class="mt-1 text-sm text-muted-foreground">
                                Trouve une réponse ou aide quelqu'un.
                            </p>
                        </div>

                        <span class="rounded-lg border bg-card px-3 py-1.5 text-xs font-medium text-muted-foreground">
                            {{ questions.data.length }}
                            résultat{{ questions.data.length > 1 ? 's' : '' }}
                        </span>
                    </div>

                    <div v-if="questions.data.length" class="space-y-4">
                        <article v-for="question in questions.data" :key="question.id"
                            class="group rounded-2xl border bg-card p-5 shadow-sm transition duration-200 hover:border-primary/30 hover:shadow-md sm:p-6">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
                                <!-- Statistiques -->
                                <div class="flex shrink-0 gap-2 sm:w-24 sm:flex-col">
                                    <div class="flex min-w-0 flex-1 items-center gap-2 rounded-xl border bg-muted/30 px-3 py-2.5 sm:flex-col sm:gap-1 sm:px-2 sm:py-3"
                                        :class="question.answers_count > 0
                                            ? 'border-emerald-500/20'
                                            : 'border-amber-500/20'
                                            ">
                                        <MessageCircle class="size-4 shrink-0" :class="question.answers_count > 0
                                            ? 'text-emerald-600 dark:text-emerald-400'
                                            : 'text-amber-600 dark:text-amber-400'
                                            " />

                                        <span class="text-lg font-bold leading-none">
                                            {{ question.answers_count }}
                                        </span>

                                        <span class="text-xs text-muted-foreground">
                                            {{ question.answers_count === 1 ? 'réponse' : 'réponses' }}
                                        </span>
                                    </div>

                                    <div
                                        class="flex min-w-0 flex-1 items-center gap-2 rounded-xl border bg-muted/30 px-3 py-2.5 sm:flex-col sm:gap-1 sm:px-2 sm:py-3">
                                        <Heart class="size-4 shrink-0 text-rose-500" />

                                        <span class="text-lg font-bold leading-none">
                                            {{ question.likes_count }}
                                        </span>

                                        <span class="text-xs text-muted-foreground">
                                            J’aime
                                        </span>
                                    </div>
                                </div>

                                <!-- Contenu -->
                                <div class="min-w-0 flex-1">
                                    <Link :href="`/questions/${question.id}`"
                                        class="inline-block wrap-break-word text-lg font-bold leading-snug tracking-tight transition group-hover:text-primary sm:text-xl">
                                        {{ question.title }}
                                    </Link>

                                    <p
                                        class="mt-3 line-clamp-3 whitespace-pre-line wrap-break-word text-sm leading-7 text-muted-foreground">
                                        {{ question.body }}
                                    </p>

                                    <!-- Tags -->
                                    <div v-if="question.tags?.length" class="mt-4 flex flex-wrap gap-2">
                                        <span v-for="tag in question.tags" :key="tag.id"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-primary/10 bg-primary/5 px-2.5 py-1.5 text-xs font-semibold text-primary">
                                            <TagIcon class="size-3" />
                                            {{ tag.name }}
                                        </span>
                                    </div>

                                    <!-- Auteur et date -->
                                    <div class="mt-5 flex flex-wrap items-center justify-between gap-3 border-t pt-4">
                                        <div class="flex min-w-0 items-center gap-2.5">
                                            <div
                                                class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-xs font-bold text-primary">
                                                {{ getInitial(question.user.name) }}
                                            </div>

                                            <div class="min-w-0">
                                                <div
                                                    class="flex flex-wrap items-center gap-x-1.5 text-xs text-muted-foreground">
                                                    <span>Posée par</span>

                                                    <Link :href="`/developers/${question.user.id}`"
                                                        class="font-semibold text-foreground transition hover:text-primary">
                                                        {{ question.user.name }}
                                                    </Link>
                                                </div>

                                                <span
                                                    class="mt-0.5 inline-flex items-center gap-1 text-xs text-muted-foreground">
                                                    <Clock3 class="size-3" />
                                                    {{ formatDate(question.created_at) }}
                                                </span>
                                            </div>
                                        </div>

                                        <Link :href="`/questions/${question.id}`"
                                            class="inline-flex items-center gap-1.5 text-sm font-semibold text-primary transition hover:gap-2.5">
                                            Voir la question
                                            <ArrowUpRight class="size-4" />
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </article>

                        <!-- Pagination -->
                        <nav v-if="questions.links.length > 3" aria-label="Pagination des questions"
                            class="flex flex-wrap items-center justify-center gap-2 pt-4">
                            <template v-for="(link, index) in questions.links" :key="`${index}-${link.label}`">
                                <Link v-if="link.url" :href="link.url" preserve-scroll
                                    :aria-current="link.active ? 'page' : undefined"
                                    class="inline-flex min-h-10 items-center justify-center rounded-xl border px-3.5 text-sm font-medium transition hover:border-primary/40 hover:bg-muted"
                                    :class="link.active
                                        ? 'border-primary bg-primary text-primary-foreground shadow-sm hover:bg-primary'
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

                    <!-- Aucun résultat -->
                    <div v-else class="rounded-3xl border border-dashed bg-card px-6 py-14 text-center sm:px-10">
                        <div
                            class="mx-auto flex size-16 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                            <SearchX class="size-8" />
                        </div>

                        <h3 class="mt-5 text-xl font-bold tracking-tight">
                            Aucune question trouvée
                        </h3>

                        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-muted-foreground">
                            Essaie d'autres mots-clés ou modifie tes filtres.
                            Tu peux aussi poser ta propre question à la communauté.
                        </p>

                        <div class="mt-6 flex flex-wrap justify-center gap-3">
                            <button v-if="hasActiveFilters" type="button"
                                class="inline-flex items-center gap-2 rounded-xl border px-4 py-2.5 text-sm font-semibold transition hover:bg-muted"
                                @click="clearFilters">
                                <RotateCcw class="size-4" />
                                Réinitialiser les filtres
                            </button>

                            <Link href="/questions/create"
                                class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-primary-foreground transition hover:opacity-90">
                                <Plus class="size-4" />
                                Poser une question
                            </Link>
                        </div>
                    </div>
                </section>

                <!-- Colonne latérale -->
                <aside class="space-y-5">
                    <section class="rounded-2xl border bg-card p-5 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="flex size-10 items-center justify-center rounded-xl bg-primary/10 text-primary">
                                <CircleHelp class="size-5" />
                            </div>

                            <div>
                                <h2 class="font-bold">Comment ça marche ?</h2>
                                <p class="text-xs text-muted-foreground">
                                    Apprendre ensemble
                                </p>
                            </div>
                        </div>

                        <div class="mt-5 space-y-4">
                            <div class="flex items-start gap-3">
                                <div
                                    class="flex size-7 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-xs font-bold text-primary">
                                    1
                                </div>
                                <div>
                                    <h3 class="text-sm font-semibold">
                                        Pose une question
                                    </h3>
                                    <p class="mt-1 text-xs leading-5 text-muted-foreground">
                                        Décris clairement ton problème et ajoute
                                        les détails utiles.
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div
                                    class="flex size-7 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-xs font-bold text-primary">
                                    2
                                </div>
                                <div>
                                    <h3 class="text-sm font-semibold">
                                        Échange avec les autres
                                    </h3>
                                    <p class="mt-1 text-xs leading-5 text-muted-foreground">
                                        Compare les solutions et approfondis
                                        tes connaissances.
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div
                                    class="flex size-7 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-xs font-bold text-primary">
                                    3
                                </div>
                                <div>
                                    <h3 class="text-sm font-semibold">
                                        Partage la solution
                                    </h3>
                                    <p class="mt-1 text-xs leading-5 text-muted-foreground">
                                        Aide les autres développeurs à résoudre
                                        des problèmes similaires.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <Link href="/questions/create"
                            class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-primary-foreground transition hover:opacity-90">
                            <Plus class="size-4" />
                            Poser une question
                        </Link>
                    </section>

                    <section class="rounded-2xl border bg-card p-5 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex size-10 items-center justify-center rounded-xl bg-violet-500/10 text-violet-500">
                                <TagIcon class="size-5" />
                            </div>

                            <div>
                                <h2 class="font-bold">Technologies</h2>
                                <p class="text-xs text-muted-foreground">
                                    Explorer par sujet
                                </p>
                            </div>
                        </div>

                        <p class="mt-3 text-sm leading-6 text-muted-foreground">
                            Filtre les questions par technologie pour retrouver
                            plus facilement les sujets qui t'intéressent.
                        </p>

                        <div v-if="tags.length" class="mt-4 flex flex-wrap gap-2">
                            <button v-for="tag in tags.slice(0, 12)" :key="tag.id" type="button"
                                class="rounded-lg border px-2.5 py-1.5 text-xs font-medium transition hover:border-primary/30 hover:bg-primary/5 hover:text-primary"
                                :class="form.tag === tag.slug
                                    ? 'border-primary bg-primary/10 text-primary'
                                    : 'bg-background text-muted-foreground'
                                    " @click="
                                        form.tag = form.tag === tag.slug ? '' : tag.slug;
                                    searchQuestions();
                                    ">
                                {{ tag.name }}
                            </button>
                        </div>

                        <p v-else class="mt-3 text-sm text-muted-foreground">
                            Aucune technologie disponible pour le moment.
                        </p>
                    </section>

                    <section class="rounded-2xl border border-primary/20 bg-primary/5 p-5">
                        <div class="flex items-start gap-3">
                            <CheckCircle2 class="mt-0.5 size-5 shrink-0 text-primary" />

                            <div>
                                <h2 class="text-sm font-bold">
                                    Le savoir se partage
                                </h2>
                                <p class="mt-1 text-xs leading-5 text-muted-foreground">
                                    Une réponse utile aujourd'hui peut débloquer
                                    le projet d'un autre développeur demain.
                                </p>
                            </div>
                        </div>

                        <Link href="/questions/create"
                            class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-primary hover:underline">
                            Contribuer à la communauté
                            <ArrowRight class="size-4" />
                        </Link>
                    </section>
                </aside>
            </div>
        </div>
    </main>
</template>