<script setup lang="ts">
import { computed, ref } from 'vue'
import {
    Head,
    Link,
    router,
    useForm,
    usePage,
} from '@inertiajs/vue3'

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
            onSuccess: () => {
                cancelEditingAnswer()
            },
        },
    )
}

function submitAnswer() {
    answerForm.post(`/questions/${props.question.id}/answers`, {
        preserveScroll: true,
        onSuccess: () => answerForm.reset(),
    })
}

const votingAnswerIds = ref<number[]>([])

function isVoting(answerId: number): boolean {
    return votingAnswerIds.value.includes(answerId)
}


function vote(answer: Answer, value: number) {
    if (isVoting(answer.id)) {
        return
    }

    votingAnswerIds.value.push(answer.id)

    const currentVote = props.userAnswerVotes[answer.id]

    const options = {
        preserveScroll: true,
        onFinish: () => {
            votingAnswerIds.value = votingAnswerIds.value.filter(
                (id) => id !== answer.id,
            )
        },
    }

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
    if (!confirm('Veux-tu accepter cette réponse comme solution ?')) {
        return
    }

    router.post(
        `/questions/${props.question.id}/answers/${answer.id}/accept`,
        {},
        {
            preserveScroll: true,
        },
    )
}

function deleteAnswer(answer: Answer) {
    if (!confirm('Veux-tu vraiment supprimer cette réponse ?')) {
        return
    }

    router.delete(
        `/questions/${props.question.id}/answers/${answer.id}`,
        {
            preserveScroll: true,
        },
    )
}

function deleteQuestion() {
    if (!confirm('Veux-tu vraiment supprimer cette question ?')) {
        return
    }

    router.delete(`/questions/${props.question.id}`)
}
</script>

<template>

    <Head :title="question.title" />

    <div class="min-h-screen bg-gray-50 py-8 dark:bg-gray-950">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <!-- Navigation -->
            <div class="mb-6">
                <Link href="/questions"
                    class="inline-flex items-center text-sm font-medium text-teal-700 transition hover:text-teal-800 dark:text-teal-400 dark:hover:text-teal-300">
                    ← Retour aux questions
                </Link>
            </div>

            <!-- Question -->
            <article
                class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="p-6 sm:p-8">
                    <!-- Titre et actions -->
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div class="min-w-0 flex-1">
                            <h1
                                class="wrap-break-word text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100 sm:text-3xl">
                                {{ question.title }}
                            </h1>

                            <div
                                class="mt-3 flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-gray-500 dark:text-gray-400">
                                <span>Posée par</span>

                                <Link :href="`/developers/${question.user.id}`"
                                    class="font-medium text-gray-700 hover:text-teal-700 dark:text-gray-300 dark:hover:text-teal-400">
                                    {{ question.user.name }}
                                </Link>

                                <span aria-hidden="true">·</span>

                                <time :datetime="question.created_at">
                                    {{
                                        new Date(
                                            question.created_at,
                                        ).toLocaleDateString()
                                    }}
                                </time>
                            </div>
                        </div>

                        <div v-if="canEdit" class="flex shrink-0 flex-wrap gap-2">
                            <Link :href="`/questions/${question.id}/edit`"
                                class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                                Modifier
                            </Link>

                            <button type="button"
                                class="rounded-lg border border-red-200 bg-white px-3 py-2 text-sm font-medium text-red-600 transition hover:bg-red-50 dark:border-red-900 dark:bg-gray-900 dark:text-red-400 dark:hover:bg-red-950"
                                @click="deleteQuestion">
                                Supprimer
                            </button>
                        </div>
                    </div>

                    <!-- Tags -->
                    <div v-if="question.tags?.length" class="mt-5 flex flex-wrap gap-2">
                        <span v-for="tag in question.tags" :key="tag.id"
                            class="rounded-md bg-teal-50 px-2.5 py-1 text-xs font-semibold text-teal-800 dark:bg-teal-950 dark:text-teal-300">
                            {{ tag.name }}
                        </span>
                    </div>

                    <!-- Corps de la question -->
                    <div
                        class="mt-6 whitespace-pre-line wrap-break-word text-base leading-7 text-gray-700 dark:text-gray-300">
                        {{ question.body }}
                    </div>

                    <!-- Statistiques -->
                    <div
                        class="mt-8 flex flex-wrap items-center gap-4 border-t border-gray-200 pt-5 text-sm text-gray-500 dark:border-gray-800 dark:text-gray-400">
                        <span>
                            {{ question.answers_count }}
                            réponse(s)
                        </span>

                        <span>
                            {{ question.likes_count }}
                            appréciation(s)
                        </span>
                    </div>
                </div>
            </article>

            <!-- Liste des réponses -->
            <section class="mt-10">
                <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        Réponses
                    </h2>

                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        {{ question.answers_count }} réponse(s)
                    </span>
                </div>

                <div class="space-y-5">
                    <article v-for="answer in answers.data" :key="answer.id"
                        class="rounded-xl border bg-white shadow-sm dark:bg-gray-900" :class="answer.is_accepted
                            ? 'border-emerald-300 dark:border-emerald-800'
                            : 'border-gray-200 dark:border-gray-800'
                            ">
                        <div class="p-5 sm:p-6">
                            <!-- Badge de réponse acceptée -->
                            <div v-if="answer.is_accepted"
                                class="mb-4 inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1.5 text-sm font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                                <span aria-hidden="true">✓</span>
                                Réponse acceptée
                            </div>

                            <div class="flex items-start gap-4">
                                <!-- Votes -->
                                <div class="flex shrink-0 flex-col items-center gap-2">
                                    <button v-if="authUserId !== null && authUserId !== answer.user.id" type="button"
                                        :disabled="isVoting(answer.id)"
                                        class="flex h-9 w-9 items-center justify-center rounded-lg border transition focus:outline-none focus:ring-2 focus:ring-teal-500 disabled:cursor-wait disabled:opacity-50"
                                        :class="userAnswerVotes[answer.id] === 1
                                            ? 'border-teal-600 bg-teal-600 text-white'
                                            : 'border-gray-300 bg-white text-gray-600 hover:border-teal-500 hover:text-teal-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:border-teal-600 dark:hover:text-teal-400'"
                                        :aria-label="userAnswerVotes[answer.id] === 1
                                            ? 'Retirer le vote positif'
                                            : 'Voter positivement'" @click="vote(answer, 1)">
                                        ▲
                                    </button>

                                    <span class="text-lg font-bold" :class="(answer.votes_sum_value ?? 0) > 0
                                        ? 'text-emerald-600 dark:text-emerald-400'
                                        : (answer.votes_sum_value ??
                                            0) < 0
                                            ? 'text-red-600 dark:text-red-400'
                                            : 'text-gray-600 dark:text-gray-300'
                                        ">
                                        {{ answer.votes_sum_value ?? 0 }}
                                    </span>

                                    <button v-if="
                                        authUserId !== null &&
                                        authUserId !== answer.user.id
                                    " type="button" :disabled="isVoting(answer.id)"
                                        class="flex h-9 w-9 items-center justify-center rounded-lg border transition focus:outline-none focus:ring-2 focus:ring-teal-500"
                                        :class="userAnswerVotes[answer.id] === -1
                                            ? 'border-teal-600 bg-teal-600 text-white'
                                            : 'border-gray-300 bg-white text-gray-600 hover:border-teal-500 hover:text-teal-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:border-teal-600 dark:hover:text-teal-400'"
                                        :aria-label="userAnswerVotes[answer.id] === -1
                                            ? 'Retirer le vote négatif'
                                            : 'Voter négativement'
                                            " @click="vote(answer, -1)">
                                        ▼
                                    </button>
                                </div>

                                <!-- Contenu de la réponse -->
                                <div class="min-w-0 flex-1">
                                    <!-- Mode modification -->
                                    <form v-if="editingAnswerId === answer.id" class="space-y-4"
                                        @submit.prevent="updateAnswer(answer)">
                                        <div>
                                            <label :for="`edit-answer-${answer.id}`"
                                                class="mb-2 block text-sm font-semibold text-gray-800 dark:text-gray-200">
                                                Modifier ta réponse
                                            </label>

                                            <textarea :id="`edit-answer-${answer.id}`" v-model="editAnswerForm.body"
                                                rows="6" required minlength="10" maxlength="20000"
                                                class="w-full resize-y rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
                                                :class="{
                                                    'border-red-500 focus:border-red-500':
                                                        editAnswerForm.errors.body,
                                                }" />

                                            <p v-if="editAnswerForm.errors.body"
                                                class="mt-2 text-sm text-red-600 dark:text-red-400">
                                                {{ editAnswerForm.errors.body }}
                                            </p>
                                        </div>

                                        <div class="flex flex-wrap justify-end gap-2">
                                            <button type="button" :disabled="editAnswerForm.processing"
                                                class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 disabled:opacity-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800"
                                                @click="cancelEditingAnswer">
                                                Annuler
                                            </button>

                                            <button type="submit" :disabled="editAnswerForm.processing"
                                                class="rounded-lg bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700 disabled:cursor-not-allowed disabled:opacity-50">
                                                {{
                                                    editAnswerForm.processing
                                                        ? 'Enregistrement...'
                                                        : 'Enregistrer'
                                                }}
                                            </button>
                                        </div>
                                    </form>

                                    <!-- Mode affichage -->
                                    <div v-else
                                        class="whitespace-pre-line wrap-break-word text-gray-700 dark:text-gray-300">
                                        {{ answer.body }}
                                    </div>

                                    <div
                                        class="mt-5 flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 pt-4 dark:border-gray-800">
                                        <div
                                            class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-gray-500 dark:text-gray-400">
                                            <span>Réponse de</span>

                                            <Link :href="`/developers/${answer.user.id}`"
                                                class="font-medium text-gray-700 hover:text-teal-700 dark:text-gray-300 dark:hover:text-teal-400">
                                                {{ answer.user.name }}
                                            </Link>

                                            <span aria-hidden="true">·</span>

                                            <time :datetime="answer.created_at">
                                                {{
                                                    new Date(
                                                        answer.created_at,
                                                    ).toLocaleDateString()
                                                }}
                                            </time>
                                        </div>

                                        <!-- Actions de la réponse -->
                                        <div class="flex flex-wrap gap-2">
                                            <button v-if="
                                                canEdit &&
                                                !answer.is_accepted
                                            " type="button"
                                                class="rounded-lg bg-emerald-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900"
                                                @click="acceptAnswer(answer)">
                                                Accepter la réponse
                                            </button>

                                            <button v-if="authUserId === answer.user.id" type="button" :disabled="editAnswerForm.processing ||
                                                editingAnswerId !== null
                                                "
                                                class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
                                                @click="startEditingAnswer(answer)">
                                                Modifier
                                            </button>

                                            <button v-if="
                                                authUserId ===
                                                answer.user.id
                                            " type="button"
                                                class="rounded-lg border border-red-200 bg-white px-3 py-2 text-sm font-medium text-red-600 transition hover:bg-red-50 dark:border-red-900 dark:bg-gray-900 dark:text-red-400 dark:hover:bg-red-950"
                                                @click="deleteAnswer(answer)">
                                                Supprimer
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>

                    <!-- Aucune réponse -->
                    <div v-if="answers.data.length === 0"
                        class="rounded-xl border border-dashed border-gray-300 bg-white px-6 py-12 text-center dark:border-gray-700 dark:bg-gray-900">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            Pas encore de réponse
                        </h3>

                        <p class="mt-2 text-gray-600 dark:text-gray-400">
                            Sois le premier à partager une solution ou une
                            piste de réflexion.
                        </p>
                    </div>
                </div>

                <!-- Pagination des réponses -->
                <nav v-if="answers.links.length > 3" aria-label="Pagination des réponses"
                    class="mt-6 flex flex-wrap justify-center gap-2">
                    <template v-for="(link, index) in answers.links" :key="index">
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
            </section>

            <!-- Formulaire de réponse -->
            <section v-if="canAnswer"
                class="mt-10 rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900 sm:p-8">
                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                    Ta réponse
                </h2>

                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Explique ta solution ou partage les pistes qui pourraient
                    aider l'auteur de la question.
                </p>

                <form class="mt-5 space-y-4" @submit.prevent="submitAnswer">
                    <div>
                        <label for="answer-body"
                            class="mb-2 block text-sm font-semibold text-gray-800 dark:text-gray-200">
                            Contenu de la réponse
                        </label>

                        <textarea id="answer-body" v-model="answerForm.body" rows="7" required
                            placeholder="Rédige ta réponse..."
                            class="w-full resize-y rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 transition focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500"
                            :class="{
                                'border-red-500 focus:border-red-500 focus:ring-red-500/20':
                                    answerForm.errors.body,
                            }" />

                        <p v-if="answerForm.errors.body" class="mt-2 text-sm text-red-600 dark:text-red-400">
                            {{ answerForm.errors.body }}
                        </p>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" :disabled="answerForm.processing"
                            class="inline-flex justify-center rounded-lg bg-teal-600 px-5 py-3 font-semibold text-white transition hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 dark:focus:ring-offset-gray-900">
                            {{
                                answerForm.processing
                                    ? 'Publication...'
                                    : 'Publier ma réponse'
                            }}
                        </button>
                    </div>
                </form>
            </section>

            <!-- Invitation à se connecter -->
            <section v-else-if="!authUserId"
                class="mt-10 rounded-xl border border-gray-200 bg-white p-6 text-center shadow-sm dark:border-gray-800 dark:bg-gray-900 sm:p-8">
                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                    Tu souhaites répondre ?
                </h2>

                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Connecte-toi à DevLink pour publier une réponse et
                    participer à la discussion.
                </p>

                <Link href="/login"
                    class="mt-5 inline-flex rounded-lg bg-teal-600 px-5 py-3 font-semibold text-white transition hover:bg-teal-700">
                    Se connecter
                </Link>
            </section>
        </div>
    </div>
</template>
