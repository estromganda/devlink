<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import {
    ArrowDownRight,
    ArrowRight,
    ArrowUpRight,
    BookOpen,
    Check,
    Code2,
    Compass,
    GitBranch,
    Lightbulb,
    MessageCircle,
    MessagesSquare,
    Rocket,
    Users,
    Wrench,
    Zap,
} from '@lucide/vue';

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
        icon: Users,
        title: 'Rencontre des développeurs',
        description:
            'Découvre des développeurs, explore leurs compétences et trouve des personnes qui partagent tes intérêts.',
        link: '/developers',
        linkText: 'Explorer les développeurs',
        available: true,
    },
    {
        number: '02',
        icon: Compass,
        title: 'Rejoins des communautés',
        description:
            'Échange autour de Laravel, Vue, Python, PostgreSQL et de nombreux autres sujets techniques.',
        link: '/communities',
        linkText: 'Explorer les communautés',
        available: true,
    },
    {
        number: '03',
        icon: MessagesSquare,
        title: 'Partage tes connaissances',
        description:
            'Pose tes questions, partage tes idées et participe aux discussions avec les autres membres.',
        link: '/posts',
        linkText: 'Voir les actualités',
        available: true,
    },
    {
        number: '04',
        icon: Rocket,
        title: 'Présente tes projets',
        description:
            'Mets tes réalisations en valeur, partage tes dépôts Git et montre ce que tu construis.',
        link: '/projects',
        linkText: 'Découvrir les projets',
        available: false,
    },
    {
        number: '05',
        icon: GitBranch,
        title: 'Construis avec les autres',
        description:
            'Trouve des collaborateurs, propose ton aide et participe à des projets collectifs.',
        link: '/my-collaboration-requests',
        linkText: 'Découvrir les collaborations',
        available: false,
    },
    {
        number: '06',
        icon: MessageCircle,
        title: 'Échange directement',
        description:
            'Reste en contact avec les développeurs et organise tes échanges au même endroit.',
        link: '/messages',
        linkText: 'Accéder à la messagerie',
        available: false,
    },
];

const principles = [
    {
        icon: Lightbulb,
        title: 'Partager',
        description: 'Transmets tes connaissances et tes découvertes.',
    },
    {
        icon: Wrench,
        title: 'Résoudre',
        description: 'Trouve de nouvelles approches aux problèmes.',
    },
    {
        icon: BookOpen,
        title: 'Progresser',
        description: 'Développe tes compétences avec les autres.',
    },
    {
        icon: Rocket,
        title: 'Construire',
        description: 'Transforme les idées en projets concrets.',
    },
];
</script>

<template>

    <Head title="DevLink — La communauté des développeurs" />

    <main class="overflow-hidden">
        <!-- Hero -->
        <section class="relative isolate border-b">
            <div
                class="pointer-events-none absolute inset-0 -z-10 bg-linear-to-br from-primary/10 via-background to-background" />

            <div
                class="pointer-events-none absolute -right-40 -top-40 -z-10 size-112 rounded-full bg-primary/10 blur-3xl" />

            <div
                class="pointer-events-none absolute -bottom-40 -left-40 -z-10 size-96 rounded-full bg-primary/5 blur-3xl" />

            <div
                class="mx-auto grid max-w-7xl items-center gap-14 px-4 py-16 sm:px-6 sm:py-24 lg:grid-cols-2 lg:gap-16 lg:px-8 lg:py-28">
                <!-- Hero content -->
                <div class="relative">
                    <div
                        class="inline-flex items-center gap-2.5 rounded-full border border-primary/20 bg-background/80 px-4 py-2 text-xs font-semibold shadow-sm backdrop-blur sm:text-sm">
                        <span class="relative flex size-2.5">
                            <span class="absolute inline-flex size-full animate-ping rounded-full bg-primary/60" />
                            <span class="relative inline-flex size-2.5 rounded-full bg-primary" />
                        </span>

                        Une communauté faite pour les développeurs
                    </div>

                    <h1
                        class="mt-7 max-w-2xl text-4xl font-black leading-[1.12] tracking-tight sm:text-5xl lg:text-6xl xl:text-7xl">
                        Le code nous
                        <span class="text-primary">rassemble.</span>
                        <span class="mt-1 block">
                            Les idées nous font avancer.
                        </span>
                    </h1>

                    <p class="mt-6 max-w-xl text-base leading-8 text-muted-foreground sm:text-lg">
                        DevLink est l'espace où les développeurs se rencontrent,
                        partagent leurs connaissances, découvrent de nouvelles
                        technologies et construisent ensemble.
                    </p>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <Link v-if="!auth.user" href="/register"
                            class="group inline-flex items-center justify-center gap-2.5 rounded-xl bg-primary px-6 py-3.5 text-sm font-bold text-primary-foreground shadow-lg shadow-primary/20 transition duration-200 hover:-translate-y-0.5 hover:shadow-xl hover:shadow-primary/25">
                            Rejoindre DevLink
                            <ArrowRight class="size-4 transition-transform group-hover:translate-x-1" />
                        </Link>

                        <Link v-else href="/posts/create"
                            class="group inline-flex items-center justify-center gap-2.5 rounded-xl bg-primary px-6 py-3.5 text-sm font-bold text-primary-foreground shadow-lg shadow-primary/20 transition duration-200 hover:-translate-y-0.5 hover:shadow-xl hover:shadow-primary/25">
                            Partager une idée
                            <ArrowRight class="size-4 transition-transform group-hover:translate-x-1" />
                        </Link>

                        <Link href="/developers"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border bg-background/80 px-6 py-3.5 text-sm font-bold transition hover:border-primary/30 hover:bg-muted">
                            Explorer la communauté
                            <ArrowUpRight class="size-4" />
                        </Link>
                    </div>

                    <div class="mt-9 flex flex-wrap gap-x-5 gap-y-3 text-sm text-muted-foreground">
                        <span class="inline-flex items-center gap-2">
                            <Check class="size-4 text-primary" />
                            Profils de développeurs
                        </span>

                        <span class="inline-flex items-center gap-2">
                            <Check class="size-4 text-primary" />
                            Communautés techniques
                        </span>

                        <span class="inline-flex items-center gap-2">
                            <Check class="size-4 text-primary" />
                            Partage de connaissances
                        </span>
                    </div>
                </div>

                <!-- Illustration de l'écosystème DevLink -->
                <div class="relative mx-auto w-full max-w-xl">
                    <div class="absolute -inset-5 rounded-4xl bg-primary/10 blur-2xl" />

                    <div class="relative overflow-hidden rounded-3xl border bg-card shadow-2xl shadow-black/5">
                        <!-- Window header -->
                        <div class="flex items-center justify-between border-b px-5 py-4 sm:px-6">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex size-11 items-center justify-center rounded-2xl bg-primary text-primary-foreground shadow-md shadow-primary/20">
                                    <Code2 class="size-6" />
                                </div>

                                <div>
                                    <p class="font-bold tracking-tight">
                                        DevLink
                                    </p>
                                    <p class="text-xs text-muted-foreground">
                                        Espace développeur
                                    </p>
                                </div>
                            </div>

                            <div
                                class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/10 px-3 py-1.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                                <span class="size-1.5 rounded-full bg-emerald-500" />
                                Connectés par le code
                            </div>
                        </div>

                        <div class="p-5 sm:p-6">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-primary">
                                        L'écosystème DevLink
                                    </p>

                                    <h2 class="mt-2 text-xl font-extrabold sm:text-2xl">
                                        Les bonnes idées se partagent.
                                    </h2>
                                </div>

                                <div
                                    class="hidden size-12 shrink-0 items-center justify-center rounded-2xl bg-primary/10 text-primary sm:flex">
                                    <Zap class="size-6" />
                                </div>
                            </div>

                            <p class="mt-3 max-w-md text-sm leading-6 text-muted-foreground">
                                Une question, une découverte ou un projet :
                                chaque échange est une occasion de progresser.
                            </p>

                            <!-- Community cards -->
                            <div class="mt-6 space-y-3">
                                <div
                                    class="group rounded-2xl border p-4 transition duration-200 hover:border-primary/30 hover:bg-muted/30">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-red-500/10 text-sm font-black text-red-500">
                                            L
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-sm font-bold">
                                                Laravel Developers
                                            </p>
                                            <p class="mt-0.5 text-xs text-muted-foreground">
                                                Développement web · Backend
                                            </p>
                                        </div>

                                        <ArrowUpRight
                                            class="size-4 shrink-0 text-muted-foreground transition group-hover:text-primary" />
                                    </div>

                                    <p class="mt-3 text-sm leading-6 text-muted-foreground">
                                        Partager des solutions, comprendre les
                                        bonnes pratiques et progresser ensemble.
                                    </p>

                                    <div class="mt-3 flex flex-wrap gap-2">
                                        <span class="rounded-md bg-muted px-2 py-1 text-[11px] font-medium">
                                            Laravel
                                        </span>
                                        <span class="rounded-md bg-muted px-2 py-1 text-[11px] font-medium">
                                            PHP
                                        </span>
                                        <span class="rounded-md bg-muted px-2 py-1 text-[11px] font-medium">
                                            API
                                        </span>
                                    </div>
                                </div>

                                <div
                                    class="group rounded-2xl border p-4 transition duration-200 hover:border-primary/30 hover:bg-muted/30">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-emerald-500/10 text-sm font-black text-emerald-600 dark:text-emerald-400">
                                            V
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-sm font-bold">
                                                Vue.js Community
                                            </p>
                                            <p class="mt-0.5 text-xs text-muted-foreground">
                                                Front-end · Interfaces
                                            </p>
                                        </div>

                                        <ArrowUpRight
                                            class="size-4 shrink-0 text-muted-foreground transition group-hover:text-primary" />
                                    </div>

                                    <p class="mt-3 text-sm leading-6 text-muted-foreground">
                                        Découvrir de nouvelles approches et
                                        échanger autour de son code.
                                    </p>

                                    <div class="mt-3 flex flex-wrap gap-2">
                                        <span class="rounded-md bg-muted px-2 py-1 text-[11px] font-medium">
                                            Vue.js
                                        </span>
                                        <span class="rounded-md bg-muted px-2 py-1 text-[11px] font-medium">
                                            TypeScript
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Bottom tagline -->
                            <div class="mt-5 flex items-center gap-3 rounded-xl bg-primary/5 px-4 py-3.5">
                                <div
                                    class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                    <GitBranch class="size-5" />
                                </div>

                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-bold">
                                        Apprendre · Partager · Construire
                                    </p>
                                    <p class="mt-0.5 text-xs text-muted-foreground">
                                        Par les développeurs, pour les développeurs.
                                    </p>
                                </div>

                                <ArrowDownRight class="size-5 shrink-0 text-primary" />
                            </div>
                        </div>
                    </div>

                    <!-- Floating badge -->
                    <div
                        class="absolute -bottom-5 -left-2 hidden max-w-xs items-center gap-3 rounded-2xl border bg-card px-4 py-3 shadow-xl sm:flex lg:-left-8">
                        <div
                            class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary">
                            <Users class="size-5" />
                        </div>

                        <div>
                            <p class="text-xs text-muted-foreground">
                                Une communauté
                            </p>
                            <p class="text-sm font-bold">
                                Une passion commune
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Fonctionnalités -->
        <section id="fonctionnalites" class="mx-auto max-w-7xl px-4 py-20 sm:px-6 sm:py-24 lg:px-8">
            <div class="flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                <div class="max-w-2xl">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-primary sm:text-sm">
                        Un espace, plusieurs possibilités
                    </p>

                    <h2 class="mt-3 text-3xl font-black tracking-tight sm:text-4xl lg:text-5xl">
                        Tout commence par
                        <span class="text-primary">une rencontre.</span>
                    </h2>

                    <p class="mt-5 max-w-2xl leading-7 text-muted-foreground">
                        Que tu débutes, que tu sois expérimenté ou que tu
                        construises un produit, DevLink t'aide à trouver les
                        bonnes personnes et les bonnes idées.
                    </p>
                </div>

                <div
                    class="inline-flex w-fit items-center gap-2 rounded-full border bg-card px-4 py-2 text-xs font-semibold text-muted-foreground">
                    <Code2 class="size-4 text-primary" />
                    Conçu pour les développeurs
                </div>
            </div>

            <div class="mt-10 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                <article v-for="feature in features" :key="feature.number"
                    class="group relative flex flex-col overflow-hidden rounded-2xl border bg-card p-6 transition duration-300 hover:-translate-y-1 hover:border-primary/30 hover:shadow-xl hover:shadow-primary/5 sm:p-7">
                    <div
                        class="pointer-events-none absolute -right-10 -top-10 size-32 rounded-full bg-primary/5 opacity-0 blur-2xl transition duration-300 group-hover:opacity-100" />

                    <div class="relative flex items-start justify-between gap-4">
                        <div
                            class="flex size-12 items-center justify-center rounded-2xl bg-primary/10 text-primary transition duration-300 group-hover:scale-110 group-hover:bg-primary group-hover:text-primary-foreground">
                            <component :is="feature.icon" class="size-6" />
                        </div>


                    </div>

                    <h3 class="relative mt-6 text-lg font-extrabold sm:text-xl">
                        {{ feature.title }}
                    </h3>

                    <p class="relative mt-3 flex-1 text-sm leading-7 text-muted-foreground">
                        {{ feature.description }}
                    </p>

                    <Link :href="feature.link"
                        class="relative mt-6 inline-flex w-fit items-center gap-2 text-sm font-bold transition" :class="feature.available
                            ? 'text-primary'
                            : 'text-muted-foreground hover:text-foreground'
                            ">
                        {{ feature.linkText }}

                        <ArrowRight class="size-4 transition-transform group-hover:translate-x-1" />
                    </Link>
                </article>
            </div>
        </section>

        <!-- Philosophie de la communauté -->
        <section class="relative border-y bg-muted/20">
            <div
                class="pointer-events-none absolute inset-0 bg-linear-to-r from-primary/4 via-transparent to-primary/4" />

            <div
                class="relative mx-auto grid max-w-7xl items-center gap-12 px-4 py-20 sm:px-6 sm:py-24 lg:grid-cols-2 lg:gap-16 lg:px-8">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-primary sm:text-sm">
                        Apprendre ensemble
                    </p>

                    <h2 class="mt-3 text-3xl font-black tracking-tight sm:text-4xl lg:text-5xl">
                        Ta prochaine collaboration peut commencer ici.
                    </h2>

                    <p class="mt-5 leading-8 text-muted-foreground">
                        Une communauté technique permet de poser des
                        questions, de confronter des idées et d'apprendre de
                        l'expérience des autres. Rejoins les discussions qui
                        t'intéressent et apporte ta propre contribution.
                    </p>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <Link href="/communities"
                            class="group inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3.5 text-sm font-bold text-primary-foreground transition hover:-translate-y-0.5 hover:shadow-lg hover:shadow-primary/20">
                            Explorer les communautés
                            <ArrowRight class="size-4 transition-transform group-hover:translate-x-1" />
                        </Link>

                        <Link href="/posts"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border bg-background px-5 py-3.5 text-sm font-bold transition hover:border-primary/30 hover:bg-muted">
                            Lire les discussions
                            <MessagesSquare class="size-4" />
                        </Link>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <article v-for="(principle, index) in principles" :key="principle.title"
                        class="rounded-2xl border bg-card p-5 transition duration-200 hover:-translate-y-1 hover:border-primary/30 hover:shadow-lg sm:p-6"
                        :class="index % 2 === 1 ? 'sm:translate-y-7' : ''">
                        <div class="flex size-12 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                            <component :is="principle.icon" class="size-6" />
                        </div>

                        <h3 class="mt-5 text-lg font-extrabold">
                            {{ principle.title }}
                        </h3>

                        <p class="mt-2 text-sm leading-7 text-muted-foreground">
                            {{ principle.description }}
                        </p>
                    </article>
                </div>
            </div>
        </section>

        <!-- Appel à l'action -->
        <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 sm:py-24 lg:px-8">
            <div
                class="relative isolate overflow-hidden rounded-3xl bg-primary px-6 py-12 text-center text-primary-foreground sm:px-12 sm:py-16 lg:px-20 lg:py-20">
                <div
                    class="pointer-events-none absolute -right-24 -top-32 -z-10 size-80 rounded-full border border-primary-foreground/15" />
                <div
                    class="pointer-events-none absolute -right-12 -top-20 -z-10 size-56 rounded-full border border-primary-foreground/15" />
                <div
                    class="pointer-events-none absolute -bottom-40 -left-20 -z-10 size-96 rounded-full border border-primary-foreground/15" />

                <div class="relative mx-auto max-w-3xl">
                    <div
                        class="mx-auto flex size-14 items-center justify-center rounded-2xl border border-primary-foreground/20 bg-primary-foreground/10">
                        <Code2 class="size-7" />
                    </div>

                    <p class="mt-6 text-xs font-bold uppercase tracking-[0.2em] opacity-80 sm:text-sm">
                        C'est à ton tour
                    </p>

                    <h2 class="mt-3 text-3xl font-black tracking-tight sm:text-4xl lg:text-5xl">
                        Le prochain échange intéressant pourrait être le tien.
                    </h2>

                    <p class="mx-auto mt-5 max-w-2xl leading-7 opacity-90 sm:text-lg sm:leading-8">
                        Rejoins les développeurs, partage tes idées et
                        participe à la construction de DevLink.
                    </p>

                    <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                        <Link v-if="!auth.user" href="/register"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-background px-6 py-3.5 text-sm font-bold text-foreground shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg">
                            Créer mon compte
                            <ArrowRight class="size-4" />
                        </Link>

                        <Link v-else href="/posts/create"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-background px-6 py-3.5 text-sm font-bold text-foreground shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg">
                            Créer une publication
                            <ArrowRight class="size-4" />
                        </Link>

                        <Link href="/developers"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-primary-foreground/40 px-6 py-3.5 text-sm font-bold transition hover:bg-primary-foreground/10">
                            Découvrir les développeurs
                            <Users class="size-4" />
                        </Link>
                    </div>
                </div>
            </div>
        </section>
    </main>
</template>