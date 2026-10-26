<script setup lang="ts">
import { computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import {
    ArrowLeft,
    ArrowRight,
    Check,
    CheckCircle2,
    Code2,
    FileText,
    Lightbulb,
    MessageCircle,
    Send,
    Sparkles,
    Tags as TagsIcon,
    X,
} from '@lucide/vue'

interface Tag {
    id: number
    name: string
    slug: string
}

const props = defineProps<{
    tags: Tag[]
}>()

const form = useForm({
    title: '',
    body: '',
    tags: [] as number[],
})

const selectedTags = computed(() =>
    props.tags.filter((tag) => form.tags.includes(tag.id)),
)

const titleLength = computed(() => form.title.length)
const bodyLength = computed(() => form.body.trim().length)

const titleProgress = computed(() =>
    Math.min((titleLength.value / 200) * 100, 100),
)

const canPublish = computed(() =>
    form.title.trim().length > 0 &&
    bodyLength.value >= 20 &&
    form.tags.length >= 1 &&
    form.tags.length <= 5,
)

function toggleTag(tagId: number) {
    const index = form.tags.indexOf(tagId)

    if (index !== -1) {
        form.tags.splice(index, 1)
    } else if (form.tags.length < 5) {
        form.tags.push(tagId)
    }
}

function removeTag(tagId: number) {
    const index = form.tags.indexOf(tagId)

    if (index !== -1) {
        form.tags.splice(index, 1)
    }
}

function submit() {
    form.post('/questions', {
        preserveScroll: true,
    })
}
</script>

<template>

    <Head title="Poser une question" />

    <div class="min-h-screen bg-slate-50 pb-16 dark:bg-gray-950">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Navigation -->
            <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
                <Link href="/questions"
                    class="group inline-flex items-center gap-2 text-sm font-semibold text-slate-600 transition hover:text-teal-700 dark:text-slate-400 dark:hover:text-teal-400">
                    <span
                        class="flex size-9 items-center justify-center rounded-xl border border-slate-200 bg-white transition group-hover:border-teal-200 group-hover:bg-teal-50 dark:border-gray-800 dark:bg-gray-900 dark:group-hover:border-teal-900 dark:group-hover:bg-teal-950/50">
                        <ArrowLeft class="size-4" />
                    </span>
                    Retour aux questions
                </Link>

                <div
                    class="inline-flex items-center gap-2 rounded-full border border-teal-100 bg-teal-50 px-3 py-1.5 text-xs font-bold text-teal-800 dark:border-teal-900/70 dark:bg-teal-950/50 dark:text-teal-300">
                    <Code2 class="size-4" />
                    DevLink · Questions & Réponses
                </div>
            </div>

            <!-- En-tête -->
            <header
                class="mb-8 overflow-hidden rounded-3xl bg-linear-to-br from-teal-600 via-teal-700 to-cyan-800 p-6 text-white shadow-lg shadow-teal-900/10 sm:p-9">
                <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                    <div class="max-w-2xl">
                        <div
                            class="mb-4 inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1.5 text-xs font-bold text-teal-50">
                            <Sparkles class="size-4" />
                            Partage tes connaissances
                        </div>

                        <h1 class="text-3xl font-black tracking-tight sm:text-4xl">
                            Poser une question
                        </h1>

                        <p class="mt-3 max-w-xl text-sm leading-7 text-teal-50 sm:text-base">
                            Bloqué sur un problème technique ? Décris-le clairement.
                            La communauté DevLink est là pour t'aider à trouver une solution.
                        </p>
                    </div>

                    <div class="hidden shrink-0 items-center justify-center sm:flex">
                        <div
                            class="flex size-24 items-center justify-center rounded-3xl border border-white/20 bg-white/10 shadow-inner">
                            <MessageCircle class="size-12" />
                        </div>
                    </div>
                </div>

                <div class="mt-7 grid gap-3 border-t border-white/20 pt-5 sm:grid-cols-3">
                    <div class="flex items-center gap-2.5 text-sm text-teal-50">
                        <CheckCircle2 class="size-5 shrink-0" />
                        <span>Un titre précis</span>
                    </div>

                    <div class="flex items-center gap-2.5 text-sm text-teal-50">
                        <CheckCircle2 class="size-5 shrink-0" />
                        <span>Une description détaillée</span>
                    </div>

                    <div class="flex items-center gap-2.5 text-sm text-teal-50">
                        <CheckCircle2 class="size-5 shrink-0" />
                        <span>Jusqu'à 5 tags</span>
                    </div>
                </div>
            </header>

            <div class="grid items-start gap-8 lg:grid-cols-[minmax(0,1fr)_300px]">
                <!-- Formulaire -->
                <main class="min-w-0">
                    <form
                        class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900"
                        @submit.prevent="submit">
                        <div class="border-b border-slate-100 px-5 py-5 dark:border-gray-800 sm:px-8">
                            <h2 class="text-xl font-black text-slate-900 dark:text-white">
                                Détails de ta question
                            </h2>

                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                Les champs marqués sont nécessaires pour publier.
                            </p>
                        </div>

                        <div class="space-y-8 p-5 sm:p-8">
                            <!-- Titre -->
                            <section>
                                <div class="mb-2 flex items-center justify-between gap-3">
                                    <label for="title"
                                        class="flex items-center gap-2 text-sm font-bold text-slate-800 dark:text-slate-200">
                                        <span
                                            class="flex size-8 items-center justify-center rounded-lg bg-teal-50 text-teal-700 dark:bg-teal-950/60 dark:text-teal-300">
                                            <FileText class="size-4" />
                                        </span>
                                        Titre de la question
                                        <span class="text-red-500">*</span>
                                    </label>

                                    <span class="text-xs tabular-nums"
                                        :class="titleLength > 200 ? 'text-red-600' : 'text-slate-400 dark:text-slate-500'">
                                        {{ titleLength }}/200
                                    </span>
                                </div>

                                <input id="title" v-model="form.title" type="text" maxlength="200" required
                                    autocomplete="off" placeholder="Ex. : Comment configurer Laravel Reverb avec Vue ?"
                                    class="w-full rounded-xl border bg-slate-50 px-4 py-3.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:bg-white focus:ring-4 focus:ring-teal-500/10 dark:bg-gray-950 dark:text-white dark:placeholder:text-slate-500 dark:focus:bg-gray-950"
                                    :class="form.errors.title
                                        ? 'border-red-400 focus:border-red-500'
                                        : 'border-slate-200 focus:border-teal-500 dark:border-gray-700'" />

                                <div class="mt-2 h-1 overflow-hidden rounded-full bg-slate-100 dark:bg-gray-800">
                                    <div class="h-full rounded-full bg-teal-500 transition-all duration-300"
                                        :style="{ width: `${titleProgress}%` }"></div>
                                </div>

                                <p v-if="form.errors.title" class="mt-2 text-sm text-red-600 dark:text-red-400">
                                    {{ form.errors.title }}
                                </p>

                                <p v-else class="mt-2 text-xs leading-5 text-slate-500 dark:text-slate-400">
                                    Résume ton problème en une phrase claire et précise.
                                </p>
                            </section>

                            <!-- Description -->
                            <section>
                                <div class="mb-2 flex items-center justify-between gap-3">
                                    <label for="body"
                                        class="flex items-center gap-2 text-sm font-bold text-slate-800 dark:text-slate-200">
                                        <span
                                            class="flex size-8 items-center justify-center rounded-lg bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300">
                                            <Code2 class="size-4" />
                                        </span>
                                        Description détaillée
                                        <span class="text-red-500">*</span>
                                    </label>

                                    <span class="text-xs tabular-nums text-slate-400 dark:text-slate-500">
                                        {{ bodyLength }} caractères
                                    </span>
                                </div>

                                <textarea id="body" v-model="form.body" rows="9" required
                                    placeholder="1. Quel est le problème rencontré ?&#10;2. Qu'as-tu déjà essayé ?&#10;3. Quel résultat obtiens-tu ?&#10;4. Quel résultat souhaites-tu obtenir ?"
                                    class="w-full resize-y rounded-xl border bg-slate-50 px-4 py-3.5 text-sm leading-7 text-slate-900 outline-none transition placeholder:text-slate-400 focus:bg-white focus:ring-4 focus:ring-teal-500/10 dark:bg-gray-950 dark:text-white dark:placeholder:text-slate-500 dark:focus:bg-gray-950"
                                    :class="form.errors.body
                                        ? 'border-red-400 focus:border-red-500'
                                        : 'border-slate-200 focus:border-teal-500 dark:border-gray-700'"></textarea>

                                <div class="mt-2 flex flex-wrap items-start justify-between gap-2">
                                    <p class="text-xs" :class="bodyLength < 20
                                        ? 'text-slate-500 dark:text-slate-400'
                                        : 'text-emerald-600 dark:text-emerald-400'">
                                        {{ bodyLength < 20 ? `Encore ${20 - bodyLength} caractère(s) minimum pour une
                                            description détaillée.`
                                            : '✓ Ta description contient suffisamment de détails.' }} </p>

                                            <p v-if="form.errors.body"
                                                class="text-sm font-medium text-red-600 dark:text-red-400">
                                                {{ form.errors.body }}
                                            </p>
                                </div>
                            </section>

                            <!-- Tags -->
                            <section>
                                <div class="mb-2 flex items-center gap-2">
                                    <span
                                        class="flex size-8 items-center justify-center rounded-lg bg-violet-50 text-violet-700 dark:bg-violet-950/60 dark:text-violet-300">
                                        <TagsIcon class="size-4" />
                                    </span>

                                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">
                                        Technologies et tags
                                        <span class="text-red-500">*</span>
                                    </h3>
                                </div>

                                <p class="mb-4 text-sm leading-6 text-slate-500 dark:text-slate-400">
                                    Choisis de 1 à 5 tags pour aider les développeurs concernés à trouver ta question.
                                </p>

                                <!-- Sélection actuelle -->
                                <div v-if="selectedTags.length"
                                    class="mb-4 rounded-2xl border border-teal-100 bg-teal-50/70 p-4 dark:border-teal-900/60 dark:bg-teal-950/30">
                                    <div class="mb-3 flex items-center justify-between gap-2">
                                        <span
                                            class="text-xs font-bold uppercase tracking-wide text-teal-800 dark:text-teal-300">
                                            Ta sélection
                                        </span>

                                        <span class="text-xs font-semibold text-teal-700 dark:text-teal-400">
                                            {{ form.tags.length }}/5
                                        </span>
                                    </div>

                                    <div class="flex flex-wrap gap-2">
                                        <span v-for="tag in selectedTags" :key="tag.id"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-teal-200 bg-white px-3 py-2 text-sm font-bold text-teal-800 dark:border-teal-900 dark:bg-gray-900 dark:text-teal-300">
                                            {{ tag.name }}

                                            <button type="button" :aria-label="`Retirer le tag ${tag.name}`"
                                                class="flex size-5 items-center justify-center rounded-md transition hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950/50 dark:hover:text-red-400"
                                                @click="removeTag(tag.id)">
                                                <X class="size-3.5" />
                                            </button>
                                        </span>
                                    </div>
                                </div>

                                <div v-if="tags.length" class="flex flex-wrap gap-2" role="group"
                                    aria-label="Choisir les tags">
                                    <button v-for="tag in tags" :key="tag.id" type="button"
                                        :aria-pressed="form.tags.includes(tag.id)"
                                        :disabled="!form.tags.includes(tag.id) && form.tags.length >= 5"
                                        class="inline-flex items-center gap-1.5 rounded-xl border px-3.5 py-2.5 text-sm font-semibold transition focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-40 dark:focus:ring-offset-gray-900"
                                        :class="form.tags.includes(tag.id)
                                            ? 'border-teal-600 bg-teal-600 text-white shadow-sm shadow-teal-600/20'
                                            : 'border-slate-200 bg-white text-slate-700 hover:border-teal-300 hover:bg-teal-50 dark:border-gray-700 dark:bg-gray-950 dark:text-slate-300 dark:hover:border-teal-800 dark:hover:bg-teal-950/40'"
                                        @click="toggleTag(tag.id)">
                                        <Check v-if="form.tags.includes(tag.id)" class="size-3.5" />
                                        <span v-else class="text-teal-600 dark:text-teal-400">#</span>
                                        {{ tag.name }}
                                    </button>
                                </div>

                                <div v-else
                                    class="rounded-xl border border-dashed border-slate-300 px-4 py-6 text-center dark:border-gray-700">
                                    <TagsIcon class="mx-auto size-6 text-slate-400" />
                                    <p class="mt-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                                        Aucun tag disponible
                                    </p>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                        Tu peux continuer à préparer ta question, mais il faudra des tags pour la
                                        publier.
                                    </p>
                                </div>

                                <p v-if="form.errors.tags" class="mt-3 text-sm text-red-600 dark:text-red-400">
                                    {{ form.errors.tags }}
                                </p>

                                <p v-if="form.errors['tags.0']" class="mt-2 text-sm text-red-600 dark:text-red-400">
                                    {{ form.errors['tags.0'] }}
                                </p>

                                <p v-if="form.errors['tags.1']" class="mt-2 text-sm text-red-600 dark:text-red-400">
                                    {{ form.errors['tags.1'] }}
                                </p>

                                <p class="mt-3 text-xs text-slate-500 dark:text-slate-400">
                                    {{ form.tags.length === 0
                                        ? 'Sélectionne au moins un tag.'
                                        : form.tags.length === 5
                                            ? 'Limite atteinte : tu as sélectionné 5 tags.'
                                            : `${5 - form.tags.length} sélection(s) supplémentaire(s) possible(s).` }}
                                </p>
                            </section>

                            <!-- Aperçu -->
                            <section
                                class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-gray-800 dark:bg-gray-950 sm:p-5">
                                <div class="mb-3 flex items-center gap-2">
                                    <Lightbulb class="size-4 text-amber-500" />
                                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">
                                        Aperçu de ta question
                                    </h3>
                                </div>

                                <p class="wrap-break-word text-base font-bold text-slate-900 dark:text-white">
                                    {{ form.title.trim() || 'Le titre de ta question apparaîtra ici' }}
                                </p>

                                <p
                                    class="mt-2 whitespace-pre-line wrap-break-word text-sm leading-6 text-slate-600 dark:text-slate-400">
                                    {{ form.body.trim() ||
                                        'La description détaillée apparaîtra ici lorsque tu commenceras à écrire.' }}
                                </p>

                                <div v-if="selectedTags.length" class="mt-3 flex flex-wrap gap-2">
                                    <span v-for="tag in selectedTags" :key="tag.id"
                                        class="rounded-lg bg-teal-100 px-2.5 py-1 text-xs font-bold text-teal-800 dark:bg-teal-950 dark:text-teal-300">
                                        # {{ tag.name }}
                                    </span>
                                </div>
                            </section>
                        </div>

                        <!-- Actions -->
                        <footer
                            class="flex flex-col-reverse gap-3 border-t border-slate-100 bg-slate-50/80 px-5 py-5 dark:border-gray-800 dark:bg-gray-950/50 sm:flex-row sm:items-center sm:justify-between sm:px-8">
                            <Link href="/questions"
                                class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-100 dark:border-gray-700 dark:bg-gray-900 dark:text-slate-300 dark:hover:bg-gray-800">
                                <ArrowLeft class="size-4" />
                                Annuler
                            </Link>

                            <button type="submit" :disabled="form.processing"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-teal-600 px-6 py-3.5 text-sm font-bold text-white shadow-sm shadow-teal-600/20 transition hover:-translate-y-0.5 hover:bg-teal-700 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:translate-y-0">
                                <span>
                                    {{ form.processing ? 'Publication en cours…' : 'Publier ma question' }}
                                </span>

                                <span v-if="!form.processing"
                                    class="flex size-5 items-center justify-center rounded-md bg-white/15">
                                    <ArrowRight class="size-3.5" />
                                </span>
                            </button>
                        </footer>
                    </form>
                </main>

                <!-- Conseils latéraux -->
                <aside class="space-y-5 lg:sticky lg:top-6">
                    <section
                        class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                        <div class="flex items-center gap-2">
                            <span
                                class="flex size-9 items-center justify-center rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-950/50 dark:text-amber-400">
                                <Lightbulb class="size-5" />
                            </span>

                            <h2 class="font-black text-slate-900 dark:text-white">
                                Conseils pratiques
                            </h2>
                        </div>

                        <div class="mt-5 space-y-5">
                            <div class="flex items-start gap-3">
                                <span
                                    class="flex size-7 shrink-0 items-center justify-center rounded-lg bg-teal-50 text-xs font-black text-teal-700 dark:bg-teal-950 dark:text-teal-300">
                                    1
                                </span>

                                <div>
                                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">
                                        Un titre explicite
                                    </h3>
                                    <p class="mt-1 text-xs leading-5 text-slate-500 dark:text-slate-400">
                                        Indique la technologie et le problème rencontré.
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <span
                                    class="flex size-7 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-xs font-black text-blue-700 dark:bg-blue-950 dark:text-blue-300">
                                    2
                                </span>

                                <div>
                                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">
                                        Le contexte compte
                                    </h3>
                                    <p class="mt-1 text-xs leading-5 text-slate-500 dark:text-slate-400">
                                        Partage les erreurs, les extraits utiles et les essais déjà effectués.
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <span
                                    class="flex size-7 shrink-0 items-center justify-center rounded-lg bg-violet-50 text-xs font-black text-violet-700 dark:bg-violet-950 dark:text-violet-300">
                                    3
                                </span>

                                <div>
                                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">
                                        Choisis les bons tags
                                    </h3>
                                    <p class="mt-1 text-xs leading-5 text-slate-500 dark:text-slate-400">
                                        Ils permettent aux développeurs concernés de retrouver ta question.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section
                        class="rounded-2xl border border-teal-100 bg-linear-to-br from-teal-50 to-cyan-50 p-5 dark:border-teal-900/60 dark:from-teal-950/40 dark:to-cyan-950/30">
                        <div
                            class="flex size-10 items-center justify-center rounded-xl bg-white text-teal-700 shadow-sm dark:bg-gray-900 dark:text-teal-300">
                            <MessageCircle class="size-5" />
                        </div>

                        <h2 class="mt-4 font-black text-slate-900 dark:text-white">
                            Chaque question compte
                        </h2>

                        <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-400">
                            Une question que tu poses aujourd'hui peut aider de nombreux développeurs demain.
                        </p>

                        <div class="mt-4 flex items-center gap-2 text-xs font-bold text-teal-800 dark:text-teal-300">
                            <CheckCircle2 class="size-4" />
                            Apprenons les uns des autres
                        </div>
                    </section>

                    <!-- État du formulaire -->
                    <section
                        class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                        <h2 class="text-sm font-black text-slate-900 dark:text-white">
                            Prêt à publier ?
                        </h2>

                        <div class="mt-4 space-y-3">
                            <div class="flex items-center justify-between gap-3 text-sm">
                                <span class="text-slate-500 dark:text-slate-400">Titre</span>
                                <span class="inline-flex items-center gap-1.5 text-xs font-bold"
                                    :class="form.title.trim() ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400'">
                                    <CheckCircle2 v-if="form.title.trim()" class="size-4" />
                                    <span>{{ form.title.trim() ? 'Renseigné' : 'À compléter' }}</span>
                                </span>
                            </div>

                            <div class="flex items-center justify-between gap-3 text-sm">
                                <span class="text-slate-500 dark:text-slate-400">Description</span>
                                <span class="inline-flex items-center gap-1.5 text-xs font-bold"
                                    :class="bodyLength >= 20 ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400'">
                                    <CheckCircle2 v-if="bodyLength >= 20" class="size-4" />
                                    <span>{{ bodyLength >= 20 ? 'Complète' : '20 caractères min.' }}</span>
                                </span>
                            </div>

                            <div class="flex items-center justify-between gap-3 text-sm">
                                <span class="text-slate-500 dark:text-slate-400">Tags</span>
                                <span class="inline-flex items-center gap-1.5 text-xs font-bold"
                                    :class="form.tags.length > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400'">
                                    <CheckCircle2 v-if="form.tags.length > 0" class="size-4" />
                                    <span>{{ form.tags.length }}/5 sélectionné(s)</span>
                                </span>
                            </div>
                        </div>

                        <div class="mt-5 h-2 overflow-hidden rounded-full bg-slate-100 dark:bg-gray-800">
                            <div class="h-full rounded-full bg-linear-to-r from-teal-500 to-cyan-500 transition-all duration-300"
                                :style="{
                                    width: `${[
                                        Boolean(form.title.trim()),
                                        bodyLength >= 20,
                                        form.tags.length > 0,
                                    ].filter(Boolean).length / 3 * 100}%`,
                                }"></div>
                        </div>

                        <p class="mt-3 text-xs leading-5 text-slate-500 dark:text-slate-400">
                            {{ canPublish
                                ? 'Tout semble prêt. Tu peux publier ta question.'
                                : 'Complète les champs obligatoires pour préparer ta question.' }}
                        </p>
                    </section>
                </aside>
            </div>
        </div>
    </div>
</template>