<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();

const auth = computed(() => page.props.auth as {
    user: {
        id: number;
        name: string;
    } | null;
});

const features = [
    {
        number: '01',
        icon: '👨‍💻',
        title: 'Rencontre des développeurs',
        description:
            'Découvre des développeurs, explore leurs compétences et trouve des personnes qui partagent tes intérêts.',
        link: '/developers',
        linkText: 'Explorer les développeurs',
        status: 'Disponible',
        available: true,
    },
    {
        number: '02',
        icon: '🌐',
        title: 'Rejoins des communautés',
        description:
            'Échange autour de Laravel, Vue, Python, PostgreSQL et de nombreux autres sujets techniques.',
        link: '/communities',
        linkText: 'Explorer les communautés',
        status: 'Disponible',
        available: true,
    },
    {
        number: '03',
        icon: '💬',
        title: 'Partage tes connaissances',
        description:
            'Publie des questions, partage tes idées et participe aux discussions avec les autres membres.',
        link: '/posts',
        linkText: 'Voir les actualités',
        status: 'Disponible',
        available: true,
    },
    {
        number: '04',
        icon: '🚀',
        title: 'Présente tes projets',
        description:
            'Mets tes réalisations en valeur, partage tes dépôts Git et montre ce que tu construis.',
        link: '/projects',
        linkText: 'Découvrir les projets',
        status: 'À venir',
        available: false,
    },
    {
        number: '05',
        icon: '🤝',
        title: 'Construis avec les autres',
        description:
            'Trouve des collaborateurs, propose ton aide et participe à des projets collectifs.',
        link: '/collaborations',
        linkText: 'Découvrir les collaborations',
        status: 'À venir',
        available: false,
    },
    {
        number: '06',
        icon: '✉️',
        title: 'Échange directement',
        description:
            'Reste en contact avec les développeurs et organise tes échanges au même endroit.',
        link: '/messages',
        linkText: 'Accéder à la messagerie',
        status: 'À venir',
        available: false,
    },
];
</script>

<template>

    <Head title="DevLink — La communauté des développeurs" />

    <div>
        <!-- Section principale -->
        <section class="relative isolate overflow-hidden border-b">
            <div
                class="pointer-events-none absolute inset-0 -z-10 bg-linear-to-br from-primary/10 via-background to-background">
            </div>

            <div
                class="pointer-events-none absolute -right-32 -top-32 -z-10 h-96 w-96 rounded-full bg-primary/10 blur-3xl">
            </div>

            <div
                class="mx-auto grid max-w-7xl items-center gap-12 px-4 py-20 sm:px-6 sm:py-28 lg:grid-cols-2 lg:px-8 lg:py-32">
                <div>
                    <div
                        class="inline-flex items-center gap-2 rounded-full border bg-background/80 px-3 py-1.5 text-sm font-medium shadow-sm">
                        <span class="h-2 w-2 rounded-full bg-primary"></span>

                        <span>Une communauté faite pour les développeurs</span>
                    </div>

                    <h1
                        class="mt-7 max-w-2xl text-4xl font-extrabold leading-tight tracking-tight sm:text-5xl lg:text-6xl">
                        Le code nous rassemble.
                        <span class="text-primary">
                            Les idées nous font avancer.
                        </span>
                    </h1>

                    <p class="mt-6 max-w-xl text-base leading-8 text-muted-foreground sm:text-lg">
                        DevLink est l'espace où les développeurs se rencontrent,
                        partagent leurs connaissances, découvrent de nouvelles
                        technologies et construisent ensemble.
                    </p>

                    <div class="mt-8 flex flex-wrap gap-3">
                        <Link v-if="!auth.user" href="/register"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-6 py-3.5 text-sm font-bold text-primary-foreground shadow-sm transition hover:-translate-y-0.5 hover:opacity-90">
                            Rejoindre DevLink
                            <span aria-hidden="true">→</span>
                        </Link>

                        <Link v-else href="/posts/create"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-6 py-3.5 text-sm font-bold text-primary-foreground shadow-sm transition hover:-translate-y-0.5 hover:opacity-90">
                            Partager une idée
                            <span aria-hidden="true">→</span>
                        </Link>

                        <Link href="/developers"
                            class="inline-flex items-center justify-center rounded-xl border bg-background px-6 py-3.5 text-sm font-bold transition hover:bg-muted">
                            Explorer la communauté
                        </Link>
                    </div>

                    <div class="mt-10 flex flex-wrap items-center gap-x-6 gap-y-3 text-sm text-muted-foreground">
                        <span class="inline-flex items-center gap-2">
                            <span class="font-bold text-primary">✓</span>
                            Profils de développeurs
                        </span>

                        <span class="inline-flex items-center gap-2">
                            <span class="font-bold text-primary">✓</span>
                            Communautés techniques
                        </span>

                        <span class="inline-flex items-center gap-2">
                            <span class="font-bold text-primary">✓</span>
                            Partage de connaissances
                        </span>
                    </div>
                </div>

                <!-- Illustration construite avec du HTML et du CSS -->
                <div class="relative mx-auto w-full max-w-lg">
                    <div class="absolute -inset-4 rounded-4xl bg-primary/10 blur-2xl"></div>

                    <div class="relative overflow-hidden rounded-3xl border bg-card p-5 shadow-2xl sm:p-7">
                        <div class="flex items-center justify-between border-b pb-5">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary text-lg font-black text-primary-foreground">
                                    D
                                </div>

                                <div>
                                    <p class="font-bold">DevLink</p>
                                    <p class="text-xs text-muted-foreground">
                                        Espace développeur
                                    </p>
                                </div>
                            </div>

                            <span class="rounded-full bg-primary/10 px-3 py-1 text-xs font-semibold text-primary">
                                Communauté
                            </span>
                        </div>

                        <div class="py-6">
                            <p class="text-xs font-bold uppercase tracking-widest text-primary">
                                Le fil des développeurs
                            </p>

                            <h2 class="mt-2 text-xl font-bold">
                                Les bonnes idées se partagent.
                            </h2>

                            <p class="mt-2 text-sm leading-6 text-muted-foreground">
                                Une question, une découverte ou un projet :
                                chaque échange peut faire la différence.
                            </p>
                        </div>

                        <div class="space-y-3">
                            <div class="rounded-2xl border p-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-500/10 text-sm font-bold text-blue-600">
                                        L
                                    </div>

                                    <div class="flex-1">
                                        <p class="text-sm font-semibold">
                                            Laravel Developers
                                        </p>

                                        <p class="text-xs text-muted-foreground">
                                            Développement web
                                        </p>
                                    </div>

                                    <span class="text-lg">⌘</span>
                                </div>

                                <p class="mt-3 text-sm leading-6 text-muted-foreground">
                                    Partager des solutions, comprendre les
                                    bonnes pratiques et progresser ensemble.
                                </p>
                            </div>

                            <div class="rounded-2xl border p-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-500/10 text-sm font-bold text-emerald-600">
                                        V
                                    </div>

                                    <div class="flex-1">
                                        <p class="text-sm font-semibold">
                                            Vue.js Community
                                        </p>

                                        <p class="text-xs text-muted-foreground">
                                            Front-end
                                        </p>
                                    </div>

                                    <span class="text-lg">↗</span>
                                </div>

                                <p class="mt-3 text-sm leading-6 text-muted-foreground">
                                    Découvrir des approches différentes et
                                    échanger autour de son code.
                                </p>
                            </div>
                        </div>

                        <div class="mt-5 flex items-center justify-between rounded-xl bg-muted/60 px-4 py-3">
                            <span class="text-sm font-medium">
                                Apprendre · Partager · Construire
                            </span>

                            <span class="text-lg text-primary">✦</span>
                        </div>
                    </div>

                    <div
                        class="absolute -bottom-5 -left-3 hidden rounded-2xl border bg-card px-4 py-3 shadow-lg sm:block">
                        <p class="text-xs text-muted-foreground">
                            Notre philosophie
                        </p>

                        <p class="mt-1 text-sm font-bold">
                            Par les développeurs, pour les développeurs.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Présentation des fonctionnalités -->
        <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <p class="text-sm font-bold uppercase tracking-widest text-primary">
                    Un espace, plusieurs possibilités
                </p>

                <h2 class="mt-3 text-3xl font-extrabold tracking-tight sm:text-4xl">
                    Tout commence par une rencontre.
                </h2>

                <p class="mt-4 leading-7 text-muted-foreground">
                    Que tu débutes, que tu sois expérimenté ou que tu
                    construises un produit, DevLink a vocation à t'aider à
                    trouver les bonnes personnes et les bonnes idées.
                </p>
            </div>

            <div class="mt-10 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                <article v-for="feature in features" :key="feature.number"
                    class="group flex flex-col rounded-2xl border bg-card p-6 transition duration-200 hover:-translate-y-1 hover:shadow-lg">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10 text-2xl"
                            aria-hidden="true">
                            {{ feature.icon }}
                        </div>

                        <span class="rounded-full px-3 py-1 text-xs font-semibold" :class="feature.available
                            ? 'bg-primary/10 text-primary'
                            : 'bg-muted text-muted-foreground'
                            ">
                            {{ feature.status }}
                        </span>
                    </div>

                    <p class="mt-6 text-xs font-bold tracking-widest text-muted-foreground">
                        {{ feature.number }}
                    </p>

                    <h3 class="mt-2 text-xl font-bold">
                        {{ feature.title }}
                    </h3>

                    <p class="mt-3 flex-1 text-sm leading-7 text-muted-foreground">
                        {{ feature.description }}
                    </p>

                    <Link :href="feature.link"
                        class="mt-6 inline-flex items-center gap-2 text-sm font-bold transition group-hover:text-primary"
                        :class="feature.available
                            ? 'text-primary'
                            : 'text-muted-foreground'
                            ">
                        {{ feature.linkText }}
                        <span aria-hidden="true">→</span>
                    </Link>
                </article>
            </div>
        </section>

        <!-- Section consacrée à la communauté -->
        <section class="border-y bg-muted/20">
            <div class="mx-auto grid max-w-7xl items-center gap-10 px-4 py-20 sm:px-6 lg:grid-cols-2 lg:px-8">
                <div>
                    <p class="text-sm font-bold uppercase tracking-widest text-primary">
                        Apprendre ensemble
                    </p>

                    <h2 class="mt-3 text-3xl font-extrabold tracking-tight sm:text-4xl">
                        Ta prochaine collaboration peut commencer ici.
                    </h2>

                    <p class="mt-5 leading-7 text-muted-foreground">
                        Une communauté technique permet de poser des
                        questions, de confronter des idées et d'apprendre
                        de l'expérience des autres. Rejoins les discussions
                        qui t'intéressent et apporte ta propre contribution.
                    </p>

                    <div class="mt-7 flex flex-wrap gap-3">
                        <Link href="/communities"
                            class="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-3 text-sm font-bold text-primary-foreground transition hover:opacity-90">
                            Explorer les communautés
                            <span aria-hidden="true">→</span>
                        </Link>

                        <Link href="/posts"
                            class="inline-flex items-center rounded-xl border bg-background px-5 py-3 text-sm font-bold transition hover:bg-muted">
                            Lire les discussions
                        </Link>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="rounded-2xl border bg-card p-5 sm:p-6">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary/10 text-xl"
                            aria-hidden="true">
                            💡
                        </div>

                        <h3 class="mt-4 font-bold">Partager</h3>

                        <p class="mt-2 text-sm leading-6 text-muted-foreground">
                            Transmets tes connaissances et tes découvertes.
                        </p>
                    </div>

                    <div class="mt-8 rounded-2xl border bg-card p-5 sm:p-6">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary/10 text-xl"
                            aria-hidden="true">
                            🧩
                        </div>

                        <h3 class="mt-4 font-bold">Résoudre</h3>

                        <p class="mt-2 text-sm leading-6 text-muted-foreground">
                            Trouve de nouvelles approches aux problèmes.
                        </p>
                    </div>

                    <div class="-mt-8 rounded-2xl border bg-card p-5 sm:p-6">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary/10 text-xl"
                            aria-hidden="true">
                            🌱
                        </div>

                        <h3 class="mt-4 font-bold">Progresser</h3>

                        <p class="mt-2 text-sm leading-6 text-muted-foreground">
                            Développe tes compétences avec les autres.
                        </p>
                    </div>

                    <div class="rounded-2xl border bg-card p-5 sm:p-6">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary/10 text-xl"
                            aria-hidden="true">
                            🛠️
                        </div>

                        <h3 class="mt-4 font-bold">Construire</h3>

                        <p class="mt-2 text-sm leading-6 text-muted-foreground">
                            Transforme les idées en projets concrets.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Appel final à l'action -->
        <section class="mx-auto max-w-5xl px-4 py-20 sm:px-6 lg:px-8">
            <div
                class="relative overflow-hidden rounded-3xl bg-primary px-6 py-12 text-center text-primary-foreground sm:px-12 sm:py-16">
                <div
                    class="pointer-events-none absolute -right-16 -top-20 h-64 w-64 rounded-full border border-white/20">
                </div>

                <div
                    class="pointer-events-none absolute -bottom-32 -left-16 h-72 w-72 rounded-full border border-white/20">
                </div>

                <div class="relative">
                    <p class="text-sm font-bold uppercase tracking-widest opacity-80">
                        C'est à ton tour
                    </p>

                    <h2 class="mx-auto mt-4 max-w-2xl text-3xl font-extrabold tracking-tight sm:text-4xl">
                        Le prochain échange intéressant pourrait être le tien.
                    </h2>

                    <p class="mx-auto mt-4 max-w-xl leading-7 opacity-90">
                        Rejoins les développeurs, partage tes idées et
                        participe à la construction de DevLink.
                    </p>

                    <div class="mt-8 flex flex-wrap justify-center gap-3">
                        <Link v-if="!auth.user" href="/register"
                            class="rounded-xl bg-background px-6 py-3.5 text-sm font-bold text-foreground transition hover:opacity-90">
                            Créer mon compte
                        </Link>

                        <Link v-else href="/posts/create"
                            class="rounded-xl bg-background px-6 py-3.5 text-sm font-bold text-foreground transition hover:opacity-90">
                            Créer une publication
                        </Link>

                        <Link href="/developers"
                            class="rounded-xl border border-primary-foreground/40 px-6 py-3.5 text-sm font-bold transition hover:bg-primary-foreground/10">
                            Découvrir les développeurs
                        </Link>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
