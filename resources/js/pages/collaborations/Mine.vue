<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    ArrowLeft,
    ArrowRight,
    BriefcaseBusiness,
    CalendarDays,
    CheckCircle2,
    Clock3,
    FileText,
    FolderOpen,
    Hourglass,
    MessageSquare,
    RotateCcw,
    Send,
    ShieldCheck,
    UserRound,
    XCircle,
} from '@lucide/vue';

interface Project {
    id: number;
    name: string;
    slug: string;
    short_description: string | null;
    status: string;
    visibility: string;
    owner: {
        id: number;
        name: string;
    };
}

interface Offer {
    id: number;
    message: string;
    proposed_role: string | null;
    status: string;
    created_at: string;
    reviewed_at: string | null;
    project: Project;
    reviewer: {
        id: number;
        name: string;
    } | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

const props = defineProps<{
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
    withdrawn: RotateCcw,
};

function withdrawOffer(offer: Offer) {
    if (!confirm('Voulez-vous vraiment retirer cette demande ?')) {
        return;
    }

    router.delete(`/collaboration-offers/${offer.id}`, {
        preserveScroll: true,
    });
}

function formatDate(date: string) {
    return new Date(date).toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
}

function formatStatus(status: string) {
    return statusLabels[status] ?? status;
}
</script>

<template>

    <Head title="Mes demandes de collaboration" />

    <main class="relative isolate min-h-screen w-full min-w-0 overflow-hidden bg-background">
        <!-- Décoration -->
        <div class="pointer-events-none absolute -left-32 top-0 -z-10 size-80 rounded-full bg-indigo-500/10 blur-3xl"
            aria-hidden="true" />
        <div class="pointer-events-none absolute -right-32 top-96 -z-10 size-80 rounded-full bg-teal-500/10 blur-3xl"
            aria-hidden="true" />

        <div class="mx-auto w-full min-w-0 max-w-7xl px-4 py-6 sm:px-6 sm:py-10 lg:px-8">
            <!-- En-tête -->
            <header class="mb-8 sm:mb-10">
                <Link href="/projects"
                    class="mb-6 inline-flex min-h-10 items-center gap-2 rounded-xl text-sm font-medium text-muted-foreground transition-colors hover:text-foreground">
                    <ArrowLeft class="size-4" />
                    Retour aux projets
                </Link>

                <div class="flex min-w-0 flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                    <div class="min-w-0">
                        <div
                            class="mb-3 inline-flex items-center gap-2 rounded-full border border-indigo-500/20 bg-indigo-500/5 px-3 py-1.5 text-xs font-semibold text-indigo-700 dark:text-indigo-300">
                            <BriefcaseBusiness class="size-3.5" />
                            <span>Collaboration</span>
                        </div>

                        <h1 class="text-2xl font-bold tracking-tight sm:text-3xl lg:text-4xl">
                            Mes demandes
                            <span class="text-indigo-600 dark:text-indigo-400">
                                de collaboration
                            </span>
                        </h1>

                        <p class="mt-3 max-w-2xl text-sm leading-6 text-muted-foreground sm:text-base">
                            Suis tes candidatures, consulte les réponses des propriétaires
                            et retrouve les projets que tu souhaites rejoindre.
                        </p>
                    </div>

                    <Link href="/projects"
                        class="inline-flex min-h-11 w-full shrink-0 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-600/15 transition-all hover:-translate-y-0.5 hover:bg-indigo-700 sm:w-auto">
                        <FolderOpen class="size-4" />
                        Explorer les projets
                        <ArrowRight class="size-4" />
                    </Link>
                </div>

                <!-- Statistiques -->
                <div class="mt-7 grid grid-cols-1 gap-3 sm:grid-cols-3 sm:gap-4">
                    <div
                        class="flex min-w-0 items-center gap-4 rounded-2xl border border-border/70 bg-card p-4 shadow-sm sm:p-5">
                        <div
                            class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-300">
                            <Send class="size-5" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-2xl font-bold tabular-nums">{{ offers.total }}</p>
                            <p class="text-sm text-muted-foreground">Demandes envoyées</p>
                        </div>
                    </div>

                    <div
                        class="flex min-w-0 items-center gap-4 rounded-2xl border border-border/70 bg-card p-4 shadow-sm sm:p-5">
                        <div
                            class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-amber-500/10 text-amber-700 dark:text-amber-300">
                            <Hourglass class="size-5" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-2xl font-bold tabular-nums">
                                {{offers.data.filter((offer) => offer.status === 'pending').length}}
                            </p>
                            <p class="text-sm text-muted-foreground">En attente sur cette page</p>
                        </div>
                    </div>

                    <div
                        class="flex min-w-0 items-center gap-4 rounded-2xl border border-border/70 bg-card p-4 shadow-sm sm:p-5">
                        <div
                            class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-700 dark:text-emerald-300">
                            <CheckCircle2 class="size-5" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-2xl font-bold tabular-nums">
                                {{offers.data.filter((offer) => offer.status === 'accepted').length}}
                            </p>
                            <p class="text-sm text-muted-foreground">Acceptées sur cette page</p>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Liste vide -->
            <section v-if="offers.data.length === 0"
                class="rounded-3xl border border-dashed border-border bg-card/70 px-5 py-12 text-center sm:px-10 sm:py-16">
                <div
                    class="mx-auto flex size-16 items-center justify-center rounded-2xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-300">
                    <FileText class="size-8" />
                </div>

                <h2 class="mt-5 text-xl font-bold tracking-tight sm:text-2xl">
                    Aucune demande pour le moment
                </h2>

                <p class="mx-auto mt-3 max-w-md text-sm leading-6 text-muted-foreground sm:text-base">
                    Découvre les projets de la communauté et envoie une candidature
                    pour proposer tes compétences.
                </p>

                <Link href="/projects"
                    class="mt-6 inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white transition-colors hover:bg-indigo-700">
                    <FolderOpen class="size-4" />
                    Découvrir les projets
                    <ArrowRight class="size-4" />
                </Link>
            </section>

            <!-- Liste des demandes -->
            <section v-else class="min-w-0">
                <div class="mb-4 flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                    <h2 class="text-lg font-bold tracking-tight">
                        Historique des candidatures
                    </h2>
                    <p class="text-sm text-muted-foreground">
                        {{ offers.total }}
                        {{ offers.total > 1 ? 'demandes au total' : 'demande au total' }}
                    </p>
                </div>

                <div class="space-y-4 sm:space-y-5">
                    <article v-for="offer in offers.data" :key="offer.id"
                        class="group min-w-0 overflow-hidden rounded-2xl border border-border/70 bg-card shadow-sm transition-all duration-200 hover:border-indigo-500/30 hover:shadow-md sm:rounded-3xl">
                        <div class="p-4 sm:p-6 lg:p-7">
                            <!-- Projet et statut -->
                            <div class="flex min-w-0 flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div class="flex min-w-0 items-start gap-3 sm:gap-4">
                                    <div
                                        class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-300 sm:size-12 sm:rounded-2xl">
                                        <FolderOpen class="size-5 sm:size-6" />
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <Link :href="`/projects/${offer.project.slug}`"
                                            class="wrap-break-word text-lg font-bold leading-snug tracking-tight transition-colors hover:text-indigo-600 dark:hover:text-indigo-400 sm:text-xl">
                                            {{ offer.project.name }}
                                        </Link>

                                        <p
                                            class="mt-1.5 flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-muted-foreground">
                                            <UserRound class="size-4 shrink-0" />
                                            <span>Proposé par</span>
                                            <span class="wrap-break-word font-medium text-foreground">
                                                {{ offer.project.owner.name }}
                                            </span>
                                        </p>

                                        <p v-if="offer.project.short_description"
                                            class="mt-3 line-clamp-2 max-w-3xl text-sm leading-6 text-muted-foreground">
                                            {{ offer.project.short_description }}
                                        </p>
                                    </div>
                                </div>

                                <span
                                    class="inline-flex w-fit max-w-full shrink-0 items-center gap-2 self-start rounded-full border px-3 py-1.5 text-xs font-semibold sm:text-sm"
                                    :class="statusClasses[offer.status] ?? 'border-border bg-muted text-muted-foreground'">
                                    <component :is="statusIcons[offer.status as keyof typeof statusIcons] ?? Clock3"
                                        class="size-4 shrink-0" />
                                    {{ formatStatus(offer.status) }}
                                </span>
                            </div>

                            <!-- Rôle proposé -->
                            <div v-if="offer.proposed_role"
                                class="mt-5 inline-flex max-w-full items-start gap-2 rounded-xl border border-border/70 bg-muted/40 px-3 py-2 text-sm">
                                <BriefcaseBusiness class="mt-0.5 size-4 shrink-0 text-muted-foreground" />
                                <span class="text-muted-foreground">Rôle proposé :</span>
                                <span class="wrap-break-word font-semibold">
                                    {{ offer.proposed_role }}
                                </span>
                            </div>

                            <!-- Message -->
                            <div class="mt-5 rounded-2xl border border-border/60 bg-muted/30 p-4 sm:p-5">
                                <div class="mb-3 flex items-center gap-2">
                                    <MessageSquare class="size-4 text-muted-foreground" />
                                    <h3 class="text-xs font-bold uppercase tracking-wider text-muted-foreground">
                                        Ton message
                                    </h3>
                                </div>

                                <p class="whitespace-pre-line wrap-break-word text-sm leading-7 text-foreground/90">
                                    {{ offer.message }}
                                </p>
                            </div>

                            <!-- Dates -->
                            <div class="mt-5 grid min-w-0 grid-cols-1 gap-3 sm:grid-cols-2">
                                <div class="flex min-w-0 items-start gap-3 rounded-xl bg-muted/30 p-3">
                                    <CalendarDays class="mt-0.5 size-4 shrink-0 text-muted-foreground" />
                                    <div class="min-w-0">
                                        <p class="text-xs text-muted-foreground">Date d'envoi</p>
                                        <p class="mt-1 wrap-break-word text-sm font-medium">
                                            {{ formatDate(offer.created_at) }}
                                        </p>
                                    </div>
                                </div>

                                <div v-if="offer.reviewed_at"
                                    class="flex min-w-0 items-start gap-3 rounded-xl bg-muted/30 p-3">
                                    <ShieldCheck class="mt-0.5 size-4 shrink-0 text-muted-foreground" />
                                    <div class="min-w-0">
                                        <p class="text-xs text-muted-foreground">Date de traitement</p>
                                        <p class="mt-1 wrap-break-word text-sm font-medium">
                                            {{ formatDate(offer.reviewed_at) }}
                                        </p>
                                        <p v-if="offer.reviewer"
                                            class="mt-1 wrap-break-word text-xs text-muted-foreground">
                                            Traitée par {{ offer.reviewer.name }}
                                        </p>
                                    </div>
                                </div>

                                <div v-else-if="offer.status === 'pending'"
                                    class="flex min-w-0 items-start gap-3 rounded-xl bg-amber-500/5 p-3 sm:col-span-1">
                                    <Clock3 class="mt-0.5 size-4 shrink-0 text-amber-600 dark:text-amber-300" />
                                    <div class="min-w-0">
                                        <p class="text-xs text-muted-foreground">Prochaine étape</p>
                                        <p class="mt-1 text-sm font-medium">
                                            En attente de réponse
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div v-if="offer.status === 'pending'"
                                class="mt-5 flex flex-col gap-3 border-t border-border/70 pt-5 sm:flex-row sm:items-center sm:justify-between">
                                <p class="text-xs leading-5 text-muted-foreground">
                                    Tu peux retirer cette candidature tant qu'elle n'a pas été traitée.
                                </p>

                                <button type="button"
                                    class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl border border-red-200 bg-background px-4 py-2.5 text-sm font-semibold text-red-700 transition-colors hover:bg-red-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2 dark:border-red-400/20 dark:text-red-300 dark:hover:bg-red-400/10 dark:focus-visible:ring-offset-background sm:w-auto"
                                    @click="withdrawOffer(offer)">
                                    <XCircle class="size-4" />
                                    Retirer ma demande
                                </button>
                            </div>
                        </div>
                    </article>
                </div>
            </section>

            <!-- Pagination -->
            <nav v-if="offers.last_page > 1" class="mt-8 flex min-w-0 flex-col items-center gap-4 sm:mt-10"
                aria-label="Pagination des demandes">
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
                    Continue à explorer les projets et à partager tes compétences avec la communauté DevColab.
                </p>
            </footer>
        </div>
    </main>
</template>