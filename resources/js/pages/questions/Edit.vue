<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'

interface Tag {
    id: number
    name: string
    slug: string
}

interface Question {
    id: number
    title: string
    body: string
    tags: Tag[]
}

const props = defineProps<{
    question: Question
    tags: Tag[]
}>()

const form = useForm({
    title: props.question.title ?? '',
    body: props.question.body ?? '',
    tags: props.question.tags?.map((tag) => tag.id) ?? [],
})

function toggleTag(tagId: number) {
    const index = form.tags.indexOf(tagId)

    if (index !== -1) {
        form.tags.splice(index, 1)
    } else if (form.tags.length < 5) {
        form.tags.push(tagId)
    }
}

function submit() {
    form.put(`/questions/${props.question.id}`)
}
</script>

<template>

    <Head :title="`Modifier : ${question.title}`" />

    <div class="min-h-screen bg-gray-50 py-8 dark:bg-gray-950">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <Link :href="`/questions/${question.id}`"
                    class="inline-flex items-center text-sm font-medium text-teal-700 transition hover:text-teal-800 dark:text-teal-400 dark:hover:text-teal-300">
                    ← Retour à la question
                </Link>

                <h1 class="mt-4 text-3xl font-bold tracking-tight text-gray-900 dark:text-gray-100">
                    Modifier la question
                </h1>

                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Mets à jour le titre, la description ou les tags de ta
                    question.
                </p>
            </div>

            <form
                class="space-y-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900 sm:p-8"
                @submit.prevent="submit">
                <!-- Titre -->
                <div>
                    <label for="title" class="mb-2 block text-sm font-semibold text-gray-800 dark:text-gray-200">
                        Titre de la question
                    </label>

                    <input id="title" v-model="form.title" type="text" maxlength="200" required
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 transition focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500"
                        :class="{
                            'border-red-500 focus:border-red-500 focus:ring-red-500/20':
                                form.errors.title,
                        }" />

                    <p v-if="form.errors.title" class="mt-2 text-sm text-red-600 dark:text-red-400">
                        {{ form.errors.title }}
                    </p>
                </div>

                <!-- Contenu -->
                <div>
                    <label for="body" class="mb-2 block text-sm font-semibold text-gray-800 dark:text-gray-200">
                        Description détaillée
                    </label>

                    <textarea id="body" v-model="form.body" rows="9" required
                        class="w-full resize-y rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 transition focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500"
                        :class="{
                            'border-red-500 focus:border-red-500 focus:ring-red-500/20':
                                form.errors.body,
                        }" />

                    <p v-if="form.errors.body" class="mt-2 text-sm text-red-600 dark:text-red-400">
                        {{ form.errors.body }}
                    </p>
                </div>

                <!-- Tags -->
                <fieldset>
                    <legend class="mb-2 block text-sm font-semibold text-gray-800 dark:text-gray-200">
                        Technologies et tags
                    </legend>

                    <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                        Sélectionne entre 1 et 5 tags.
                        {{ form.tags.length }}/5 sélectionné(s).
                    </p>

                    <div class="flex flex-wrap gap-2" role="group" aria-label="Choisir les tags">
                        <button v-for="tag in tags" :key="tag.id" type="button"
                            class="rounded-lg border px-3 py-2 text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900"
                            :class="form.tags.includes(tag.id)
                                ? 'border-teal-600 bg-teal-600 text-white'
                                : 'border-gray-300 bg-white text-gray-700 hover:border-teal-400 hover:bg-teal-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:border-teal-700 dark:hover:bg-gray-700'
                                " :aria-pressed="form.tags.includes(tag.id)" :disabled="!form.tags.includes(tag.id) &&
                                    form.tags.length >= 5
                                    " @click="toggleTag(tag.id)">
                            {{ tag.name }}
                        </button>
                    </div>

                    <p v-if="form.errors.tags" class="mt-3 text-sm text-red-600 dark:text-red-400">
                        {{ form.errors.tags }}
                    </p>

                    <p v-if="form.errors['tags.0']" class="mt-2 text-sm text-red-600 dark:text-red-400">
                        {{ form.errors['tags.0'] }}
                    </p>
                </fieldset>

                <!-- Actions -->
                <div
                    class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-6 dark:border-gray-800 sm:flex-row sm:justify-end">
                    <Link :href="`/questions/${question.id}`"
                        class="inline-flex justify-center rounded-lg border border-gray-300 bg-white px-5 py-3 font-semibold text-gray-700 transition hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                        Annuler
                    </Link>

                    <button type="submit" :disabled="form.processing"
                        class="inline-flex justify-center rounded-lg bg-teal-600 px-5 py-3 font-semibold text-white transition hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 dark:focus:ring-offset-gray-900">
                        {{
                            form.processing
                                ? 'Enregistrement...'
                                : 'Enregistrer les modifications'
                        }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
