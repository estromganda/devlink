<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { reactive } from 'vue'

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

function searchQuestions() {
    router.get(
        '/questions',
        {
            search: form.search || undefined,
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
</script>

<template>

    <Head title="Questions" />

    <div class="min-h-screen bg-gray-50 py-8 dark:bg-gray-950">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <!-- En-tête -->
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-gray-100">
                        Questions & Réponses
                    </h1>

                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Pose tes questions, partage tes connaissances et aide
                        les autres développeurs.
                    </p>
                </div>

                <Link href="/questions/create"
                    class="inline-flex items-center justify-center rounded-lg bg-teal-600 px-5 py-3 font-semibold text-white transition hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 dark:focus:ring-offset-gray-950">
                    Poser une question
                </Link>
            </div>

            <!-- Recherche et filtres -->
            <div
                class="mb-8 rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <form class="grid grid-cols-1 gap-4 md:grid-cols-4" @submit.prevent="searchQuestions">
                    <div class="md:col-span-2">
                        <label for="search" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Rechercher
                        </label>

                        <input id="search" v-model="form.search" type="search" placeholder="Titre, contenu..."
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 placeholder-gray-400 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500" />
                    </div>

                    <div>
                        <label for="tag" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Technologie
                        </label>

                        <select id="tag" v-model="form.tag"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                            <option value="">Toutes les technologies</option>

                            <option v-for="tag in tags" :key="tag.id" :value="tag.slug">
                                {{ tag.name }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label for="filter" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Trier par
                        </label>

                        <select id="filter" v-model="form.filter"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                            <option value="latest">Plus récentes</option>
                            <option value="oldest">Plus anciennes</option>
                            <option value="votes">Les plus appréciées</option>
                            <option value="unanswered">Sans réponse</option>
                        </select>
                    </div>

                    <div class="flex flex-wrap gap-3 md:col-span-4">
                        <button type="submit"
                            class="rounded-lg bg-teal-600 px-5 py-2.5 font-semibold text-white transition hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                            Rechercher
                        </button>

                        <button type="button"
                            class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 font-semibold text-gray-700 transition hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
                            @click="clearFilters">
                            Réinitialiser
                        </button>
                    </div>
                </form>
            </div>

            <!-- Liste des questions -->
            <div class="space-y-4">
                <article v-for="question in questions.data" :key="question.id"
                    class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:border-teal-300 hover:shadow-md dark:border-gray-800 dark:bg-gray-900 dark:hover:border-teal-700">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
                        <!-- Statistiques -->
                        <div class="flex shrink-0 gap-4 text-sm sm:w-24 sm:flex-col sm:gap-2">
                            <div class="rounded-lg bg-gray-50 px-3 py-2 text-center dark:bg-gray-800">
                                <div class="font-bold text-gray-900 dark:text-gray-100">
                                    {{ question.answers_count }}
                                </div>
                                <div class="text-gray-500 dark:text-gray-400">
                                    réponses
                                </div>
                            </div>

                            <div class="rounded-lg bg-gray-50 px-3 py-2 text-center dark:bg-gray-800">
                                <div class="font-bold text-gray-900 dark:text-gray-100">
                                    {{ question.likes_count }}
                                </div>
                                <div class="text-gray-500 dark:text-gray-400">
                                    appréciations
                                </div>
                            </div>
                        </div>

                        <!-- Contenu -->
                        <div class="min-w-0 flex-1">
                            <Link :href="`/questions/${question.id}`"
                                class="text-lg font-semibold text-teal-700 transition hover:text-teal-800 dark:text-teal-400 dark:hover:text-teal-300">
                                {{ question.title }}
                            </Link>

                            <p
                                class="mt-2 line-clamp-3 whitespace-pre-line text-sm leading-6 text-gray-600 dark:text-gray-300">
                                {{ question.body }}
                            </p>

                            <div v-if="question.tags?.length" class="mt-4 flex flex-wrap gap-2">
                                <span v-for="tag in question.tags" :key="tag.id"
                                    class="rounded-md bg-teal-50 px-2.5 py-1 text-xs font-medium text-teal-800 dark:bg-teal-950 dark:text-teal-300">
                                    {{ tag.name }}
                                </span>
                            </div>

                            <div
                                class="mt-4 flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-gray-500 dark:text-gray-400">
                                <span>Posée par</span>

                                <Link :href="`/developers/${question.user.id}`"
                                    class="font-medium text-gray-700 hover:text-teal-700 dark:text-gray-300 dark:hover:text-teal-400">
                                    {{ question.user.name }}
                                </Link>

                                <span aria-hidden="true">·</span>

                                <span>
                                    {{
                                        new Date(
                                            question.created_at,
                                        ).toLocaleDateString()
                                    }}
                                </span>
                            </div>
                        </div>
                    </div>
                </article>

                <!-- État vide -->
                <div v-if="questions.data.length === 0"
                    class="rounded-xl border border-dashed border-gray-300 bg-white px-6 py-12 text-center dark:border-gray-700 dark:bg-gray-900">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        Aucune question trouvée
                    </h2>

                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Essaie de modifier tes filtres ou pose la première
                        question.
                    </p>

                    <Link href="/questions/create"
                        class="mt-5 inline-flex rounded-lg bg-teal-600 px-4 py-2.5 font-semibold text-white transition hover:bg-teal-700">
                        Poser une question
                    </Link>
                </div>
            </div>

            <!-- Pagination -->
            <nav v-if="questions.links.length > 3" aria-label="Pagination des questions"
                class="mt-8 flex flex-wrap justify-center gap-2">
                <template v-for="(link, index) in questions.links" :key="index">
                    <Link v-if="link.url" :href="link.url" preserve-scroll
                        class="rounded-lg border px-3 py-2 text-sm transition" :class="link.active
                                ? 'border-teal-600 bg-teal-600 text-white'
                                : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800'
                            " v-html="link.label" />

                    <span v-else
                        class="cursor-not-allowed rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 text-sm text-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-600"
                        v-html="link.label" />
                </template>
            </nav>
        </div>
    </div>
</template>