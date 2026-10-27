<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import {
    ArrowLeft,
    Check,
    Code2,
    Crown,
    Search,
    ShieldCheck,
    Trash2,
    UserPlus,
    Users,
    X,
} from '@lucide/vue';

interface Project {
    id: number;
    name: string;
    slug: string;
    owner_id: number;
}

interface TeamRole {
    value: string;
    label: string;
}

interface TeamMember {
    id: number;
    member_id: number;
    name: string;
    email: string | null;
    profile: {
        avatar_path?: string | null;
        headline?: string | null;
    } | null;
    role: string;
    joined_at: string | null;
}

const props = defineProps<{
    project: Project;
    members: TeamMember[];
    isOwner: boolean;
    roles: TeamRole[];
}>();

const search = ref('');
const showAddForm = ref(false);
const memberBeingEdited = ref<number | null>(null);
const roleBeingEdited = ref('');

const addForm = useForm({
    email: '',
    role: 'contributor',
});

const filteredMembers = computed(() => {
    const query = search.value.trim().toLowerCase();

    if (!query) {
        return props.members;
    }

    return props.members.filter((member) => {
        return (
            member.name.toLowerCase().includes(query) ||
            (member.profile?.headline ?? '').toLowerCase().includes(query) ||
            member.role.toLowerCase().includes(query)
        );
    });
});

const roleLabels: Record<string, string> = {
    contributor: 'Contributeur',
    maintainer: 'Mainteneur',
    manager: 'Responsable',
};

function roleLabel(role: string): string {
    return roleLabels[role] ?? role;
}

function roleDescription(role: string): string {
    const descriptions: Record<string, string> = {
        contributor: 'Participe au développement du projet.',
        maintainer: 'Contribue à la maintenance des ressources.',
        manager: 'Coordonne le travail de l’équipe.',
    };

    return descriptions[role] ?? 'Membre de l’équipe.';
}

function initials(name: string): string {
    return name
        .trim()
        .split(/\s+/)
        .slice(0, 2)
        .map((part) => part.charAt(0))
        .join('')
        .toUpperCase();
}

function formatDate(date: string | null): string {
    if (!date) {
        return 'Date inconnue';
    }

    const parsed = new Date(date);

    if (Number.isNaN(parsed.getTime())) {
        return 'Date inconnue';
    }

    return new Intl.DateTimeFormat('fr-FR', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    }).format(parsed);
}

function openRoleEditor(member: TeamMember): void {
    memberBeingEdited.value = member.member_id;
    roleBeingEdited.value = member.role;
}

function cancelRoleEditor(): void {
    memberBeingEdited.value = null;
    roleBeingEdited.value = '';
}

function updateRole(member: TeamMember): void {
    if (!roleBeingEdited.value || roleBeingEdited.value === member.role) {
        cancelRoleEditor();
        return;
    }

    router.patch(
        `/projects/${props.project.slug}/team/${member.member_id}`,
        { role: roleBeingEdited.value },
        {
            preserveScroll: true,
            onSuccess: () => cancelRoleEditor(),
        },
    );
}

function removeMember(member: TeamMember): void {
    if (
        !window.confirm(
            `Retirer ${member.name} de l’équipe de « ${props.project.name} » ?`,
        )
    ) {
        return;
    }

    router.delete(
        `/projects/${props.project.slug}/team/${member.member_id}`,
        { preserveScroll: true },
    );
}

function addMember(): void {
    addForm.post(`/projects/${props.project.slug}/team`, {
        preserveScroll: true,
        onSuccess: () => {
            addForm.reset();
            addForm.role = 'contributor';
            showAddForm.value = false;
        },
    });
}
</script>

<template>

    <Head :title="`Équipe — ${project.name}`" />

    <main class="min-h-screen bg-gray-50 pb-16 text-gray-900 dark:bg-gray-950 dark:text-gray-100">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Retour au projet -->
            <Link :href="`/projects/${project.slug}`"
                class="mb-7 inline-flex items-center gap-2 text-sm font-medium text-gray-500 transition hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400">
                <ArrowLeft class="size-4" />
                Retour au projet
            </Link>

            <!-- En-tête -->
            <section
                class="relative overflow-hidden rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900 sm:p-8">
                <div
                    class="pointer-events-none absolute -right-12 -top-20 size-64 rounded-full bg-indigo-500/10 blur-3xl" />

                <div class="relative flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div
                            class="mb-3 inline-flex items-center gap-2 rounded-full bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300">
                            <Code2 class="size-3.5" />
                            Équipe du projet
                        </div>

                        <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">
                            Construisons ensemble.
                        </h1>

                        <p class="mt-3 max-w-2xl text-sm leading-6 text-gray-600 dark:text-gray-400 sm:text-base">
                            Retrouvez les développeurs qui contribuent à
                            <span class="font-semibold text-gray-900 dark:text-white">
                                {{ project.name }}
                            </span>
                            et gérez l’organisation de votre équipe.
                        </p>
                    </div>

                    <button v-if="isOwner" type="button"
                        class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900"
                        @click="showAddForm = !showAddForm">
                        <X v-if="showAddForm" class="size-4" />
                        <UserPlus v-else class="size-4" />
                        {{ showAddForm ? 'Fermer' : 'Ajouter un membre' }}
                    </button>
                </div>

                <!-- Statistiques -->
                <div class="relative mt-8 grid gap-3 border-t border-gray-100 pt-6 dark:border-gray-800 sm:grid-cols-2">
                    <div class="flex items-center gap-3 rounded-2xl bg-gray-50 p-4 dark:bg-gray-800/60">
                        <div
                            class="flex size-11 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700 dark:bg-indigo-500/15 dark:text-indigo-300">
                            <Users class="size-5" />
                        </div>
                        <div>
                            <p class="text-2xl font-bold">{{ members.length }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ members.length > 1 ? 'Membres dans l’équipe' : 'Membre dans l’équipe' }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 rounded-2xl bg-gray-50 p-4 dark:bg-gray-800/60">
                        <div
                            class="flex size-11 items-center justify-center rounded-xl bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300">
                            <Crown class="size-5" />
                        </div>
                        <div>
                            <p class="font-semibold">Propriétaire du projet</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Responsable de la gestion de l’équipe
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Formulaire d'ajout -->
            <section v-if="isOwner && showAddForm"
                class="mt-6 rounded-3xl border border-indigo-200 bg-white p-6 shadow-sm dark:border-indigo-500/30 dark:bg-gray-900 sm:p-8">
                <div class="mb-6 flex items-start gap-3">
                    <div
                        class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700 dark:bg-indigo-500/15 dark:text-indigo-300">
                        <UserPlus class="size-5" />
                    </div>
                    <div>
                        <h2 class="text-lg font-bold">Inviter un développeur</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Ajoutez un compte DevLink à l’équipe de votre projet.
                        </p>
                    </div>
                </div>

                <form class="grid gap-5 md:grid-cols-2" @submit.prevent="addMember">
                    <div>
                        <label for="member-email" class="mb-2 block text-sm font-semibold">
                            Adresse e-mail
                        </label>
                        <input id="member-email" v-model="addForm.email" type="email" required autocomplete="email"
                            placeholder="developpeur@exemple.com"
                            class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition placeholder:text-gray-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 dark:border-gray-700 dark:bg-gray-950"
                            :class="{ 'border-red-500': addForm.errors.email }" />
                        <p v-if="addForm.errors.email" class="mt-2 text-sm text-red-600">
                            {{ addForm.errors.email }}
                        </p>
                    </div>

                    <div>
                        <label for="member-role" class="mb-2 block text-sm font-semibold">
                            Rôle dans le projet
                        </label>
                        <select id="member-role" v-model="addForm.role"
                            class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 dark:border-gray-700 dark:bg-gray-950">
                            <option v-for="role in roles" :key="role.value" :value="role.value">
                                {{ role.label }}
                            </option>
                        </select>
                        <p v-if="addForm.errors.role" class="mt-2 text-sm text-red-600">
                            {{ addForm.errors.role }}
                        </p>
                    </div>

                    <div
                        class="flex flex-col gap-3 border-t border-gray-100 pt-5 dark:border-gray-800 sm:flex-row sm:items-center sm:justify-between md:col-span-2">
                        <p class="text-xs leading-5 text-gray-500 dark:text-gray-400">
                            Le compte doit déjà exister sur DevLink.
                        </p>
                        <button type="submit" :disabled="addForm.processing"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60">
                            <Check class="size-4" />
                            {{ addForm.processing ? 'Ajout en cours…' : 'Ajouter à l’équipe' }}
                        </button>
                    </div>
                </form>
            </section>

            <!-- Liste des membres -->
            <section class="mt-8">
                <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="text-xl font-bold">Les membres</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Les personnes qui font avancer ce projet.
                        </p>
                    </div>

                    <div class="relative w-full sm:max-w-xs">
                        <Search class="absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-gray-400" />
                        <input v-model="search" type="search" placeholder="Rechercher un membre…"
                            aria-label="Rechercher un membre"
                            class="w-full rounded-xl border border-gray-200 bg-white py-2.5 pl-10 pr-4 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 dark:border-gray-800 dark:bg-gray-900" />
                    </div>
                </div>

                <div v-if="filteredMembers.length" class="grid gap-4 lg:grid-cols-2">
                    <article v-for="member in filteredMembers" :key="member.member_id"
                        class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:border-indigo-200 hover:shadow-md dark:border-gray-800 dark:bg-gray-900 dark:hover:border-indigo-500/30 sm:p-6">
                        <div class="flex items-start gap-4">
                            <img v-if="member.profile?.avatar_path" :src="`/storage/${member.profile.avatar_path}`"
                                :alt="`Photo de ${member.name}`" class="size-14 shrink-0 rounded-2xl object-cover" />
                            <div v-else
                                class="flex size-14 shrink-0 items-center justify-center rounded-2xl bg-linear-to-br from-indigo-500 to-violet-600 text-lg font-bold text-white">
                                {{ initials(member.name) }}
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="truncate font-bold">{{ member.name }}</h3>
                                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold" :class="{
                                        'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-300': member.role === 'contributor',
                                        'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300': member.role === 'maintainer',
                                        'bg-violet-50 text-violet-700 dark:bg-violet-500/10 dark:text-violet-300': member.role === 'manager',
                                    }">
                                        {{ roleLabel(member.role) }}
                                    </span>
                                </div>

                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    {{ member.profile?.headline || 'Développeur DevLink' }}
                                </p>

                                <p class="mt-3 text-xs text-gray-400 dark:text-gray-500">
                                    Membre depuis le {{ formatDate(member.joined_at) }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-5 rounded-xl bg-gray-50 px-4 py-3 dark:bg-gray-800/60">
                            <div class="flex items-center gap-2 text-sm font-semibold">
                                <ShieldCheck class="size-4 text-indigo-500" />
                                {{ roleLabel(member.role) }}
                            </div>
                            <p class="mt-1 pl-6 text-xs leading-5 text-gray-500 dark:text-gray-400">
                                {{ roleDescription(member.role) }}
                            </p>
                        </div>

                        <div v-if="isOwner"
                            class="mt-4 flex flex-col gap-3 border-t border-gray-100 pt-4 dark:border-gray-800 sm:flex-row">
                            <template v-if="memberBeingEdited === member.member_id">
                                <select v-model="roleBeingEdited" :aria-label="`Nouveau rôle de ${member.name}`"
                                    class="min-w-0 flex-1 rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-950">
                                    <option v-for="role in roles" :key="role.value" :value="role.value">
                                        {{ role.label }}
                                    </option>
                                </select>

                                <button type="button"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700"
                                    @click="updateRole(member)">
                                    <Check class="size-4" />
                                    Enregistrer
                                </button>

                                <button type="button"
                                    class="inline-flex items-center justify-center rounded-xl border border-gray-200 px-3 py-2.5 text-gray-500 transition hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800"
                                    aria-label="Annuler la modification du rôle" @click="cancelRoleEditor">
                                    <X class="size-4" />
                                </button>
                            </template>

                            <template v-else>
                                <button type="button"
                                    class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-semibold transition hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-700 dark:border-gray-700 dark:hover:bg-indigo-500/10 dark:hover:text-indigo-300"
                                    @click="openRoleEditor(member)">
                                    <ShieldCheck class="size-4" />
                                    Modifier le rôle
                                </button>

                                <button type="button"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-red-200 px-4 py-2.5 text-sm font-semibold text-red-600 transition hover:bg-red-50 dark:border-red-500/20 dark:text-red-400 dark:hover:bg-red-500/10"
                                    @click="removeMember(member)">
                                    <Trash2 class="size-4" />
                                    Retirer
                                </button>
                            </template>
                        </div>
                    </article>
                </div>

                <!-- Aucun résultat -->
                <div v-else
                    class="rounded-3xl border border-dashed border-gray-300 bg-white px-6 py-14 text-center dark:border-gray-700 dark:bg-gray-900">
                    <div
                        class="mx-auto flex size-14 items-center justify-center rounded-2xl bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                        <Users class="size-6" />
                    </div>

                    <h3 class="mt-4 text-lg font-bold">
                        {{ search ? 'Aucun résultat' : 'L’équipe est encore vide' }}
                    </h3>

                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-500 dark:text-gray-400">
                        {{
                            search
                                ? 'Essayez un autre nom ou un autre terme de recherche.'
                                : 'Ajoutez des développeurs pour commencer à collaborer sur ce projet.'
                        }}
                    </p>

                    <button v-if="isOwner && !search" type="button"
                        class="mt-5 inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700"
                        @click="showAddForm = true">
                        <UserPlus class="size-4" />
                        Ajouter le premier membre
                    </button>
                </div>
            </section>

            <!-- Rappel des rôles -->
            <section
                class="mt-10 rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center gap-3">
                    <div
                        class="flex size-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-300">
                        <ShieldCheck class="size-5" />
                    </div>
                    <div>
                        <h2 class="font-bold">Les rôles de l’équipe</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Chaque rôle correspond à un niveau de responsabilité.
                        </p>
                    </div>
                </div>

                <div class="mt-5 grid gap-3 sm:grid-cols-3">
                    <div v-for="role in roles" :key="role.value"
                        class="rounded-xl border border-gray-100 p-4 dark:border-gray-800">
                        <p class="text-sm font-semibold">{{ role.label }}</p>
                        <p class="mt-2 text-xs leading-5 text-gray-500 dark:text-gray-400">
                            {{ roleDescription(role.value) }}
                        </p>
                    </div>
                </div>
            </section>
        </div>
    </main>
</template>