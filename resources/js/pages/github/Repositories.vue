<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    Search,
    GitBranch,
    ExternalLink,
    LockKeyhole,
    Globe,
    RefreshCw,
    Code2,
    ArrowUpRight,
    FolderGit2,
    SlidersHorizontal,
    CircleCheck,
} from '@lucide/vue';

interface Repository {
    external_id: string;
    full_name: string;
    name: string;
    description: string | null;
    repository_url: string;
    default_branch: string | null;
    is_private: boolean;
    owner: string | null;
    associated_project_id: number | null;
    updated_at: string | null;
}

interface ProjectOption {
    id: number;
    name: string;
    slug: string;
}

function getAssociationForm(repository: Repository) {
    if (!associationForms.value[repository.external_id]) {
        associationForms.value[repository.external_id] = useForm({
            project_id: repository.associated_project_id
                ? String(repository.associated_project_id)
                : '',
            external_id: repository.external_id,
        });
    }

    return associationForms.value[repository.external_id];
}

function associateRepository(repository: Repository) {
    const form = getAssociationForm(repository);

    form.post('/github/repositories/associate', {
        preserveScroll: true,
    });
}

const associationForms = ref<Record<string, ReturnType<typeof useForm>>>({});

const props = defineProps<{
    repositories: Repository[];
    githubUsername: string;
    projects: ProjectOption[];
}>();

const search = ref('');
const visibility = ref<'all' | 'public' | 'private'>('all');
const sortBy = ref<'updated' | 'name'>('updated');

const filteredRepositories = computed(() => {
    const query = search.value.trim().toLowerCase();

    return [...props.repositories]
        .filter((repository) => {
            const matchesSearch =
                !query ||
                repository.name.toLowerCase().includes(query) ||
                repository.full_name.toLowerCase().includes(query) ||
                (repository.description ?? '').toLowerCase().includes(query);

            const matchesVisibility =
                visibility.value === 'all' ||
                (visibility.value === 'private' && repository.is_private) ||
                (visibility.value === 'public' && !repository.is_private);

            return matchesSearch && matchesVisibility;
        })
        .sort((a, b) => {
            if (sortBy.value === 'name') {
                return a.full_name.localeCompare(b.full_name);
            }

            return (
                new Date(b.updated_at ?? 0).getTime() -
                new Date(a.updated_at ?? 0).getTime()
            );
        });
});

const privateCount = computed(
    () => props.repositories.filter((repository) => repository.is_private).length,
);

const publicCount = computed(
    () => props.repositories.filter((repository) => !repository.is_private).length,
);

function formatDate(date: string | null): string {
    if (!date) {
        return 'Date inconnue';
    }

    const parsedDate = new Date(date);

    if (Number.isNaN(parsedDate.getTime())) {
        return 'Date inconnue';
    }

    return new Intl.DateTimeFormat('fr-FR', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    }).format(parsedDate);
}
</script>

<template>

    <Head title="Mes dépôts GitHub" />

    <div class="min-h-screen bg-gray-50 text-gray-900 dark:bg-gray-950 dark:text-gray-100">
        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <div class="mb-6 flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                <Link href="/dashboard" class="transition hover:text-teal-600 dark:hover:text-teal-400">
                    Dashboard
                </Link>

                <span>/</span>

                <span class="font-medium text-gray-900 dark:text-gray-100">
                    Dépôts GitHub
                </span>
            </div>

            <!-- Page heading -->
            <section
                class="relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900 sm:p-8">
                <div
                    class="pointer-events-none absolute -right-12 -top-16 h-56 w-56 rounded-full bg-teal-400/10 blur-3xl" />

                <div class="relative flex flex-col justify-between gap-6 sm:flex-row sm:items-center">
                    <div class="flex items-start gap-4">
                        <div
                            class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gray-900 text-white dark:bg-gray-100 dark:text-gray-900">
                            <Code2 class="h-7 w-7" />
                        </div>

                        <div>
                            <div class="mb-2 flex flex-wrap items-center gap-2">
                                <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">
                                    Mes dépôts GitHub
                                </h1>

                                <span
                                    class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">
                                    <CircleCheck class="h-3.5 w-3.5" />
                                    Connecté
                                </span>
                            </div>

                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Explore les dépôts accessibles depuis ton compte GitHub.
                            </p>

                            <p class="mt-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Compte :
                                <span class="text-teal-700 dark:text-teal-400">
                                    @{{ githubUsername }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <a :href="`https://github.com/${encodeURIComponent(githubUsername)}`" target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-semibold transition hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800">
                        Voir mon profil
                        <ArrowUpRight class="h-4 w-4" />
                    </a>
                </div>
            </section>

            <!-- Statistics -->
            <section class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            Tous les dépôts
                        </p>
                        <FolderGit2 class="h-5 w-5 text-teal-600 dark:text-teal-400" />
                    </div>

                    <p class="mt-3 text-3xl font-bold">
                        {{ repositories.length }}
                    </p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            Dépôts publics
                        </p>
                        <Globe class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                    </div>

                    <p class="mt-3 text-3xl font-bold">
                        {{ publicCount }}
                    </p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            Dépôts privés
                        </p>
                        <LockKeyhole class="h-5 w-5 text-amber-600 dark:text-amber-400" />
                    </div>

                    <p class="mt-3 text-3xl font-bold">
                        {{ privateCount }}
                    </p>
                </div>
            </section>

            <!-- Filters -->
            <section
                class="mt-8 rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 sm:p-5">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center">
                    <div class="relative flex-1">
                        <Search class="absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" />

                        <input v-model="search" type="search" placeholder="Rechercher un dépôt..."
                            aria-label="Rechercher un dépôt"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 py-3 pl-11 pr-4 text-sm outline-none transition placeholder:text-gray-400 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 dark:border-gray-700 dark:bg-gray-950" />
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <div class="relative">
                            <!-- <SlidersHorizontal
                                class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" /> -->

                            <select v-model="visibility" aria-label="Filtrer par visibilité"
                                class="w-full appearance-none rounded-xl border border-gray-200 bg-white py-3 pl-9 pr-8 text-sm font-medium outline-none focus:border-teal-500 dark:border-gray-700 dark:bg-gray-900 sm:w-44">
                                <option value="all">Toutes les visibilités</option>
                                <option value="public">Publics uniquement</option>
                                <option value="private">Privés uniquement</option>
                            </select>
                        </div>

                        <select v-model="sortBy" aria-label="Trier les dépôts"
                            class="rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-medium outline-none focus:border-teal-500 dark:border-gray-700 dark:bg-gray-900">
                            <option value="updated">Dernière mise à jour</option>
                            <option value="name">Nom du dépôt</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap items-center justify-between gap-2">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        <span class="font-semibold text-gray-900 dark:text-gray-100">
                            {{ filteredRepositories.length }}
                        </span>
                        dépôt(s) affiché(s)
                    </p>

                    <button v-if="search || visibility !== 'all' || sortBy !== 'updated'" type="button"
                        class="text-sm font-medium text-teal-700 hover:underline dark:text-teal-400" @click="
                            search = '';
                        visibility = 'all';
                        sortBy = 'updated';
                        ">
                        Réinitialiser les filtres
                    </button>
                </div>
            </section>

            <!-- Repository list -->
            <section class="mt-5">
                <div v-if="filteredRepositories.length" class="grid grid-cols-1 gap-4 xl:grid-cols-2">
                    <article v-for="repository in filteredRepositories" :key="repository.external_id"
                        class="group flex min-w-0 flex-col rounded-2xl border border-gray-200 bg-white p-5 transition duration-200 hover:border-teal-300 hover:shadow-md dark:border-gray-800 dark:bg-gray-900 dark:hover:border-teal-700 sm:p-6">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex min-w-0 items-start gap-3">
                                <div
                                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-teal-50 text-teal-700 dark:bg-teal-500/10 dark:text-teal-400">
                                    <FolderGit2 class="h-5 w-5" />
                                </div>

                                <div class="min-w-0">
                                    <a :href="repository.repository_url" target="_blank" rel="noopener noreferrer"
                                        class="wrap-break-word text-base font-bold text-gray-900 transition hover:text-teal-700 dark:text-gray-100 dark:hover:text-teal-400">
                                        {{ repository.full_name }}
                                    </a>

                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Mis à jour le {{ formatDate(repository.updated_at) }}
                                    </p>
                                </div>
                            </div>

                            <span v-if="repository.is_private"
                                class="inline-flex shrink-0 items-center gap-1 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-500/10 dark:text-amber-400">
                                <LockKeyhole class="h-3.5 w-3.5" />
                                Privé
                            </span>

                            <span v-else
                                class="inline-flex shrink-0 items-center gap-1 rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-500/10 dark:text-blue-400">
                                <Globe class="h-3.5 w-3.5" />
                                Public
                            </span>
                        </div>

                        <p class="mt-5 min-h-10 text-sm leading-6 text-gray-600 dark:text-gray-400">
                            {{
                                repository.description ||
                                'Aucune description pour ce dépôt.'
                            }}
                        </p>

                        <div
                            class="mt-5 flex flex-wrap items-center gap-x-4 gap-y-2 border-t border-gray-100 pt-4 text-xs text-gray-500 dark:border-gray-800 dark:text-gray-400">
                            <span v-if="repository.default_branch" class="inline-flex items-center gap-1.5">
                                <GitBranch class="h-4 w-4" />
                                {{ repository.default_branch }}
                            </span>

                            <span class="inline-flex items-center gap-1.5">
                                <Code2 class="h-4 w-4" />
                                {{ repository.owner || 'Propriétaire inconnu' }}
                            </span>
                        </div>

                        <div class="mt-5 flex flex-col gap-3 border-t border-gray-100 pt-4 dark:border-gray-800">
                            <template v-if="projects.length">

                                <label v-if="repository.associated_project_id"
                                    :for="`project-${repository.external_id}`"
                                    class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <p class="text-xs text-emerald-600 dark:text-emerald-400">
                                        Ce dépôt est déjà associé à un projet.
                                    </p>
                                </label>

                                <label v-else :for="`project-${repository.external_id}`"
                                    class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Associer à un projet DevColab
                                </label>

                                <select :id="`project-${repository.external_id}`"
                                    v-model="getAssociationForm(repository).project_id"
                                    class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-teal-500 dark:border-gray-700 dark:bg-gray-950">
                                    <option value="" disabled>Choisir un projet</option>

                                    <option v-for="project in projects" :key="project.id" :value="project.id">
                                        {{ project.name }}
                                    </option>
                                </select>

                                <p v-if="getAssociationForm(repository).errors.project_id" class="text-sm text-red-600">
                                    {{ getAssociationForm(repository).errors.project_id }}
                                </p>

                                <p v-if="getAssociationForm(repository).errors.external_id"
                                    class="text-sm text-red-600">
                                    {{ getAssociationForm(repository).errors.external_id }}
                                </p>

                                <button type="button" :disabled="!getAssociationForm(repository).project_id ||
                                    getAssociationForm(repository).processing
                                    "
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-teal-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-teal-700 disabled:cursor-not-allowed disabled:opacity-50"
                                    @click="associateRepository(repository)">
                                    {{
                                        getAssociationForm(repository).processing
                                            ? 'Association en cours…'
                                            : 'Associer au projet'
                                    }}
                                </button>
                            </template>

                            <p v-else class="text-sm text-gray-500 dark:text-gray-400">
                                Tu dois posséder un projet DevColab pour pouvoir y associer ce dépôt.
                            </p>

                            <a :href="repository.repository_url" target="_blank" rel="noopener noreferrer"
                                class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-semibold transition hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800">
                                Ouvrir le dépôt
                                <ExternalLink class="h-4 w-4" />
                            </a>
                        </div>
                    </article>
                </div>

                <!-- Empty state -->
                <div v-else
                    class="rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-16 text-center dark:border-gray-700 dark:bg-gray-900">
                    <div
                        class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                        <Search class="h-6 w-6" />
                    </div>

                    <h2 class="mt-5 text-lg font-bold">
                        {{
                            repositories.length
                                ? 'Aucun dépôt trouvé'
                                : 'Aucun dépôt accessible'
                        }}
                    </h2>

                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-500 dark:text-gray-400">
                        {{
                            repositories.length
                                ? 'Essaie de modifier ta recherche ou tes filtres.'
                                : 'Vérifie que ton compte GitHub possède des dépôts accessibles et que les autorisations ' +
                                'nécessaires ont été accordées.'
                        }}
                    </p>

                    <button v-if="repositories.length" type="button"
                        class="mt-5 inline-flex items-center gap-2 rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-semibold transition hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800"
                        @click="
                            search = '';
                        visibility = 'all';
                        sortBy = 'updated';
                        ">
                        <RefreshCw class="h-4 w-4" />
                        Effacer les filtres
                    </button>

                    <a v-else href="https://github.com" target="_blank" rel="noopener noreferrer"
                        class="mt-5 inline-flex items-center gap-2 rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-teal-700 dark:bg-gray-100 dark:text-gray-900">
                        Vérifier mon compte GitHub
                        <ArrowUpRight class="h-4 w-4" />
                    </a>
                </div>
            </section>

            <!-- Security note -->
            <div
                class="mt-6 flex items-start gap-3 rounded-xl border border-teal-200 bg-teal-50/70 p-4 dark:border-teal-900 dark:bg-teal-950/30">
                <LockKeyhole class="mt-0.5 h-5 w-5 shrink-0 text-teal-700 dark:text-teal-400" />

                <p class="text-sm leading-6 text-teal-900 dark:text-teal-200">
                    Cette page affiche les informations des dépôts accessibles à ton compte.
                    Elle ne modifie pas les dépôts sur GitHub. Les autorisations de l'application
                    seront affinées dans une étape ultérieure.
                </p>
            </div>
        </main>
    </div>
</template>