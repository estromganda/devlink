<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    ArrowLeft,
    ArrowRight,
    CheckCircle2,
    Code2,
    Globe,
    MessageSquare,
    Send,
    Sparkles,
    Users,
    X,
} from '@lucide/vue';

type Community = {
    id: number;
    name: string;
};

const props = defineProps<{
    communities: Community[];
}>();

const form = useForm({
    title: '',
    body: '',
    community_id: '' as number | '',
});

const selectedCommunity = computed(() =>
    props.communities.find(
        (community) => community.id === Number(form.community_id),
    ),
);

const canPublish = computed(
    () => form.body.trim().length > 0 && !form.processing,
);

function submit() {
    if (!canPublish.value) return;

    form.post('/posts');
}

function clearTitle() {
    form.title = '';
}
</script>

<template>

    <Head title="Créer une publication" />

    <main class="min-h-screen bg-background">
        <div class="mx-auto max-w-6xl px-4 py-6 sm:px-6 sm:py-10">
            <!-- Navigation -->
            <Link href="/posts"
                class="inline-flex items-center gap-2 rounded-xl border bg-card px-3.5 py-2.5 text-sm font-medium text-muted-foreground transition hover:border-primary/30 hover:bg-muted hover:text-foreground">
                <ArrowLeft class="size-4" />
                Retour au fil d’actualité
            </Link>

            <div class="mt-6 grid grid-cols-1 items-start gap-6 lg:grid-cols-[minmax(0,1fr)_300px]">
                <!-- Formulaire principal -->
                <div class="min-w-0">
                    <!-- En-tête -->
                    <header class="relative mb-6 overflow-hidden rounded-3xl border bg-card p-6 shadow-sm sm:p-8">
                        <div
                            class="pointer-events-none absolute -right-12 -top-16 size-56 rounded-full bg-primary/10 blur-3xl" />
                        <div
                            class="pointer-events-none absolute -bottom-20 right-1/4 size-48 rounded-full bg-violet-500/10 blur-3xl" />

                        <div class="relative">
                            <div
                                class="mb-4 inline-flex items-center gap-2 rounded-full border bg-background/80 px-3 py-1.5 text-xs font-semibold text-primary">
                                <Sparkles class="size-3.5" />
                                Partage avec la communauté
                            </div>

                            <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">
                                Créer une publication<span class="text-primary">.</span>
                            </h1>

                            <p class="mt-3 max-w-xl text-sm leading-6 text-muted-foreground sm:text-base">
                                Une idée, une découverte ou un projet ?
                                Partage tes connaissances et lance une discussion
                                avec les autres développeurs.
                            </p>
                        </div>
                    </header>

                    <!-- Formulaire -->
                    <form class="overflow-hidden rounded-2xl border bg-card shadow-sm" @submit.prevent="submit">
                        <div class="space-y-7 p-5 sm:p-8">
                            <!-- Titre -->
                            <section>
                                <div class="mb-3 flex items-center justify-between gap-3">
                                    <label for="title" class="text-sm font-bold">
                                        Titre
                                        <span class="ml-1 font-normal text-muted-foreground">
                                            (facultatif)
                                        </span>
                                    </label>

                                    <span class="text-xs text-muted-foreground" :class="form.title.length >= 180
                                        ? 'text-amber-500'
                                        : ''
                                        ">
                                        {{ form.title.length }} / 200
                                    </span>
                                </div>

                                <div class="relative">
                                    <input id="title" v-model="form.title" type="text" maxlength="200"
                                        placeholder="Ex. : Comment organiser un projet Laravel ?"
                                        class="w-full rounded-xl border bg-background px-4 py-3.5 pr-11 text-sm outline-none transition placeholder:text-muted-foreground/70 focus:border-primary/50 focus:ring-2 focus:ring-primary/20"
                                        :aria-invalid="!!form.errors.title" :aria-describedby="form.errors.title
                                            ? 'title-error'
                                            : undefined
                                            " />

                                    <button v-if="form.title" type="button" aria-label="Effacer le titre"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 rounded-lg p-1 text-muted-foreground transition hover:bg-muted hover:text-foreground"
                                        @click="clearTitle">
                                        <X class="size-4" />
                                    </button>
                                </div>

                                <p v-if="form.errors.title" id="title-error" class="mt-2 text-sm text-destructive">
                                    {{ form.errors.title }}
                                </p>

                                <p v-else class="mt-2 text-xs leading-5 text-muted-foreground">
                                    Un titre clair aide les autres développeurs
                                    à comprendre ton sujet.
                                </p>
                            </section>

                            <!-- Communauté -->
                            <section>
                                <label for="community" class="mb-3 block text-sm font-bold">
                                    Où publier ?
                                </label>

                                <div class="flex items-start gap-3 rounded-xl border bg-muted/30 p-3.5">
                                    <div
                                        class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary">
                                        <Users class="size-5" />
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-semibold">
                                            {{ selectedCommunity?.name || 'Fil général de DevLink' }}
                                        </p>

                                        <p class="mt-1 text-xs leading-5 text-muted-foreground">
                                            {{
                                                selectedCommunity
                                                    ? 'Ta publication sera associée à cette communauté.'
                                                    : 'Ta publication sera partagée dans le fil général.'
                                            }}
                                        </p>
                                    </div>
                                </div>

                                <select id="community" v-model="form.community_id"
                                    class="mt-3 w-full rounded-xl border bg-background px-4 py-3.5 text-sm outline-none transition focus:border-primary/50 focus:ring-2 focus:ring-primary/20"
                                    :aria-invalid="!!form.errors.community_id">
                                    <option value="">
                                        Fil général de DevLink
                                    </option>

                                    <option v-for="community in communities" :key="community.id" :value="community.id">
                                        {{ community.name }}
                                    </option>
                                </select>

                                <p v-if="form.errors.community_id" class="mt-2 text-sm text-destructive">
                                    {{ form.errors.community_id }}
                                </p>

                                <p v-else class="mt-2 text-xs leading-5 text-muted-foreground">
                                    Choisis une communauté dans laquelle tu peux publier,
                                    ou conserve le fil général.
                                </p>
                            </section>

                            <!-- Contenu -->
                            <section>
                                <div class="mb-3 flex flex-wrap items-center justify-between gap-3">
                                    <label for="body" class="text-sm font-bold">
                                        Ton contenu
                                        <span class="text-destructive">*</span>
                                    </label>

                                    <span class="text-xs text-muted-foreground" :class="form.body.length >= 18000
                                        ? 'text-amber-500'
                                        : ''
                                        ">
                                        {{ form.body.length.toLocaleString('fr-FR') }} / 20 000
                                    </span>
                                </div>

                                <textarea id="body" v-model="form.body" rows="10" maxlength="20000" required
                                    placeholder="Qu'aimerais-tu partager avec les autres développeurs ?&#10;&#10;Tu peux présenter une idée, expliquer une solution technique, partager une découverte ou demander de l'aide..."
                                    class="w-full resize-y rounded-xl border bg-background px-4 py-4 text-sm leading-7 outline-none transition placeholder:text-muted-foreground/70 focus:border-primary/50 focus:ring-2 focus:ring-primary/20"
                                    :aria-invalid="!!form.errors.body" :aria-describedby="form.errors.body
                                        ? 'body-error'
                                        : 'body-help'
                                        " />

                                <p v-if="form.errors.body" id="body-error" class="mt-2 text-sm text-destructive">
                                    {{ form.errors.body }}
                                </p>

                                <p v-else id="body-help" class="mt-2 text-xs leading-5 text-muted-foreground">
                                    Explique ton idée clairement pour faciliter
                                    les échanges et obtenir des réponses utiles.
                                </p>
                            </section>

                            <!-- Erreur générale -->
                            <div v-if="form.hasErrors" role="alert"
                                class="rounded-xl border border-destructive/30 bg-destructive/5 p-4">
                                <p class="text-sm font-semibold text-destructive">
                                    Certains champs doivent être corrigés avant
                                    de pouvoir publier.
                                </p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <footer
                            class="flex flex-col-reverse gap-3 border-t bg-muted/20 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-8">
                            <Link href="/posts"
                                class="inline-flex items-center justify-center gap-2 rounded-xl border bg-card px-5 py-3 text-sm font-semibold transition hover:bg-muted">
                                Annuler
                            </Link>

                            <button type="submit" :disabled="!canPublish"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-primary-foreground shadow-md shadow-primary/20 transition hover:-translate-y-0.5 hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:translate-y-0">
                                <span>
                                    {{
                                        form.processing
                                            ? 'Publication en cours…'
                                            : 'Publier'
                                    }}
                                </span>

                                <Send v-if="!form.processing" class="size-4" />
                            </button>
                        </footer>
                    </form>
                </div>

                <!-- Colonne latérale -->
                <aside class="space-y-5">
                    <!-- Conseils -->
                    <section class="rounded-2xl border bg-card p-5 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="flex size-10 items-center justify-center rounded-xl bg-primary/10 text-primary">
                                <Sparkles class="size-5" />
                            </div>

                            <div>
                                <h2 class="font-bold">
                                    Une bonne publication
                                </h2>
                                <p class="text-xs text-muted-foreground">
                                    Quelques conseils
                                </p>
                            </div>
                        </div>

                        <div class="mt-5 space-y-4">
                            <div class="flex items-start gap-3">
                                <div
                                    class="flex size-7 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-xs font-bold text-primary">
                                    1
                                </div>

                                <div>
                                    <h3 class="text-sm font-semibold">
                                        Sois précis
                                    </h3>
                                    <p class="mt-1 text-xs leading-5 text-muted-foreground">
                                        Présente clairement ton idée ou le problème
                                        que tu souhaites partager.
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div
                                    class="flex size-7 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-xs font-bold text-primary">
                                    2
                                </div>

                                <div>
                                    <h3 class="text-sm font-semibold">
                                        Donne du contexte
                                    </h3>
                                    <p class="mt-1 text-xs leading-5 text-muted-foreground">
                                        Ajoute les détails utiles pour aider
                                        les autres à comprendre ton sujet.
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div
                                    class="flex size-7 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-xs font-bold text-primary">
                                    3
                                </div>

                                <div>
                                    <h3 class="text-sm font-semibold">
                                        Favorise les échanges
                                    </h3>
                                    <p class="mt-1 text-xs leading-5 text-muted-foreground">
                                        Reste constructif et ouvert aux idées
                                        des autres développeurs.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Aperçu -->
                    <section class="overflow-hidden rounded-2xl border bg-card shadow-sm">
                        <div class="border-b bg-muted/30 p-5">
                            <div class="flex items-center gap-2">
                                <Code2 class="size-4 text-primary" />
                                <h2 class="text-sm font-bold">
                                    Aperçu de ta publication
                                </h2>
                            </div>
                            <p class="mt-1 text-xs text-muted-foreground">
                                Un aperçu indicatif de ton contenu.
                            </p>
                        </div>

                        <div class="p-5">
                            <div class="flex items-center gap-2.5">
                                <div
                                    class="flex size-9 items-center justify-center rounded-xl bg-primary/10 text-primary">
                                    <Users class="size-4" />
                                </div>

                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold">
                                        {{ selectedCommunity?.name || 'Fil général de DevLink' }}
                                    </p>
                                    <p class="text-xs text-muted-foreground">
                                        Ta publication
                                    </p>
                                </div>
                            </div>

                            <h3 v-if="form.title.trim()" class="mt-4 wrap-break-word text-sm font-bold">
                                {{ form.title }}
                            </h3>

                            <p class="mt-2 whitespace-pre-wrap wrap-break-word text-sm leading-6 text-muted-foreground">
                                {{
                                    form.body.trim()
                                        ? form.body
                                        : 'Le contenu de ta publication apparaîtra ici au fur et à mesure que tu écris…'
                                }}
                            </p>

                            <div class="mt-4 flex items-center gap-4 border-t pt-3 text-xs text-muted-foreground">
                                <span class="inline-flex items-center gap-1.5">
                                    <Code2 class="size-3.5" />
                                    DevLink
                                </span>

                                <span class="inline-flex items-center gap-1.5">
                                    <MessageSquare class="size-3.5" />
                                    Discussion ouverte
                                </span>
                            </div>
                        </div>
                    </section>

                    <!-- Rappel -->
                    <section class="rounded-2xl border border-primary/20 bg-primary/5 p-5">
                        <div class="flex items-start gap-3">
                            <CheckCircle2 class="mt-0.5 size-5 shrink-0 text-primary" />

                            <div>
                                <h2 class="text-sm font-bold">
                                    Prêt à partager ?
                                </h2>
                                <p class="mt-1 text-xs leading-5 text-muted-foreground">
                                    Tes idées peuvent aider quelqu'un, résoudre
                                    un problème ou inspirer un nouveau projet.
                                </p>
                            </div>
                        </div>
                    </section>
                </aside>
            </div>
        </div>
    </main>
</template>