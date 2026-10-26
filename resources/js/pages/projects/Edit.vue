<script setup lang="ts">
import { computed, onBeforeUnmount, ref, watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
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
    Pencil,
    Rocket,
    Save,
    Sparkles,
    Users,
    X,
} from '@lucide/vue';

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

const coverPreview = ref<string | null>(null);
let previewUrl: string | null = null;

const statuses = [
    {
        value: 'planning',
        label: 'En préparation',
        description: 'Le projet prend forme.',
        icon: Lightbulb,
    },
    {
        value: 'in_progress',
        label: 'En développement',
        description: 'Le travail est en cours.',
        icon: Code2,
    },
    {
        value: 'completed',
        label: 'Terminé',
        description: 'Le projet est abouti.',
        icon: CheckCircle2,
    },
    {
        value: 'paused',
        label: 'En pause',
        description: 'Le développement est suspendu.',
        icon: Sparkles,
    },
];

const visibilities = [
    {
        value: 'public',
        label: 'Public',
        description: 'Visible par la communauté.',
        icon: Globe,
    },
    {
        value: 'private',
        label: 'Privé',
        description: 'Réservé aux personnes autorisées.',
        icon: LockKeyhole,
    },
];

const selectedStatus = computed(
    () => statuses.find((status) => status.value === form.status) ?? statuses[0],
);

const selectedVisibility = computed(
    () =>
        visibilities.find((visibility) => visibility.value === form.visibility) ??
        visibilities[0],
);

const displayedCover = computed(() => {
    if (coverPreview.value) {
        return coverPreview.value;
    }

    if (form.remove_cover) {
        return null;
    }

    return props.project.cover_path
        ? `/storage/${props.project.cover_path}`
        : null;
});

const descriptionLength = computed(() => form.description.length);
const shortDescriptionLength = computed(() => form.short_description.length);

const hasRepository = computed(() => form.repository_url.trim().length > 0);
const hasDemo = computed(() => form.demo_url.trim().length > 0);

function updateCover(event: Event): void {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0] ?? null;

    form.cover = file;

    if (previewUrl) {
        URL.revokeObjectURL(previewUrl);
        previewUrl = null;
    }

    coverPreview.value = null;

    if (file) {
        previewUrl = URL.createObjectURL(file);
        coverPreview.value = previewUrl;
        form.remove_cover = false;
    }
}

function removeCurrentCover(): void {
    form.remove_cover = true;
    form.cover = null;
    coverPreview.value = null;

    if (previewUrl) {
        URL.revokeObjectURL(previewUrl);
        previewUrl = null;
    }

    const input = document.getElementById('cover') as HTMLInputElement | null;

    if (input) {
        input.value = '';
    }
}

function keepCurrentCover(): void {
    form.remove_cover = false;
}

function submit(): void {
    form
        .transform((data) => ({
            ...data,
            _method: 'put',
        }))
        .post(`/projects/${props.project.slug}`, {
            forceFormData: true,
            preserveScroll: true,
        });
}

watch(
    () => props.project,
    () => {
        form.name = props.project.name;
        form.short_description = props.project.short_description ?? '';
        form.description = props.project.description;
        form.status = props.project.status;
        form.visibility = props.project.visibility;
        form.repository_url = props.project.repository_url ?? '';
        form.demo_url = props.project.demo_url ?? '';
        form.looking_for_collaborators = props.project.looking_for_collaborators;
    },
);

onBeforeUnmount(() => {
    if (previewUrl) {
        URL.revokeObjectURL(previewUrl);
    }
});
</script>

<template>

    <Head :title="`Modifier ${project.name}`" />

    <main class="min-h-screen bg-gray-50/70 pb-16 dark:bg-gray-950">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Navigation -->
            <Link :href="`/projects/${project.slug}`"
                class="group inline-flex items-center gap-2 text-sm font-medium text-muted-foreground transition hover:text-primary">
                <ArrowLeft class="size-4 transition-transform group-hover:-translate-x-1" />
                Retour au projet
            </Link>

            <!-- Header -->
            <header class="mt-7 flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
                <div class="max-w-2xl">
                    <div
                        class="mb-3 inline-flex items-center gap-2 rounded-full border border-primary/20 bg-primary/5 px-3 py-1.5 text-xs font-semibold text-primary">
                        <Pencil class="size-3.5" />
                        Gestion du projet
                    </div>

                    <h1 class="text-3xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-4xl">
                        Modifier ton projet
                    </h1>

                    <p class="mt-3 text-sm leading-6 text-muted-foreground sm:text-base">
                        Fais évoluer la présentation de ton projet, actualise son statut
                        et partage les dernières informations avec la communauté.
                    </p>
                </div>

                <Link :href="`/projects/${project.slug}`"
                    class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl border bg-background px-4 py-2.5 text-sm font-semibold transition hover:bg-muted">
                    <Eye class="size-4" />
                    Voir le projet
                    <ExternalLink class="size-3.5 text-muted-foreground" />
                </Link>
            </header>

            <form class="mt-8 grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_320px]" @submit.prevent="submit">
                <!-- Main form -->
                <div class="min-w-0 space-y-6">
                    <!-- Project information -->
                    <section class="overflow-hidden rounded-2xl border bg-card shadow-sm">
                        <div class="border-b px-5 py-5 sm:px-7">
                            <div class="flex items-start gap-3">
                                <div
                                    class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary">
                                    <Rocket class="size-5" />
                                </div>

                                <div>
                                    <h2 class="font-semibold">Informations générales</h2>
                                    <p class="mt-1 text-sm text-muted-foreground">
                                        Le nom et la description de ton projet.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-6 p-5 sm:p-7">
                            <div>
                                <div class="mb-2 flex items-center justify-between gap-3">
                                    <label for="name" class="text-sm font-semibold">
                                        Nom du projet <span class="text-destructive">*</span>
                                    </label>
                                    <span class="text-xs text-muted-foreground">
                                        {{ form.name.length }}/150
                                    </span>
                                </div>

                                <input id="name" v-model="form.name" required maxlength="150"
                                    placeholder="Ex. DevLink, mon portfolio..."
                                    class="w-full rounded-xl border bg-background px-4 py-3 text-sm outline-none transition placeholder:text-muted-foreground/70 focus:border-primary focus:ring-4 focus:ring-primary/10"
                                    :class="{ 'border-destructive': form.errors.name }" />

                                <p v-if="form.errors.name" class="mt-2 text-sm text-destructive">
                                    {{ form.errors.name }}
                                </p>
                            </div>

                            <div>
                                <div class="mb-2 flex items-center justify-between gap-3">
                                    <label for="short_description" class="text-sm font-semibold">
                                        Résumé du projet
                                    </label>
                                    <span class="text-xs text-muted-foreground">
                                        {{ shortDescriptionLength }}/1000
                                    </span>
                                </div>

                                <textarea id="short_description" v-model="form.short_description" rows="3"
                                    maxlength="1000" placeholder="Présente ton projet en quelques phrases..."
                                    class="w-full resize-y rounded-xl border bg-background px-4 py-3 text-sm leading-6 outline-none transition placeholder:text-muted-foreground/70 focus:border-primary focus:ring-4 focus:ring-primary/10"
                                    :class="{ 'border-destructive': form.errors.short_description }" />

                                <p v-if="form.errors.short_description" class="mt-2 text-sm text-destructive">
                                    {{ form.errors.short_description }}
                                </p>
                            </div>

                            <div>
                                <div class="mb-2 flex items-center justify-between gap-3">
                                    <label for="description" class="text-sm font-semibold">
                                        Description complète <span class="text-destructive">*</span>
                                    </label>
                                    <span class="text-xs"
                                        :class="descriptionLength > 20000 ? 'text-destructive' : 'text-muted-foreground'">
                                        {{ descriptionLength }}/20000
                                    </span>
                                </div>

                                <textarea id="description" v-model="form.description" required rows="9"
                                    maxlength="20000"
                                    placeholder="Explique les objectifs, les fonctionnalités, les technologies utilisées et les prochaines étapes..."
                                    class="w-full resize-y rounded-xl border bg-background px-4 py-3 text-sm leading-6 outline-none transition placeholder:text-muted-foreground/70 focus:border-primary focus:ring-4 focus:ring-primary/10"
                                    :class="{ 'border-destructive': form.errors.description }" />

                                <p class="mt-2 text-xs text-muted-foreground">
                                    Décris ce que fait ton projet, les problèmes qu'il résout et ce que tu souhaites
                                    construire.
                                </p>

                                <p v-if="form.errors.description" class="mt-2 text-sm text-destructive">
                                    {{ form.errors.description }}
                                </p>
                            </div>
                        </div>
                    </section>

                    <!-- Status and visibility -->
                    <section class="overflow-hidden rounded-2xl border bg-card shadow-sm">
                        <div class="border-b px-5 py-5 sm:px-7">
                            <div class="flex items-start gap-3">
                                <div
                                    class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                                    <CheckCircle2 class="size-5" />
                                </div>

                                <div>
                                    <h2 class="font-semibold">Statut et visibilité</h2>
                                    <p class="mt-1 text-sm text-muted-foreground">
                                        Indique où en est ton projet et qui peut le voir.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-6 p-5 sm:p-7">
                            <div>
                                <label class="mb-3 block text-sm font-semibold">
                                    Statut du projet <span class="text-destructive">*</span>
                                </label>

                                <div class="grid gap-3 sm:grid-cols-2">
                                    <button v-for="status in statuses" :key="status.value" type="button"
                                        class="flex items-start gap-3 rounded-xl border p-4 text-left transition"
                                        :class="form.status === status.value
                                            ? 'border-primary bg-primary/5 ring-2 ring-primary/15'
                                            : 'hover:border-primary/40 hover:bg-muted/50'
                                            " @click="form.status = status.value">
                                        <div class="flex size-9 shrink-0 items-center justify-center rounded-lg"
                                            :class="form.status === status.value ? 'bg-primary text-primary-foreground' : 'bg-muted text-muted-foreground'">
                                            <component :is="status.icon" class="size-4.5" />
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-semibold">{{ status.label }}</p>
                                            <p class="mt-1 text-xs leading-5 text-muted-foreground">
                                                {{ status.description }}
                                            </p>
                                        </div>

                                        <Check v-if="form.status === status.value"
                                            class="size-4 shrink-0 text-primary" />
                                    </button>
                                </div>

                                <p v-if="form.errors.status" class="mt-2 text-sm text-destructive">
                                    {{ form.errors.status }}
                                </p>
                            </div>

                            <div class="border-t pt-6">
                                <label class="mb-3 block text-sm font-semibold">
                                    Visibilité <span class="text-destructive">*</span>
                                </label>

                                <div class="grid gap-3 sm:grid-cols-2">
                                    <button v-for="visibility in visibilities" :key="visibility.value" type="button"
                                        class="flex items-start gap-3 rounded-xl border p-4 text-left transition"
                                        :class="form.visibility === visibility.value
                                            ? 'border-primary bg-primary/5 ring-2 ring-primary/15'
                                            : 'hover:border-primary/40 hover:bg-muted/50'
                                            " @click="form.visibility = visibility.value">
                                        <div class="flex size-9 shrink-0 items-center justify-center rounded-lg"
                                            :class="form.visibility === visibility.value ? 'bg-primary text-primary-foreground' : 'bg-muted text-muted-foreground'">
                                            <component :is="visibility.icon" class="size-4.5" />
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-semibold">{{ visibility.label }}</p>
                                            <p class="mt-1 text-xs leading-5 text-muted-foreground">
                                                {{ visibility.description }}
                                            </p>
                                        </div>

                                        <Check v-if="form.visibility === visibility.value"
                                            class="size-4 shrink-0 text-primary" />
                                    </button>
                                </div>

                                <p v-if="form.errors.visibility" class="mt-2 text-sm text-destructive">
                                    {{ form.errors.visibility }}
                                </p>
                            </div>
                        </div>
                    </section>

                    <!-- Links -->
                    <section class="overflow-hidden rounded-2xl border bg-card shadow-sm">
                        <div class="border-b px-5 py-5 sm:px-7">
                            <div class="flex items-start gap-3">
                                <div
                                    class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-sky-500/10 text-sky-600 dark:text-sky-400">
                                    <Code2 class="size-5" />
                                </div>

                                <div>
                                    <h2 class="font-semibold">Liens du projet</h2>
                                    <p class="mt-1 text-sm text-muted-foreground">
                                        Permets aux visiteurs d'accéder au code et à la démonstration.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-5 p-5 sm:p-7">
                            <div>
                                <label for="repository_url" class="mb-2 block text-sm font-semibold">
                                    Dépôt du code source
                                </label>

                                <div class="relative">
                                    <Code2
                                        class="pointer-events-none absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                                    <input id="repository_url" v-model="form.repository_url" type="url"
                                        placeholder="https://github.com/..."
                                        class="w-full rounded-xl border bg-background py-3 pl-10 pr-4 text-sm outline-none transition placeholder:text-muted-foreground/70 focus:border-primary focus:ring-4 focus:ring-primary/10"
                                        :class="{ 'border-destructive': form.errors.repository_url }" />
                                </div>

                                <p v-if="form.errors.repository_url" class="mt-2 text-sm text-destructive">
                                    {{ form.errors.repository_url }}
                                </p>
                            </div>

                            <div>
                                <label for="demo_url" class="mb-2 block text-sm font-semibold">
                                    Démonstration en ligne
                                </label>

                                <div class="relative">
                                    <Globe
                                        class="pointer-events-none absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                                    <input id="demo_url" v-model="form.demo_url" type="url"
                                        placeholder="https://mon-projet.com"
                                        class="w-full rounded-xl border bg-background py-3 pl-10 pr-4 text-sm outline-none transition placeholder:text-muted-foreground/70 focus:border-primary focus:ring-4 focus:ring-primary/10"
                                        :class="{ 'border-destructive': form.errors.demo_url }" />
                                </div>

                                <p v-if="form.errors.demo_url" class="mt-2 text-sm text-destructive">
                                    {{ form.errors.demo_url }}
                                </p>
                            </div>
                        </div>
                    </section>

                    <!-- Cover -->
                    <section class="overflow-hidden rounded-2xl border bg-card shadow-sm">
                        <div class="border-b px-5 py-5 sm:px-7">
                            <div class="flex items-start gap-3">
                                <div
                                    class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-violet-500/10 text-violet-600 dark:text-violet-400">
                                    <ImagePlus class="size-5" />
                                </div>

                                <div>
                                    <h2 class="font-semibold">Image de couverture</h2>
                                    <p class="mt-1 text-sm text-muted-foreground">
                                        Personnalise l'apparence de la page de ton projet.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="p-5 sm:p-7">
                            <div v-if="displayedCover"
                                class="group relative mb-5 overflow-hidden rounded-xl border bg-muted">
                                <img :src="displayedCover" :alt="`Aperçu de la couverture de ${project.name}`"
                                    class="max-h-80 min-h-40 w-full object-cover" />

                                <div
                                    class="absolute inset-x-0 bottom-0 bg-linear-to-t from-black/70 to-transparent p-4 pt-12">
                                    <p class="text-sm font-semibold text-white">
                                        {{ form.name || 'Nom du projet' }}
                                    </p>
                                    <p class="mt-1 line-clamp-2 text-xs text-white/80">
                                        {{ form.short_description || 'Résumé du projet' }}
                                    </p>
                                </div>
                            </div>

                            <div v-else
                                class="mb-5 flex flex-col items-center justify-center rounded-xl border-2 border-dashed bg-muted/30 px-5 py-9 text-center">
                                <div
                                    class="flex size-12 items-center justify-center rounded-xl bg-muted text-muted-foreground">
                                    <FileImage class="size-6" />
                                </div>
                                <p class="mt-3 text-sm font-semibold">Aucune couverture sélectionnée</p>
                                <p class="mt-1 text-xs text-muted-foreground">
                                    Ajoute une image pour mettre ton projet en valeur.
                                </p>
                            </div>

                            <div v-if="form.remove_cover"
                                class="mb-4 flex items-start gap-3 rounded-xl border border-amber-500/30 bg-amber-500/5 p-4">
                                <X class="mt-0.5 size-4 shrink-0 text-amber-600" />
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold">Couverture à supprimer</p>
                                    <p class="mt-1 text-xs leading-5 text-muted-foreground">
                                        La couverture actuelle sera retirée lors de l'enregistrement.
                                    </p>
                                </div>

                                <button type="button" class="text-xs font-semibold text-primary hover:underline"
                                    @click="keepCurrentCover">
                                    Annuler
                                </button>
                            </div>

                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div class="min-w-0">
                                    <label for="cover" class="block text-sm font-semibold">
                                        {{ displayedCover ? 'Remplacer la couverture' : 'Choisir une image' }}
                                    </label>
                                    <p class="mt-1 text-xs text-muted-foreground">
                                        JPG, PNG ou WebP · 5 Mo maximum
                                    </p>
                                </div>

                                <label for="cover"
                                    class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-xl border bg-background px-4 py-2.5 text-sm font-semibold transition hover:bg-muted">
                                    <ImagePlus class="size-4" />
                                    Parcourir
                                </label>
                            </div>

                            <input id="cover" type="file" accept="image/jpeg,image/png,image/webp" class="sr-only"
                                @change="updateCover" />

                            <div v-if="form.cover"
                                class="mt-3 flex items-center gap-2 rounded-lg bg-muted/60 px-3 py-2">
                                <FileImage class="size-4 shrink-0 text-muted-foreground" />
                                <span class="min-w-0 flex-1 truncate text-xs font-medium">
                                    {{ form.cover.name }}
                                </span>
                                <button type="button"
                                    class="rounded-md p-1 text-muted-foreground transition hover:bg-background hover:text-destructive"
                                    aria-label="Retirer la nouvelle image"
                                    @click="form.cover = null; coverPreview = null; if (previewUrl) { URL.revokeObjectURL(previewUrl); previewUrl = null; }">
                                    <X class="size-4" />
                                </button>
                            </div>

                            <p v-if="form.errors.cover" class="mt-2 text-sm text-destructive">
                                {{ form.errors.cover }}
                            </p>
                            <p v-if="form.errors.remove_cover" class="mt-2 text-sm text-destructive">
                                {{ form.errors.remove_cover }}
                            </p>

                            <button v-if="project.cover_path && !form.remove_cover && !form.cover" type="button"
                                class="mt-4 text-sm font-semibold text-destructive transition hover:underline"
                                @click="removeCurrentCover">
                                Retirer la couverture actuelle
                            </button>
                        </div>
                    </section>

                    <!-- Collaboration -->
                    <section class="rounded-2xl border bg-card p-5 shadow-sm sm:p-7">
                        <label class="flex cursor-pointer items-start gap-4">
                            <input v-model="form.looking_for_collaborators" type="checkbox"
                                class="mt-1 size-4 shrink-0 rounded accent-primary" />

                            <span class="flex min-w-0 flex-1 items-start gap-3">
                                <span
                                    class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400">
                                    <Users class="size-5" />
                                </span>

                                <span>
                                    <span class="block text-sm font-semibold">
                                        Je recherche des collaborateurs
                                    </span>
                                    <span class="mt-1 block text-sm leading-6 text-muted-foreground">
                                        Fais savoir aux autres développeurs que tu es ouvert à la collaboration sur ce
                                        projet.
                                    </span>
                                </span>
                            </span>
                        </label>
                    </section>

                    <!-- Form actions -->
                    <div
                        class="flex flex-col-reverse gap-3 border-t pt-5 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-xs text-muted-foreground">
                            <span class="text-destructive">*</span>
                            Champs obligatoires
                        </p>

                        <div class="flex flex-col-reverse gap-3 sm:flex-row">
                            <Link :href="`/projects/${project.slug}`"
                                class="inline-flex items-center justify-center rounded-xl border bg-background px-5 py-3 text-sm font-semibold transition hover:bg-muted">
                                Annuler
                            </Link>

                            <button type="submit" :disabled="form.processing"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-primary-foreground shadow-sm transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-50">
                                <Save v-if="!form.processing" class="size-4" />
                                <span v-else
                                    class="size-4 animate-spin rounded-full border-2 border-current border-r-transparent" />
                                {{ form.processing ? 'Enregistrement…' : 'Enregistrer les modifications' }}
                                <ArrowRight v-if="!form.processing" class="size-4" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <aside class="space-y-5 lg:sticky lg:top-6">
                    <!-- Live preview -->
                    <section class="overflow-hidden rounded-2xl border bg-card shadow-sm">
                        <div class="flex items-center gap-2 border-b px-5 py-4">
                            <Eye class="size-4 text-primary" />
                            <h2 class="text-sm font-semibold">Aperçu du projet</h2>
                            <span
                                class="ml-auto rounded-full bg-muted px-2 py-1 text-[10px] font-semibold text-muted-foreground">
                                En direct
                            </span>
                        </div>

                        <div class="p-4">
                            <div class="overflow-hidden rounded-xl border">
                                <div
                                    class="relative flex h-32 items-center justify-center overflow-hidden bg-linear-to-br from-primary/20 via-primary/5 to-violet-500/20">
                                    <img v-if="displayedCover" :src="displayedCover"
                                        :alt="`Couverture de ${form.name || project.name}`"
                                        class="absolute inset-0 size-full object-cover" />
                                    <div v-else
                                        class="flex size-14 items-center justify-center rounded-2xl bg-background/80 text-primary shadow-sm">
                                        <Code2 class="size-7" />
                                    </div>

                                    <span
                                        class="absolute right-3 top-3 rounded-full border bg-background/90 px-2.5 py-1 text-[10px] font-semibold">
                                        {{ selectedVisibility.label }}
                                    </span>
                                </div>

                                <div class="space-y-3 p-4">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full bg-primary/10 px-2.5 py-1 text-[10px] font-semibold text-primary">
                                            <component :is="selectedStatus.icon" class="size-3" />
                                            {{ selectedStatus.label }}
                                        </span>

                                        <span v-if="form.looking_for_collaborators"
                                            class="inline-flex items-center gap-1 rounded-full bg-emerald-500/10 px-2.5 py-1 text-[10px] font-semibold text-emerald-700 dark:text-emerald-400">
                                            <Users class="size-3" />
                                            Collaborateurs
                                        </span>
                                    </div>

                                    <h3 class="wrap-break-word text-base font-bold">
                                        {{ form.name || 'Nom de ton projet' }}
                                    </h3>

                                    <p class="line-clamp-3 text-xs leading-5 text-muted-foreground">
                                        {{ form.short_description || 'Le résumé de ton projet apparaîtra ici.' }}
                                    </p>

                                    <div class="flex flex-wrap gap-2 border-t pt-3">
                                        <span v-if="hasRepository"
                                            class="inline-flex items-center gap-1.5 rounded-lg bg-muted px-2.5 py-1.5 text-[10px] font-medium">
                                            <Code2 class="size-3" />
                                            Code source
                                        </span>
                                        <span v-if="hasDemo"
                                            class="inline-flex items-center gap-1.5 rounded-lg bg-muted px-2.5 py-1.5 text-[10px] font-medium">
                                            <Globe class="size-3" />
                                            Démo en ligne
                                        </span>
                                        <span v-if="!hasRepository && !hasDemo"
                                            class="text-[10px] text-muted-foreground">
                                            Les liens apparaîtront ici.
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <p class="mt-3 text-xs leading-5 text-muted-foreground">
                                Cet aperçu est indicatif. La page publique peut présenter d'autres informations.
                            </p>
                        </div>
                    </section>

                    <!-- Checklist -->
                    <section class="rounded-2xl border bg-card p-5 shadow-sm">
                        <h2 class="flex items-center gap-2 text-sm font-semibold">
                            <CheckCircle2 class="size-4 text-primary" />
                            Vérification rapide
                        </h2>

                        <div class="mt-4 space-y-3">
                            <div class="flex items-start gap-2.5">
                                <CheckCircle2 class="mt-0.5 size-4 shrink-0"
                                    :class="form.name.trim() ? 'text-emerald-500' : 'text-muted-foreground/40'" />
                                <span class="text-xs leading-5"
                                    :class="form.name.trim() ? '' : 'text-muted-foreground'">
                                    Nom du projet renseigné
                                </span>
                            </div>

                            <div class="flex items-start gap-2.5">
                                <CheckCircle2 class="mt-0.5 size-4 shrink-0"
                                    :class="form.description.trim() ? 'text-emerald-500' : 'text-muted-foreground/40'" />
                                <span class="text-xs leading-5"
                                    :class="form.description.trim() ? '' : 'text-muted-foreground'">
                                    Description complète renseignée
                                </span>
                            </div>

                            <div class="flex items-start gap-2.5">
                                <CheckCircle2 class="mt-0.5 size-4 shrink-0"
                                    :class="displayedCover ? 'text-emerald-500' : 'text-muted-foreground/40'" />
                                <span class="text-xs leading-5" :class="displayedCover ? '' : 'text-muted-foreground'">
                                    Image de couverture ajoutée
                                </span>
                            </div>

                            <div class="flex items-start gap-2.5">
                                <CheckCircle2 class="mt-0.5 size-4 shrink-0"
                                    :class="hasRepository || hasDemo ? 'text-emerald-500' : 'text-muted-foreground/40'" />
                                <span class="text-xs leading-5"
                                    :class="hasRepository || hasDemo ? '' : 'text-muted-foreground'">
                                    Au moins un lien ajouté
                                </span>
                            </div>
                        </div>
                    </section>

                    <!-- Tip -->
                    <section class="rounded-2xl border border-primary/20 bg-primary/5 p-5">
                        <div class="flex items-center gap-2 text-primary">
                            <Sparkles class="size-4" />
                            <h2 class="text-sm font-semibold">Conseil DevLink</h2>
                        </div>

                        <p class="mt-3 text-sm leading-6 text-muted-foreground">
                            Garde les informations à jour. Un statut précis et une description claire aident les autres
                            développeurs à comprendre où en est ton projet.
                        </p>
                    </section>
                </aside>
            </form>
        </div>
    </main>
</template>