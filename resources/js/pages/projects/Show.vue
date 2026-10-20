<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';

defineOptions({
    layout: PublicLayout,
});

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

const props = defineProps<{
    project: Project;
    canEdit?: boolean;
    canApply: boolean;
    existingOfferStatus: string | null;
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

                    <!-- Btn modifier -->
                    <div v-if="canEdit" class="mt-5 flex flex-wrap gap-3">
                        <Link :href="`/projects/${project.slug}/edit`"
                            class="inline-flex items-center justify-center rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-primary-foreground transition hover:opacity-90">
                            Modifier le projet
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
                            Présente-toi au propriétaire et explique comment tu pourrais contribuer.
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
    </div>
</template>
