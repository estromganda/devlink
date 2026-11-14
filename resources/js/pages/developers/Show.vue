<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import {
    ArrowLeft,
    ArrowUpRight,
    Globe,
    MapPin,
    Code2,
    Star,
    BriefcaseBusiness,
    Sparkles,
    UserRound,
    Terminal,
    GraduationCap,
    ExternalLink,
    Users,
} from '@lucide/vue';
import { computed } from 'vue';

interface Developer {
    id: number;
    name: string;
    headline: string | null;
    bio: string | null;
    location: string | null;
    website: string | null;
    github_url: string | null;
    linkedin_url: string | null;
}

interface Technology {
    id: number;
    name: string;
    category: string | null;
    skill_level: string;
    years_experience: number | null;
    is_primary: boolean;
}

type AuthUser = {
    id: number;
    name: string;
    email?: string;
    avatar_path?: string | null;
};

const page = usePage();

const auth = computed(() => page.props.auth as {
    user: AuthUser | null;
});

const messageForm = useForm({
    recipient_id: 0,
});

function startConversation() {
    if (!props.developer.id || props.developer.id === auth.value?.user?.id) {
        return;
    }

    messageForm.recipient_id = props.developer.id;

    messageForm.post('/messages/start', {
        preserveScroll: true,
    });
}


const props = defineProps<{
    developer: Developer;
    technologies: Technology[];
}>();

const skillLevelLabels: Record<string, string> = {
    beginner: 'Débutant',
    intermediate: 'Intermédiaire',
    advanced: 'Avancé',
    expert: 'Expert',
};

function formatSkillLevel(level: string): string {
    return skillLevelLabels[level] ?? level;
}

function skillLevelClass(_level: string): string {
    return 'border-primary/20 bg-primary/10 text-primary';
}

function formatExperience(years: number | null): string {
    if (years === null) {
        return 'Expérience non précisée';
    }

    if (years === 0) {
        return 'Moins d’un an';
    }

    return `${years} an${years > 1 ? 's' : ''} d’expérience`;
}

function developerInitial(name: string): string {
    return name.trim().charAt(0).toUpperCase() || '?';
}

const primaryTechnologies = computed(() =>
    props.technologies.filter((technology) => technology.is_primary),
);

const professionalLinks = computed(() => [
    {
        label: 'Site personnel',
        url: props.developer.website,
        icon: Globe,
        description: 'Portfolio et site web',
    },
    {
        label: 'GitHub',
        url: props.developer.github_url,
        icon: Code2,
        description: 'Code et projets',
    },
    {
        label: 'LinkedIn',
        url: props.developer.linkedin_url,
        icon: Globe,
        description: 'Profil professionnel',
    },
].filter((item) => item.url));
</script>

<template>

    <Head :title="`${developer.name} — Développeur DevColab`">
        <meta name="description" :content="developer.bio || `Découvrez le profil de ${developer.name} sur DevColab.`" />
    </Head>

    <main class="min-h-screen bg-background py-6 sm:py-10">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">

            <!-- Navigation -->
            <div class="mb-6 flex items-center justify-between gap-3">
                <Link href="/developers"
                    class="group inline-flex items-center gap-2 rounded-lg text-sm font-medium text-muted-foreground transition hover:text-primary">
                    <ArrowLeft class="size-4 transition-transform group-hover:-translate-x-1" />
                    Tous les développeurs
                </Link>

                <Link v-if="!auth.user" href="/login"
                    class="inline-flex items-center gap-2 rounded-xl border bg-card px-4 py-2.5 text-sm font-semibold transition hover:border-primary/40 hover:bg-muted">
                    Se connecter
                    <ArrowUpRight class="size-4 text-primary" />
                </Link>
            </div>

            <!-- Carte principale du profil -->
            <section class="overflow-hidden rounded-3xl border bg-card shadow-sm">

                <!-- Bannière -->
                <div
                    class="relative h-36 overflow-hidden border-b bg-linear-to-br from-primary/15 via-primary/5 to-background sm:h-48">
                    <div class="absolute -left-10 -top-20 size-64 rounded-full border border-primary/10" />
                    <div class="absolute -bottom-36 left-1/3 size-96 rounded-full border border-primary/10" />
                    <div class="absolute -right-10 top-5 size-48 rounded-full border border-primary/10" />

                    <div
                        class="absolute right-5 top-5 inline-flex items-center gap-2 rounded-full border bg-background/80 px-3 py-1.5 text-xs font-semibold text-primary backdrop-blur-sm sm:right-8 sm:top-8">
                        <Sparkles class="size-3.5" />
                        Membre de DevColab
                    </div>

                    <div
                        class="absolute bottom-5 left-5 flex items-center gap-2 text-muted-foreground sm:bottom-8 sm:left-8">
                        <Terminal class="size-5 text-primary" />
                        <span class="text-xs font-medium tracking-wide sm:text-sm">
                            DEVELOPER PROFILE
                        </span>
                    </div>
                </div>

                <div class="px-5 pb-7 sm:px-8 sm:pb-9 lg:px-10">

                    <!-- Identité -->
                    <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                        <div class="min-w-0">

                            <!-- Avatar -->
                            <div
                                class="-mt-12 flex size-24 items-center justify-center rounded-3xl border-4 border-card bg-primary/10 text-3xl font-bold text-primary shadow-sm sm:-mt-14 sm:size-28">
                                <span aria-label="Initiale du développeur">
                                    {{ developerInitial(developer.name) }}
                                </span>
                            </div>

                            <div class="mt-5">
                                <div class="flex flex-wrap items-center gap-3">
                                    <h1
                                        class="wrap-break-word text-3xl font-bold tracking-tight text-foreground sm:text-4xl">
                                        {{ developer.name }}
                                    </h1>

                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full border border-primary/20 bg-primary/10 px-2.5 py-1 text-xs font-semibold text-primary">
                                        <span class="size-1.5 rounded-full bg-primary" />
                                        Développeur
                                    </span>
                                </div>

                                <p v-if="developer.headline"
                                    class="mt-3 max-w-2xl text-base leading-7 text-muted-foreground sm:text-lg">
                                    {{ developer.headline }}
                                </p>

                                <div
                                    class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-3 text-sm text-muted-foreground">
                                    <div v-if="developer.location" class="inline-flex items-center gap-2">
                                        <MapPin class="size-4 shrink-0 text-primary" />
                                        {{ developer.location }}
                                    </div>

                                    <div class="inline-flex items-center gap-2">
                                        <Code2 class="size-4 shrink-0 text-primary" />
                                        {{ technologies.length }}
                                        {{ technologies.length > 1 ? 'technologies' : 'technologie' }}
                                    </div>

                                    <div v-if="primaryTechnologies.length > 0" class="inline-flex items-center gap-2">
                                        <Star class="size-4 shrink-0 text-primary" />
                                        {{ primaryTechnologies.length }}
                                        {{ primaryTechnologies.length > 1
                                            ? 'spécialités principales'
                                            : 'spécialité principale' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Liens rapides -->
                        <div v-if="professionalLinks.length > 0" class="flex shrink-0 flex-wrap gap-2 sm:pb-1">
                            <a v-for="item in professionalLinks" :key="item.label" :href="item.url!" target="_blank"
                                rel="noopener noreferrer" :aria-label="`${item.label} — nouvel onglet`"
                                :title="item.label"
                                class="inline-flex size-11 items-center justify-center rounded-xl border bg-background text-muted-foreground transition hover:border-primary/40 hover:bg-primary/5 hover:text-primary">
                                <component :is="item.icon" class="size-5" />
                            </a>
                        </div>
                    </div>

                    <!-- À propos -->
                    <section class="mt-9 border-t pt-7">
                        <div class="flex items-center gap-3">
                            <div class="flex size-10 items-center justify-center rounded-xl bg-primary/10 text-primary">
                                <UserRound class="size-5" />
                            </div>

                            <div>
                                <h2 class="text-lg font-bold text-foreground">
                                    À propos
                                </h2>
                                <p class="text-xs text-muted-foreground">
                                    Présentation du développeur
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 rounded-2xl border bg-muted/30 p-4 sm:p-5">
                            <p v-if="developer.bio"
                                class="whitespace-pre-line wrap-break-word text-sm leading-7 text-foreground/80 sm:text-base">
                                {{ developer.bio }}
                            </p>

                            <p v-else class="text-sm leading-6 text-muted-foreground">
                                Ce développeur n'a pas encore rédigé sa biographie.
                            </p>
                        </div>
                    </section>

                    <!-- Compétences techniques -->
                    <section class="mt-9 border-t pt-7">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex size-10 items-center justify-center rounded-xl bg-primary/10 text-primary">
                                    <Code2 class="size-5" />
                                </div>

                                <div>
                                    <h2 class="text-lg font-bold text-foreground">
                                        Compétences techniques
                                    </h2>
                                    <p class="text-xs text-muted-foreground">
                                        Technologies et niveaux d'expérience
                                    </p>
                                </div>
                            </div>

                            <span
                                class="rounded-xl border bg-muted/50 px-3 py-2 text-sm font-semibold text-muted-foreground">
                                {{ technologies.length }}
                                {{ technologies.length > 1 ? 'compétences' : 'compétence' }}
                            </span>
                        </div>

                        <!-- Technologies -->
                        <div v-if="technologies.length > 0" class="mt-5 grid gap-4 sm:grid-cols-2">
                            <article v-for="technology in technologies" :key="technology.id"
                                class="group rounded-2xl border bg-card p-4 transition duration-200 hover:-translate-y-0.5 hover:border-primary/30 hover:shadow-md hover:shadow-primary/5 sm:p-5">
                                <div class="flex items-start gap-3">
                                    <div
                                        class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary transition group-hover:bg-primary/15">
                                        <Code2 class="size-5" />
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-start justify-between gap-2">
                                            <h3 class="wrap-break-word font-bold text-foreground">
                                                {{ technology.name }}
                                            </h3>

                                            <span v-if="technology.is_primary"
                                                class="inline-flex shrink-0 items-center gap-1 rounded-full border border-primary/20 bg-primary/10 px-2 py-1 text-xs font-semibold text-primary">
                                                <Star class="size-3 fill-current" />
                                                Principale
                                            </span>
                                        </div>

                                        <p v-if="technology.category" class="mt-1 text-xs text-muted-foreground">
                                            {{ technology.category }}
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-5 flex flex-wrap items-center justify-between gap-2 border-t pt-4">
                                    <span
                                        class="inline-flex items-center rounded-lg border px-2.5 py-1.5 text-xs font-semibold"
                                        :class="skillLevelClass(technology.skill_level)">
                                        {{ formatSkillLevel(technology.skill_level) }}
                                    </span>

                                    <span class="inline-flex items-center gap-1.5 text-xs text-muted-foreground">
                                        <BriefcaseBusiness class="size-3.5" />
                                        {{ formatExperience(technology.years_experience) }}
                                    </span>
                                </div>
                            </article>
                        </div>

                        <!-- Aucune compétence -->
                        <div v-else class="mt-5 rounded-2xl border border-dashed px-5 py-10 text-center">
                            <div
                                class="mx-auto flex size-12 items-center justify-center rounded-xl bg-muted text-muted-foreground">
                                <Code2 class="size-6" />
                            </div>

                            <h3 class="mt-4 font-semibold text-foreground">
                                Aucune compétence renseignée
                            </h3>

                            <p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-muted-foreground">
                                Les technologies et spécialités de ce développeur apparaîtront ici lorsqu'elles seront
                                renseignées.
                            </p>
                        </div>
                    </section>

                    <!-- Liens professionnels -->
                    <section v-if="professionalLinks.length > 0" class="mt-9 border-t pt-7">
                        <div class="flex items-center gap-3">
                            <div class="flex size-10 items-center justify-center rounded-xl bg-primary/10 text-primary">
                                <Globe class="size-5" />
                            </div>

                            <div>
                                <h2 class="text-lg font-bold text-foreground">
                                    Liens professionnels
                                </h2>
                                <p class="text-xs text-muted-foreground">
                                    Retrouvez ce développeur sur le web
                                </p>
                            </div>
                        </div>

                        <div class="mt-5 grid gap-3 sm:grid-cols-2">
                            <a v-for="item in professionalLinks" :key="item.label" :href="item.url!" target="_blank"
                                rel="noopener noreferrer"
                                class="group flex min-w-0 items-center gap-3 rounded-2xl border bg-card p-4 transition hover:border-primary/40 hover:bg-primary/5">
                                <div
                                    class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-muted text-muted-foreground transition group-hover:bg-primary/10 group-hover:text-primary">
                                    <component :is="item.icon" class="size-5" />
                                </div>

                                <div class="min-w-0 flex-1">
                                    <p class="font-semibold text-foreground">
                                        {{ item.label }}
                                    </p>
                                    <p class="mt-1 text-xs text-muted-foreground">
                                        {{ item.description }}
                                    </p>
                                </div>

                                <ExternalLink
                                    class="size-4 shrink-0 text-muted-foreground transition group-hover:text-primary" />
                            </a>
                        </div>

                        <button v-if="auth?.user && auth.user.id !== developer.id" type="button"
                            :disabled="messageForm.processing"
                            class="inline-flex items-center justify-center gap-2 mt-9 rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-primary-foreground transition-opacity hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-50"
                            @click="startConversation">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5Z" />
                            </svg>

                            {{
                                messageForm.processing
                                    ? 'Ouverture de la conversation...'
                                    : 'Envoyer un message'
                            }}
                        </button>
                    </section>

                    <!-- Pied du profil -->
                    <footer class="mt-9 border-t pt-6">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex size-10 items-center justify-center rounded-xl bg-primary/10 text-primary">
                                    <Users class="size-5" />
                                </div>

                                <div>
                                    <p class="text-sm font-semibold text-foreground">
                                        Membre de la communauté DevColab
                                    </p>
                                    <p class="mt-1 text-xs text-muted-foreground">
                                        Pour les développeurs, par les développeurs.
                                    </p>
                                </div>
                            </div>

                            <Link href="/developers"
                                class="inline-flex items-center justify-center gap-2 rounded-xl border bg-background px-4 py-2.5 text-sm font-semibold transition hover:border-primary/40 hover:bg-muted">
                                <ArrowLeft class="size-4" />
                                Retour aux développeurs
                            </Link>
                        </div>
                    </footer>
                </div>
            </section>

            <!-- Bandeau final -->
            <aside class="mt-6 flex items-start gap-3 rounded-2xl border bg-primary/5 p-5">
                <div
                    class="flex size-10 shrink-0 items-center justify-center rounded-xl border bg-card text-primary shadow-sm">
                    <GraduationCap class="size-5" />
                </div>

                <div>
                    <h2 class="text-sm font-bold text-foreground">
                        Le développement se construit ensemble.
                    </h2>

                    <p class="mt-1 text-sm leading-6 text-muted-foreground">
                        Explore les compétences, découvre de nouvelles approches
                        techniques et trouve l'inspiration auprès de la communauté.
                    </p>
                </div>
            </aside>
        </div>
    </main>
</template>
