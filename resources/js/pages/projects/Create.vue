<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    short_description: '',
    description: '',
    status: 'planning',
    visibility: 'public',
    repository_url: '',
    demo_url: '',
    cover: null as File | null,
    looking_for_collaborators: false,
});

function submit(): void {
    form.post('/projects', {
        forceFormData: true,
    });
}
</script>

<template>

    <Head title="Créer un projet" />

    <main class="mx-auto max-w-3xl px-4 py-12 sm:px-6">
        <Link href="/projects" class="text-sm font-medium text-muted-foreground hover:text-primary">
            ← Retour aux projets
        </Link>

        <div class="mt-6">
            <p class="text-sm font-semibold uppercase tracking-widest text-primary">
                Partage tes idées
            </p>

            <h1 class="mt-3 text-3xl font-extrabold tracking-tight">
                Présenter un projet
            </h1>

            <p class="mt-3 leading-7 text-muted-foreground">
                Présente ton travail à la communauté DevLink et, si tu le
                souhaites, trouve des développeurs pour t'accompagner.
            </p>
        </div>

        <form class="mt-8 space-y-6 rounded-2xl border bg-card p-5 sm:p-8" @submit.prevent="submit">
            <div>
                <label for="name" class="mb-2 block text-sm font-semibold">
                    Nom du projet *
                </label>

                <input id="name" v-model="form.name" required maxlength="150"
                    class="w-full rounded-xl border bg-background px-4 py-3 outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"
                    placeholder="Ex. DevLink" />

                <p v-if="form.errors.name" class="mt-2 text-sm text-destructive">
                    {{ form.errors.name }}
                </p>
            </div>

            <div>
                <label for="short_description" class="mb-2 block text-sm font-semibold">
                    Résumé du projet
                </label>

                <textarea id="short_description" v-model="form.short_description" rows="2" maxlength="1000"
                    class="w-full rounded-xl border bg-background px-4 py-3 outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"
                    placeholder="Présente ton projet en quelques phrases." />

                <p v-if="form.errors.short_description" class="mt-2 text-sm text-destructive">
                    {{ form.errors.short_description }}
                </p>
            </div>

            <div>
                <label for="description" class="mb-2 block text-sm font-semibold">
                    Description complète *
                </label>

                <textarea id="description" v-model="form.description" required rows="7" maxlength="20000"
                    class="w-full rounded-xl border bg-background px-4 py-3 outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"
                    placeholder="Quel problème ton projet résout-il ? Comment fonctionne-t-il ? Quelles technologies utilises-tu ?" />

                <p v-if="form.errors.description" class="mt-2 text-sm text-destructive">
                    {{ form.errors.description }}
                </p>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="status" class="mb-2 block text-sm font-semibold">
                        État du projet *
                    </label>

                    <select id="status" v-model="form.status"
                        class="w-full rounded-xl border bg-background px-4 py-3 outline-none focus:border-primary">
                        <option value="planning">En préparation</option>
                        <option value="in_progress">En développement</option>
                        <option value="completed">Terminé</option>
                        <option value="paused">En pause</option>
                    </select>

                    <p v-if="form.errors.status" class="mt-2 text-sm text-destructive">
                        {{ form.errors.status }}
                    </p>
                </div>

                <div>
                    <label for="visibility" class="mb-2 block text-sm font-semibold">
                        Visibilité *
                    </label>

                    <select id="visibility" v-model="form.visibility"
                        class="w-full rounded-xl border bg-background px-4 py-3 outline-none focus:border-primary">
                        <option value="public">Public</option>
                        <option value="private">Privé</option>
                    </select>

                    <p v-if="form.errors.visibility" class="mt-2 text-sm text-destructive">
                        {{ form.errors.visibility }}
                    </p>
                </div>
            </div>

            <div>
                <label for="repository_url" class="mb-2 block text-sm font-semibold">
                    Dépôt du code source
                </label>

                <input id="repository_url" v-model="form.repository_url" type="url"
                    class="w-full rounded-xl border bg-background px-4 py-3 outline-none focus:border-primary"
                    placeholder="https://github.com/..." />

                <p v-if="form.errors.repository_url" class="mt-2 text-sm text-destructive">
                    {{ form.errors.repository_url }}
                </p>
            </div>

            <div>
                <label for="demo_url" class="mb-2 block text-sm font-semibold">
                    Démonstration en ligne
                </label>

                <input id="demo_url" v-model="form.demo_url" type="url"
                    class="w-full rounded-xl border bg-background px-4 py-3 outline-none focus:border-primary"
                    placeholder="https://..." />

                <p v-if="form.errors.demo_url" class="mt-2 text-sm text-destructive">
                    {{ form.errors.demo_url }}
                </p>
            </div>

            <div>
                <label for="cover" class="mb-2 block text-sm font-semibold">
                    Image de couverture
                </label>

                <input id="cover" type="file" accept="image/jpeg,image/png,image/webp"
                    class="block w-full text-sm text-muted-foreground file:mr-4 file:rounded-lg file:border-0 file:bg-muted file:px-4 file:py-2 file:font-semibold"
                    @change="
                        form.cover = ($event.target as HTMLInputElement).files?.[0] ?? null
                        " />

                <p class="mt-2 text-xs text-muted-foreground">
                    JPG, PNG ou WebP, maximum 5 Mo.
                </p>

                <p v-if="form.errors.cover" class="mt-2 text-sm text-destructive">
                    {{ form.errors.cover }}
                </p>
            </div>

            <label class="flex items-start gap-3 rounded-xl border p-4 transition hover:bg-muted/40">
                <input v-model="form.looking_for_collaborators" type="checkbox"
                    class="mt-1 h-4 w-4 rounded accent-primary" />

                <span>
                    <span class="block text-sm font-semibold">
                        Je recherche des collaborateurs
                    </span>

                    <span class="mt-1 block text-sm text-muted-foreground">
                        Indique aux autres développeurs que tu souhaites
                        accueillir de nouveaux participants.
                    </span>
                </span>
            </label>

            <p v-if="form.errors.looking_for_collaborators" class="text-sm text-destructive">
                {{ form.errors.looking_for_collaborators }}
            </p>

            <div class="flex flex-wrap items-center justify-end gap-3 border-t pt-5">
                <Link href="/projects"
                    class="rounded-xl border px-5 py-3 text-sm font-semibold transition hover:bg-muted">
                    Annuler
                </Link>

                <button type="submit" :disabled="form.processing"
                    class="rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-primary-foreground transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-50">
                    {{ form.processing ? 'Publication…' : 'Créer le projet' }}
                </button>
            </div>
        </form>
    </main>
</template>
