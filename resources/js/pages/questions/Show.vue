<script setup lang="ts">
import { computed, ref } from 'vue'
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3'
import {
    ArrowLeft,
    ArrowUp,
    ArrowDown,
    Check,
    CheckCircle2,
    Clock3,
    Code2,
    MessageCircle,
    Pencil,
    Send,
    ShieldCheck,
    Sparkles,
    ThumbsUp,
    Trash2,
    UserRound,
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

interface Answer {
    id: number
    body: string
    created_at: string
    user: User
    is_accepted: boolean
    votes_count: number
    votes_sum_value: number | null
}

interface PaginationLink {
    url: string | null
    label: string
    active: boolean
}

interface PaginatedAnswers {
    data: Answer[]
    links: PaginationLink[]
}

const props = defineProps<{
    question: Question
    answers: PaginatedAnswers
    userQuestionVote: boolean
    userAnswerVotes: Record<number, number>
    canAnswer: boolean
    canEdit: boolean
}>()

const page = usePage()

const authUserId = computed<number | null>(() => {
    const auth = page.props.auth as
        | { user?: { id: number } | null }
        | undefined

    return auth?.user?.id ?? null
})

const answerForm = useForm({
    body: '',
})

const editAnswerForm = useForm({
    body: '',
})

const editingAnswerId = ref<number | null>(null)
const votingAnswerIds = ref<number[]>([])

const acceptedAnswer = computed(() =>
    props.answers.data.find((answer) => answer.is_accepted),
)

function startEditingAnswer(answer: Answer) {
    editingAnswerId.value = answer.id
    editAnswerForm.body = answer.body
    editAnswerForm.clearErrors()
}

function cancelEditingAnswer() {
    editingAnswerId.value = null
    editAnswerForm.reset()
    editAnswerForm.clearErrors()
}

function updateAnswer(answer: Answer) {
    editAnswerForm.put(
        `/questions/${props.question.id}/answers/${answer.id}`,
        {
            preserveScroll: true,
            onSuccess: () => cancelEditingAnswer(),
        },
    )
}

function submitAnswer() {
    answerForm.post(`/questions/${props.question.id}/answers`, {
        preserveScroll: true,
        onSuccess: () => answerForm.reset(),
    })
}

function isVoting(answerId: number): boolean {
    return votingAnswerIds.value.includes(answerId)
}

function vote(answer: Answer, value: number) {
    if (isVoting(answer.id)) return

    votingAnswerIds.value.push(answer.id)

    const options = {
        preserveScroll: true,
        onFinish: () => {
            votingAnswerIds.value = votingAnswerIds.value.filter(
                (id) => id !== answer.id,
            )
        },
    }

    const currentVote = props.userAnswerVotes[answer.id]

    if (currentVote === value) {
        router.delete(
            `/questions/${props.question.id}/answers/${answer.id}/vote`,
            options,
        )
        return
    }

    router.put(
        `/questions/${props.question.id}/answers/${answer.id}/vote`,
        { value },
        options,
    )
}

function acceptAnswer(answer: Answer) {
    if (!confirm('Veux-tu accepter cette réponse comme solution ?')) return

    router.post(
        `/questions/${props.question.id}/answers/${answer.id}/accept`,
        {},
        { preserveScroll: true },
    )
}

function deleteAnswer(answer: Answer) {
    if (!confirm('Veux-tu vraiment supprimer cette réponse ?')) return

    router.delete(
        `/questions/${props.question.id}/answers/${answer.id}`,
        { preserveScroll: true },
    )
}

function deleteQuestion() {
    if (!confirm('Veux-tu vraiment supprimer cette question ?')) return

    router.delete(`/questions/${props.question.id}`)
}

function formatDate(date: string): string {
    return new Intl.DateTimeFormat('fr-FR', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    }).format(new Date(date))
}

function formatDateTime(date: string): string {
    return new Date(date).toISOString()
}

function getInitial(name: string): string {
    return name?.trim().charAt(0).toUpperCase() || '?'
}

function paginationLabel(label: string): string {
    const normalized = label
        .replace(/&laquo;|&#171;/gi, '')
        .replace(/&raquo;|&#187;/gi, '')
        .replace(/<[^>]*>/g, '')
        .trim()
        .toLowerCase()

    if (
        normalized.includes('previous') ||
        normalized.includes('précédent') ||
        normalized.includes('precedent')
    ) {
        return '←'
    }

    if (
        normalized.includes('next') ||
        normalized.includes('suivant')
    ) {
        return '→'
    }

    return normalized
}
</script>

<template>

    <Head :title="question.title" />

    <div class="min-h-screen bg-slate-50 pb-16 dark:bg-gray-950">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Navigation -->
            <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
                <Link href="/questions"
                    class="group inline-flex items-center gap-2 text-sm font-semibold text-slate-600 transition hover:text-teal-700 dark:text-slate-400 dark:hover:text-teal-400">
                    <span
                        class="flex size-9 items-center justify-center rounded-xl border border-slate-200 bg-white transition group-hover:border-teal-200 group-hover:bg-teal-50 dark:border-gray-800 dark:bg-gray-900 dark:group-hover:border-teal-900 dark:group-hover:bg-teal-950">
                        <ArrowLeft class="size-4" />
                    </span>
                    Retour aux questions
                </Link>

                <div
                    class="inline-flex items-center gap-2 rounded-full border border-teal-100 bg-teal-50 px-3 py-1.5 text-xs font-semibold text-teal-800 dark:border-teal-900/70 dark:bg-teal-950/50 dark:text-teal-300">
                    <Code2 class="size-4" />
                    DevLink · Questions & Réponses
                </div>
            </div>

            <div class="grid items-start gap-8 lg:grid-cols-[minmax(0,1fr)_300px]">
                <main class="min-w-0 space-y-8">
                    <!-- Question principale -->
                    <article
                        class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                        <div class="h-1.5 bg-linear-to-r from-teal-500 via-cyan-500 to-blue-500"></div>

                        <div class="p-5 sm:p-8">
                            <div class="flex flex-wrap items-center gap-2">
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-700 dark:bg-blue-950/60 dark:text-blue-300">
                                    <MessageCircle class="size-3.5" />
                                    Question
                                </span>

                                <span v-if="acceptedAnswer"
                                    class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300">
                                    <CheckCircle2 class="size-3.5" />
                                    Solution acceptée
                                </span>
                            </div>

                            <div class="mt-5 flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
                                <h1
                                    class="min-w-0 flex-1 wrap-break-word text-2xl font-black leading-tight tracking-tight text-slate-900 dark:text-white sm:text-3xl">
                                    {{ question.title }}
                                </h1>

                                <div v-if="canEdit" class="flex shrink-0 flex-wrap gap-2">
                                    <Link :href="`/questions/${question.id}/edit`"
                                        class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-teal-200 hover:bg-teal-50 hover:text-teal-800 dark:border-gray-700 dark:text-slate-300 dark:hover:border-teal-900 dark:hover:bg-teal-950/50 dark:hover:text-teal-300">
                                        <Pencil class="size-4" />
                                        Modifier
                                    </Link>

                                    <button type="button"
                                        class="inline-flex items-center gap-2 rounded-xl border border-red-200 px-3.5 py-2.5 text-sm font-semibold text-red-600 transition hover:bg-red-50 dark:border-red-900/60 dark:text-red-400 dark:hover:bg-red-950/40"
                                        @click="deleteQuestion">
                                        <Trash2 class="size-4" />
                                        Supprimer
                                    </button>
                                </div>
                            </div>

                            <!-- Auteur et date -->
                            <div
                                class="mt-5 flex flex-wrap items-center gap-x-3 gap-y-2 text-sm text-slate-500 dark:text-slate-400">
                                <Link :href="`/developers/${question.user.id}`"
                                    class="inline-flex items-center gap-2.5 rounded-full transition hover:text-teal-700 dark:hover:text-teal-400">
                                    <span
                                        class="flex size-9 items-center justify-center rounded-full bg-linear-to-br from-teal-500 to-cyan-600 text-sm font-bold text-white shadow-sm">
                                        {{ getInitial(question.user.name) }}
                                    </span>
                                    <span class="font-semibold text-slate-800 dark:text-slate-200">
                                        {{ question.user.name }}
                                    </span>
                                </Link>

                                <span class="text-slate-300 dark:text-gray-700">·</span>

                                <span class="inline-flex items-center gap-1.5">
                                    <Clock3 class="size-4" />
                                    <time :datetime="formatDateTime(question.created_at)">
                                        Posée le {{ formatDate(question.created_at) }}
                                    </time>
                                </span>
                            </div>

                            <!-- Tags -->
                            <div v-if="question.tags?.length" class="mt-6 flex flex-wrap gap-2">
                                <span v-for="tag in question.tags" :key="tag.id"
                                    class="rounded-lg border border-teal-100 bg-teal-50/80 px-3 py-1.5 text-xs font-bold text-teal-800 dark:border-teal-900/60 dark:bg-teal-950/40 dark:text-teal-300">
                                    # {{ tag.name }}
                                </span>
                            </div>

                            <!-- Contenu -->
                            <div
                                class="mt-7 whitespace-pre-line wrap-break-word text-[15px] leading-8 text-slate-700 dark:text-slate-300 sm:text-base">
                                {{ question.body }}
                            </div>

                            <!-- Statistiques -->
                            <div
                                class="mt-8 flex flex-wrap items-center gap-3 border-t border-slate-100 pt-5 dark:border-gray-800">
                                <div
                                    class="inline-flex items-center gap-2 rounded-xl bg-slate-50 px-3.5 py-2.5 text-sm font-semibold text-slate-600 dark:bg-gray-800 dark:text-slate-300">
                                    <MessageCircle class="size-4 text-teal-600 dark:text-teal-400" />
                                    {{ question.answers_count }}
                                    {{ question.answers_count === 1 ? 'réponse' : 'réponses' }}
                                </div>

                                <div
                                    class="inline-flex items-center gap-2 rounded-xl bg-slate-50 px-3.5 py-2.5 text-sm font-semibold text-slate-600 dark:bg-gray-800 dark:text-slate-300">
                                    <ThumbsUp class="size-4 text-amber-500" />
                                    {{ question.likes_count }}
                                    {{ question.likes_count === 1 ? 'appréciation' : 'appréciations' }}
                                </div>
                            </div>
                        </div>
                    </article>

                    <!-- Réponses -->
                    <section>
                        <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2">
                                    <h2 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">
                                        Réponses
                                    </h2>

                                    <span
                                        class="rounded-full bg-teal-100 px-2.5 py-1 text-xs font-bold text-teal-800 dark:bg-teal-950 dark:text-teal-300">
                                        {{ question.answers_count }}
                                    </span>
                                </div>

                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                    Les idées et solutions partagées par la communauté.
                                </p>
                            </div>
                        </div>

                        <div v-if="answers.data.length" class="space-y-5">
                            <article v-for="(answer, index) in answers.data" :key="answer.id"
                                class="overflow-hidden rounded-2xl border bg-white shadow-sm transition-shadow hover:shadow-md dark:bg-gray-900"
                                :class="answer.is_accepted
                                    ? 'border-emerald-300 dark:border-emerald-800'
                                    : 'border-slate-200 dark:border-gray-800'">
                                <div v-if="answer.is_accepted"
                                    class="flex items-center gap-2 border-b border-emerald-100 bg-emerald-50/80 px-5 py-3 text-sm font-bold text-emerald-800 dark:border-emerald-900/60 dark:bg-emerald-950/40 dark:text-emerald-300">
                                    <CheckCircle2 class="size-4" />
                                    Solution acceptée par l'auteur
                                </div>

                                <div class="p-5 sm:p-6">
                                    <div class="flex items-start gap-3 sm:gap-5">
                                        <!-- Vote -->
                                        <div
                                            class="flex shrink-0 flex-col items-center gap-1 rounded-2xl border border-slate-100 bg-slate-50 p-1.5 dark:border-gray-800 dark:bg-gray-950">
                                            <button type="button" :disabled="isVoting(answer.id)"
                                                :aria-label="userAnswerVotes[answer.id] === 1 ? 'Retirer le vote positif' : 'Voter positivement'"
                                                class="flex size-9 items-center justify-center rounded-xl transition disabled:opacity-50"
                                                :class="userAnswerVotes[answer.id] === 1
                                                    ? 'bg-teal-100 text-teal-700 dark:bg-teal-950 dark:text-teal-300'
                                                    : 'text-slate-500 hover:bg-teal-50 hover:text-teal-700 dark:text-slate-400 dark:hover:bg-teal-950/60 dark:hover:text-teal-300'"
                                                @click="vote(answer, 1)">
                                                <ArrowUp class="size-5" />
                                            </button>

                                            <span
                                                class="min-w-7 text-center text-sm font-black tabular-nums text-slate-800 dark:text-slate-200">
                                                {{ answer.votes_sum_value ?? answer.votes_count ?? 0 }}
                                            </span>

                                            <button type="button" :disabled="isVoting(answer.id)"
                                                :aria-label="userAnswerVotes[answer.id] === -1 ? 'Retirer le vote négatif' : 'Voter négativement'"
                                                class="flex size-9 items-center justify-center rounded-xl transition disabled:opacity-50"
                                                :class="userAnswerVotes[answer.id] === -1
                                                    ? 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-300'
                                                    : 'text-slate-500 hover:bg-red-50 hover:text-red-700 dark:text-slate-400 dark:hover:bg-red-950/50 dark:hover:text-red-300'"
                                                @click="vote(answer, -1)">
                                                <ArrowDown class="size-5" />
                                            </button>
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <!-- Édition -->
                                            <form v-if="editingAnswerId === answer.id" class="space-y-4"
                                                @submit.prevent="updateAnswer(answer)">
                                                <label :for="`edit-answer-${answer.id}`"
                                                    class="block text-sm font-bold text-slate-800 dark:text-slate-200">
                                                    Modifier ta réponse
                                                </label>

                                                <textarea :id="`edit-answer-${answer.id}`" v-model="editAnswerForm.body"
                                                    rows="6" required
                                                    class="w-full resize-y rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm leading-7 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-500/10 dark:border-gray-700 dark:bg-gray-950 dark:text-white dark:focus:border-teal-500 dark:focus:bg-gray-950"
                                                    :class="{ 'border-red-500': editAnswerForm.errors.body }" />

                                                <p v-if="editAnswerForm.errors.body"
                                                    class="text-sm text-red-600 dark:text-red-400">
                                                    {{ editAnswerForm.errors.body }}
                                                </p>

                                                <div class="flex flex-wrap justify-end gap-2">
                                                    <button type="button"
                                                        class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 dark:border-gray-700 dark:text-slate-300 dark:hover:bg-gray-800"
                                                        @click="cancelEditingAnswer">
                                                        Annuler
                                                    </button>

                                                    <button type="submit" :disabled="editAnswerForm.processing"
                                                        class="inline-flex items-center gap-2 rounded-xl bg-teal-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-teal-700 disabled:cursor-not-allowed disabled:opacity-50">
                                                        <Check class="size-4" />
                                                        {{ editAnswerForm.processing ? 'Enregistrement…' : 'Enregistrer'
                                                        }}
                                                    </button>
                                                </div>
                                            </form>

                                            <!-- Affichage de la réponse -->
                                            <template v-else>
                                                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                                                    <div class="flex items-center gap-2.5">
                                                        <span
                                                            class="flex size-9 items-center justify-center rounded-full bg-linear-to-br from-violet-500 to-indigo-600 text-sm font-bold text-white">
                                                            {{ getInitial(answer.user.name) }}
                                                        </span>

                                                        <div>
                                                            <Link :href="`/developers/${answer.user.id}`"
                                                                class="text-sm font-bold text-slate-900 transition hover:text-teal-700 dark:text-white dark:hover:text-teal-400">
                                                                {{ answer.user.name }}
                                                            </Link>

                                                            <p
                                                                class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                                                                {{ formatDate(answer.created_at) }}
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <span
                                                        class="text-xs font-medium text-slate-400 dark:text-slate-500">
                                                        Réponse #{{ index + 1 }}
                                                    </span>
                                                </div>

                                                <div
                                                    class="whitespace-pre-line wrap-break-word text-sm leading-7 text-slate-700 dark:text-slate-300 sm:text-base">
                                                    {{ answer.body }}
                                                </div>
                                            </template>

                                            <!-- Actions -->
                                            <div
                                                class="mt-5 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-4 dark:border-gray-800">
                                                <div v-if="answer.is_accepted"
                                                    class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 dark:text-emerald-400">
                                                    <ShieldCheck class="size-4" />
                                                    Réponse validée
                                                </div>
                                                <span v-else class="text-xs text-slate-400 dark:text-slate-500">
                                                    Aide la communauté à trouver la meilleure solution.
                                                </span>

                                                <div v-if="editingAnswerId !== answer.id" class="flex flex-wrap gap-2">
                                                    <button v-if="canEdit && !answer.is_accepted" type="button"
                                                        class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-2 text-xs font-bold text-white transition hover:bg-emerald-700"
                                                        @click="acceptAnswer(answer)">
                                                        <CheckCircle2 class="size-4" />
                                                        Accepter
                                                    </button>

                                                    <button v-if="authUserId === answer.user.id" type="button"
                                                        :disabled="editAnswerForm.processing || editingAnswerId !== null"
                                                        class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 py-2 text-xs font-bold text-slate-600 transition hover:border-teal-200 hover:bg-teal-50 hover:text-teal-800 disabled:opacity-50 dark:border-gray-700 dark:text-slate-300 dark:hover:bg-teal-950/40"
                                                        @click="startEditingAnswer(answer)">
                                                        <Pencil class="size-3.5" />
                                                        Modifier
                                                    </button>

                                                    <button v-if="authUserId === answer.user.id" type="button"
                                                        class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 px-3 py-2 text-xs font-bold text-red-600 transition hover:bg-red-50 dark:border-red-900/60 dark:text-red-400 dark:hover:bg-red-950/40"
                                                        @click="deleteAnswer(answer)">
                                                        <Trash2 class="size-3.5" />
                                                        Supprimer
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        </div>

                        <!-- Aucune réponse -->
                        <div v-else
                            class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center dark:border-gray-700 dark:bg-gray-900 sm:px-10">
                            <div
                                class="mx-auto flex size-16 items-center justify-center rounded-2xl bg-teal-50 text-teal-700 dark:bg-teal-950/60 dark:text-teal-300">
                                <MessageCircle class="size-8" />
                            </div>

                            <h3 class="mt-5 text-xl font-black text-slate-900 dark:text-white">
                                La discussion commence ici
                            </h3>

                            <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500 dark:text-slate-400">
                                Personne n'a encore répondu. Partage tes connaissances pour aider l'auteur et faire
                                avancer la communauté.
                            </p>
                        </div>

                        <!-- Pagination -->
                        <nav v-if="answers.links.length > 3" aria-label="Pagination des réponses"
                            class="mt-6 flex flex-wrap items-center justify-center gap-2">
                            <template v-for="(link, index) in answers.links" :key="index">
                                <Link v-if="link.url" :href="link.url" preserve-scroll
                                    :aria-label="paginationLabel(link.label)"
                                    class="flex min-h-10 min-w-10 items-center justify-center rounded-xl border px-3 py-2 text-sm font-semibold transition"
                                    :class="link.active
                                        ? 'border-teal-600 bg-teal-600 text-white shadow-sm'
                                        : 'border-slate-200 bg-white text-slate-600 hover:border-teal-200 hover:bg-teal-50 dark:border-gray-800 dark:bg-gray-900 dark:text-slate-300 dark:hover:bg-teal-950/40'">
                                    {{ paginationLabel(link.label) }}
                                </Link>

                                <span v-else
                                    class="flex min-h-10 min-w-10 cursor-not-allowed items-center justify-center rounded-xl border border-slate-200 bg-slate-100 px-3 py-2 text-sm text-slate-400 dark:border-gray-800 dark:bg-gray-900 dark:text-slate-600"
                                    aria-disabled="true">
                                    {{ paginationLabel(link.label) }}
                                </span>
                            </template>
                        </nav>
                    </section>

                    <!-- Formulaire de réponse -->
                    <section v-if="canAnswer"
                        class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                        <div
                            class="border-b border-slate-100 bg-linear-to-r from-teal-50 to-cyan-50/60 px-5 py-5 dark:border-gray-800 dark:from-teal-950/30 dark:to-cyan-950/20 sm:px-7">
                            <div class="flex items-start gap-3">
                                <div
                                    class="flex size-11 shrink-0 items-center justify-center rounded-2xl bg-teal-600 text-white shadow-sm shadow-teal-600/20">
                                    <Send class="size-5" />
                                </div>

                                <div>
                                    <h2 class="text-xl font-black text-slate-900 dark:text-white">
                                        Ta réponse
                                    </h2>
                                    <p class="mt-1 text-sm leading-6 text-slate-600 dark:text-slate-400">
                                        Une explication claire peut faire toute la différence.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <form class="space-y-4 p-5 sm:p-7" @submit.prevent="submitAnswer">
                            <div>
                                <label for="answer-body"
                                    class="mb-2 block text-sm font-bold text-slate-800 dark:text-slate-200">
                                    Contenu de la réponse
                                </label>

                                <textarea id="answer-body" v-model="answerForm.body" rows="7" required
                                    placeholder="Explique ton raisonnement, partage un exemple de code ou détaille les étapes de ta solution…"
                                    class="w-full resize-y rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm leading-7 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-500/10 dark:border-gray-700 dark:bg-gray-950 dark:text-white dark:placeholder:text-slate-500 dark:focus:bg-gray-950"
                                    :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-500/10': answerForm.errors.body }" />

                                <div class="mt-2 flex flex-wrap items-start justify-between gap-2">
                                    <p class="text-xs text-slate-500 dark:text-slate-400">
                                        Sois précis, respectueux et constructif.
                                    </p>

                                    <p v-if="answerForm.errors.body"
                                        class="text-sm font-medium text-red-600 dark:text-red-400">
                                        {{ answerForm.errors.body }}
                                    </p>
                                </div>
                            </div>

                            <div
                                class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-4 dark:border-gray-800">
                                <p class="text-xs text-slate-400 dark:text-slate-500">
                                    Ta réponse sera visible dans cette discussion.
                                </p>

                                <button type="submit" :disabled="answerForm.processing || !answerForm.body.trim()"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-teal-600 px-5 py-3 text-sm font-bold text-white shadow-sm shadow-teal-600/20 transition hover:-translate-y-0.5 hover:bg-teal-700 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:translate-y-0">
                                    <Send class="size-4" />
                                    {{ answerForm.processing ? 'Publication…' : 'Publier ma réponse' }}
                                </button>
                            </div>
                        </form>
                    </section>

                    <!-- Connexion requise -->
                    <section v-else-if="!authUserId"
                        class="rounded-3xl border border-slate-200 bg-white p-7 text-center shadow-sm dark:border-gray-800 dark:bg-gray-900 sm:p-10">
                        <div
                            class="mx-auto flex size-14 items-center justify-center rounded-2xl bg-teal-50 text-teal-700 dark:bg-teal-950/60 dark:text-teal-300">
                            <UserRound class="size-7" />
                        </div>

                        <h2 class="mt-4 text-xl font-black text-slate-900 dark:text-white">
                            Tu as une solution à partager ?
                        </h2>

                        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500 dark:text-slate-400">
                            Connecte-toi à DevLink pour répondre à cette question et participer aux échanges de la
                            communauté.
                        </p>

                        <Link href="/login"
                            class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl bg-teal-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-teal-700">
                            Se connecter
                            <ArrowLeft class="size-4 rotate-180" />
                        </Link>
                    </section>
                </main>

                <!-- Barre latérale -->
                <aside class="space-y-5 lg:sticky lg:top-6">
                    <section
                        class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                        <div class="bg-linear-to-br from-teal-600 to-cyan-700 p-5 text-white">
                            <div class="flex size-10 items-center justify-center rounded-xl bg-white/15">
                                <Sparkles class="size-5" />
                            </div>

                            <h2 class="mt-4 text-lg font-black">
                                Une communauté qui progresse ensemble
                            </h2>

                            <p class="mt-2 text-sm leading-6 text-teal-50">
                                Chaque question est une occasion d'apprendre et chaque réponse peut aider un autre
                                développeur.
                            </p>
                        </div>

                        <div class="space-y-4 p-5">
                            <div class="flex items-start gap-3">
                                <span
                                    class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-teal-50 text-teal-700 dark:bg-teal-950 dark:text-teal-300">
                                    <CheckCircle2 class="size-4" />
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">
                                        Explique ta solution
                                    </h3>
                                    <p class="mt-1 text-xs leading-5 text-slate-500 dark:text-slate-400">
                                        Décris les étapes et précise pourquoi elles fonctionnent.
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <span
                                    class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-700 dark:bg-blue-950 dark:text-blue-300">
                                    <Code2 class="size-4" />
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">
                                        Ajoute des exemples
                                    </h3>
                                    <p class="mt-1 text-xs leading-5 text-slate-500 dark:text-slate-400">
                                        Le code et les exemples concrets facilitent la compréhension.
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <span
                                    class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                                    <ThumbsUp class="size-4" />
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">
                                        Vote utilement
                                    </h3>
                                    <p class="mt-1 text-xs leading-5 text-slate-500 dark:text-slate-400">
                                        Mets en valeur les réponses qui aident vraiment.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section
                        class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                        <h2 class="text-sm font-black text-slate-900 dark:text-white">
                            À propos de cette question
                        </h2>

                        <div class="mt-4 space-y-3">
                            <div class="flex items-center justify-between gap-3 text-sm">
                                <span class="text-slate-500 dark:text-slate-400">Réponses</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ question.answers_count
                                }}</span>
                            </div>

                            <div class="flex items-center justify-between gap-3 text-sm">
                                <span class="text-slate-500 dark:text-slate-400">Appréciations</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ question.likes_count
                                }}</span>
                            </div>

                            <div class="flex items-center justify-between gap-3 text-sm">
                                <span class="text-slate-500 dark:text-slate-400">Solution acceptée</span>
                                <span
                                    :class="acceptedAnswer ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500'"
                                    class="font-bold">
                                    {{ acceptedAnswer ? 'Oui' : 'Pas encore' }}
                                </span>
                            </div>
                        </div>

                        <Link href="/questions"
                            class="mt-5 flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700 transition hover:border-teal-200 hover:bg-teal-50 hover:text-teal-800 dark:border-gray-700 dark:text-slate-300 dark:hover:bg-teal-950/40 dark:hover:text-teal-300">
                            Explorer les questions
                            <ArrowLeft class="size-4 rotate-180" />
                        </Link>
                    </section>
                </aside>
            </div>
        </div>
    </div>
</template>