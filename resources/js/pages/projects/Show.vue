<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import {
    ListTodo,
    Users,
    GitBranchIcon,
    GitBranch,
    ExternalLink,
    LockKeyhole,
    Globe,
    Trash2,
    Plus,
    X,
    AlertTriangle,
    GitCommit,
} from '@lucide/vue';
import { ref } from 'vue';

interface ProjectOwner {
    id: number;
    name: string;
}

interface Project {
    id: number;
    name: string;
    slug: string;
    description: string;
    short_description: string | null;
    status: string;
    visibility: string;
    repository_url: string | null;
    demo_url: string | null;
    cover_path: string | null;
    looking_for_collaborators: boolean;
    members_count: number;
    owner: ProjectOwner;
    created_at: string;
}

interface ProjectRepository {
    id: number;
    provider: string;
    full_name: string;
    name: string;
    description: string | null;
    repository_url: string;
    default_branch: string | null;
    is_private: boolean;
    connected_by: number;
    last_synced_at: string | null;
    created_at: string;
}

const props = defineProps<{
    project: Project;
    canEdit?: boolean;
    canApply: boolean;
    existingOfferStatus: string | null;
    repositories: ProjectRepository[];
}>();

const applicationForm = useForm({
    message: '',
    proposed_role: '',
});

function submitApplication() {
    applicationForm.post(
        `/projects/${props.project.slug}/collaboration-requests`,
        {
            preserveScroll: true,
            onSuccess: () => {
                applicationForm.reset();
            },
        },
    );
}

const repositoryToRemove = ref<ProjectRepository | null>(null);
const isRemovingRepository = ref(false);

function confirmRemoveRepository(repository: ProjectRepository) {
    repositoryToRemove.value = repository;
}

function cancelRemoveRepository() {
    if (isRemovingRepository.value) {
        return;
    }

    repositoryToRemove.value = null;
}

function removeRepository() {
    const repository = repositoryToRemove.value;

    if (!repository || isRemovingRepository.value) {
        return;
    }

    isRemovingRepository.value = true;

    router.delete(
        `/projects/${props.project.slug}/repositories/${repository.id}`,
        {
            preserveScroll: true,
            onFinish: () => {
                isRemovingRepository.value = false;
                repositoryToRemove.value = null;
            },
        },
    );
}

function projectStatus(status: string): string {
    const statuses: Record<string, string> = {
        planning: 'En préparation',
        in_progress: 'En développement',
        completed: 'Terminé',
        paused: 'En pause',
        active: 'Actif',
    };

    return statuses[status] || status;
}

const createdDate = new Date(props.project.created_at).toLocaleDateString(
    'fr-FR',
    {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    },
);
</script>

<template>

    <Head :title="`${project.name} — DevLink`" />

    <div class="min-h-screen">
        <!-- Fil d'Ariane -->
        <div class="border-b">
            <div class="mx-auto max-w-6xl px-4 py-4 text-sm text-muted-foreground sm:px-6 lg:px-8">
                <Link href="/" class="transition hover:text-primary">
                    Accueil
                </Link>

                <span class="mx-2">/</span>

                <Link href="/projects" class="transition hover:text-primary">
                    Projets
                </Link>

                <span class="mx-2">/</span>

                <span class="text-foreground">{{ project.name }}</span>
            </div>
        </div>

        <main class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
            <!-- Couverture -->
            <div
                class="relative flex min-h-56 items-center justify-center overflow-hidden rounded-3xl border bg-linear-to-br from-indigo-500/15 via-primary/10 to-violet-500/20 sm:min-h-80">
                <img v-if="project.cover_path" :src="`/storage/${project.cover_path}`"
                    :alt="`Couverture du projet ${project.name}`" class="absolute inset-0 h-full w-full object-cover" />

                <div v-else class="text-8xl font-black text-primary/50 sm:text-9xl" aria-hidden="true">
                    {{ project.name.trim().charAt(0).toUpperCase() }}
                </div>

                <div class="absolute inset-0 bg-linear-to-t from-background/70 to-transparent"></div>

                <span
                    class="absolute bottom-5 left-5 rounded-full border bg-background/90 px-4 py-2 text-sm font-semibold backdrop-blur">
                    {{ projectStatus(project.status) }}
                </span>
            </div>

            <div class="mt-8 grid gap-8 lg:grid-cols-[1fr_300px]">
                <!-- Présentation -->
                <article class="min-w-0">
                    <div class="flex flex-wrap items-center gap-3">
                        <span v-if="project.looking_for_collaborators"
                            class="rounded-full bg-emerald-500/10 px-3 py-1.5 text-sm font-semibold text-emerald-600 dark:text-emerald-400">
                            Recherche des collaborateurs
                        </span>

                        <span class="rounded-full bg-muted px-3 py-1.5 text-sm text-muted-foreground">
                            {{ project.members_count }}
                            {{
                                project.members_count > 1
                                    ? 'membres'
                                    : 'membre'
                            }}
                        </span>
                    </div>

                    <h1 class="mt-5 wrap-break-word text-3xl font-extrabold tracking-tight sm:text-4xl">
                        {{ project.name }}
                    </h1>

                    <p v-if="project.short_description" class="mt-4 text-lg leading-8 text-muted-foreground">
                        {{ project.short_description }}
                    </p>

                    <!-- Dépôts associés -->
                    <section
                        class="mt-8 rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900 sm:p-6">
                        <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                    Dépôts associés
                                </h2>

                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Le code source lié à ce projet.
                                </p>
                            </div>

                            <div class="flex items-center gap-3">
                                <Link v-if="canEdit" href="/github/repositories"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-primary-foreground transition hover:opacity-90">
                                    <Plus class="h-4 w-4" />
                                    Relier un dépôt GitHub
                                </Link>

                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-200"
                                    aria-hidden="true">
                                    <GitBranchIcon class="h-5 w-5" />
                                </div>
                            </div>
                        </div>

                        <div v-if="repositories.length" class="space-y-3">
                            <article v-for="repository in repositories" :key="repository.id"
                                class="rounded-xl border border-gray-200 p-4 transition hover:border-teal-300 dark:border-gray-800 dark:hover:border-teal-700">
                                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
                                    <div class="min-w-0 flex-1">
                                        <a :href="repository.repository_url" target="_blank" rel="noopener noreferrer"
                                            class="wrap-break-word font-semibold text-gray-900 hover:text-teal-700 dark:text-gray-100 dark:hover:text-teal-400">
                                            {{ repository.full_name }}
                                        </a>

                                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                            {{
                                                repository.description ||
                                                'Aucune description disponible.'
                                            }}
                                        </p>

                                        <div
                                            class="mt-3 flex flex-wrap items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                                            <span class="inline-flex items-center gap-1.5">
                                                <GitBranch class="h-3.5 w-3.5" />
                                                {{
                                                    repository.default_branch ||
                                                    'Branche inconnue'
                                                }}
                                            </span>

                                            <span v-if="repository.is_private" class="inline-flex items-center gap-1.5">
                                                <LockKeyhole class="h-3.5 w-3.5" />
                                                Privé
                                            </span>

                                            <span v-else class="inline-flex items-center gap-1.5">
                                                <Globe class="h-3.5 w-3.5" />
                                                Public
                                            </span>

                                            <span class="inline-flex items-center gap-1.5">
                                                {{
                                                    repository.last_synced_at
                                                        ? `Synchronisé le ${new
                                                            Date(repository.last_synced_at).toLocaleString('fr-FR', {
                                                                dateStyle: 'short',
                                                                timeStyle: 'short',
                                                            })}`
                                                        : 'Jamais synchronisé'
                                                }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex shrink-0 flex-wrap items-center gap-2">
                                        <a :href="repository.repository_url" target="_blank" rel="noopener noreferrer"
                                            class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium transition hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800">
                                            Ouvrir
                                            <ExternalLink class="h-4 w-4" />
                                        </a>

                                        <button v-if="canEdit" type="button"
                                            class="inline-flex items-center justify-center gap-2 rounded-lg border border-teal-200 px-3 py-2 text-sm font-medium text-teal-700 transition hover:bg-teal-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-teal-900 dark:text-teal-400 dark:hover:bg-teal-950/30"
                                            @click="
                                                router.post(
                                                    `/projects/${project.slug}/repositories/${repository.id}/sync`,
                                                    {},
                                                    { preserveScroll: true }
                                                )
                                                ">
                                            <GitBranch class="h-4 w-4" />
                                            Synchroniser
                                        </button>

                                        <Link v-if="canEdit"
                                            :href="`/projects/${project.slug}/repositories/${repository.id}/commits`"
                                            class="inline-flex items-center justify-center gap-2 rounded-lg border border-indigo-200 px-3 py-2 text-sm font-medium text-indigo-700 transition hover:bg-indigo-50 dark:border-indigo-900 dark:text-indigo-400 dark:hover:bg-indigo-950/30">
                                            <GitCommit class="h-4 w-4" />
                                            Commits
                                        </Link>

                                        <button v-if="canEdit" type="button"
                                            class="inline-flex items-center justify-center gap-2 rounded-lg border border-red-200 px-3 py-2 text-sm font-medium text-red-600 transition hover:bg-red-50 dark:border-red-900 dark:hover:bg-red-950/30"
                                            @click="confirmRemoveRepository(repository)">
                                            <Trash2 class="h-4 w-4" />
                                            Retirer
                                        </button>
                                    </div>
                                </div>
                            </article>
                        </div>

                        <div v-else
                            class="rounded-xl border border-dashed border-gray-300 px-5 py-10 text-center dark:border-gray-700">
                            <GitBranchIcon class="mx-auto h-8 w-8 text-gray-400" />

                            <p class="mt-3 font-medium text-gray-800 dark:text-gray-200">
                                Aucun dépôt associé
                            </p>

                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Les dépôts liés au projet apparaîtront ici.
                            </p>

                            <Link v-if="canEdit" href="/github/repositories"
                                class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-primary-foreground transition hover:opacity-90">
                                <Plus class="h-4 w-4" />
                                Relier mon premier dépôt
                            </Link>
                        </div>
                    </section>

                    <div class="mt-8 border-t pt-8">
                        <h2 class="text-xl font-bold">
                            À propos du projet
                        </h2>

                        <p class="mt-4 whitespace-pre-line wrap-break-word leading-8 text-muted-foreground">
                            {{
                                project.description ||
                                'Aucune description disponible pour le moment.'
                            }}
                        </p>
                    </div>

                    <!-- Boutons de gestion du projet -->
                    <div v-if="canEdit" class="mt-5 flex flex-wrap gap-3">
                        <Link :href="`/projects/${project.slug}/edit`"
                            class="inline-flex items-center justify-center rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-primary-foreground transition hover:opacity-90">
                            Modifier le projet
                        </Link>

                        <Link :href="`/projects/${project.slug}/team`"
                            class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-700 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-indigo-500/10">
                            <Users class="size-4" />
                            Équipe
                            <span
                                class="rounded-full bg-indigo-100 px-2 py-0.5 text-xs font-bold text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300">
                                {{ project.members_count ?? 0 }}
                            </span>
                        </Link>

                        <Link :href="`/projects/${project.slug}/tasks`"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold transition hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-800">
                            <ListTodo class="size-4" />
                            Tâches du projet
                        </Link>

                        <Link :href="`/projects/${project.slug}/collaboration-requests`"
                            class="inline-flex items-center justify-center rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-primary-foreground transition hover:opacity-90">
                            Voir les candidatures
                        </Link>

                        <Link href="/my-projects"
                            class="inline-flex items-center justify-center rounded-xl border px-5 py-3 text-sm font-semibold transition hover:bg-muted">
                            Mes projets
                        </Link>
                    </div>
                </article>

                <!-- Informations complémentaires -->
                <aside class="space-y-5">
                    <section class="rounded-2xl border bg-card p-5">
                        <h2 class="font-bold">Le projet</h2>

                        <div class="mt-5">
                            <p class="text-sm text-muted-foreground">
                                Créé par
                            </p>

                            <Link :href="`/developers/${project.owner.id}`"
                                class="mt-1 inline-block font-semibold transition hover:text-primary">
                                {{ project.owner.name }}
                            </Link>
                        </div>

                        <div class="mt-5">
                            <p class="text-sm text-muted-foreground">
                                Date de création
                            </p>

                            <p class="mt-1 font-medium">
                                {{ createdDate }}
                            </p>
                        </div>

                        <div class="mt-5">
                            <p class="text-sm text-muted-foreground">
                                Visibilité
                            </p>

                            <p class="mt-1 font-medium">
                                Public
                            </p>
                        </div>
                    </section>

                    <section class="rounded-2xl border bg-card p-5">
                        <h2 class="font-bold">Liens du projet</h2>

                        <div class="mt-4 space-y-3">
                            <a v-if="project.repository_url" :href="project.repository_url" target="_blank"
                                rel="noopener noreferrer"
                                class="flex items-center justify-center gap-2 rounded-xl border px-4 py-3 text-sm font-semibold transition hover:bg-muted">
                                Voir le dépôt source
                                <span aria-hidden="true">↗</span>
                            </a>

                            <a v-if="project.demo_url" :href="project.demo_url" target="_blank"
                                rel="noopener noreferrer"
                                class="flex items-center justify-center gap-2 rounded-xl bg-primary px-4 py-3 text-sm font-semibold text-primary-foreground transition hover:opacity-90">
                                Découvrir la démo
                                <span aria-hidden="true">↗</span>
                            </a>

                            <p v-if="
                                !project.repository_url &&
                                !project.demo_url
                            " class="text-sm leading-6 text-muted-foreground">
                                Aucun lien externe n'a encore été ajouté à ce
                                projet.
                            </p>
                        </div>
                    </section>

                    <section v-if="canApply && project.looking_for_collaborators"
                        class="rounded-2xl border border-primary/30 bg-primary/5 p-5">
                        <h2 class="font-bold">
                            Envie de participer ?
                        </h2>
                    </section>

                    <!-- Collaborer -->
                    <section v-if="canApply" class="rounded-2xl border border-primary/30 bg-primary/5 p-5">
                        <h2 class="text-xl font-bold">
                            Collaborer sur ce projet
                        </h2>

                        <p class="mt-2 text-sm">
                            Présente-toi au propriétaire et explique comment
                            tu pourrais contribuer.
                        </p>

                        <form class="mt-5 space-y-5" @submit.prevent="submitApplication">
                            <div>
                                <label for="proposed_role" class="mb-2 block text-sm font-medium text-gray-700">
                                    Rôle souhaité
                                </label>

                                <input id="proposed_role" v-model="applicationForm.proposed_role" type="text"
                                    maxlength="100" placeholder="Ex. : Développeur Vue.js"
                                    class="w-full rounded-xl border border-gray-300 px-4 py-3 outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-100" />

                                <p v-if="applicationForm.errors.proposed_role" class="mt-1 text-sm text-red-600">
                                    {{ applicationForm.errors.proposed_role }}
                                </p>
                            </div>

                            <div>
                                <label for="application_message" class="mb-2 block text-sm font-medium text-gray-700">
                                    Message de candidature
                                </label>

                                <textarea id="application_message" v-model="applicationForm.message" rows="5"
                                    minlength="10" maxlength="3000" required
                                    placeholder="Présente tes compétences, ta motivation et ce que tu pourrais apporter au projet..."
                                    class="w-full rounded-xl border border-gray-300 px-4 py-3 outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-100" />

                                <p v-if="applicationForm.errors.message" class="mt-1 text-sm text-red-600">
                                    {{ applicationForm.errors.message }}
                                </p>
                            </div>

                            <button type="submit" :disabled="applicationForm.processing"
                                class="rounded-xl bg-teal-600 px-5 py-3 font-semibold text-white transition hover:bg-teal-700 disabled:cursor-not-allowed disabled:opacity-50">
                                {{
                                    applicationForm.processing
                                        ? 'Envoi en cours...'
                                        : 'Envoyer ma candidature'
                                }}
                            </button>
                        </form>
                    </section>

                    <div v-else-if="existingOfferStatus === 'pending'"
                        class="mt-8 rounded-2xl border border-amber-200 bg-amber-50 p-5">
                        <p class="font-semibold text-amber-900">
                            Ta demande est en attente de réponse.
                        </p>
                    </div>

                    <div v-else-if="existingOfferStatus === 'accepted'"
                        class="mt-8 rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
                        <p class="font-semibold text-emerald-900">
                            Tu as été accepté dans ce projet !
                        </p>
                    </div>

                    <Link href="/projects"
                        class="inline-flex items-center gap-2 text-sm font-semibold text-primary hover:underline">
                        ← Retour aux projets
                    </Link>
                </aside>
            </div>
        </main>

        <!-- Boîte de dialogue de confirmation -->
        <Teleport to="body">
            <div v-if="repositoryToRemove" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
                role="presentation" @keydown.esc="cancelRemoveRepository" @click.self="cancelRemoveRepository">
                <section role="alertdialog" aria-modal="true" aria-labelledby="remove-repository-title"
                    aria-describedby="remove-repository-description"
                    class="w-full max-w-md rounded-2xl border border-gray-200 bg-white p-6 shadow-2xl dark:border-gray-700 dark:bg-gray-900">
                    <div class="flex items-start gap-4">
                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600 dark:bg-red-950/50 dark:text-red-400">
                            <AlertTriangle class="h-5 w-5" />
                        </div>

                        <div class="min-w-0 flex-1">
                            <h2 id="remove-repository-title" class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                Retirer ce dépôt ?
                            </h2>

                            <p id="remove-repository-description"
                                class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300">
                                Tu vas retirer
                                <strong class="wrap-break-word">
                                    {{ repositoryToRemove.full_name }}
                                </strong>
                                des dépôts associés à ce projet DevLink.
                                <strong>Le dépôt GitHub ne sera pas supprimé.</strong>
                            </p>
                        </div>

                        <button type="button"
                            class="rounded-lg p-1 text-gray-500 transition hover:bg-gray-100 hover:text-gray-800 disabled:opacity-50 dark:hover:bg-gray-800 dark:hover:text-gray-200"
                            aria-label="Fermer la boîte de dialogue" :disabled="isRemovingRepository"
                            @click="cancelRemoveRepository">
                            <X class="h-5 w-5" />
                        </button>
                    </div>

                    <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                        <button type="button" :disabled="isRemovingRepository"
                            class="inline-flex items-center justify-center rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800"
                            @click="cancelRemoveRepository">
                            Annuler
                        </button>

                        <button type="button" :disabled="isRemovingRepository"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-50"
                            @click="removeRepository">
                            <Trash2 class="h-4 w-4" />
                            {{
                                isRemovingRepository
                                    ? 'Retrait en cours...'
                                    : 'Confirmer le retrait'
                            }}
                        </button>
                    </div>
                </section>
            </div>
        </Teleport>
    </div>
</template>