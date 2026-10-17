<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

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

function formatExperience(years: number | null): string {
    if (years === null) {
        return 'Expérience non précisée';
    }

    if (years === 0) {
        return 'Moins d’un an';
    }

    return `${years} an${years > 1 ? 's' : ''} d’expérience`;
}
</script>

<template>

    <Head :title="`${developer.name} — Développeur DevLink`">
        <meta name="description" :content="developer.bio ||
            `Découvrez le profil de ${developer.name} sur DevLink.`
            " />
    </Head>

    <main class="min-h-screen bg-background px-4 py-10 text-foreground sm:px-6">
        <div class="mx-auto max-w-3xl">
            <!-- En-tête -->
            <header class="mb-8 flex items-center justify-between">
                <a href="/" class="text-xl font-bold tracking-tight">
                    DevLink
                </a>

                <a href="/login" class="rounded-md border px-4 py-2 text-sm transition hover:bg-muted">
                    Se connecter
                </a>
            </header>

            <!-- Profil -->
            <section class="overflow-hidden rounded-2xl border bg-card shadow-sm">
                <div class="h-32 bg-muted sm:h-40"></div>

                <div class="px-6 pb-8 sm:px-8">
                    <!-- Avatar -->
                    <div class="-mt-10 flex h-20 w-20 items-center justify-center rounded-2xl border-4 border-card bg-background text-2xl font-bold shadow-sm"
                        aria-label="Initiale du développeur">
                        {{ developer.name.charAt(0).toUpperCase() }}
                    </div>

                    <!-- Informations générales -->
                    <div class="mt-5">
                        <h1 class="text-3xl font-bold tracking-tight">
                            {{ developer.name }}
                        </h1>

                        <p v-if="developer.headline" class="mt-2 text-lg text-muted-foreground">
                            {{ developer.headline }}
                        </p>

                        <p v-if="developer.location" class="mt-3 text-sm text-muted-foreground">
                            📍 {{ developer.location }}
                        </p>
                    </div>

                    <!-- Biographie -->
                    <section class="mt-8 border-t pt-6">
                        <h2 class="text-lg font-semibold">
                            À propos
                        </h2>

                        <p v-if="developer.bio" class="mt-3 whitespace-pre-line leading-7 text-muted-foreground">
                            {{ developer.bio }}
                        </p>

                        <p v-else class="mt-3 text-sm text-muted-foreground">
                            Ce développeur n'a pas encore rédigé sa biographie.
                        </p>
                    </section>

                    <!-- Compétences techniques -->
                    <section class="mt-8 border-t pt-6">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <h2 class="text-lg font-semibold">
                                Compétences techniques
                            </h2>

                            <span v-if="technologies.length > 0"
                                class="rounded-full bg-muted px-3 py-1 text-sm text-muted-foreground">
                                {{ technologies.length }}
                                {{ technologies.length > 1 ? 'technologies' : 'technologie' }}
                            </span>
                        </div>

                        <!-- Technologies disponibles -->
                        <div v-if="technologies.length > 0" class="mt-5 grid gap-4 sm:grid-cols-2">
                            <article v-for="technology in technologies" :key="technology.id"
                                class="rounded-xl border p-4 transition hover:bg-muted/40">
                                <div class="flex flex-wrap items-start justify-between gap-2">
                                    <h3 class="font-semibold">
                                        {{ technology.name }}
                                    </h3>

                                    <span v-if="technology.is_primary"
                                        class="rounded-full border px-2 py-1 text-xs font-medium">
                                        Principale
                                    </span>
                                </div>

                                <p v-if="technology.category" class="mt-1 text-xs text-muted-foreground">
                                    {{ technology.category }}
                                </p>

                                <div class="mt-4">
                                    <span class="inline-flex rounded-full bg-muted px-3 py-1 text-sm">
                                        {{ formatSkillLevel(technology.skill_level) }}
                                    </span>

                                    <p class="mt-3 text-sm text-muted-foreground">
                                        {{ formatExperience(technology.years_experience) }}
                                    </p>
                                </div>
                            </article>
                        </div>

                        <!-- Aucune technologie -->
                        <p v-else class="mt-4 rounded-xl border border-dashed p-5 text-sm text-muted-foreground">
                            Ce développeur n'a pas encore renseigné ses compétences techniques.
                        </p>
                    </section>

                    <!-- Liens professionnels -->
                    <section v-if="
                        developer.website ||
                        developer.github_url ||
                        developer.linkedin_url
                    " class="mt-8 border-t pt-6">
                        <h2 class="text-lg font-semibold">
                            Liens professionnels
                        </h2>

                        <ul class="mt-4 flex flex-wrap gap-3">
                            <li v-if="developer.website">
                                <a :href="developer.website" target="_blank" rel="noopener noreferrer"
                                    class="inline-flex rounded-lg border px-4 py-2 text-sm transition hover:bg-muted">
                                    Site personnel ↗
                                </a>
                            </li>

                            <li v-if="developer.github_url">
                                <a :href="developer.github_url" target="_blank" rel="noopener noreferrer"
                                    class="inline-flex rounded-lg border px-4 py-2 text-sm transition hover:bg-muted">
                                    GitHub ↗
                                </a>
                            </li>

                            <li v-if="developer.linkedin_url">
                                <a :href="developer.linkedin_url" target="_blank" rel="noopener noreferrer"
                                    class="inline-flex rounded-lg border px-4 py-2 text-sm transition hover:bg-muted">
                                    LinkedIn ↗
                                </a>
                            </li>
                        </ul>
                    </section>

                    <!-- Pied du profil -->
                    <footer class="mt-8 border-t pt-5 text-sm text-muted-foreground">
                        Membre de la communauté DevLink
                    </footer>
                </div>
            </section>
        </div>
    </main>
</template>
