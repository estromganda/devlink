<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';

interface Community {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    avatar_path: string | null;
    cover_path: string | null;
    is_private: boolean;
}

const props = defineProps<{
    community: Community;
}>();

const form = useForm({
    name: props.community.name,
    description: props.community.description ?? '',
    is_private: props.community.is_private,
    avatar: null as File | null,
    cover: null as File | null,
});

function selectAvatar(event: Event) {
    const input = event.target as HTMLInputElement;
    form.avatar = input.files?.[0] ?? null;
}

function selectCover(event: Event) {
    const input = event.target as HTMLInputElement;
    form.cover = input.files?.[0] ?? null;
}

function submit() {
    form
        .transform((data) => ({
            ...data,
            _method: 'put',
        }))
        .post(`/communities/${props.community.slug}`, {
            forceFormData: true,
            preserveScroll: true,
        });
}
</script>

<template>

    <Head :title="`Modifier ${community.name}`" />

    <div class="min-h-screen bg-background text-foreground">
        <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
            <Link :href="`/communities/${community.slug}`"
                class="text-sm font-medium text-muted-foreground transition-colors hover:text-primary">
                ← Retour à la communauté
            </Link>

            <div class="mt-6">
                <h1 class="text-3xl font-bold tracking-tight">
                    Modifier la communauté
                </h1>

                <p class="mt-2 text-sm text-muted-foreground">
                    Mets à jour les informations et l'apparence de ta communauté.
                </p>
            </div>

            <form class="mt-8 space-y-6 rounded-2xl border bg-card p-6 shadow-sm sm:p-8" @submit.prevent="submit">
                <!-- Nom -->
                <div>
                    <label for="name" class="mb-2 block text-sm font-medium">
                        Nom de la communauté
                    </label>

                    <input id="name" v-model="form.name" type="text" required minlength="3" maxlength="120"
                        class="w-full rounded-lg border bg-background px-4 py-3 text-foreground outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                        placeholder="Ex. Laravel Developers" />

                    <p v-if="form.errors.name" class="mt-2 text-sm text-destructive">
                        {{ form.errors.name }}
                    </p>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="mb-2 block text-sm font-medium">
                        Description
                    </label>

                    <textarea id="description" v-model="form.description" rows="5" maxlength="5000"
                        class="w-full resize-y rounded-lg border bg-background px-4 py-3 text-foreground outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                        placeholder="Présente ta communauté et explique ce que les membres peuvent y trouver..." />

                    <div class="mt-1 flex justify-between gap-4">
                        <p v-if="form.errors.description" class="text-sm text-destructive">
                            {{ form.errors.description }}
                        </p>

                        <p class="ml-auto text-xs text-muted-foreground">
                            {{ form.description.length }}/5000
                        </p>
                    </div>
                </div>

                <!-- Visibilité -->
                <div class="rounded-xl border p-4">
                    <label for="is_private" class="flex cursor-pointer items-start gap-3">
                        <input id="is_private" v-model="form.is_private" type="checkbox"
                            class="mt-1 h-4 w-4 rounded border-input accent-primary" />

                        <span>
                            <span class="block text-sm font-medium">
                                Communauté privée
                            </span>

                            <span class="mt-1 block text-sm text-muted-foreground">
                                Une communauté privée ne doit pas apparaître dans l'annuaire public.
                            </span>
                        </span>
                    </label>

                    <p v-if="form.errors.is_private" class="mt-2 text-sm text-destructive">
                        {{ form.errors.is_private }}
                    </p>
                </div>

                <!-- Avatar -->
                <div>
                    <label for="avatar" class="mb-2 block text-sm font-medium">
                        Avatar
                    </label>

                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                        <img v-if="community.avatar_path" :src="`/storage/${community.avatar_path}`"
                            :alt="`Avatar actuel de ${community.name}`"
                            class="h-20 w-20 rounded-xl border object-cover" />

                        <div class="min-w-0 flex-1">
                            <input id="avatar" type="file" accept="image/jpeg,image/png,image/webp"
                                class="block w-full text-sm text-muted-foreground file:mr-4 file:rounded-lg file:border-0 file:bg-primary/10 file:px-4 file:py-2 file:font-medium file:text-primary hover:file:bg-primary/20"
                                @change="selectAvatar" />

                            <p class="mt-2 text-xs text-muted-foreground">
                                JPG, PNG ou WebP — 2 Mo maximum.
                            </p>
                        </div>
                    </div>

                    <p v-if="form.errors.avatar" class="mt-2 text-sm text-destructive">
                        {{ form.errors.avatar }}
                    </p>
                </div>

                <!-- Bannière -->
                <div>
                    <label for="cover" class="mb-2 block text-sm font-medium">
                        Bannière
                    </label>

                    <img v-if="community.cover_path" :src="`/storage/${community.cover_path}`"
                        :alt="`Bannière actuelle de ${community.name}`"
                        class="mb-4 h-40 w-full rounded-xl border object-cover" />

                    <input id="cover" type="file" accept="image/jpeg,image/png,image/webp"
                        class="block w-full text-sm text-muted-foreground file:mr-4 file:rounded-lg file:border-0 file:bg-primary/10 file:px-4 file:py-2 file:font-medium file:text-primary hover:file:bg-primary/20"
                        @change="selectCover" />

                    <p class="mt-2 text-xs text-muted-foreground">
                        JPG, PNG ou WebP — 5 Mo maximum.
                    </p>

                    <p v-if="form.errors.cover" class="mt-2 text-sm text-destructive">
                        {{ form.errors.cover }}
                    </p>
                </div>

                <!-- Erreurs générales -->
                <div v-if="form.hasErrors"
                    class="rounded-lg border border-destructive/30 bg-destructive/10 p-4 text-sm text-destructive"
                    role="alert">
                    Certains champs contiennent des erreurs. Vérifie le formulaire avant de réessayer.
                </div>

                <!-- Actions -->
                <div class="flex flex-col-reverse gap-3 border-t pt-6 sm:flex-row sm:justify-end">
                    <Link :href="`/communities/${community.slug}`"
                        class="inline-flex items-center justify-center rounded-lg border bg-background px-5 py-2.5 text-sm font-semibold transition-colors hover:bg-muted">
                        Annuler
                    </Link>

                    <button type="submit" :disabled="form.processing"
                        class="inline-flex items-center justify-center rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-primary-foreground transition-opacity hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-50">
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
