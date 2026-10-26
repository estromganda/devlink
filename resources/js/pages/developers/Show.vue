<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
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

const skillLevelStyles: Record<string, string> = {
    beginner:
        'border-sky-200 bg-sky-50 text-sky-700 dark:border-sky-900 dark:bg-sky-950 dark:text-sky-300',
    intermediate:
        'border-teal-200 bg-teal-50 text-teal-700 dark:border-teal-900 dark:bg-teal-950 dark:text-teal-300',
    advanced:
        'border-violet-200 bg-violet-50 text-violet-700 dark:border-violet-900 dark:bg-violet-950 dark:text-violet-300',
    expert:
        'border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-900 dark:bg-amber-950 dark:text-amber-300',
};

function formatSkillLevel(level: string): string {
    return skillLevelLabels[level] ?? level;
}

function skillLevelClass(level: string): string {
    return skillLevelStyles[level] ??
        'border-gray-200 bg-gray-50 text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300';
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

    <Head :title="`${developer.name} — Développeur DevLink`">
        <meta name="description" :content="developer.bio || `Découvrez le profil de ${developer.name} sur DevLink.`" />
    </Head>

    <main class="min-h-screen bg-gray-50/70 py-6 dark:bg-gray-950 sm:py-10">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">

            <!-- Navigation -->
            <div class="mb-6 flex items-center justify-between gap-3">
                <Link href="/developers"
                    class="group inline-flex items-center gap-2 rounded-lg text-sm font-medium text-gray-500 transition hover:text-teal-700 dark:text-gray-400 dark:hover:text-teal-400">
                    <ArrowLeft class="size-4 transition-transform group-hover:-translate-x-1" />
                    Tous les développeurs
                </Link>

                <Link v-if="!auth.user" href="/login"
                    class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:border-teal-300 hover:text-teal-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-200 dark:hover:text-teal-400">
                    Se connecter
                    <ArrowUpRight class="size-4" />
                </Link>
            </div>

            <!-- Carte principale du profil -->
            <section
                class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">

                <!-- Bannière -->
                <div
                    class="relative h-36 overflow-hidden bg-linear-to-br from-teal-600 via-cyan-600 to-blue-700 sm:h-48">
                    <div class="absolute -left-10 -top-20 size-64 rounded-full border border-white/10" />
                    <div class="absolute -bottom-36 left-1/3 size-96 rounded-full border border-white/10" />
                    <div class="absolute -right-10 top-5 size-48 rounded-full border border-white/10" />

                    <div
                        class="absolute right-5 top-5 inline-flex items-center gap-2 rounded-full border border-white/20 bg-black/10 px-3 py-1.5 text-xs font-semibold text-white backdrop-blur-sm sm:right-8 sm:top-8">
                        <Sparkles class="size-3.5" />
                        Membre de DevLink
                    </div>

                    <div class="absolute bottom-5 left-5 flex items-center gap-2 text-white/80 sm:bottom-8 sm:left-8">
                        <Terminal class="size-5" />
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
                                class="-mt-12 flex size-24 items-center justify-center rounded-3xl border-4 border-white bg-linear-to-br from-teal-100 to-cyan-100 text-3xl font-bold text-teal-800 shadow-sm dark:border-gray-900 dark:from-teal-950 dark:to-cyan-950 dark:text-teal-300 sm:-mt-14 sm:size-28">
                                <span aria-label="Initiale du développeur">
                                    {{ developerInitial(developer.name) }}
                                </span>
                            </div>

                            <div class="mt-5">
                                <div class="flex flex-wrap items-center gap-3">
                                    <h1
                                        class="wrap-break-word text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                                        {{ developer.name }}
                                    </h1>

                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full border border-teal-200 bg-teal-50 px-2.5 py-1 text-xs font-semibold text-teal-700 dark:border-teal-900 dark:bg-teal-950 dark:text-teal-300">
                                        <span class="size-1.5 rounded-full bg-teal-500" />
                                        Développeur
                                    </span>
                                </div>

                                <p v-if="developer.headline"
                                    class="mt-3 max-w-2xl text-base leading-7 text-gray-600 dark:text-gray-300 sm:text-lg">
                                    {{ developer.headline }}
                                </p>

                                <div
                                    class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-3 text-sm text-gray-500 dark:text-gray-400">
                                    <div v-if="developer.location" class="inline-flex items-center gap-2">
                                        <MapPin class="size-4 text-teal-600 dark:text-teal-400" />
                                        {{ developer.location }}
                                    </div>

                                    <div class="inline-flex items-center gap-2">
                                        <Code2 class="size-4 text-teal-600 dark:text-teal-400" />
                                        {{ technologies.length }}
                                        {{ technologies.length > 1 ? 'technologies' : 'technologie' }}
                                    </div>

                                    <div v-if="primaryTechnologies.length > 0" class="inline-flex items-center gap-2">
                                        <Star class="size-4 text-amber-500" />
                                        {{ primaryTechnologies.length }}
                                        {{ primaryTechnologies.length > 1 ?
                                            'spécialités principales' : 'spécialité principale' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Liens rapides -->
                        <div v-if="professionalLinks.length > 0" class="flex shrink-0 flex-wrap gap-2 sm:pb-1">
                            <a v-for="item in professionalLinks" :key="item.label" :href="item.url!" target="_blank"
                                rel="noopener noreferrer" :aria-label="`${item.label} — nouvel onglet`"
                                :title="item.label"
                                class="inline-flex size-11 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-600 transition hover:border-teal-300 hover:bg-teal-50 hover:text-teal-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:border-teal-800 dark:hover:bg-teal-950 dark:hover:text-teal-300">
                                <component :is="item.icon" class="size-5" />
                            </a>
                        </div>
                    </div>

                    <!-- À propos -->
                    <section class="mt-9 border-t border-gray-100 pt-7 dark:border-gray-800">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex size-10 items-center justify-center rounded-xl bg-teal-50 text-teal-700 dark:bg-teal-950 dark:text-teal-300">
                                <UserRound class="size-5" />
                            </div>

                            <div>
                                <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                                    À propos
                                </h2>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Présentation du développeur
                                </p>
                            </div>
                        </div>

                        <div
                            class="mt-4 rounded-2xl border border-gray-100 bg-gray-50/80 p-4 dark:border-gray-800 dark:bg-gray-800/40 sm:p-5">
                            <p v-if="developer.bio"
                                class="whitespace-pre-line wrap-break-word text-sm leading-7 text-gray-600 dark:text-gray-300 sm:text-base">
                                {{ developer.bio }}
                            </p>

                            <p v-else class="text-sm leading-6 text-gray-500 dark:text-gray-400">
                                Ce développeur n'a pas encore rédigé sa biographie.
                            </p>
                        </div>
                    </section>

                    <!-- Compétences techniques -->
                    <section class="mt-9 border-t border-gray-100 pt-7 dark:border-gray-800">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex size-10 items-center justify-center rounded-xl bg-teal-50 text-teal-700 dark:bg-teal-950 dark:text-teal-300">
                                    <Code2 class="size-5" />
                                </div>

                                <div>
                                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                                        Compétences techniques
                                    </h2>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Technologies et niveaux d'expérience
                                    </p>
                                </div>
                            </div>

                            <span
                                class="rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm font-semibold text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                {{ technologies.length }}
                                {{ technologies.length > 1 ? 'compétences' : 'compétence' }}
                            </span>
                        </div>

                        <!-- Technologies -->
                        <div v-if="technologies.length > 0" class="mt-5 grid gap-4 sm:grid-cols-2">
                            <article v-for="technology in technologies" :key="technology.id"
                                class="group rounded-2xl border border-gray-200 bg-white p-4 transition duration-200 hover:border-teal-200 hover:shadow-sm dark:border-gray-800 dark:bg-gray-900 dark:hover:border-teal-900 sm:p-5">
                                <div class="flex items-start gap-3">
                                    <div
                                        class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-gray-50 text-teal-700 transition group-hover:bg-teal-50 dark:bg-gray-800 dark:text-teal-400 dark:group-hover:bg-teal-950">
                                        <Code2 class="size-5" />
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-start justify-between gap-2">
                                            <h3 class="wrap-break-word font-bold text-gray-900 dark:text-white">
                                                {{ technology.name }}
                                            </h3>

                                            <span v-if="technology.is_primary"
                                                class="inline-flex shrink-0 items-center gap-1 rounded-full border border-amber-200 bg-amber-50 px-2 py-1 text-xs font-semibold text-amber-700 dark:border-amber-900 dark:bg-amber-950 dark:text-amber-300">
                                                <Star class="size-3 fill-current" />
                                                Principale
                                            </span>
                                        </div>

                                        <p v-if="technology.category"
                                            class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            {{ technology.category }}
                                        </p>
                                    </div>
                                </div>

                                <div
                                    class="mt-5 flex flex-wrap items-center justify-between gap-2 border-t border-gray-100 pt-4 dark:border-gray-800">
                                    <span
                                        class="inline-flex items-center rounded-lg border px-2.5 py-1.5 text-xs font-semibold"
                                        :class="skillLevelClass(technology.skill_level)">
                                        {{ formatSkillLevel(technology.skill_level) }}
                                    </span>

                                    <span
                                        class="inline-flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                                        <BriefcaseBusiness class="size-3.5" />
                                        {{ formatExperience(technology.years_experience) }}
                                    </span>
                                </div>
                            </article>
                        </div>

                        <!-- Aucune compétence -->
                        <div v-else
                            class="mt-5 rounded-2xl border border-dashed border-gray-300 px-5 py-10 text-center dark:border-gray-700">
                            <div
                                class="mx-auto flex size-12 items-center justify-center rounded-xl bg-gray-100 text-gray-400 dark:bg-gray-800">
                                <Code2 class="size-6" />
                            </div>

                            <h3 class="mt-4 font-semibold text-gray-900 dark:text-white">
                                Aucune compétence renseignée
                            </h3>

                            <p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                                Les technologies et spécialités de ce développeur apparaîtront ici lorsqu'elles seront
                                renseignées.
                            </p>
                        </div>
                    </section>

                    <!-- Liens professionnels -->
                    <section v-if="professionalLinks.length > 0"
                        class="mt-9 border-t border-gray-100 pt-7 dark:border-gray-800">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex size-10 items-center justify-center rounded-xl bg-teal-50 text-teal-700 dark:bg-teal-950 dark:text-teal-300">
                                <Globe class="size-5" />
                            </div>

                            <div>
                                <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                                    Liens professionnels
                                </h2>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Retrouvez ce développeur sur le web
                                </p>
                            </div>
                        </div>

                        <div class="mt-5 grid gap-3 sm:grid-cols-2">
                            <a v-for="item in professionalLinks" :key="item.label" :href="item.url!" target="_blank"
                                rel="noopener noreferrer"
                                class="group flex min-w-0 items-center gap-3 rounded-2xl border border-gray-200 bg-white p-4 transition hover:border-teal-300 hover:bg-teal-50/50 dark:border-gray-800 dark:bg-gray-900 dark:hover:border-teal-900 dark:hover:bg-teal-950/30">
                                <div
                                    class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-gray-50 text-gray-600 transition group-hover:bg-teal-100 group-hover:text-teal-700 dark:bg-gray-800 dark:text-gray-300 dark:group-hover:bg-teal-950 dark:group-hover:text-teal-300">
                                    <component :is="item.icon" class="size-5" />
                                </div>

                                <div class="min-w-0 flex-1">
                                    <p class="font-semibold text-gray-900 dark:text-white">
                                        {{ item.label }}
                                    </p>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        {{ item.description }}
                                    </p>
                                </div>

                                <ExternalLink
                                    class="size-4 shrink-0 text-gray-400 transition group-hover:text-teal-600 dark:group-hover:text-teal-400" />
                            </a>
                        </div>
                    </section>

                    <!-- Pied du profil -->
                    <footer class="mt-9 border-t border-gray-100 pt-6 dark:border-gray-800">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex size-10 items-center justify-center rounded-xl bg-teal-50 text-teal-700 dark:bg-teal-950 dark:text-teal-300">
                                    <Users class="size-5" />
                                </div>

                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                        Membre de la communauté DevLink
                                    </p>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Pour les développeurs, par les développeurs.
                                    </p>
                                </div>
                            </div>

                            <Link href="/developers"
                                class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:border-teal-300 hover:text-teal-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:text-teal-400">
                                <ArrowLeft class="size-4" />
                                Retour aux développeurs
                            </Link>
                        </div>
                    </footer>
                </div>
            </section>

            <!-- Bandeau final -->
            <aside
                class="mt-6 flex items-start gap-3 rounded-2xl border border-teal-100 bg-teal-50/70 p-5 dark:border-teal-900/60 dark:bg-teal-950/30">
                <div
                    class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-white text-teal-700 shadow-sm dark:bg-gray-900 dark:text-teal-300">
                    <GraduationCap class="size-5" />
                </div>

                <div>
                    <h2 class="text-sm font-bold text-gray-900 dark:text-white">
                        Le développement se construit ensemble.
                    </h2>

                    <p class="mt-1 text-sm leading-6 text-gray-600 dark:text-gray-400">
                        Explore les compétences, découvre de nouvelles approches
                        techniques et trouve l'inspiration auprès de la communauté.
                    </p>
                </div>
            </aside>
        </div>
    </main>
</template>