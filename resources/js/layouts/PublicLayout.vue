<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

type AuthUser = {
    id: number;
    name: string;
    email?: string;
};

const page = usePage();

const auth = page.props.auth

const flash = page.props.flash as {
    success?: string | null;
};

function getInitial(name?: string | null): string {
    return name?.trim().charAt(0).toUpperCase() || '?';
}
</script>

<template>
    <div class="flex min-h-screen flex-col bg-background text-foreground">
        <!-- En-tête et navigation principale -->
        <header class="sticky top-0 z-50 border-b bg-background/95 backdrop-blur">
            <nav class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8"
                aria-label="Navigation principale">
                <!-- Logo -->
                <Link href="/" class="flex shrink-0 items-center gap-2 text-xl font-extrabold tracking-tight">
                    <span
                        class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary text-sm font-black text-primary-foreground">
                        D
                    </span>

                    <span>Dev<span class="text-primary">Link</span></span>
                </Link>

                <!-- Navigation des modules -->
                <div
                    class="order-3 flex w-full flex-wrap items-center gap-x-5 gap-y-3 border-t pt-3 text-sm font-medium sm:order-2 sm:w-auto sm:border-0 sm:pt-0">
                    <Link href="/" class="transition hover:text-primary" :class="{ 'text-primary': page.url === '/' }">
                        Accueil
                    </Link>

                    <Link href="/developers" class="transition hover:text-primary"
                        :class="{ 'text-primary': page.url.startsWith('/developers') }">
                        Développeurs
                    </Link>

                    <Link href="/communities" class="transition hover:text-primary"
                        :class="{ 'text-primary': page.url.startsWith('/communities') }">
                        Communautés
                    </Link>

                    <Link href="/posts" class="transition hover:text-primary"
                        :class="{ 'text-primary': page.url.startsWith('/posts') }">
                        Actualités
                    </Link>

                    <Link href="/projects" class="transition hover:text-primary"
                        :class="{ 'text-primary': page.url.startsWith('/projects') }">
                        Projets
                    </Link>

                    <Link href="/messages" class="transition hover:text-primary"
                        :class="{ 'text-primary': page.url.startsWith('/messages') }">
                        Messages
                    </Link>
                    <Link href="/questions" class="transition hover:text-primary"
                        :class="{ 'text-primary': page.url.startsWith('/questions') }">
                        Q&A
                    </Link>
                </div>

                <!-- Espace utilisateur -->
                <div class="order-2 flex shrink-0 items-center gap-3 sm:order-3">
                    <template v-if="auth.user">
                        <Link href="/developer-profile"
                            class="flex items-center gap-2 rounded-full transition hover:opacity-80"
                            :title="`Profil de ${auth.user.name}`">
                            <!-- Avatar ou initiale -->
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-full bg-primary text-sm font-bold text-primary-foreground ring-2 ring-border">
                                <img v-if="auth.profile.avatar_path" :src="`/storage/${auth.profile.avatar_path}`"
                                    :alt="`Avatar de ${auth.user.name}`" class="h-full w-full object-cover" />

                                <span v-else>
                                    {{ getInitial(auth.user.name) }}
                                </span>
                            </div>

                            <span class="hidden max-w-32 truncate text-sm font-semibold sm:block"
                                :title="auth.user.name">
                                {{ auth.user.name }}
                            </span>
                        </Link>

                        <Link href="/settings/profile"
                            class="hidden rounded-lg border px-3 py-2 text-sm font-medium transition hover:bg-muted md:inline-flex">
                            Paramètres
                        </Link>

                        <Link href="/logout" method="post" as="button"
                            class="hidden rounded-lg px-3 py-2 text-sm font-medium text-muted-foreground transition hover:bg-muted hover:text-foreground lg:inline-flex">
                            Déconnexion
                        </Link>
                    </template>

                    <template v-else>
                        <Link href="/login"
                            class="rounded-lg px-3 py-2 text-sm font-semibold transition hover:bg-muted">
                            Connexion
                        </Link>

                        <Link href="/register"
                            class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-primary-foreground transition hover:opacity-90">
                            S'inscrire
                        </Link>
                    </template>
                </div>
            </nav>
        </header>

        <div v-if="flash?.success" role="status" class="mx-auto mt-4 max-w-7xl px-4 sm:px-6 lg:px-8">
            <div
                class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ flash.success }}
            </div>
        </div>

        <!-- Contenu de la page -->
        <main class="flex-1">
            <slot />
        </main>

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
                                <Link href="/collaborations" class="transition hover:text-primary">
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
                                <Link href="/developer-profile" class="transition hover:text-primary">
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
    </div>
</template>