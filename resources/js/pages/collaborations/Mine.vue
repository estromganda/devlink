<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';

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
    pending: 'bg-amber-100 text-amber-800',
    accepted: 'bg-emerald-100 text-emerald-800',
    rejected: 'bg-red-100 text-red-800',
    withdrawn: 'bg-gray-100 text-gray-600',
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
        month: 'long',
        year: 'numeric',
    });
}
</script>

<template>

    <Head title="Mes demandes de collaboration" />

    <main class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-8">
            <Link href="/projects" class="text-sm font-medium text-teal-700 hover:text-teal-900">
                ← Retour aux projets
            </Link>

            <h1 class="mt-4 text-3xl font-bold tracking-tight text-gray-900">
                Mes demandes de collaboration
            </h1>

            <p class="mt-2 text-gray-600">
                Suivez les demandes que vous avez envoyées aux propriétaires
                de projets.
            </p>
        </div>

        <div v-if="offers.data.length === 0"
            class="rounded-2xl border border-dashed border-gray-300 bg-white p-10 text-center">
            <h2 class="text-lg font-semibold text-gray-900">
                Aucune demande pour le moment
            </h2>

            <p class="mt-2 text-gray-600">
                Découvrez des projets et proposez votre aide à leurs propriétaires.
            </p>

            <Link href="/projects"
                class="mt-5 inline-flex rounded-xl bg-teal-600 px-5 py-3 font-semibold text-white hover:bg-teal-700">
                Découvrir les projets
            </Link>
        </div>

        <div v-else class="space-y-5">
            <article v-for="offer in offers.data" :key="offer.id"
                class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div class="min-w-0">
                        <Link :href="`/projects/${offer.project.slug}`"
                            class="text-xl font-bold text-gray-900 hover:text-teal-700">
                            {{ offer.project.name }}
                        </Link>

                        <p class="mt-1 text-sm text-gray-500">
                            Propriétaire :
                            {{ offer.project.owner.name }}
                        </p>

                        <p v-if="offer.proposed_role" class="mt-2 text-sm text-gray-600">
                            Rôle proposé : {{ offer.proposed_role }}
                        </p>
                    </div>

                    <span class="inline-flex w-fit shrink-0 rounded-full px-3 py-1 text-sm font-semibold"
                        :class="statusClasses[offer.status] ?? 'bg-gray-100 text-gray-700'">
                        {{ statusLabels[offer.status] ?? offer.status }}
                    </span>
                </div>

                <div class="mt-5 rounded-xl bg-gray-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                        Votre message
                    </p>

                    <p class="mt-2 whitespace-pre-line text-sm leading-6 text-gray-700">
                        {{ offer.message }}
                    </p>
                </div>

                <div
                    class="mt-4 flex flex-col gap-3 text-sm text-gray-500 sm:flex-row sm:items-center sm:justify-between">
                    <p>
                        Envoyée le {{ formatDate(offer.created_at) }}
                    </p>

                    <p v-if="offer.reviewed_at">
                        Traitée le {{ formatDate(offer.reviewed_at) }}
                        <span v-if="offer.reviewer">
                            par {{ offer.reviewer.name }}
                        </span>
                    </p>
                </div>

                <div v-if="offer.status === 'pending'" class="mt-4 flex justify-end">
                    <button type="button"
                        class="rounded-xl border border-red-200 px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-50"
                        @click="withdrawOffer(offer)">
                        Retirer ma demande
                    </button>
                </div>
            </article>
        </div>

        <nav v-if="offers.last_page > 1" class="mt-8 flex flex-wrap justify-center gap-2" aria-label="Pagination">
            <template v-for="(link, index) in offers.links" :key="index">
                <Link v-if="link.url" :href="link.url" preserve-scroll class="rounded-lg border px-3 py-2 text-sm"
                    :class="link.active
                        ? 'border-teal-600 bg-teal-600 text-white'
                        : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50'
                        " v-html="link.label" />

                <span v-else class="rounded-lg border border-gray-100 px-3 py-2 text-sm text-gray-300"
                    v-html="link.label" />
            </template>
        </nav>
    </main>
</template>
