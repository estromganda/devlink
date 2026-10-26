<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    ArrowLeft,
    ArrowRight,
    BriefcaseBusiness,
    CalendarDays,
    CheckCircle2,
    Clock3,
    Code2,
    FolderOpen,
    MessageSquare,
    ShieldCheck,
    UserRound,
    Users,
    XCircle,
} from '@lucide/vue';

interface Project {
    id: number;
    name: string;
    slug: string;
}

interface DeveloperProfile {
    avatar_path: string | null;
    headline?: string | null;
}

interface Developer {
    id: number;
    name: string;
    profile: DeveloperProfile | null;
}

interface Offer {
    id: number;
    user_id: number;
    message: string;
    proposed_role: string | null;
    status: string;
    created_at: string;
    user: Developer;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

const props = defineProps<{
    project: Project;
    offers: {
        data: Offer[];
        links: PaginationLink[];
        current_page: number;
        last_page: number;
        total: number;
    };
}>();

const statusLabels: Record<string, string> = {
    pending: 'En attente',
    accepted: 'Acceptée',
    rejected: 'Refusée',
    withdrawn: 'Retirée',
};

const statusClasses: Record<string, string> = {
    pending:
        'border-amber-200 bg-amber-50 text-amber-800 dark:border-amber-400/20 dark:bg-amber-400/10 dark:text-amber-300',
    accepted:
        'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-400/20 dark:bg-emerald-400/10 dark:text-emerald-300',
    rejected:
        'border-red-200 bg-red-50 text-red-800 dark:border-red-400/20 dark:bg-red-400/10 dark:text-red-300',
    withdrawn:
        'border-border bg-muted text-muted-foreground',
};

const statusIcons = {
    pending: Clock3,
    accepted: CheckCircle2,
    rejected: XCircle,
    withdrawn: UserRound,
};

function updateOffer(offer: Offer, status: 'accepted' | 'rejected') {
    const action = status === 'accepted' ? 'accepter' : 'refuser';

    if (!confirm(`Voulez-vous ${action} cette demande ?`)) {
        return;
    }

    router.patch(
        `/collaboration-offers/${offer.id}`,
        { status },
        { preserveScroll: true },
    );
}

function formatDate(date: string) {
    return new Date(date).toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
}

function developerInitial(name: string) {
    return name.trim().charAt(0).toUpperCase() || '?';
}
</script>

<template>

    <Head :title="`Candidatures — ${project.name}`" />

    <main class="relative isolate min-h-screen w-full min-w-0 overflow-hidden bg-background">
        <!-- Décoration d'arrière-plan -->
        <div class="pointer-events-none absolute -left-32 top-0 -z-10 size-80 rounded-full bg-indigo-500/10 blur-3xl"
            aria-hidden="true" />
        <div class="pointer-events-none absolute -right-32 top-96 -z-10 size-80 rounded-full bg-teal-500/10 blur-3xl"
            aria-hidden="true" />

        <div class="mx-auto w-full min-w-0 max-w-7xl px-4 py-6 sm:px-6 sm:py-10 lg:px-8">
            <!-- Navigation -->
            <Link :href="`/projects/${project.slug}`"
                class="mb-6 inline-flex min-h-10 items-center gap-2 rounded-xl text-sm font-medium text-muted-foreground transition-colors hover:text-foreground">
                <ArrowLeft class="size-4" />
                Retour au projet
            </Link>

            <!-- En-tête -->
            <header class="mb-8 sm:mb-10">
                <div class="flex min-w-0 flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                    <div class="min-w-0">
                        <div
                            class="mb-3 inline-flex items-center gap-2 rounded-full border border-indigo-500/20 bg-indigo-500/5 px-3 py-1.5 text-xs font-semibold text-indigo-700 dark:text-indigo-300">
                            <Users class="size-3.5" />
                            <span>Espace de collaboration</span>
                        </div>

                        <h1 class="text-2xl font-bold tracking-tight sm:text-3xl lg:text-4xl">
                            Demandes de
                            <span class="text-indigo-600 dark:text-indigo-400">
                                collaboration
                            </span>
                        </h1>

                        <div class="mt-3 flex min-w-0 items-start gap-2 text-sm text-muted-foreground sm:text-base">
                            <FolderOpen class="mt-0.5 size-4 shrink-0" />
                            <p class="min-w-0 wrap-break-word">
                                Projet :
                                <span class="font-semibold text-foreground">
                                    {{ project.name }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <div
                        class="flex w-full items-center gap-3 rounded-2xl border border-border/70 bg-card p-4 shadow-sm sm:w-auto sm:min-w-44">
                        <div
                            class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-300">
                            <Users class="size-5" />
                        </div>

                        <div>
                            <p class="text-2xl font-bold tabular-nums">{{ offers.total }}</p>
                            <p class="text-xs text-muted-foreground">
                                {{ offers.total > 1 ? 'Candidatures reçues' : 'Candidature reçue' }}
                            </p>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Statistiques -->
            <section class="mb-8 grid grid-cols-1 gap-3 sm:grid-cols-3 sm:gap-4">
                <div class="flex min-w-0 items-center gap-3 rounded-2xl border border-border/70 bg-card p-4 shadow-sm">
                    <div
                        class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-amber-500/10 text-amber-700 dark:text-amber-300">
                        <Clock3 class="size-5" />
                    </div>
                    <div class="min-w-0">
                        <p class="text-xl font-bold tabular-nums">
                            {{offers.data.filter((offer) => offer.status === 'pending').length}}
                        </p>
                        <p class="text-sm text-muted-foreground">En attente sur cette page</p>
                    </div>
                </div>

                <div class="flex min-w-0 items-center gap-3 rounded-2xl border border-border/70 bg-card p-4 shadow-sm">
                    <div
                        class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-700 dark:text-emerald-300">
                        <CheckCircle2 class="size-5" />
                    </div>
                    <div class="min-w-0">
                        <p class="text-xl font-bold tabular-nums">
                            {{offers.data.filter((offer) => offer.status === 'accepted').length}}
                        </p>
                        <p class="text-sm text-muted-foreground">Acceptées sur cette page</p>
                    </div>
                </div>

                <div class="flex min-w-0 items-center gap-3 rounded-2xl border border-border/70 bg-card p-4 shadow-sm">
                    <div
                        class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-red-500/10 text-red-700 dark:text-red-300">
                        <XCircle class="size-5" />
                    </div>
                    <div class="min-w-0">
                        <p class="text-xl font-bold tabular-nums">
                            {{offers.data.filter((offer) => offer.status === 'rejected').length}}
                        </p>
                        <p class="text-sm text-muted-foreground">Refusées sur cette page</p>
                    </div>
                </div>
            </section>

            <!-- Aucune candidature -->
            <section v-if="offers.data.length === 0"
                class="rounded-3xl border border-dashed border-border bg-card/70 px-5 py-12 text-center sm:px-10 sm:py-16">
                <div
                    class="mx-auto flex size-16 items-center justify-center rounded-2xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-300">
                    <MessageSquare class="size-8" />
                </div>

                <h2 class="mt-5 text-xl font-bold tracking-tight sm:text-2xl">
                    Aucune candidature reçue
                </h2>

                <p class="mx-auto mt-3 max-w-md text-sm leading-6 text-muted-foreground sm:text-base">
                    Les développeurs qui souhaitent rejoindre ton projet apparaîtront ici.
                    En attendant, tu peux consulter les informations de ton projet.
                </p>

                <Link :href="`/projects/${project.slug}`"
                    class="mt-6 inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white transition-colors hover:bg-indigo-700">
                    <FolderOpen class="size-4" />
                    Voir le projet
                    <ArrowRight class="size-4" />
                </Link>
            </section>

            <!-- Candidatures -->
            <section v-else class="min-w-0">
                <div class="mb-4 flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                    <h2 class="text-lg font-bold tracking-tight">
                        Candidatures des développeurs
                    </h2>

                    <p class="text-sm text-muted-foreground">
                        {{ offers.total }}
                        {{ offers.total > 1 ? 'candidatures au total' : 'candidature au total' }}
                    </p>
                </div>

                <div class="space-y-4 sm:space-y-5">
                    <article v-for="offer in offers.data" :key="offer.id"
                        class="group min-w-0 overflow-hidden rounded-2xl border border-border/70 bg-card shadow-sm transition-all duration-200 hover:border-indigo-500/30 hover:shadow-md sm:rounded-3xl">
                        <div class="p-4 sm:p-6 lg:p-7">
                            <!-- Profil et statut -->
                            <div class="flex min-w-0 flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div class="flex min-w-0 items-start gap-3 sm:gap-4">
                                    <Link :href="`/developers/${offer.user.id}`"
                                        class="shrink-0 rounded-2xl focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2"
                                        :aria-label="`Voir le profil de ${offer.user.name}`">
                                        <img v-if="offer.user.profile?.avatar_path"
                                            :src="`/storage/${offer.user.profile.avatar_path}`" :alt="offer.user.name"
                                            class="size-12 rounded-2xl border border-border/70 object-cover sm:size-14" />

                                        <div v-else
                                            class="flex size-12 items-center justify-center rounded-2xl bg-indigo-500/10 text-lg font-bold text-indigo-700 dark:text-indigo-300 sm:size-14 sm:text-xl">
                                            {{ developerInitial(offer.user.name) }}
                                        </div>
                                    </Link>

                                    <div class="min-w-0 flex-1">
                                        <Link :href="`/developers/${offer.user.id}`"
                                            class="wrap-break-word text-base font-bold leading-snug transition-colors hover:text-indigo-600 dark:hover:text-indigo-400 sm:text-lg">
                                            {{ offer.user.name }}
                                        </Link>

                                        <p v-if="offer.user.profile?.headline"
                                            class="mt-1 wrap-break-word text-sm leading-5 text-muted-foreground">
                                            {{ offer.user.profile.headline }}
                                        </p>

                                        <p
                                            class="mt-2 flex flex-wrap items-center gap-1.5 text-xs text-muted-foreground">
                                            <CalendarDays class="size-3.5 shrink-0" />
                                            <span>Envoyée le {{ formatDate(offer.created_at) }}</span>
                                        </p>
                                    </div>
                                </div>

                                <span
                                    class="inline-flex w-fit max-w-full shrink-0 items-center gap-2 self-start rounded-full border px-3 py-1.5 text-xs font-semibold sm:text-sm"
                                    :class="statusClasses[offer.status] ?? 'border-border bg-muted text-muted-foreground'">
                                    <component :is="statusIcons[offer.status as keyof typeof statusIcons] ?? Clock3"
                                        class="size-4 shrink-0" />
                                    {{ statusLabels[offer.status] ?? offer.status }}
                                </span>
                            </div>

                            <!-- Rôle proposé -->
                            <div v-if="offer.proposed_role"
                                class="mt-5 rounded-xl border border-border/70 bg-muted/30 p-4">
                                <div class="mb-2 flex items-center gap-2">
                                    <BriefcaseBusiness class="size-4 text-muted-foreground" />
                                    <h3 class="text-xs font-bold uppercase tracking-wider text-muted-foreground">
                                        Rôle proposé
                                    </h3>
                                </div>

                                <p class="wrap-break-word text-sm font-semibold">
                                    {{ offer.proposed_role }}
                                </p>
                            </div>

                            <!-- Message du candidat -->
                            <div class="mt-5 rounded-2xl border border-border/60 bg-muted/30 p-4 sm:p-5">
                                <div class="mb-3 flex items-center gap-2">
                                    <MessageSquare class="size-4 text-muted-foreground" />
                                    <h3 class="text-xs font-bold uppercase tracking-wider text-muted-foreground">
                                        Message du développeur
                                    </h3>
                                </div>

                                <p class="whitespace-pre-line wrap-break-word text-sm leading-7 text-foreground/90">
                                    {{ offer.message }}
                                </p>
                            </div>

                            <!-- Actions de modération -->
                            <div v-if="offer.status === 'pending'"
                                class="mt-5 flex flex-col gap-3 border-t border-border/70 pt-5 sm:flex-row sm:items-center sm:justify-between">
                                <p class="text-xs leading-5 text-muted-foreground">
                                    Consulte le profil du candidat avant de prendre ta décision.
                                </p>

                                <div
                                    class="flex w-full flex-col-reverse gap-2 sm:w-auto sm:flex-row sm:items-center sm:gap-3">
                                    <button type="button"
                                        class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl border border-red-200 bg-background px-4 py-2.5 text-sm font-semibold text-red-700 transition-colors hover:bg-red-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2 dark:border-red-400/20 dark:text-red-300 dark:hover:bg-red-400/10 dark:focus-visible:ring-offset-background sm:w-auto"
                                        @click="updateOffer(offer, 'rejected')">
                                        <XCircle class="size-4" />
                                        Refuser
                                    </button>

                                    <button type="button"
                                        class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:-translate-y-0.5 hover:bg-indigo-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-background sm:w-auto"
                                        @click="updateOffer(offer, 'accepted')">
                                        <CheckCircle2 class="size-4" />
                                        Accepter la candidature
                                    </button>
                                </div>
                            </div>

                            <!-- Confirmation de l'acceptation -->
                            <div v-else-if="offer.status === 'accepted'"
                                class="mt-5 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-400/20 dark:bg-emerald-400/10">
                                <CheckCircle2 class="mt-0.5 size-5 shrink-0 text-emerald-600 dark:text-emerald-300" />
                                <div>
                                    <p class="text-sm font-semibold text-emerald-800 dark:text-emerald-300">
                                        Candidature acceptée
                                    </p>
                                    <p class="mt-1 text-sm leading-6 text-emerald-700 dark:text-emerald-300/80">
                                        Ce développeur fait partie du projet.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
            </section>

            <!-- Pagination -->
            <nav v-if="offers.last_page > 1" class="mt-8 flex min-w-0 flex-col items-center gap-4 sm:mt-10"
                aria-label="Pagination des candidatures">
                <p class="text-xs text-muted-foreground">
                    Page {{ offers.current_page }} sur {{ offers.last_page }}
                </p>

                <div class="flex max-w-full flex-wrap items-center justify-center gap-1.5 sm:gap-2">
                    <template v-for="(link, index) in offers.links" :key="index">
                        <Link v-if="link.url" :href="link.url" preserve-scroll
                            :aria-current="link.active ? 'page' : undefined"
                            class="inline-flex min-h-10 min-w-10 items-center justify-center rounded-xl border px-3 py-2 text-sm font-medium transition-colors"
                            :class="link.active
                                ? 'border-indigo-600 bg-indigo-600 text-white shadow-sm'
                                : 'border-border bg-card text-foreground hover:border-indigo-500/40 hover:bg-muted'
                                " v-html="link.label" />

                        <span v-else
                            class="inline-flex min-h-10 min-w-10 items-center justify-center rounded-xl border border-border/50 px-3 py-2 text-sm text-muted-foreground/50"
                            v-html="link.label" />
                    </template>
                </div>
            </nav>

            <!-- Pied de page -->
            <footer class="mt-10 border-t border-border/60 pt-5 text-center sm:mt-12">
                <p class="text-xs leading-5 text-muted-foreground">
                    Une bonne collaboration commence par la rencontre de développeurs partageant les mêmes objectifs.
                </p>
            </footer>
        </div>
    </main>
</template>