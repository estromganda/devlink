<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
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
        { search: search.value || undefined },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
}

function startConversation(developer: Developer) {
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
</script>

<template>

    <Head title="Nouvelle conversation" />

    <main class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
        <Link href="/messages" class="text-sm font-medium text-teal-700 hover:text-teal-900">
            ← Retour à la messagerie
        </Link>

        <h1 class="mt-4 text-3xl font-bold tracking-tight text-gray-900">
            Nouvelle conversation
        </h1>

        <p class="mt-2 text-gray-600">
            Choisis un développeur pour démarrer un échange privé.
        </p>

        <form class="mt-6 flex flex-col gap-3 sm:flex-row" @submit.prevent="searchDevelopers">
            <input v-model="search" type="search" placeholder="Rechercher par nom..."
                class="min-w-0 flex-1 rounded-xl border border-gray-300 px-4 py-3 outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-100" />

            <button type="submit" class="rounded-xl bg-gray-900 px-5 py-3 font-semibold text-white hover:bg-gray-800">
                Rechercher
            </button>
        </form>

        <div v-if="developers.data.length === 0"
            class="mt-8 rounded-2xl border border-dashed border-gray-300 bg-white p-10 text-center">
            <h2 class="font-semibold text-gray-900">
                Aucun développeur trouvé
            </h2>

            <p class="mt-2 text-sm text-gray-600">
                Essaie un autre nom.
            </p>
        </div>

        <div v-else class="mt-8 overflow-hidden rounded-2xl border border-gray-200 bg-white">
            <div v-for="developer in developers.data" :key="developer.id"
                class="flex items-center gap-4 border-b border-gray-100 p-5 last:border-b-0">
                <img v-if="developer.profile?.avatar_path" :src="`/storage/${developer.profile.avatar_path}`"
                    :alt="developer.name" class="h-12 w-12 rounded-full object-cover" />

                <div v-else
                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-teal-100 text-lg font-bold text-teal-800">
                    {{ developer.name.charAt(0).toUpperCase() }}
                </div>

                <div class="min-w-0 flex-1">
                    <Link :href="`/developers/${developer.id}`" class="font-semibold text-gray-900 hover:text-teal-700">
                        {{ developer.name }}
                    </Link>

                    <p v-if="developer.profile?.headline" class="mt-1 truncate text-sm text-gray-500">
                        {{ developer.profile.headline }}
                    </p>
                </div>

                <button type="button" :disabled="processingId !== null"
                    class="shrink-0 rounded-xl bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700 disabled:opacity-50"
                    @click="startConversation(developer)">
                    {{
                        processingId === developer.id
                            ? 'Ouverture...'
                            : 'Écrire'
                    }}
                </button>
            </div>
        </div>

        <nav v-if="developers.last_page > 1" class="mt-6 flex flex-wrap justify-center gap-2" aria-label="Pagination">
            <template v-for="(link, index) in developers.links" :key="index">
                <Link v-if="link.url" :href="link.url" class="rounded-lg border px-3 py-2 text-sm" :class="link.active
                    ? 'border-teal-600 bg-teal-600 text-white'
                    : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50'
                    " v-html="link.label" />

                <span v-else class="rounded-lg border border-gray-100 px-3 py-2 text-sm text-gray-300"
                    v-html="link.label" />
            </template>
        </nav>
    </main>
</template>
