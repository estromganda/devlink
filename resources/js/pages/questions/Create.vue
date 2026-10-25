<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'

interface Tag {
    id: number
    name: string
    slug: string
}

defineProps<{
    tags: Tag[]
}>()

const form = useForm({
    title: '',
    body: '',
    tags: [] as number[],
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
    form.post('/questions')
}
</script>

<template>

    <Head title="Poser une question" />

    <div class="min-h-screen bg-gray-50 py-8 dark:bg-gray-950">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <Link href="/questions"
                    class="inline-flex items-center text-sm font-medium text-teal-700 transition hover:text-teal-800 dark:text-teal-400 dark:hover:text-teal-300">
                    ← Retour aux questions
                </Link>

                <h1 class="mt-4 text-3xl font-bold tracking-tight text-gray-900 dark:text-gray-100">
                    Poser une question
                </h1>

                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Décris clairement ton problème pour aider les autres
                    développeurs à te répondre.
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
                        placeholder="Ex. : Comment configurer Laravel Reverb avec Vue ?"
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 transition focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500"
                        :class="{
                            'border-red-500 focus:border-red-500 focus:ring-red-500/20':
                                form.errors.title,
                        }" />

                    <p v-if="form.errors.title" class="mt-2 text-sm text-red-600 dark:text-red-400">
                        {{ form.errors.title }}
                    </p>

                    <p v-else class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        Utilise un titre précis qui résume ton problème.
                    </p>
                </div>

                <!-- Contenu -->
                <div>
                    <label for="body" class="mb-2 block text-sm font-semibold text-gray-800 dark:text-gray-200">
                        Description détaillée
                    </label>

                    <textarea id="body" v-model="form.body" rows="9" required
                        placeholder="Explique le problème, ce que tu as essayé et le résultat que tu souhaites obtenir..."
                        class="w-full resize-y rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 transition focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500"
                        :class="{
                            'border-red-500 focus:border-red-500 focus:ring-red-500/20':
                                form.errors.body,
                        }" />

                    <p v-if="form.errors.body" class="mt-2 text-sm text-red-600 dark:text-red-400">
                        {{ form.errors.body }}
                    </p>

                    <p v-else class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        Minimum 20 caractères. Ajoute les informations
                        nécessaires pour comprendre ton problème.
                    </p>
                </div>

                <!-- Tags -->
                <fieldset>
                    <legend class="mb-2 block text-sm font-semibold text-gray-800 dark:text-gray-200">
                        Technologies et tags
                    </legend>

                    <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                        Sélectionne entre 1 et 5 tags pertinents.
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
                    <Link href="/questions"
                        class="inline-flex justify-center rounded-lg border border-gray-300 bg-white px-5 py-3 font-semibold text-gray-700 transition hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                        Annuler
                    </Link>

                    <button type="submit" :disabled="form.processing"
                        class="inline-flex justify-center rounded-lg bg-teal-600 px-5 py-3 font-semibold text-white transition hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 dark:focus:ring-offset-gray-900">
                        {{
                            form.processing
                                ? 'Publication...'
                                : 'Publier la question'
                        }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
