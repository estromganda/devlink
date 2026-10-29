<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppHeader from '@/components/AppHeader.vue';
import AppShell from '@/components/AppShell.vue';
import { Toaster } from '@/components/ui/sonner';
import type { BreadcrumbItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { onMounted, onUpdated, ref } from 'vue';

type Props = {
    breadcrumbs?: BreadcrumbItem[];
};

const auth = usePage().props.auth;
withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const page = usePage();
const flash = page.props.flash as {
    success?: string | null;
};
let pageError = page.props.error;
let showFlash = ref(true);

setTimeout(() => {
    pageError = null;
    showFlash.value = false;
}, 2000)

</script>

<template>
    <AppShell variant="header">
        <AppHeader :breadcrumbs="breadcrumbs" />
        <AppContent variant="header">
            <div v-if="flash?.success && showFlash" role="status" class="mx-auto mt-4 max-w-7xl px-4 sm:px-6 lg:px-8">
                <div
                    class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                    {{ flash.success }}
                </div>
            </div>
            <div v-if="showFlash && pageError" role="status" class="mx-auto mt-4 max-w-7xl px-4 sm:px-6 lg:px-8">
                <div
                    class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-red-800">
                    {{ pageError }}
                </div>
            </div>
            <slot />
        </AppContent>
        <!-- Pied de page et navigation secondaire -->
        <footer class="mt-16 border-t bg-muted/20">
            <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
                <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
                    <!-- Présentation -->
                    <div>
                        <Link href="/" class="inline-flex items-center gap-2 text-lg font-extrabold tracking-tight">
                            <span
                                class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary text-xs font-black text-primary-foreground">
                                D
                            </span>

                            <span>Dev<span class="text-primary">Link</span></span>
                        </Link>

                        <p class="mt-4 max-w-xs text-sm leading-6 text-muted-foreground">
                            La plateforme des développeurs pour partager des
                            connaissances, présenter des projets et construire
                            ensemble.
                        </p>
                    </div>

                    <!-- Communauté -->
                    <div>
                        <h2 class="text-sm font-bold">Communauté</h2>

                        <ul class="mt-4 space-y-3 text-sm text-muted-foreground">
                            <li>
                                <Link href="/developers" class="transition hover:text-primary">
                                    Trouver des développeurs
                                </Link>
                            </li>

                            <li>
                                <Link href="/communities" class="transition hover:text-primary">
                                    Explorer les communautés
                                </Link>
                            </li>

                            <li>
                                <Link href="/posts" class="transition hover:text-primary">
                                    Fil d'actualité
                                </Link>
                            </li>

                            <li>
                                <Link href="/communities/create" class="transition hover:text-primary">
                                    Créer une communauté
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <!-- Projets et collaboration -->
                    <div>
                        <h2 class="text-sm font-bold">Créer ensemble</h2>

                        <ul class="mt-4 space-y-3 text-sm text-muted-foreground">
                            <li>
                                <Link href="/projects" class="transition hover:text-primary">
                                    Découvrir les projets
                                </Link>
                            </li>

                            <li>
                                <Link href="/projects/create" class="transition hover:text-primary">
                                    Publier un projet
                                </Link>
                            </li>

                            <li>
                                <Link href="/my-collaboration-requests" class="transition hover:text-primary">
                                    Collaborations
                                </Link>
                            </li>

                            <li>
                                <Link href="/messages" class="transition hover:text-primary">
                                    Messagerie
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <!-- Compte -->
                    <div>
                        <h2 class="text-sm font-bold">Mon compte</h2>

                        <ul class="mt-4 space-y-3 text-sm text-muted-foreground">
                            <li v-if="auth.user">
                                <Link href="/settings/developer-profile" class="transition hover:text-primary">
                                    Mon profil développeur
                                </Link>
                            </li>

                            <li v-if="auth.user">
                                <Link href="/settings/profile" class="transition hover:text-primary">
                                    Paramètres du compte
                                </Link>
                            </li>

                            <li>
                                <Link href="/notifications" class="transition hover:text-primary">
                                    Notifications
                                </Link>
                            </li>

                            <li v-if="!auth.user">
                                <Link href="/login" class="transition hover:text-primary">
                                    Se connecter
                                </Link>
                            </li>

                            <li v-if="!auth.user">
                                <Link href="/register" class="transition hover:text-primary">
                                    Créer un compte
                                </Link>
                            </li>
                            <li v-if="auth.user">
                                <Link href="/my-projects" class="nav-link">
                                    Mes projets
                                </Link>
                            </li>
                            <li v-if="auth.user">
                                <Link href="/my-collaboration-requests" class="nav-link">
                                    Mes candidatures
                                </Link>
                            </li>

                        </ul>
                    </div>
                </div>

                <!-- Bas de page -->
                <div
                    class="mt-10 flex flex-col gap-3 border-t pt-6 text-center text-xs text-muted-foreground sm:flex-row sm:items-center sm:justify-between sm:text-left">
                    <p>
                        © {{ new Date().getFullYear() }} DevLink.
                        La communauté des développeurs.
                    </p>

                    <p>
                        Conçu par et pour les développeurs.
                    </p>
                </div>
            </div>
        </footer>
        <Toaster />
    </AppShell>
</template>
