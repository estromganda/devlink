<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, ref } from 'vue';

const form = useForm({
    name: '',
    description: '',
    is_private: false,
    avatar: null as File | null,
    cover: null as File | null,
});

const avatarInput = ref<HTMLInputElement | null>(null);
const coverInput = ref<HTMLInputElement | null>(null);

const avatarPreview = ref<string | null>(null);
const coverPreview = ref<string | null>(null);

const avatarPreviewIsLocal = ref(false);
const coverPreviewIsLocal = ref(false);

const avatarInitial = computed(
    () => form.name.trim().charAt(0).toUpperCase() || '?',
);

function updateImagePreview(
    event: Event,
    type: 'avatar' | 'cover',
) {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0] ?? null;

    if (type === 'avatar') {
        if (avatarPreview.value && avatarPreviewIsLocal.value) {
            URL.revokeObjectURL(avatarPreview.value);
        }

        form.avatar = file;
        avatarPreview.value = file ? URL.createObjectURL(file) : null;
        avatarPreviewIsLocal.value = !!file;
    } else {
        if (coverPreview.value && coverPreviewIsLocal.value) {
            URL.revokeObjectURL(coverPreview.value);
        }

        form.cover = file;
        coverPreview.value = file ? URL.createObjectURL(file) : null;
        coverPreviewIsLocal.value = !!file;
    }
}

function removeImage(type: 'avatar' | 'cover') {
    if (type === 'avatar') {
        if (avatarPreview.value && avatarPreviewIsLocal.value) {
            URL.revokeObjectURL(avatarPreview.value);
        }

        form.avatar = null;
        avatarPreview.value = null;
        avatarPreviewIsLocal.value = false;

        if (avatarInput.value) {
            avatarInput.value.value = '';
        }
    } else {
        if (coverPreview.value && coverPreviewIsLocal.value) {
            URL.revokeObjectURL(coverPreview.value);
        }

        form.cover = null;
        coverPreview.value = null;
        coverPreviewIsLocal.value = false;

        if (coverInput.value) {
            coverInput.value.value = '';
        }
    }
}

function submit() {
    form.post('/communities', {
        preserveScroll: true,
        forceFormData: true,
    });
}

onBeforeUnmount(() => {
    if (avatarPreview.value && avatarPreviewIsLocal.value) {
        URL.revokeObjectURL(avatarPreview.value);
    }

    if (coverPreview.value && coverPreviewIsLocal.value) {
        URL.revokeObjectURL(coverPreview.value);
    }
});
</script>

<template>

    <Head title="Créer une communauté" />

    <div class="min-h-screen bg-background text-foreground">
        <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Retour -->
            <Link href="/communities"
                class="inline-flex items-center gap-2 text-sm font-medium text-muted-foreground transition-colors hover:text-primary">
                <span aria-hidden="true">←</span>
                Retour aux communautés
            </Link>

            <!-- En-tête -->
            <div class="mt-6">
                <div class="overflow-hidden rounded-2xl border bg-card shadow-sm">
                    <div class="bg-linear-to-br from-primary/15 via-primary/5 to-background px-6 py-8 sm:px-8">
                        <p class="text-sm font-semibold uppercase tracking-wider text-primary">
                            DevColab
                        </p>

                        <h1 class="mt-2 text-3xl font-bold tracking-tight sm:text-4xl">
                            Créer une communauté
                        </h1>

                        <p class="mt-3 max-w-xl leading-7 text-muted-foreground">
                            Rassemble des développeurs autour d'une technologie,
                            d'un projet ou d'un centre d'intérêt.
                        </p>
                    </div>

                    <!-- Formulaire -->
                    <form class="space-y-7 p-6 sm:p-8" @submit.prevent="submit">
                        <!-- Nom -->
                        <div>
                            <label for="name" class="mb-2 block text-sm font-medium">
                                Nom de la communauté
                                <span class="text-destructive">*</span>
                            </label>

                            <input id="name" v-model="form.name" type="text" minlength="3" maxlength="120" required
                                placeholder="Ex. : Laravel developers"
                                class="w-full rounded-lg border bg-background px-4 py-3 text-foreground outline-none transition placeholder:text-muted-foreground focus:border-primary focus:ring-2 focus:ring-primary/20"
                                :class="{
                                    'border-destructive': form.errors.name,
                                }" />

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
                                placeholder="Quel est l'objectif de votre communauté ?"
                                class="w-full resize-y rounded-lg border bg-background px-4 py-3 text-foreground outline-none transition placeholder:text-muted-foreground focus:border-primary focus:ring-2 focus:ring-primary/20"
                                :class="{
                                    'border-destructive':
                                        form.errors.description,
                                }" />

                            <p v-if="form.errors.description" class="mt-2 text-sm text-destructive">
                                {{ form.errors.description }}
                            </p>

                            <p class="mt-1 text-right text-xs text-muted-foreground">
                                {{ form.description.length }}/5000
                            </p>
                        </div>

                        <!-- Avatar -->
                        <div>
                            <label for="avatar" class="mb-2 block text-sm font-medium">
                                Avatar de la communauté
                            </label>

                            <p class="mb-4 text-sm text-muted-foreground">
                                Choisis une image qui permettra aux membres de
                                reconnaître ta communauté.
                            </p>

                            <div class="flex flex-col gap-4 rounded-xl border p-4 sm:flex-row sm:items-center">
                                <!-- Aperçu -->
                                <div
                                    class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-2xl border bg-primary/10 text-3xl font-bold text-primary">
                                    <img v-if="avatarPreview" :src="avatarPreview" alt="Aperçu de l'avatar"
                                        class="h-full w-full object-cover" />

                                    <span v-else>
                                        {{ avatarInitial }}
                                    </span>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <input id="avatar" ref="avatarInput" type="file"
                                        accept="image/jpeg,image/png,image/webp"
                                        class="block w-full text-sm text-muted-foreground file:mr-4 file:rounded-lg file:border-0 file:bg-primary/10 file:px-4 file:py-2 file:font-medium file:text-primary hover:file:bg-primary/20"
                                        :class="{
                                            'text-destructive':
                                                form.errors.avatar,
                                        }" @change="
                                            updateImagePreview($event, 'avatar')
                                            " />

                                    <p class="mt-2 text-xs text-muted-foreground">
                                        JPG, PNG ou WebP. Taille maximale : 2 Mo.
                                    </p>

                                    <button v-if="form.avatar" type="button"
                                        class="mt-3 text-sm font-medium text-destructive hover:underline"
                                        @click="removeImage('avatar')">
                                        Retirer l'avatar
                                    </button>
                                </div>
                            </div>

                            <p v-if="form.errors.avatar" class="mt-2 text-sm text-destructive">
                                {{ form.errors.avatar }}
                            </p>
                        </div>

                        <!-- Bannière -->
                        <div>
                            <label for="cover" class="mb-2 block text-sm font-medium">
                                Bannière de la communauté
                            </label>

                            <p class="mb-4 text-sm text-muted-foreground">
                                Une bannière permet de personnaliser la page de
                                ta communauté.
                            </p>

                            <!-- Aperçu -->
                            <div
                                class="relative mb-4 h-40 overflow-hidden rounded-xl border bg-linear-to-br from-primary/20 via-primary/10 to-muted sm:h-48">
                                <img v-if="coverPreview" :src="coverPreview" alt="Aperçu de la bannière"
                                    class="h-full w-full object-cover" />

                                <div v-else class="flex h-full items-center justify-center">
                                    <span class="text-5xl font-bold text-primary/30">
                                        {{ avatarInitial }}
                                    </span>
                                </div>
                            </div>

                            <input id="cover" ref="coverInput" type="file" accept="image/jpeg,image/png,image/webp"
                                class="block w-full text-sm text-muted-foreground file:mr-4 file:rounded-lg file:border-0 file:bg-primary/10 file:px-4 file:py-2 file:font-medium file:text-primary hover:file:bg-primary/20"
                                :class="{
                                    'text-destructive': form.errors.cover,
                                }" @change="
                                    updateImagePreview($event, 'cover')
                                    " />

                            <p class="mt-2 text-xs text-muted-foreground">
                                JPG, PNG ou WebP. Taille maximale : 5 Mo.
                            </p>

                            <button v-if="form.cover" type="button"
                                class="mt-3 text-sm font-medium text-destructive hover:underline"
                                @click="removeImage('cover')">
                                Retirer la bannière
                            </button>

                            <p v-if="form.errors.cover" class="mt-2 text-sm text-destructive">
                                {{ form.errors.cover }}
                            </p>
                        </div>

                        <!-- Visibilité -->
                        <div class="rounded-xl border p-4">
                            <label for="is_private" class="flex cursor-pointer items-start gap-3">
                                <input id="is_private" v-model="form.is_private" type="checkbox"
                                    class="mt-1 h-4 w-4 rounded border-input accent-primary" />

                                <span>
                                    <span class="block text-sm font-semibold">
                                        Communauté privée
                                    </span>

                                    <span class="mt-1 block text-sm leading-6 text-muted-foreground">
                                        Si cette option est activée, la
                                        communauté ne sera pas visible dans
                                        l'annuaire public.
                                    </span>
                                </span>
                            </label>

                            <p v-if="form.errors.is_private" class="mt-2 text-sm text-destructive">
                                {{ form.errors.is_private }}
                            </p>
                        </div>

                        <!-- Erreurs générales -->
                        <div v-if="form.hasErrors"
                            class="rounded-lg border border-destructive/30 bg-destructive/10 p-4 text-sm text-destructive"
                            role="alert">
                            Certains champs contiennent des erreurs. Vérifie
                            le formulaire avant de continuer.
                        </div>

                        <!-- Boutons -->
                        <div class="flex flex-col-reverse gap-3 border-t pt-6 sm:flex-row sm:justify-end">
                            <Link href="/communities"
                                class="inline-flex items-center justify-center rounded-lg border bg-background px-5 py-3 text-sm font-semibold transition-colors hover:bg-muted">
                                Annuler
                            </Link>

                            <button type="submit" :disabled="form.processing"
                                class="inline-flex items-center justify-center rounded-lg bg-primary px-5 py-3 text-sm font-semibold text-primary-foreground transition-opacity hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-50">
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
    </div>
</template>
