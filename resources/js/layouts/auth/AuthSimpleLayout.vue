<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Code2, Sparkles } from '@lucide/vue';

import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="relative isolate flex min-h-svh w-full flex-col items-center justify-center overflow-hidden bg-background px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
        <!-- Décoration d'arrière-plan -->
        <div class="pointer-events-none absolute -left-32 -top-24 -z-10 size-72 rounded-full bg-indigo-500/10 blur-3xl sm:size-96"
            aria-hidden="true" />

        <div class="pointer-events-none absolute -bottom-32 -right-24 -z-10 size-72 rounded-full bg-violet-500/10 blur-3xl sm:size-96"
            aria-hidden="true" />

        <!-- Conteneur principal -->
        <div class="w-full max-w-sm sm:max-w-md lg:max-w-2xl xl:max-w-3xl 2xl:max-w-4xl"> <!-- Marque DevLink -->
            <div class="mb-7 flex justify-center sm:mb-9">
                <Link :href="home()" aria-label="Retour à l'accueil de DevLink"
                    class="group inline-flex items-center gap-3 rounded-2xl p-2 transition-opacity hover:opacity-80 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                    <div
                        class="flex size-12 items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-lg shadow-indigo-600/20 transition-transform group-hover:scale-105 sm:size-14">
                        <AppLogoIcon class="size-7 fill-current sm:size-8" />
                    </div>

                    <div class="text-left">
                        <p class="text-xl font-bold tracking-tight sm:text-2xl">
                            DevLink
                        </p>

                        <p class="text-xs text-muted-foreground sm:text-sm">
                            La communauté des développeurs
                        </p>
                    </div>
                </Link>
            </div>

            <!-- Carte d'authentification -->
            <div
                class="overflow-hidden rounded-2xl border border-border/70 bg-card shadow-xl shadow-black/5 sm:rounded-3xl">
                <div class="px-5 py-7 sm:px-8 sm:py-9">
                    <!-- En-tête -->
                    <div class="mb-7 text-center sm:mb-8">
                        <div
                            class="mx-auto mb-5 flex size-12 items-center justify-center rounded-2xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-300 sm:size-14">
                            <Sparkles class="size-6 sm:size-7" aria-hidden="true" />
                        </div>

                        <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">
                            {{ title }}
                        </h1>

                        <p v-if="description"
                            class="mx-auto mt-3 max-w-sm text-sm leading-6 text-muted-foreground sm:text-base sm:leading-7">
                            {{ description }}
                        </p>
                    </div>

                    <!-- Formulaire ou contenu de la page -->
                    <div class="min-w-0">
                        <slot />
                    </div>
                </div>
            </div>

            <!-- Pied de page -->
            <footer class="mt-6 px-2 text-center sm:mt-8">
                <Link :href="home()"
                    class="inline-flex min-h-10 items-center justify-center gap-2 rounded-lg px-3 text-sm text-muted-foreground transition-colors hover:text-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                    <Code2 class="size-4 shrink-0" />
                    <span>Retour à l'accueil de DevLink</span>
                </Link>

                <p class="mt-2 text-xs leading-5 text-muted-foreground">
                    Un espace pour apprendre, partager et construire ensemble.
                </p>
            </footer>
        </div>
    </div>
</template>
