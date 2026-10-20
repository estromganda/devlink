<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    description: '',
    is_private: false,
});

function submit() {
    form.post('/communities', {
        preserveScroll: true,
    });
}
</script>

<template>

    <Head title="Créer une communauté" />

    <div class="min-h-screen bg-gray-50 dark:bg-gray-950">
        <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
            <Link href="/communities" class="text-sm font-medium text-indigo-600 hover:underline dark:text-indigo-400">
                ← Retour aux communautés
            </Link>

            <div
                class="mt-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900 sm:p-8">
                <div class="mb-8">
                    <p class="text-sm font-semibold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">
                        DevLink
                    </p>

                    <h1 class="mt-2 text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                        Créer une communauté
                    </h1>

                    <p class="mt-3 text-gray-600 dark:text-gray-400">
                        Rassemblez des développeurs autour d'une technologie,
                        d'un projet ou d'un centre d'intérêt.
                    </p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Nom -->
                    <div>
                        <label for="name" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-200">
                            Nom de la communauté
                        </label>

                        <input id="name" v-model="form.name" type="text" maxlength="120" required
                            placeholder="Ex. : Laravel France"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                            :class="{
                                'border-red-500': form.errors.name,
                            }" />

                        <p v-if="form.errors.name" class="mt-2 text-sm text-red-600">
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description"
                            class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-200">
                            Description
                        </label>

                        <textarea id="description" v-model="form.description" rows="5" maxlength="5000"
                            placeholder="Quel est l'objectif de votre communauté ?"
                            class="w-full resize-y rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                            :class="{
                                'border-red-500': form.errors.description,
                            }" />

                        <p v-if="form.errors.description" class="mt-2 text-sm text-red-600">
                            {{ form.errors.description }}
                        </p>

                        <p class="mt-1 text-right text-xs text-gray-500">
                            {{ form.description.length }}/5000
                        </p>
                    </div>

                    <!-- Visibilité -->
                    <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
                        <label class="flex cursor-pointer items-start gap-3" for="is_private">
                            <input id="is_private" v-model="form.is_private" type="checkbox"
                                class="mt-1 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />

                            <span>
                                <span class="block text-sm font-semibold text-gray-900 dark:text-white">
                                    Communauté privée
                                </span>

                                <span class="mt-1 block text-sm leading-5 text-gray-600 dark:text-gray-400">
                                    Si cette option est activée, la communauté
                                    ne sera pas visible dans l'annuaire public.
                                </span>
                            </span>
                        </label>
                    </div>

                    <!-- Erreurs générales -->
                    <div v-if="form.hasErrors"
                        class="rounded-lg bg-red-50 p-4 text-sm text-red-700 dark:bg-red-950 dark:text-red-300">
                        Vérifiez les champs signalés avant de continuer.
                    </div>

                    <!-- Boutons -->
                    <div
                        class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-6 dark:border-gray-800 sm:flex-row sm:justify-end">
                        <Link href="/communities"
                            class="rounded-lg border border-gray-300 px-5 py-3 text-center text-sm font-semibold text-gray-700 transition hover:bg-gray-100 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">
                            Annuler
                        </Link>

                        <button type="submit" :disabled="form.processing"
                            class="rounded-lg bg-indigo-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60">
                            {{
                                form.processing
                                    ? 'Création en cours...'
                                    : 'Créer la communauté'
                            }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>