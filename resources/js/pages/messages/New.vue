<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    ArrowLeft,
    ArrowRight,
    Search,
    MessageCircle,
    MessageSquarePlus,
    Users,
    Code2,
    LoaderCircle,
    X,
    UserRound,
} from '@lucide/vue';
import { ref } from 'vue';

interface DeveloperProfile {
    avatar_path: string | null;
    headline?: string | null;
}

interface Developer {
    id: number;
    name: string;
    profile: DeveloperProfile | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

const props = defineProps<{
    developers: {
        data: Developer[];
        links: PaginationLink[];
        current_page: number;
        last_page: number;
        total: number;
    };
    filters: {
        search: string;
    };
}>();

const search = ref(props.filters.search);
const processingId = ref<number | null>(null);

function searchDevelopers() {
    router.get(
        '/messages/new',
        { search: search.value.trim() || undefined },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
}

function clearSearch() {
    search.value = '';
    searchDevelopers();
}

function startConversation(developer: Developer) {
    if (processingId.value !== null) return;

    processingId.value = developer.id;

    router.post(
        '/messages/start',
        { recipient_id: developer.id },
        {
            onFinish: () => {
                processingId.value = null;
            },
        },
    );
}

function getInitials(name: string): string {
    return name
        .trim()
        .split(/\s+/)
        .slice(0, 2)
        .map((part) => part.charAt(0).toUpperCase())
        .join('');
}
</script>

<template>

    <Head title="Nouvelle conversation" />

    <main class="min-h-[calc(100vh-4rem)] bg-gray-50/70 py-6 dark:bg-gray-950 sm:py-10">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <!-- Retour -->
            <Link href="/messages"
                class="group inline-flex items-center gap-2 rounded-lg text-sm font-medium text-gray-500 transition hover:text-teal-700 dark:text-gray-400 dark:hover:text-teal-400">
                <ArrowLeft class="size-4 transition-transform group-hover:-translate-x-1" />
                Retour à la messagerie
            </Link>

            <!-- En-tête -->
            <section
                class="mt-6 overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div
                    class="relative overflow-hidden border-b border-gray-100 px-5 py-7 dark:border-gray-800 sm:px-8 sm:py-9">
                    <div
                        class="pointer-events-none absolute -right-12 -top-16 size-56 rounded-full bg-teal-500/10 blur-3xl" />
                    <div
                        class="pointer-events-none absolute -bottom-20 right-32 size-44 rounded-full bg-cyan-500/10 blur-3xl" />

                    <div class="relative flex items-start gap-4">
                        <div
                            class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-teal-50 text-teal-700 dark:bg-teal-950 dark:text-teal-300 sm:size-14">
                            <MessageSquarePlus class="size-6 sm:size-7" />
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-3xl">
                                    Nouvelle conversation
                                </h1>

                                <span
                                    class="rounded-full bg-teal-50 px-2.5 py-1 text-xs font-semibold text-teal-700 dark:bg-teal-950 dark:text-teal-300">
                                    Privée
                                </span>
                            </div>

                            <p class="mt-2 max-w-2xl text-sm leading-6 text-gray-500 dark:text-gray-400 sm:text-base">
                                Retrouve un développeur et échange avec lui autour de tes idées,
                                de tes projets ou de tes questions techniques.
                            </p>
                        </div>
                    </div>

                    <!-- Statistiques -->
                    <div
                        class="relative mt-6 flex flex-wrap items-center gap-x-5 gap-y-3 text-sm text-gray-500 dark:text-gray-400">
                        <div class="inline-flex items-center gap-2">
                            <Users class="size-4 text-teal-600 dark:text-teal-400" />
                            <span>
                                <strong class="font-semibold text-gray-900 dark:text-gray-100">
                                    {{ developers.total }}
                                </strong>
                                développeurs
                            </span>
                        </div>

                        <div class="inline-flex items-center gap-2">
                            <MessageCircle class="size-4 text-teal-600 dark:text-teal-400" />
                            <span>Échanges directs entre développeurs</span>
                        </div>
                    </div>
                </div>

                <!-- Recherche -->
                <div class="p-5 sm:p-7">
                    <form class="flex flex-col gap-3 sm:flex-row" role="search" @submit.prevent="searchDevelopers">
                        <div class="relative min-w-0 flex-1">
                            <Search
                                class="pointer-events-none absolute left-4 top-1/2 size-5 -translate-y-1/2 text-gray-400" />

                            <input v-model="search" type="search" name="search" autocomplete="off"
                                placeholder="Rechercher un développeur par son nom..."
                                aria-label="Rechercher un développeur"
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 py-3.5 pl-12 pr-11 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-teal-500 dark:focus:bg-gray-800" />

                            <button v-if="search" type="button" aria-label="Effacer la recherche"
                                class="absolute right-3 top-1/2 flex size-7 -translate-y-1/2 items-center justify-center rounded-lg text-gray-400 transition hover:bg-gray-200 hover:text-gray-700 dark:hover:bg-gray-700 dark:hover:text-gray-200"
                                @click="clearSearch">
                                <X class="size-4" />
                            </button>
                        </div>

                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-teal-600 px-6 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-teal-700 focus:outline-none focus:ring-4 focus:ring-teal-500/20">
                            <Search class="size-4" />
                            Rechercher
                        </button>
                    </form>

                    <p class="mt-3 text-xs text-gray-400 dark:text-gray-500">
                        Astuce : saisis le nom d'un développeur pour le retrouver plus rapidement.
                    </p>
                </div>
            </section>

            <!-- Résultats -->
            <section class="mt-8">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ search ? 'Résultats de recherche' : 'Découvrir les développeurs' }}
                        </h2>

                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            <template v-if="developers.total > 0">
                                {{ developers.total }}
                                {{ developers.total === 1 ? 'développeur disponible' : 'développeurs disponibles' }}
                            </template>
                            <template v-else>
                                Aucun résultat pour le moment
                            </template>
                        </p>
                    </div>

                    <span v-if="developers.last_page > 1"
                        class="rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-500 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400">
                        Page {{ developers.current_page }} sur {{ developers.last_page }}
                    </span>
                </div>

                <!-- Aucun résultat -->
                <div v-if="developers.data.length === 0"
                    class="rounded-2xl border border-dashed border-gray-300 bg-white px-5 py-14 text-center dark:border-gray-700 dark:bg-gray-900 sm:px-10">
                    <div
                        class="mx-auto flex size-16 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800">
                        <template v-if="search">
                            <Search class="size-7 text-gray-400" />
                        </template>
                        <template v-else>
                            <Users class="size-7 text-gray-400" />
                        </template>
                    </div>

                    <h3 class="mt-5 text-lg font-semibold text-gray-900 dark:text-white">
                        {{ search ? 'Aucun développeur trouvé' : 'Aucun développeur disponible' }}
                    </h3>

                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-500 dark:text-gray-400">
                        {{
                            search
                                ? 'Nous ne trouvons personne correspondant à ta recherche. Essaie un autre nom.'
                                : 'Les développeurs disponibles apparaîtront ici.'
                        }}
                    </p>

                    <button v-if="search" type="button"
                        class="mt-5 inline-flex items-center gap-2 rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800"
                        @click="clearSearch">
                        <X class="size-4" />
                        Effacer la recherche
                    </button>
                </div>

                <!-- Liste des développeurs -->
                <div v-else class="grid gap-3 sm:grid-cols-2">
                    <article v-for="developer in developers.data" :key="developer.id"
                        class="group flex min-w-0 flex-col rounded-2xl border border-gray-200 bg-white p-4 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-teal-300 hover:shadow-md dark:border-gray-800 dark:bg-gray-900 dark:hover:border-teal-800 sm:p-5">
                        <div class="flex min-w-0 items-start gap-3">
                            <!-- Avatar -->
                            <Link :href="`/developers/${developer.id}`"
                                class="relative flex size-12 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-linear-to-br from-teal-100 to-cyan-100 text-base font-bold text-teal-800 ring-1 ring-black/5 dark:from-teal-950 dark:to-cyan-950 dark:text-teal-300 dark:ring-white/10 sm:size-14"
                                :aria-label="`Voir le profil de ${developer.name}`">
                                <img v-if="developer.profile?.avatar_path"
                                    :src="`/storage/${developer.profile.avatar_path}`" :alt="developer.name"
                                    class="size-full object-cover" />
                                <span v-else>{{ getInitials(developer.name) || '?' }}</span>
                            </Link>

                            <!-- Informations -->
                            <div class="min-w-0 flex-1 pt-0.5">
                                <Link :href="`/developers/${developer.id}`"
                                    class="block truncate font-semibold text-gray-900 transition hover:text-teal-700 dark:text-white dark:hover:text-teal-400">
                                    {{ developer.name }}
                                </Link>

                                <p v-if="developer.profile?.headline"
                                    class="mt-1 line-clamp-2 text-sm leading-5 text-gray-500 dark:text-gray-400">
                                    {{ developer.profile.headline }}
                                </p>

                                <p v-else
                                    class="mt-1 flex items-center gap-1.5 text-xs text-gray-400 dark:text-gray-500">
                                    <Code2 class="size-3.5" />
                                    Développeur DevLink
                                </p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="mt-5 flex items-center gap-2 border-t border-gray-100 pt-4 dark:border-gray-800">
                            <Link :href="`/developers/${developer.id}`"
                                class="inline-flex min-w-0 flex-1 items-center justify-center gap-1.5 rounded-xl border border-gray-200 px-3 py-2.5 text-sm font-semibold text-gray-700 transition hover:border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">
                                <UserRound class="size-4 shrink-0" />
                                <span>Profil</span>
                            </Link>

                            <button type="button" :disabled="processingId !== null"
                                class="inline-flex min-w-0 flex-1 items-center justify-center gap-2 rounded-xl bg-teal-600 px-3 py-2.5 text-sm font-semibold text-white transition hover:bg-teal-700 focus:outline-none focus:ring-4 focus:ring-teal-500/20 disabled:cursor-not-allowed disabled:opacity-60"
                                @click="startConversation(developer)">
                                <LoaderCircle v-if="processingId === developer.id"
                                    class="size-4 shrink-0 animate-spin" />
                                <MessageCircle v-else class="size-4 shrink-0" />

                                <span>
                                    {{ processingId === developer.id ? 'Ouverture...' : 'Écrire' }}
                                </span>

                                <ArrowRight v-if="processingId !== developer.id"
                                    class="hidden size-3.5 shrink-0 sm:block" />
                            </button>
                        </div>
                    </article>
                </div>

                <!-- Pagination -->
                <nav v-if="developers.last_page > 1" class="mt-7 flex flex-wrap items-center justify-center gap-2"
                    aria-label="Pagination des développeurs">
                    <template v-for="(link, index) in developers.links" :key="index">
                        <Link v-if="link.url" :href="link.url" preserve-scroll
                            class="inline-flex min-h-10 items-center justify-center rounded-xl border px-3.5 py-2 text-sm font-medium transition"
                            :class="link.active
                                ? 'border-teal-600 bg-teal-600 text-white shadow-sm'
                                : 'border-gray-200 bg-white text-gray-600 hover:border-teal-300 hover:bg-teal-50 hover:text-teal-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-teal-800 dark:hover:bg-teal-950 dark:hover:text-teal-300'
                                " v-html="link.label" />

                        <span v-else
                            class="inline-flex min-h-10 items-center justify-center rounded-xl border border-gray-100 px-3.5 py-2 text-sm text-gray-300 dark:border-gray-800 dark:text-gray-600"
                            v-html="link.label" />
                    </template>
                </nav>
            </section>

            <!-- Note de bas de page -->
            <div
                class="mt-8 flex items-start gap-3 rounded-2xl border border-teal-100 bg-teal-50/70 p-4 dark:border-teal-900/60 dark:bg-teal-950/30">
                <div
                    class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-white text-teal-700 shadow-sm dark:bg-gray-900 dark:text-teal-300">
                    <MessageCircle class="size-4" />
                </div>

                <div>
                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                        Les meilleures collaborations commencent par une conversation.
                    </p>
                    <p class="mt-1 text-xs leading-5 text-gray-600 dark:text-gray-400">
                        Présente ton idée, pose une question ou fais connaissance avec d'autres membres de la
                        communauté.
                    </p>
                </div>
            </div>
        </div>
    </main>
</template>