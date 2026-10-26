<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, ref } from 'vue';
import {
    ArrowLeft,
    ArrowRight,
    Check,
    CheckCircle2,
    Code2,
    ExternalLink,
    Eye,
    FileImage,
    Globe,
    ImagePlus,
    Lightbulb,
    LockKeyhole,
    Rocket,
    Sparkles,
    Users,
    X,
} from '@lucide/vue';

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

const coverInput = ref<HTMLInputElement | null>(null);
const coverPreview = ref<string | null>(null);

const nameLength = computed(() => form.name.length);
const summaryLength = computed(() => form.short_description.length);
const descriptionLength = computed(() => form.description.length);

const projectStatuses = [
    {
        value: 'planning',
        label: 'En préparation',
        description: 'Le projet est en phase de conception.',
        icon: Lightbulb,
    },
    {
        value: 'in_progress',
        label: 'En développement',
        description: 'Le projet est en cours de réalisation.',
        icon: Code2,
    },
    {
        value: 'completed',
        label: 'Terminé',
        description: 'Le projet est prêt ou finalisé.',
        icon: CheckCircle2,
    },
    {
        value: 'paused',
        label: 'En pause',
        description: 'Le développement est temporairement arrêté.',
        icon: LockKeyhole,
    },
];

const visibilityOptions = [
    {
        value: 'public',
        label: 'Public',
        description: 'Visible par la communauté.',
        icon: Globe,
    },
    {
        value: 'private',
        label: 'Privé',
        description: 'Réservé selon les règles d’accès du projet.',
        icon: LockKeyhole,
    },
];

const selectedStatus = computed(
    () =>
        projectStatuses.find((status) => status.value === form.status) ??
        projectStatuses[0],
);

const selectedVisibility = computed(
    () =>
        visibilityOptions.find(
            (option) => option.value === form.visibility,
        ) ?? visibilityOptions[0],
);

function selectCover(event: Event): void {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0] ?? null;

    if (coverPreview.value) {
        URL.revokeObjectURL(coverPreview.value);
        coverPreview.value = null;
    }

    form.cover = file;

    if (file) {
        coverPreview.value = URL.createObjectURL(file);
    }
}

function removeCover(): void {
    if (coverPreview.value) {
        URL.revokeObjectURL(coverPreview.value);
        coverPreview.value = null;
    }

    form.cover = null;

    if (coverInput.value) {
        coverInput.value.value = '';
    }
}

function submit(): void {
    form.post('/projects', {
        forceFormData: true,
    });
}

onBeforeUnmount(() => {
    if (coverPreview.value) {
        URL.revokeObjectURL(coverPreview.value);
    }
});
</script>

<template>

    <Head title="Créer un projet" />

    <main class="min-h-screen">
        <!-- En-tête -->
        <section class="relative isolate overflow-hidden border-b">
            <div
                class="pointer-events-none absolute inset-0 -z-10 bg-linear-to-br from-primary/10 via-background to-background" />

            <div
                class="pointer-events-none absolute -right-32 -top-32 -z-10 size-80 rounded-full bg-primary/10 blur-3xl" />

            <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
                <Link href="/projects"
                    class="group inline-flex items-center gap-2 rounded-lg text-sm font-semibold text-muted-foreground transition hover:text-primary">
                    <ArrowLeft class="size-4 transition-transform group-hover:-translate-x-1" />
                    Retour aux projets
                </Link>

                <div class="mt-8 max-w-3xl">
                    <div
                        class="inline-flex items-center gap-2 rounded-full border bg-background/80 px-3.5 py-2 text-xs font-bold text-primary shadow-sm backdrop-blur sm:text-sm">
                        <Rocket class="size-4" />
                        L'espace projets DevLink
                    </div>

                    <h1 class="mt-5 text-3xl font-black tracking-tight sm:text-4xl lg:text-5xl">
                        Donne vie à tes idées.
                    </h1>

                    <p class="mt-4 max-w-2xl text-sm leading-7 text-muted-foreground sm:text-base sm:leading-8">
                        Présente ton travail à la communauté DevLink, partage
                        les technologies que tu utilises et, si tu le souhaites,
                        trouve des développeurs pour t'accompagner.
                    </p>

                    <div class="mt-6 flex flex-wrap gap-3">
                        <span
                            class="inline-flex items-center gap-2 rounded-lg border bg-background/70 px-3 py-2 text-xs font-medium text-muted-foreground">
                            <Check class="size-3.5 text-primary" />
                            Présente ton travail
                        </span>

                        <span
                            class="inline-flex items-center gap-2 rounded-lg border bg-background/70 px-3 py-2 text-xs font-medium text-muted-foreground">
                            <Check class="size-3.5 text-primary" />
                            Partage ton code
                        </span>

                        <span
                            class="inline-flex items-center gap-2 rounded-lg border bg-background/70 px-3 py-2 text-xs font-medium text-muted-foreground">
                            <Check class="size-3.5 text-primary" />
                            Trouve des collaborateurs
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Formulaire et aperçu -->
        <section
            class="mx-auto grid max-w-7xl items-start gap-8 px-4 py-8 sm:px-6 sm:py-12 lg:grid-cols-[minmax(0,1fr)_320px] lg:px-8">
            <form class="min-w-0 space-y-6" enctype="multipart/form-data" @submit.prevent="submit">
                <!-- Informations générales -->
                <section class="overflow-hidden rounded-2xl border bg-card">
                    <div class="border-b px-5 py-5 sm:px-7">
                        <div class="flex items-start gap-3">
                            <div
                                class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary">
                                <FileImage class="size-5" />
                            </div>

                            <div>
                                <h2 class="font-extrabold tracking-tight">
                                    Présentation du projet
                                </h2>

                                <p class="mt-1 text-sm leading-6 text-muted-foreground">
                                    Les informations qui permettront aux autres
                                    développeurs de découvrir ton projet.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6 p-5 sm:p-7">
                        <!-- Nom -->
                        <div>
                            <div class="mb-2 flex items-center justify-between gap-3">
                                <label for="name" class="text-sm font-bold">
                                    Nom du projet
                                    <span class="text-destructive">*</span>
                                </label>

                                <span class="text-xs text-muted-foreground" :class="nameLength > 140
                                    ? 'text-amber-600 dark:text-amber-400'
                                    : ''
                                    ">
                                    {{ nameLength }}/150
                                </span>
                            </div>

                            <input id="name" v-model="form.name" required maxlength="150" autocomplete="off"
                                placeholder="Ex. DevLink, mon application SaaS..."
                                class="w-full rounded-xl border bg-background px-4 py-3.5 text-sm outline-none transition placeholder:text-muted-foreground/60 focus:border-primary focus:ring-4 focus:ring-primary/10"
                                :aria-invalid="!!form.errors.name" :aria-describedby="form.errors.name ? 'name-error' : undefined
                                    " />

                            <p v-if="form.errors.name" id="name-error" class="mt-2 text-sm text-destructive">
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <!-- Résumé -->
                        <div>
                            <div class="mb-2 flex items-center justify-between gap-3">
                                <label for="short_description" class="text-sm font-bold">
                                    Résumé du projet
                                    <span class="font-normal text-muted-foreground">
                                        (facultatif)
                                    </span>
                                </label>

                                <span class="text-xs text-muted-foreground">
                                    {{ summaryLength }}/1000
                                </span>
                            </div>

                            <textarea id="short_description" v-model="form.short_description" rows="3" maxlength="1000"
                                placeholder="En quelques phrases, explique ce que fait ton projet et pourquoi il est intéressant."
                                class="w-full resize-y rounded-xl border bg-background px-4 py-3.5 text-sm leading-7 outline-none transition placeholder:text-muted-foreground/60 focus:border-primary focus:ring-4 focus:ring-primary/10"
                                :aria-invalid="!!form.errors.short_description
                                    " />

                            <p v-if="form.errors.short_description" class="mt-2 text-sm text-destructive">
                                {{ form.errors.short_description }}
                            </p>

                            <p class="mt-2 text-xs leading-5 text-muted-foreground">
                                Un bon résumé aide les visiteurs à comprendre
                                rapidement la valeur de ton projet.
                            </p>
                        </div>

                        <!-- Description complète -->
                        <div>
                            <div class="mb-2 flex items-center justify-between gap-3">
                                <label for="description" class="text-sm font-bold">
                                    Description complète
                                    <span class="text-destructive">*</span>
                                </label>

                                <span class="text-xs text-muted-foreground">
                                    {{ descriptionLength }}/20 000
                                </span>
                            </div>

                            <textarea id="description" v-model="form.description" required rows="8" maxlength="20000"
                                placeholder="Quel problème ton projet résout-il ? Comment fonctionne-t-il ? Quelles sont ses fonctionnalités principales ? Quelles technologies utilises-tu ?"
                                class="w-full resize-y rounded-xl border bg-background px-4 py-3.5 text-sm leading-7 outline-none transition placeholder:text-muted-foreground/60 focus:border-primary focus:ring-4 focus:ring-primary/10"
                                :aria-invalid="!!form.errors.description" />

                            <p v-if="form.errors.description" class="mt-2 text-sm text-destructive">
                                {{ form.errors.description }}
                            </p>

                            <div class="mt-3 flex items-start gap-2 rounded-xl bg-muted/50 p-3.5">
                                <Lightbulb class="mt-0.5 size-4 shrink-0 text-primary" />

                                <p class="text-xs leading-6 text-muted-foreground">
                                    <span class="font-semibold text-foreground">
                                        Conseil :
                                    </span>
                                    explique le problème, la solution proposée,
                                    les fonctionnalités et les technologies
                                    utilisées.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Statut et visibilité -->
                <section class="overflow-hidden rounded-2xl border bg-card">
                    <div class="border-b px-5 py-5 sm:px-7">
                        <div class="flex items-start gap-3">
                            <div
                                class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary">
                                <Eye class="size-5" />
                            </div>

                            <div>
                                <h2 class="font-extrabold tracking-tight">
                                    État et visibilité
                                </h2>

                                <p class="mt-1 text-sm leading-6 text-muted-foreground">
                                    Indique où en est ton projet et qui pourra
                                    le consulter.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6 p-5 sm:p-7">
                        <!-- Statut -->
                        <fieldset>
                            <legend class="text-sm font-bold">
                                État du projet
                                <span class="text-destructive">*</span>
                            </legend>

                            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                <button v-for="status in projectStatuses" :key="status.value" type="button"
                                    class="flex items-start gap-3 rounded-xl border p-4 text-left transition" :class="form.status === status.value
                                        ? 'border-primary bg-primary/5 ring-2 ring-primary/15'
                                        : 'hover:border-primary/30 hover:bg-muted/40'
                                        " :aria-pressed="form.status === status.value
                                            " @click="form.status = status.value">
                                    <div class="flex size-10 shrink-0 items-center justify-center rounded-xl transition"
                                        :class="form.status === status.value
                                            ? 'bg-primary text-primary-foreground'
                                            : 'bg-muted text-muted-foreground'
                                            ">
                                        <component :is="status.icon" class="size-5" />
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-bold">
                                            {{ status.label }}
                                        </p>

                                        <p class="mt-1 text-xs leading-5 text-muted-foreground">
                                            {{ status.description }}
                                        </p>
                                    </div>

                                    <CheckCircle2 v-if="form.status === status.value"
                                        class="size-4 shrink-0 text-primary" />
                                </button>
                            </div>

                            <p v-if="form.errors.status" class="mt-2 text-sm text-destructive">
                                {{ form.errors.status }}
                            </p>
                        </fieldset>

                        <!-- Visibilité -->
                        <fieldset>
                            <legend class="text-sm font-bold">
                                Visibilité du projet
                                <span class="text-destructive">*</span>
                            </legend>

                            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                <button v-for="option in visibilityOptions" :key="option.value" type="button"
                                    class="flex items-start gap-3 rounded-xl border p-4 text-left transition" :class="form.visibility === option.value
                                        ? 'border-primary bg-primary/5 ring-2 ring-primary/15'
                                        : 'hover:border-primary/30 hover:bg-muted/40'
                                        " :aria-pressed="form.visibility === option.value
                                            " @click="form.visibility = option.value">
                                    <div class="flex size-10 shrink-0 items-center justify-center rounded-xl transition"
                                        :class="form.visibility === option.value
                                            ? 'bg-primary text-primary-foreground'
                                            : 'bg-muted text-muted-foreground'
                                            ">
                                        <component :is="option.icon" class="size-5" />
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-bold">
                                            {{ option.label }}
                                        </p>

                                        <p class="mt-1 text-xs leading-5 text-muted-foreground">
                                            {{ option.description }}
                                        </p>
                                    </div>

                                    <CheckCircle2 v-if="
                                        form.visibility === option.value
                                    " class="size-4 shrink-0 text-primary" />
                                </button>
                            </div>

                            <p v-if="form.errors.visibility" class="mt-2 text-sm text-destructive">
                                {{ form.errors.visibility }}
                            </p>
                        </fieldset>
                    </div>
                </section>

                <!-- Liens du projet -->
                <section class="overflow-hidden rounded-2xl border bg-card">
                    <div class="border-b px-5 py-5 sm:px-7">
                        <div class="flex items-start gap-3">
                            <div
                                class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary">
                                <Code2 class="size-5" />
                            </div>

                            <div>
                                <h2 class="font-extrabold tracking-tight">
                                    Liens du projet
                                </h2>

                                <p class="mt-1 text-sm leading-6 text-muted-foreground">
                                    Permets aux visiteurs d'explorer ton code
                                    ou de tester ton application.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-5 p-5 sm:p-7">
                        <!-- Dépôt -->
                        <div>
                            <label for="repository_url" class="mb-2 block text-sm font-bold">
                                Dépôt du code source
                                <span class="font-normal text-muted-foreground">
                                    (facultatif)
                                </span>
                            </label>

                            <div class="relative">
                                <Code2
                                    class="pointer-events-none absolute left-4 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />

                                <input id="repository_url" v-model="form.repository_url" type="url"
                                    placeholder="https://github.com/..."
                                    class="w-full rounded-xl border bg-background py-3.5 pl-11 pr-4 text-sm outline-none transition placeholder:text-muted-foreground/60 focus:border-primary focus:ring-4 focus:ring-primary/10"
                                    :aria-invalid="!!form.errors.repository_url
                                        " />
                            </div>

                            <p v-if="form.errors.repository_url" class="mt-2 text-sm text-destructive">
                                {{ form.errors.repository_url }}
                            </p>
                        </div>

                        <!-- Démonstration -->
                        <div>
                            <label for="demo_url" class="mb-2 block text-sm font-bold">
                                Démonstration en ligne
                                <span class="font-normal text-muted-foreground">
                                    (facultatif)
                                </span>
                            </label>

                            <div class="relative">
                                <Globe
                                    class="pointer-events-none absolute left-4 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />

                                <input id="demo_url" v-model="form.demo_url" type="url"
                                    placeholder="https://mon-projet.com"
                                    class="w-full rounded-xl border bg-background py-3.5 pl-11 pr-4 text-sm outline-none transition placeholder:text-muted-foreground/60 focus:border-primary focus:ring-4 focus:ring-primary/10"
                                    :aria-invalid="!!form.errors.demo_url" />
                            </div>

                            <p v-if="form.errors.demo_url" class="mt-2 text-sm text-destructive">
                                {{ form.errors.demo_url }}
                            </p>
                        </div>
                    </div>
                </section>

                <!-- Couverture -->
                <section class="overflow-hidden rounded-2xl border bg-card">
                    <div class="border-b px-5 py-5 sm:px-7">
                        <div class="flex items-start gap-3">
                            <div
                                class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary">
                                <ImagePlus class="size-5" />
                            </div>

                            <div>
                                <h2 class="font-extrabold tracking-tight">
                                    Image de couverture
                                </h2>

                                <p class="mt-1 text-sm leading-6 text-muted-foreground">
                                    Personnalise la présentation de ton projet.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 sm:p-7">
                        <input id="cover" ref="coverInput" type="file" accept="image/jpeg,image/png,image/webp"
                            class="sr-only" @change="selectCover" />

                        <div v-if="coverPreview" class="relative overflow-hidden rounded-2xl border bg-muted">
                            <img :src="coverPreview" alt="Aperçu de la couverture sélectionnée"
                                class="max-h-80 w-full object-contain" />

                            <button type="button" aria-label="Supprimer l'image sélectionnée"
                                class="absolute right-3 top-3 flex size-9 items-center justify-center rounded-xl border bg-background/90 text-foreground shadow-sm backdrop-blur transition hover:bg-background"
                                @click="removeCover">
                                <X class="size-4" />
                            </button>

                            <div class="flex items-center gap-2 border-t bg-card px-4 py-3 text-sm font-medium">
                                <CheckCircle2 class="size-4 text-emerald-500" />
                                Image sélectionnée
                            </div>
                        </div>

                        <label v-else for="cover"
                            class="group flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed px-5 py-10 text-center transition hover:border-primary/50 hover:bg-primary/3 sm:py-12">
                            <div
                                class="flex size-14 items-center justify-center rounded-2xl bg-primary/10 text-primary transition group-hover:scale-105">
                                <ImagePlus class="size-7" />
                            </div>

                            <p class="mt-4 text-sm font-bold">
                                Choisir une image de couverture
                            </p>

                            <p class="mt-2 max-w-sm text-xs leading-6 text-muted-foreground">
                                Sélectionne une image sur ton appareil pour
                                illustrer ton projet.
                            </p>

                            <span
                                class="mt-4 inline-flex items-center gap-2 rounded-lg border bg-background px-4 py-2 text-xs font-bold transition group-hover:border-primary/30">
                                Parcourir les fichiers
                                <ArrowRight class="size-3.5" />
                            </span>
                        </label>

                        <p class="mt-3 text-xs leading-5 text-muted-foreground">
                            Formats acceptés : JPG, PNG ou WebP. Taille maximale :
                            5 Mo.
                        </p>

                        <p v-if="form.errors.cover" class="mt-2 text-sm text-destructive">
                            {{ form.errors.cover }}
                        </p>
                    </div>
                </section>

                <!-- Recherche de collaborateurs -->
                <section class="overflow-hidden rounded-2xl border transition" :class="form.looking_for_collaborators
                    ? 'border-emerald-500/40 bg-emerald-500/3'
                    : 'bg-card'
                    ">
                    <label for="looking_for_collaborators" class="flex cursor-pointer items-start gap-4 p-5 sm:p-7">
                        <div class="flex size-11 shrink-0 items-center justify-center rounded-xl transition" :class="form.looking_for_collaborators
                            ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'
                            : 'bg-muted text-muted-foreground'
                            ">
                            <Users class="size-5" />
                        </div>

                        <div class="min-w-0 flex-1">
                            <span class="block text-sm font-extrabold">
                                Je recherche des collaborateurs
                            </span>

                            <span class="mt-1.5 block text-sm leading-6 text-muted-foreground">
                                Indique aux autres développeurs que tu souhaites
                                accueillir de nouveaux participants dans ton
                                projet.
                            </span>

                            <span v-if="form.looking_for_collaborators"
                                class="mt-3 inline-flex items-center gap-1.5 text-xs font-bold text-emerald-600 dark:text-emerald-400">
                                <CheckCircle2 class="size-4" />
                                Ton projet signalera qu'il recrute
                            </span>
                        </div>

                        <input id="looking_for_collaborators" v-model="form.looking_for_collaborators" type="checkbox"
                            class="mt-1 size-4 shrink-0 rounded border-input accent-primary focus:ring-2 focus:ring-primary/30" />
                    </label>

                    <p v-if="form.errors.looking_for_collaborators" class="px-5 pb-4 text-sm text-destructive sm:px-7">
                        {{ form.errors.looking_for_collaborators }}
                    </p>
                </section>

                <!-- Actions -->
                <div class="flex flex-col-reverse gap-3 border-t pt-6 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-xs leading-5 text-muted-foreground">
                        <span class="text-destructive">*</span>
                        Champs obligatoires
                    </p>

                    <div class="flex flex-col-reverse gap-3 sm:flex-row">
                        <Link href="/projects"
                            class="inline-flex items-center justify-center rounded-xl border px-5 py-3 text-sm font-bold transition hover:bg-muted">
                            Annuler
                        </Link>

                        <button type="submit" :disabled="form.processing"
                            class="group inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-6 py-3 text-sm font-bold text-primary-foreground shadow-lg shadow-primary/15 transition hover:-translate-y-0.5 hover:shadow-xl hover:shadow-primary/20 disabled:cursor-not-allowed disabled:opacity-50">
                            <span v-if="form.processing"
                                class="size-4 animate-spin rounded-full border-2 border-current border-r-transparent" />

                            <Rocket v-else class="size-4" />

                            {{
                                form.processing
                                    ? 'Création en cours…'
                                    : 'Créer le projet'
                            }}

                            <ArrowRight v-if="!form.processing"
                                class="size-4 transition-transform group-hover:translate-x-1" />
                        </button>
                    </div>
                </div>
            </form>

            <!-- Colonne latérale -->
            <aside class="space-y-5 lg:sticky lg:top-6">
                <!-- Aperçu -->
                <section class="overflow-hidden rounded-2xl border bg-card">
                    <div class="flex items-center justify-between border-b px-5 py-4">
                        <div>
                            <h2 class="text-sm font-extrabold">
                                Aperçu du projet
                            </h2>

                            <p class="mt-1 text-xs text-muted-foreground">
                                Un aperçu de ta présentation
                            </p>
                        </div>

                        <Eye class="size-4 text-muted-foreground" />
                    </div>

                    <div
                        class="relative flex h-36 items-center justify-center overflow-hidden bg-linear-to-br from-primary/15 via-violet-500/10 to-blue-500/15">
                        <img v-if="coverPreview" :src="coverPreview" alt=""
                            class="absolute inset-0 size-full object-cover" />

                        <div v-else
                            class="flex size-14 items-center justify-center rounded-2xl border border-primary/15 bg-background/80 text-2xl font-black text-primary shadow-sm backdrop-blur">
                            {{ form.name.trim().charAt(0).toUpperCase() || 'P' }}
                        </div>
                    </div>

                    <div class="p-5">
                        <div class="flex flex-wrap gap-2">
                            <span class="rounded-full px-2.5 py-1 text-[11px] font-bold" :class="form.visibility === 'public'
                                ? 'bg-primary/10 text-primary'
                                : 'bg-muted text-muted-foreground'
                                ">
                                {{
                                    form.visibility === 'public'
                                        ? 'Public'
                                        : 'Privé'
                                }}
                            </span>

                            <span class="rounded-full bg-muted px-2.5 py-1 text-[11px] font-bold text-muted-foreground">
                                {{ selectedStatus.label }}
                            </span>
                        </div>

                        <h3 class="mt-4 wrap-break-word text-lg font-extrabold tracking-tight">
                            {{ form.name.trim() || 'Nom du projet' }}
                        </h3>

                        <p class="mt-2 line-clamp-4 whitespace-pre-line text-sm leading-6 text-muted-foreground">
                            {{
                                form.short_description.trim() ||
                                'Le résumé de ton projet apparaîtra ici. Décris ton idée en quelques phrases.'
                            }}
                        </p>

                        <div v-if="form.looking_for_collaborators"
                            class="mt-4 inline-flex items-center gap-1.5 rounded-lg bg-emerald-500/10 px-3 py-2 text-xs font-bold text-emerald-700 dark:text-emerald-400">
                            <Users class="size-3.5" />
                            Recherche des collaborateurs
                        </div>

                        <div v-if="form.repository_url || form.demo_url"
                            class="mt-4 flex flex-wrap gap-2 border-t pt-4">
                            <span v-if="form.repository_url"
                                class="inline-flex items-center gap-1.5 text-xs font-semibold text-muted-foreground">
                                <Code2 class="size-3.5" />
                                Dépôt disponible
                            </span>

                            <span v-if="form.demo_url"
                                class="inline-flex items-center gap-1.5 text-xs font-semibold text-muted-foreground">
                                <ExternalLink class="size-3.5" />
                                Démo disponible
                            </span>
                        </div>
                    </div>
                </section>

                <!-- Conseils -->
                <section class="rounded-2xl border bg-card p-5">
                    <div class="flex items-center gap-2.5">
                        <div
                            class="flex size-9 items-center justify-center rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400">
                            <Lightbulb class="size-4.5" />
                        </div>

                        <h2 class="text-sm font-extrabold">
                            Conseils pour bien démarrer
                        </h2>
                    </div>

                    <ul class="mt-5 space-y-4">
                        <li class="flex items-start gap-3">
                            <CheckCircle2 class="mt-0.5 size-4 shrink-0 text-primary" />

                            <p class="text-xs leading-6 text-muted-foreground">
                                <span class="font-bold text-foreground">
                                    Sois clair.
                                </span>
                                Explique le problème que ton projet cherche à
                                résoudre.
                            </p>
                        </li>

                        <li class="flex items-start gap-3">
                            <CheckCircle2 class="mt-0.5 size-4 shrink-0 text-primary" />

                            <p class="text-xs leading-6 text-muted-foreground">
                                <span class="font-bold text-foreground">
                                    Montre ton travail.
                                </span>
                                Ajoute une couverture et des liens utiles.
                            </p>
                        </li>

                        <li class="flex items-start gap-3">
                            <CheckCircle2 class="mt-0.5 size-4 shrink-0 text-primary" />

                            <p class="text-xs leading-6 text-muted-foreground">
                                <span class="font-bold text-foreground">
                                    Ouvre la collaboration.
                                </span>
                                Indique si tu souhaites accueillir d'autres
                                développeurs.
                            </p>
                        </li>
                    </ul>
                </section>

                <!-- Confidentialité -->
                <div class="flex items-start gap-3 rounded-2xl border bg-muted/30 p-4">
                    <LockKeyhole class="mt-0.5 size-4 shrink-0 text-muted-foreground" />

                    <p class="text-xs leading-6 text-muted-foreground">
                        <span class="font-semibold text-foreground">
                            À propos de la visibilité
                        </span>
                        <br />
                        Choisis la visibilité qui convient à ton projet.
                        Vérifie également les règles d'accès de ton application
                        pour les projets privés.
                    </p>
                </div>
            </aside>
        </section>
    </main>
</template>