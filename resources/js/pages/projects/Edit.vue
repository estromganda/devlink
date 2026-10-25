<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';

interface Project {
    id: number;
    name: string;
    slug: string;
    short_description: string | null;
    description: string;
    status: string;
    visibility: string;
    repository_url: string | null;
    demo_url: string | null;
    cover_path: string | null;
    looking_for_collaborators: boolean;
}

const props = defineProps<{
    project: Project;
}>();

const form = useForm({
    name: props.project.name,
    short_description: props.project.short_description ?? '',
    description: props.project.description,
    status: props.project.status,
    visibility: props.project.visibility,
    repository_url: props.project.repository_url ?? '',
    demo_url: props.project.demo_url ?? '',
    cover: null as File | null,
    remove_cover: false,
    looking_for_collaborators: props.project.looking_for_collaborators,
});

function submit(): void {
    form
        .transform((data) => ({
            ...data,
            _method: 'put',
        }))
        .post(`/projects/${props.project.slug}`, {
            forceFormData: true,
        });
}
</script>

<template>

    <Head :title="`Modifier ${project.name}`" />

    <main class="mx-auto max-w-3xl px-4 py-12 sm:px-6">
        <Link :href="`/projects/${project.slug}`" class="text-sm font-medium text-muted-foreground hover:text-primary">
            ← Retour au projet
        </Link>

        <h1 class="mt-6 text-3xl font-extrabold tracking-tight">
            Modifier le projet
        </h1>

        <p class="mt-3 text-muted-foreground">
            Mets à jour les informations de ton projet.
        </p>

        <form class="mt-8 space-y-6 rounded-2xl border bg-card p-5 sm:p-8" @submit.prevent="submit">
            <div>
                <label for="name" class="mb-2 block text-sm font-semibold">
                    Nom du projet *
                </label>

                <input id="name" v-model="form.name" required maxlength="150"
                    class="w-full rounded-xl border bg-background px-4 py-3 outline-none focus:border-primary" />

                <p v-if="form.errors.name" class="mt-2 text-sm text-destructive">
                    {{ form.errors.name }}
                </p>
            </div>

            <div>
                <label for="short_description" class="mb-2 block text-sm font-semibold">
                    Résumé
                </label>

                <textarea id="short_description" v-model="form.short_description" rows="2" maxlength="1000"
                    class="w-full rounded-xl border bg-background px-4 py-3 outline-none focus:border-primary" />

                <p v-if="form.errors.short_description" class="mt-2 text-sm text-destructive">
                    {{ form.errors.short_description }}
                </p>
            </div>

            <div>
                <label for="description" class="mb-2 block text-sm font-semibold">
                    Description complète *
                </label>

                <textarea id="description" v-model="form.description" required rows="7" maxlength="20000"
                    class="w-full rounded-xl border bg-background px-4 py-3 outline-none focus:border-primary" />

                <p v-if="form.errors.description" class="mt-2 text-sm text-destructive">
                    {{ form.errors.description }}
                </p>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="status" class="mb-2 block text-sm font-semibold">
                        Statut *
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

            <div class="rounded-xl border p-4">
                <p class="mb-3 text-sm font-semibold">Image de couverture</p>

                <img v-if="project.cover_path && !form.remove_cover" :src="`/storage/${project.cover_path}`"
                    :alt="`Couverture actuelle de ${project.name}`"
                    class="mb-4 max-h-56 w-full rounded-xl object-cover" />

                <p v-if="form.remove_cover" class="mb-3 text-sm text-muted-foreground">
                    La couverture actuelle sera supprimée lors de
                    l'enregistrement.
                </p>

                <label for="cover" class="mb-2 block text-sm font-medium">
                    Choisir une nouvelle image
                </label>

                <input id="cover" type="file" accept="image/jpeg,image/png,image/webp"
                    class="block w-full text-sm text-muted-foreground" @change="
                        form.cover = ($event.target as HTMLInputElement).files?.[0] ?? null;
                    if (form.cover) form.remove_cover = false;
                    " />

                <p class="mt-2 text-xs text-muted-foreground">
                    JPG, PNG ou WebP, maximum 5 Mo.
                </p>

                <p v-if="form.errors.cover" class="mt-2 text-sm text-destructive">
                    {{ form.errors.cover }}
                </p>

                <button v-if="project.cover_path && !form.remove_cover" type="button"
                    class="mt-4 text-sm font-semibold text-destructive hover:underline"
                    @click="form.remove_cover = true; form.cover = null">
                    Retirer la couverture actuelle
                </button>

                <button v-if="project.cover_path && form.remove_cover" type="button"
                    class="mt-4 text-sm font-semibold text-primary hover:underline" @click="form.remove_cover = false">
                    Conserver la couverture actuelle
                </button>
            </div>

            <label class="flex items-start gap-3 rounded-xl border p-4">
                <input v-model="form.looking_for_collaborators" type="checkbox" class="mt-1 h-4 w-4 accent-primary" />

                <span>
                    <span class="block text-sm font-semibold">
                        Je recherche des collaborateurs
                    </span>

                    <span class="mt-1 block text-sm text-muted-foreground">
                        Afficher cette information sur la page du projet.
                    </span>
                </span>
            </label>

            <div class="flex flex-wrap justify-end gap-3 border-t pt-5">
                <Link :href="`/projects/${project.slug}`"
                    class="rounded-xl border px-5 py-3 text-sm font-semibold hover:bg-muted">
                    Annuler
                </Link>

                <button type="submit" :disabled="form.processing"
                    class="rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-primary-foreground disabled:opacity-50">
                    {{ form.processing ? 'Enregistrement…' : 'Enregistrer' }}
                </button>
            </div>
        </form>
    </main>
</template>
