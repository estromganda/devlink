<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';

defineOptions({
    layout: PublicLayout,
});

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
        month: 'long',
        year: 'numeric',
    });
}
</script>

<template>

    <Head :title="`Demandes de collaboration — ${project.name}`" />

    <main class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        <Link :href="`/projects/${project.slug}`" class="text-sm font-medium text-teal-700 hover:text-teal-900">
            ← Retour au projet
        </Link>

        <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">
                    Demandes de collaboration
                </h1>

                <p class="mt-2 text-gray-600">
                    Projet : {{ project.name }}
                </p>
            </div>

            <p class="text-sm text-gray-500">
                {{ offers.total }}
                demande{{ offers.total > 1 ? 's' : '' }}
            </p>
        </div>

        <div v-if="offers.data.length === 0"
            class="mt-8 rounded-2xl border border-dashed border-gray-300 bg-white p-10 text-center">
            <h2 class="text-lg font-semibold text-gray-900">
                Aucune demande reçue
            </h2>

            <p class="mt-2 text-gray-600">
                Les demandes des développeurs apparaîtront ici.
            </p>
        </div>

        <div v-else class="mt-8 space-y-5">
            <article v-for="offer in offers.data" :key="offer.id"
                class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div class="flex items-center gap-3">
                        <img v-if="offer.user.profile?.avatar_path" :src="`/storage/${offer.user.profile.avatar_path}`"
                            :alt="offer.user.name" class="h-12 w-12 rounded-full object-cover" />

                        <div v-else
                            class="flex h-12 w-12 items-center justify-center rounded-full bg-teal-100 text-lg font-bold text-teal-800">
                            {{ offer.user.name.charAt(0).toUpperCase() }}
                        </div>

                        <div>
                            <Link :href="`/developers/${offer.user.id}`"
                                class="font-bold text-gray-900 hover:text-teal-700">
                                {{ offer.user.name }}
                            </Link>

                            <p v-if="offer.user.profile?.headline" class="mt-1 text-sm text-gray-500">
                                {{ offer.user.profile.headline }}
                            </p>

                            <p class="mt-1 text-xs text-gray-400">
                                Envoyée le {{ formatDate(offer.created_at) }}
                            </p>
                        </div>
                    </div>

                    <span class="inline-flex w-fit rounded-full px-3 py-1 text-sm font-semibold" :class="{
                        'bg-amber-100 text-amber-800': offer.status === 'pending',
                        'bg-emerald-100 text-emerald-800': offer.status === 'accepted',
                        'bg-red-100 text-red-800': offer.status === 'rejected',
                        'bg-gray-100 text-gray-600': offer.status === 'withdrawn',
                    }">
                        {{
                            {
                                pending: 'En attente',
                                accepted: 'Acceptée',
                                rejected: 'Refusée',
                                withdrawn: 'Retirée',
                            }[offer.status] ?? offer.status
                        }}
                    </span>
                </div>

                <div v-if="offer.proposed_role" class="mt-5">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                        Rôle proposé
                    </p>

                    <p class="mt-1 text-sm text-gray-800">
                        {{ offer.proposed_role }}
                    </p>
                </div>

                <div class="mt-5 rounded-xl bg-gray-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                        Message du développeur
                    </p>

                    <p class="mt-2 whitespace-pre-line text-sm leading-6 text-gray-700">
                        {{ offer.message }}
                    </p>
                </div>

                <div v-if="offer.status === 'pending'" class="mt-5 flex flex-wrap justify-end gap-3">
                    <button type="button"
                        class="rounded-xl border border-red-200 px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-50"
                        @click="updateOffer(offer, 'rejected')">
                        Refuser
                    </button>

                    <button type="button"
                        class="rounded-xl bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700"
                        @click="updateOffer(offer, 'accepted')">
                        Accepter
                    </button>
                </div>

                <p v-else-if="offer.status === 'accepted'" class="mt-4 text-sm font-medium text-emerald-700">
                    Ce développeur fait partie du projet.
                </p>
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
