<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps<{
    communities: Array<{
        id: number;
        name: string;
    }>;
}>();

const form = useForm({
    title: '',
    body: '',
    community_id: '' as number | '',
});

function submit() {
    form.post('/posts');
}
</script>

<template>

    <Head title="Créer une publication" />

    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6">
        <div class="mb-6">
            <Link href="/posts" class="text-sm text-muted-foreground hover:text-foreground">
                ← Retour au fil d'actualité
            </Link>

            <h1 class="mt-4 text-3xl font-bold tracking-tight">
                Créer une publication
            </h1>

            <p class="mt-2 text-muted-foreground">
                Partage une idée, pose une question ou présente ton projet
                à la communauté des développeurs.
            </p>
        </div>

        <form class="space-y-6 rounded-2xl border bg-card p-6 shadow-sm" @submit.prevent="submit">
            <div>
                <label for="title" class="mb-2 block text-sm font-medium">
                    Titre <span class="text-muted-foreground">(facultatif)</span>
                </label>

                <input id="title" v-model="form.title" type="text" maxlength="200"
                    placeholder="Ex. : Comment organiser un projet Laravel ?"
                    class="w-full rounded-lg border bg-background px-4 py-3 outline-none transition focus:ring-2 focus:ring-ring" />

                <p v-if="form.errors.title" class="mt-1 text-sm text-destructive">
                    {{ form.errors.title }}
                </p>
            </div>

            <div>
                <label for="community" class="mb-2 block text-sm font-medium">
                    Publier dans
                </label>

                <select id="community" v-model="form.community_id"
                    class="w-full rounded-lg border bg-background px-4 py-3 outline-none transition focus:ring-2 focus:ring-ring">
                    <option value="">
                        Le fil général de DevLink
                    </option>

                    <option v-for="community in communities" :key="community.id" :value="community.id">
                        {{ community.name }}
                    </option>
                </select>

                <p class="mt-1 text-sm text-muted-foreground">
                    Tu peux choisir une communauté dont tu es membre.
                </p>

                <p v-if="form.errors.community_id" class="mt-1 text-sm text-destructive">
                    {{ form.errors.community_id }}
                </p>
            </div>

            <div>
                <label for="body" class="mb-2 block text-sm font-medium">
                    Contenu
                </label>

                <textarea id="body" v-model="form.body" rows="9" maxlength="20000" required
                    placeholder="Qu'aimerais-tu partager avec les autres développeurs ?"
                    class="w-full resize-y rounded-lg border bg-background px-4 py-3 outline-none transition focus:ring-2 focus:ring-ring"></textarea>

                <div class="mt-1 flex justify-between gap-3">
                    <p v-if="form.errors.body" class="text-sm text-destructive">
                        {{ form.errors.body }}
                    </p>

                    <span class="ml-auto text-xs text-muted-foreground">
                        {{ form.body.length }} / 20000
                    </span>
                </div>
            </div>

            <div class="flex flex-wrap justify-end gap-3">
                <Link href="/posts"
                    class="rounded-lg border px-5 py-2.5 text-sm font-semibold transition hover:bg-muted">
                    Annuler
                </Link>

                <button type="submit" :disabled="form.processing || !form.body.trim()"
                    class="rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-primary-foreground transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-50">
                    {{
                        form.processing
                            ? 'Publication en cours…'
                            : 'Publier'
                    }}
                </button>
            </div>
        </form>
    </div>
</template>