<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import {
    Activity,
    AlertCircle,
    ArrowLeft,
    CalendarDays,
    Check,
    CheckCircle2,
    ChevronDown,
    CircleAlert,
    CircleCheck,
    Clock,
    Clock3,
    ListTodo,
    LoaderCircle,
    Pencil,
    Plus,
    Search,
    SlidersHorizontal,
    Trash2,
    UserRound,
    X,
} from '@lucide/vue';

type TeamMember = {
    id: number;
    name: string;
};

type Task = {
    id: number;
    title: string;
    description: string | null;
    status: string;
    priority: string;
    due_date: string | null;
    completed_at: string | null;
    created_at: string;
    creator: { id: number; name: string } | null;
    assignee: { id: number; name: string } | null;
};

type PaginatedTasks = {
    data: Task[];
    current_page: number;
    last_page: number;
    total: number;
    links: Array<{
        url: string | null;
        label: string;
        active: boolean;
    }>;
};

interface TaskActivity {
    id: number;
    action: string;
    description: string | null;
    old_values: Record<string, unknown> | null;
    new_values: Record<string, unknown> | null;
    created_at: string | null;
    user: {
        id: number;
        name: string;
    } | null;
    task: {
        id: number;
        title: string;
    } | null;
}

const props = defineProps<{
    project: {
        id: number;
        name: string;
        slug: string;
    };
    tasks: PaginatedTasks;
    teamMembers: TeamMember[];
    canManageTasks: boolean;
    filters?: {
        status?: string | null;
        priority?: string | null;
        search?: string | null;
    };
    activities: TaskActivity[];
}>();

const activityIcon = (action: string) => {
    if (action === 'created') return Plus;
    if (action === 'status_changed') return CheckCircle2;
    if (action === 'priority_changed') return AlertCircle;

    return Pencil;
};

const formatActivityDate = (date: string | null) => {
    if (!date) return 'Date inconnue';

    return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(new Date(date));
};

const statusOptions = [
    { value: 'todo', label: 'À faire' },
    { value: 'in_progress', label: 'En cours' },
    { value: 'in_review', label: 'En révision' },
    { value: 'done', label: 'Terminée' },
];

const priorityOptions = [
    { value: 'low', label: 'Basse' },
    { value: 'normal', label: 'Normale' },
    { value: 'high', label: 'Haute' },
    { value: 'urgent', label: 'Urgente' },
];

const statusStyles: Record<string, string> = {
    todo: 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
    in_progress: 'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300',
    in_review: 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300',
    done: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300',
};

const priorityStyles: Record<string, string> = {
    low: 'text-slate-500 dark:text-slate-400',
    normal: 'text-blue-600 dark:text-blue-400',
    high: 'text-orange-600 dark:text-orange-400',
    urgent: 'text-red-600 dark:text-red-400',
};

const statusLabels: Record<string, string> = {
    todo: 'À faire',
    in_progress: 'En cours',
    in_review: 'En révision',
    done: 'Terminée',
};

const priorityLabels: Record<string, string> = {
    low: 'Basse',
    normal: 'Normale',
    high: 'Haute',
    urgent: 'Urgente',
};

const search = ref(props.filters?.search ?? '');
const selectedStatus = ref(props.filters?.status ?? '');
const selectedPriority = ref(props.filters?.priority ?? '');
const showCreateForm = ref(false);
const editingTaskId = ref<number | null>(null);
const deletingTaskId = ref<number | null>(null);
const taskToDelete = ref<Task | null>(null);

const createForm = useForm({
    title: '',
    description: '',
    status: 'todo',
    priority: 'normal',
    assigned_to: '',
    due_date: '',
});

const editForm = useForm({
    title: '',
    description: '',
    status: 'todo',
    priority: 'normal',
    assigned_to: '',
    due_date: '',
});

const visibleTasks = computed(() => props.tasks.data);

const counts = computed(() => ({
    total: props.tasks.total,
    todo: props.tasks.data.filter((task) => task.status === 'todo').length,
    inProgress: props.tasks.data.filter((task) => task.status === 'in_progress').length,
    done: props.tasks.data.filter((task) => task.status === 'done').length,
}));

let searchTimer: ReturnType<typeof setTimeout> | undefined;

function applyFilters() {
    router.get(
        `/projects/${props.project.slug}/tasks`,
        {
            search: search.value || undefined,
            status: selectedStatus.value || undefined,
            priority: selectedPriority.value || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
}

function scheduleSearch() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(applyFilters, 350);
}

watch([selectedStatus, selectedPriority], applyFilters);

function resetFilters() {
    search.value = '';
    selectedStatus.value = '';
    selectedPriority.value = '';
    applyFilters();
}

function submitCreate() {
    createForm.post(`/projects/${props.project.slug}/tasks`, {
        preserveScroll: true,
        onSuccess: () => {
            createForm.reset();
            createForm.status = 'todo';
            createForm.priority = 'normal';
            showCreateForm.value = false;
        },
    });
}

function startEditing(task: Task) {
    editingTaskId.value = task.id;
    editForm.title = task.title;
    editForm.description = task.description ?? '';
    editForm.status = task.status;
    editForm.priority = task.priority;
    editForm.assigned_to = task.assignee ? String(task.assignee.id) : '';
    editForm.due_date = task.due_date ? task.due_date.slice(0, 10) : '';
}

function cancelEditing() {
    editingTaskId.value = null;
    editForm.reset();
    editForm.clearErrors();
}

function submitEdit(task: Task) {
    editForm.patch(`/projects/${props.project.slug}/tasks/${task.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            editingTaskId.value = null;
            editForm.reset();
        },
    });
}

function changeStatus(task: Task, status: string) {
    router.patch(
        `/projects/${props.project.slug}/tasks/${task.id}`,
        { status },
        { preserveScroll: true },
    );
}

function confirmDelete(task: Task) {
    taskToDelete.value = task;
}

function deleteTask() {
    if (!taskToDelete.value) return;

    deletingTaskId.value = taskToDelete.value.id;

    router.delete(
        `/projects/${props.project.slug}/tasks/${taskToDelete.value.id}`,
        {
            preserveScroll: true,
            onFinish: () => {
                deletingTaskId.value = null;
                taskToDelete.value = null;
            },
        },
    );
}

function formatDate(date: string | null) {
    if (!date) return null;

    const parsed = new Date(date);
    if (Number.isNaN(parsed.getTime())) return date;

    return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    }).format(parsed);
}

function isOverdue(task: Task) {
    if (!task.due_date || task.status === 'done') return false;

    const dueDate = new Date(task.due_date);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    dueDate.setHours(0, 0, 0, 0);

    return dueDate < today;
}

const auth = usePage().props.auth;
</script>

<template>

    <Head :title="`Tâches — ${project.name}`" />

    <div class="min-h-screen bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100">
        <div class="mx-auto max-w-7xl space-y-8 px-4 py-8 sm:px-6 lg:px-8">
            <!-- Navigation -->
            <div class="flex flex-wrap items-center justify-between gap-4">
                <Link :href="`/projects/${project.slug}`"
                    class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 transition hover:text-teal-600 dark:text-slate-400 dark:hover:text-teal-400">
                    <ArrowLeft class="size-4" />
                    Retour au projet
                </Link>

                <div
                    class="flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300">
                    <ListTodo class="size-4 text-teal-600 dark:text-teal-400" />
                    Espace de travail
                </div>
            </div>

            <!-- Header -->
            <section
                class="relative overflow-hidden rounded-3xl bg-linear-to-br from-slate-900 via-slate-800 to-teal-950 p-6 text-white shadow-lg sm:p-9">
                <div
                    class="pointer-events-none absolute -right-10 -top-20 size-64 rounded-full bg-teal-400/10 blur-3xl" />
                <div
                    class="pointer-events-none absolute -bottom-20 right-1/3 size-52 rounded-full bg-blue-400/10 blur-3xl" />

                <div class="relative flex flex-col justify-between gap-6 md:flex-row md:items-end">
                    <div class="max-w-2xl">
                        <div
                            class="mb-4 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-xs font-medium text-teal-100">
                            <ListTodo class="size-4" />
                            Gestion de projet
                        </div>

                        <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">
                            Tâches du projet
                        </h1>

                        <p class="mt-3 text-sm leading-6 text-slate-300 sm:text-base">
                            Organisez le travail, attribuez les responsabilités et suivez
                            la progression de votre équipe.
                        </p>

                        <p class="mt-4 text-sm font-medium text-teal-200">
                            {{ project.name }}
                        </p>
                    </div>

                    <button v-if="canManageTasks" type="button" @click="showCreateForm = !showCreateForm"
                        class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-teal-400 px-5 py-3 text-sm font-bold text-slate-950 shadow-lg shadow-teal-950/20 transition hover:bg-teal-300">
                        <X v-if="showCreateForm" class="size-4" />
                        <Plus v-else class="size-4" />
                        {{ showCreateForm ? 'Fermer le formulaire' : 'Nouvelle tâche' }}
                    </button>
                </div>
            </section>

            <!-- Statistics -->
            <section class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-sm font-medium text-slate-500 dark:text-slate-400">Total des tâches</span>
                        <ListTodo class="size-5 text-teal-600 dark:text-teal-400" />
                    </div>
                    <p class="mt-3 text-3xl font-bold">{{ counts.total }}</p>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Résultats selon les filtres</p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-sm font-medium text-slate-500 dark:text-slate-400">À faire</span>
                        <Clock3 class="size-5 text-slate-500" />
                    </div>
                    <p class="mt-3 text-3xl font-bold">{{ counts.todo }}</p>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Sur la page actuelle</p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-sm font-medium text-slate-500 dark:text-slate-400">En cours</span>
                        <LoaderCircle class="size-5 text-blue-500" />
                    </div>
                    <p class="mt-3 text-3xl font-bold">{{ counts.inProgress }}</p>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Sur la page actuelle</p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-sm font-medium text-slate-500 dark:text-slate-400">Terminées</span>
                        <CircleCheck class="size-5 text-emerald-500" />
                    </div>
                    <p class="mt-3 text-3xl font-bold">{{ counts.done }}</p>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Sur la page actuelle</p>
                </div>
            </section>

            <!-- Create task -->
            <section v-if="showCreateForm && canManageTasks"
                class="rounded-2xl border border-teal-200 bg-white p-5 shadow-sm dark:border-teal-900 dark:bg-slate-900 sm:p-7">
                <div class="mb-6 flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-bold">Créer une tâche</h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Définissez le travail à réaliser et la personne responsable.
                        </p>
                    </div>
                    <button type="button" aria-label="Fermer" @click="showCreateForm = false"
                        class="rounded-lg p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800">
                        <X class="size-5" />
                    </button>
                </div>

                <form @submit.prevent="submitCreate" class="space-y-5">
                    <div>
                        <label for="new-title" class="mb-2 block text-sm font-semibold">Titre *</label>
                        <input id="new-title" v-model="createForm.title" required maxlength="200"
                            placeholder="Ex. : Implémenter l'authentification"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-teal-500 focus:ring-2 focus:ring-teal-500/15 dark:border-slate-700 dark:bg-slate-950" />
                        <p v-if="createForm.errors.title" class="mt-1 text-sm text-red-500">{{ createForm.errors.title
                        }}</p>
                    </div>

                    <div>
                        <label for="new-description" class="mb-2 block text-sm font-semibold">Description</label>
                        <textarea id="new-description" v-model="createForm.description" rows="4"
                            placeholder="Décrivez les objectifs et les critères de réalisation..."
                            class="w-full resize-y rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-teal-500 focus:ring-2 focus:ring-teal-500/15 dark:border-slate-700 dark:bg-slate-950" />
                        <p v-if="createForm.errors.description" class="mt-1 text-sm text-red-500">{{
                            createForm.errors.description }}</p>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                        <div>
                            <label for="new-status" class="mb-2 block text-sm font-semibold">Statut</label>
                            <select id="new-status" v-model="createForm.status" class="form-select">
                                <option v-for="option in statusOptions" :key="option.value" :value="option.value">{{
                                    option.label }}</option>
                            </select>
                        </div>

                        <div>
                            <label for="new-priority" class="mb-2 block text-sm font-semibold">Priorité</label>
                            <select id="new-priority" v-model="createForm.priority" class="form-select">
                                <option v-for="option in priorityOptions" :key="option.value" :value="option.value">{{
                                    option.label }}</option>
                            </select>
                        </div>

                        <div>
                            <label for="new-assignee" class="mb-2 block text-sm font-semibold">Responsable</label>
                            <select id="new-assignee" v-model="createForm.assigned_to" class="form-select">
                                <option value="">Non attribuée</option>
                                <option v-for="member in teamMembers" :key="member.id" :value="String(member.id)">{{
                                    member.name }}</option>
                            </select>
                            <p v-if="createForm.errors.assigned_to" class="mt-1 text-sm text-red-500">{{
                                createForm.errors.assigned_to }}</p>
                        </div>

                        <div>
                            <label for="new-due-date" class="mb-2 block text-sm font-semibold">Échéance</label>
                            <input id="new-due-date" v-model="createForm.due_date" type="date" class="form-input" />
                            <p v-if="createForm.errors.due_date" class="mt-1 text-sm text-red-500">{{
                                createForm.errors.due_date }}</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap justify-end gap-3 border-t border-slate-100 pt-5 dark:border-slate-800">
                        <button type="button" @click="showCreateForm = false"
                            class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-800">
                            Annuler
                        </button>
                        <button type="submit" :disabled="createForm.processing"
                            class="inline-flex items-center gap-2 rounded-xl bg-teal-600 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-teal-700 disabled:opacity-60">
                            <LoaderCircle v-if="createForm.processing" class="size-4 animate-spin" />
                            <Plus v-else class="size-4" />
                            Créer la tâche
                        </button>
                    </div>
                </form>
            </section>

            <!-- Filters -->
            <section
                class="rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900 sm:p-5">
                <div class="mb-4 flex items-center gap-2">
                    <SlidersHorizontal class="size-4 text-teal-600 dark:text-teal-400" />
                    <h2 class="text-sm font-bold">Rechercher et filtrer</h2>
                </div>

                <div class="grid gap-3 md:grid-cols-2 lg:grid-cols-[minmax(220px,1fr)_200px_200px_auto]">
                    <div class="relative">
                        <Search class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-400" />
                        <input v-model="search" @input="scheduleSearch" placeholder="Rechercher une tâche..."
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-3 text-sm outline-none focus:border-teal-500 dark:border-slate-700 dark:bg-slate-950" />
                    </div>

                    <select v-model="selectedStatus" aria-label="Filtrer par statut" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option v-for="option in statusOptions" :key="option.value" :value="option.value">{{
                            option.label }}</option>
                    </select>

                    <select v-model="selectedPriority" aria-label="Filtrer par priorité" class="form-select">
                        <option value="">Toutes les priorités</option>
                        <option v-for="option in priorityOptions" :key="option.value" :value="option.value">{{
                            option.label }}</option>
                    </select>

                    <button type="button" @click="resetFilters"
                        class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                        Réinitialiser
                    </button>
                </div>
            </section>

            <!-- Task list -->
            <section class="space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h2 class="text-xl font-bold">Liste des tâches</h2>
                    <span class="text-sm text-slate-500 dark:text-slate-400">
                        {{ tasks.total }} résultat{{ tasks.total > 1 ? 's' : '' }}
                    </span>
                </div>

                <div v-if="visibleTasks.length === 0"
                    class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center dark:border-slate-700 dark:bg-slate-900">
                    <div
                        class="mx-auto flex size-14 items-center justify-center rounded-2xl bg-teal-50 text-teal-600 dark:bg-teal-950 dark:text-teal-400">
                        <ListTodo class="size-7" />
                    </div>
                    <h3 class="mt-4 text-lg font-bold">Aucune tâche trouvée</h3>
                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500 dark:text-slate-400">
                        Aucune tâche ne correspond aux filtres sélectionnés. Essayez de modifier votre recherche ou
                        créez une nouvelle tâche.
                    </p>
                    <button v-if="canManageTasks" type="button" @click="showCreateForm = true"
                        class="mt-5 inline-flex items-center gap-2 rounded-xl bg-teal-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-teal-700">
                        <Plus class="size-4" />
                        Créer une tâche
                    </button>
                </div>

                <article v-for="task in visibleTasks" :key="task.id"
                    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:border-slate-300 hover:shadow-md dark:border-slate-800 dark:bg-slate-900 dark:hover:border-slate-700">
                    <!-- Edit form -->
                    <form v-if="editingTaskId === task.id" @submit.prevent="submitEdit(task)"
                        class="space-y-5 p-5 sm:p-6">
                        <div class="flex items-center justify-between gap-3">
                            <h3 class="font-bold">Modifier la tâche</h3>
                            <button type="button" @click="cancelEditing"
                                class="rounded-lg p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800">
                                <X class="size-4" />
                            </button>
                        </div>

                        <div>
                            <label :for="`edit-title-${task.id}`" class="mb-2 block text-sm font-semibold">Titre
                                *</label>
                            <input :id="`edit-title-${task.id}`" v-model="editForm.title" required maxlength="200"
                                class="form-input" />
                            <p v-if="editForm.errors.title" class="mt-1 text-sm text-red-500">{{ editForm.errors.title
                            }}</p>
                        </div>

                        <div>
                            <label :for="`edit-description-${task.id}`"
                                class="mb-2 block text-sm font-semibold">Description</label>
                            <textarea :id="`edit-description-${task.id}`" v-model="editForm.description" rows="3"
                                class="form-input" />
                            <p v-if="editForm.errors.description" class="mt-1 text-sm text-red-500">{{
                                editForm.errors.description }}</p>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            <div>
                                <label :for="`edit-status-${task.id}`"
                                    class="mb-2 block text-sm font-semibold">Statut</label>
                                <select :id="`edit-status-${task.id}`" v-model="editForm.status" class="form-select">
                                    <option v-for="option in statusOptions" :key="option.value" :value="option.value">{{
                                        option.label }}</option>
                                </select>
                            </div>

                            <div>
                                <label :for="`edit-priority-${task.id}`"
                                    class="mb-2 block text-sm font-semibold">Priorité</label>
                                <select :id="`edit-priority-${task.id}`" v-model="editForm.priority"
                                    class="form-select">
                                    <option v-for="option in priorityOptions" :key="option.value" :value="option.value">
                                        {{ option.label }}</option>
                                </select>
                            </div>

                            <div>
                                <label :for="`edit-assignee-${task.id}`"
                                    class="mb-2 block text-sm font-semibold">Responsable</label>
                                <select :id="`edit-assignee-${task.id}`" v-model="editForm.assigned_to"
                                    class="form-select">
                                    <option value="">Non attribuée</option>
                                    <option v-for="member in teamMembers" :key="member.id" :value="String(member.id)">{{
                                        member.name }}</option>
                                </select>
                                <p v-if="editForm.errors.assigned_to" class="mt-1 text-sm text-red-500">{{
                                    editForm.errors.assigned_to }}</p>
                            </div>

                            <div>
                                <label :for="`edit-due-${task.id}`"
                                    class="mb-2 block text-sm font-semibold">Échéance</label>
                                <input :id="`edit-due-${task.id}`" v-model="editForm.due_date" type="date"
                                    class="form-input" />
                                <p v-if="editForm.errors.due_date" class="mt-1 text-sm text-red-500">{{
                                    editForm.errors.due_date }}</p>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 border-t border-slate-100 pt-4 dark:border-slate-800">
                            <button type="button" @click="cancelEditing"
                                class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold dark:border-slate-700">Annuler</button>
                            <button type="submit" :disabled="editForm.processing"
                                class="inline-flex items-center gap-2 rounded-xl bg-teal-600 px-4 py-2 text-sm font-bold text-white hover:bg-teal-700 disabled:opacity-60">
                                <LoaderCircle v-if="editForm.processing" class="size-4 animate-spin" />
                                <Check v-else class="size-4" />
                                Enregistrer
                            </button>
                        </div>
                    </form>

                    <!-- Task card -->
                    <div v-else class="p-5 sm:p-6">
                        <div class="flex flex-col justify-between gap-4 lg:flex-row lg:items-start">
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold"
                                        :class="statusStyles[task.status] ?? statusStyles.todo">
                                        {{ statusLabels[task.status] ?? task.status }}
                                    </span>

                                    <span class="inline-flex items-center gap-1 text-xs font-bold"
                                        :class="priorityStyles[task.priority] ?? priorityStyles.normal">
                                        <CircleAlert v-if="task.priority === 'urgent' || task.priority === 'high'"
                                            class="size-3.5" />
                                        {{ priorityLabels[task.priority] ?? task.priority }}
                                    </span>

                                    <span v-if="isOverdue(task)"
                                        class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-600 dark:bg-red-950 dark:text-red-300">
                                        En retard
                                    </span>
                                </div>

                                <h3 class="mt-3 wrap-break-word text-lg font-bold tracking-tight">
                                    {{ task.title }}
                                </h3>

                                <p v-if="task.description"
                                    class="mt-2 whitespace-pre-line wrap-break-word text-sm leading-6 text-slate-600 dark:text-slate-300">
                                    {{ task.description }}
                                </p>

                                <div
                                    class="mt-4 flex flex-wrap gap-x-5 gap-y-2 text-xs text-slate-500 dark:text-slate-400">
                                    <span class="inline-flex items-center gap-1.5">
                                        <UserRound class="size-3.5" />
                                        {{ task.assignee?.name ?? 'Non attribuée' }}
                                    </span>

                                    <span v-if="task.due_date" class="inline-flex items-center gap-1.5"
                                        :class="{ 'font-semibold text-red-600 dark:text-red-400': isOverdue(task) }">
                                        <CalendarDays class="size-3.5" />
                                        Échéance : {{ formatDate(task.due_date) }}
                                    </span>

                                    <span v-if="task.creator" class="text-slate-400">
                                        Créée par {{ task.creator.name }}
                                    </span>
                                </div>
                            </div>

                            <div v-if="canManageTasks || (task.assignee && task.assignee.id == auth?.user?.id)"
                                class="flex flex-wrap items-center gap-2 lg:justify-end">
                                <label class="sr-only" :for="`quick-status-${task.id}`">Changer le statut</label>
                                <div v-if="canManageTasks || (task.assignee && task.assignee.id == auth?.user?.id)"
                                    class="relative">
                                    <select :id="`quick-status-${task.id}`" :value="task.status"
                                        @change="changeStatus(task, ($event.target as HTMLSelectElement).value)"
                                        class="max-w-40 appearance-none rounded-xl border border-slate-200 bg-white py-2 pl-3 pr-8 text-xs font-semibold outline-none focus:border-teal-500 dark:border-slate-700 dark:bg-slate-950">
                                        <option v-for="option in statusOptions" :key="option.value"
                                            :value="option.value">{{ option.label }}</option>
                                    </select>
                                    <ChevronDown
                                        class="pointer-events-none absolute right-2.5 top-1/2 size-3.5 -translate-y-1/2 text-slate-400" />
                                </div>

                                <button v-if="canManageTasks || (task.assignee && task.assignee.id == auth?.user?.id)"
                                    type="button" @click="startEditing(task)"
                                    class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold transition hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-800">
                                    Modifier
                                </button>

                                <button v-if="canManageTasks" type="button" @click="confirmDelete(task)"
                                    :disabled="deletingTaskId === task.id" :aria-label="`Supprimer ${task.title}`"
                                    class="rounded-xl border border-red-200 p-2 text-red-600 transition hover:bg-red-50 disabled:opacity-50 dark:border-red-900 dark:hover:bg-red-950">
                                    <LoaderCircle v-if="deletingTaskId === task.id" class="size-4 animate-spin" />
                                    <Trash2 v-else class="size-4" />
                                </button>
                            </div>
                        </div>
                    </div>
                </article>

                <!-- Task Activities -->
                <section
                    class="mt-8 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900 sm:p-6">
                    <div class="mb-6 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                                <Activity class="h-5 w-5" />
                            </div>

                            <div>
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    Activité récente
                                </h2>

                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Historique des modifications des tâches
                                </p>
                            </div>
                        </div>

                        <span
                            class="rounded-full bg-gray-100 px-3 py-1 text-sm font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                            {{ props.activities.length }}
                        </span>
                    </div>

                    <div v-if="props.activities.length === 0"
                        class="rounded-xl border border-dashed border-gray-300 px-4 py-10 text-center dark:border-gray-700">
                        <Clock class="mx-auto mb-3 h-8 w-8 text-gray-400" />

                        <p class="font-medium text-gray-700 dark:text-gray-200">
                            Aucune activité pour le moment
                        </p>

                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Les prochaines actions sur les tâches apparaîtront ici.
                        </p>
                    </div>

                    <div v-else class="relative">
                        <div class="absolute bottom-3 left-5 top-3 w-px bg-gray-200 dark:bg-gray-700" />

                        <div class="space-y-6">
                            <article v-for="activity in props.activities" :key="activity.id"
                                class="relative flex gap-4">
                                <div
                                    class="relative z-10 flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-600 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                    <component :is="activityIcon(activity.action)" class="h-4 w-4" />
                                </div>

                                <div class="min-w-0 flex-1 pb-1">
                                    <p class="text-sm leading-6 text-gray-700 dark:text-gray-300">
                                        <span class="font-semibold text-gray-900 dark:text-white">
                                            {{ activity.user?.name ?? 'Utilisateur supprimé' }}
                                        </span>

                                        {{ activity.description ?? 'a effectué une modification.' }}
                                    </p>

                                    <p v-if="activity.task"
                                        class="mt-1 truncate text-sm font-medium text-indigo-600 dark:text-indigo-400">
                                        {{ activity.task.title }}
                                    </p>

                                    <div
                                        class="mt-2 flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                                        <Clock class="h-3.5 w-3.5" />

                                        <time :datetime="activity.created_at ?? undefined">
                                            {{ formatActivityDate(activity.created_at) }}
                                        </time>
                                    </div>
                                </div>
                            </article>
                        </div>
                    </div>
                </section>

                <!-- Pagination -->
                <nav v-if="tasks.last_page > 1" aria-label="Pagination des tâches"
                    class="flex flex-wrap items-center justify-center gap-2 pt-3">
                    <template v-for="(link, index) in tasks.links" :key="index">
                        <Link v-if="link.url" :href="link.url" preserve-scroll
                            class="rounded-lg border px-3 py-2 text-sm transition"
                            :class="link.active
                                ? 'border-teal-600 bg-teal-600 font-semibold text-white'
                                : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800'"
                            v-html="link.label" />
                        <span v-else
                            class="rounded-lg border border-slate-100 px-3 py-2 text-sm text-slate-300 dark:border-slate-800 dark:text-slate-600"
                            v-html="link.label" />
                    </template>
                </nav>
            </section>
        </div>

        <!-- Delete confirmation -->
        <div v-if="taskToDelete"
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm"
            @click.self="taskToDelete = null" @keydown.esc="taskToDelete = null">
            <div role="dialog" aria-modal="true" aria-labelledby="delete-task-title"
                class="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl dark:border-slate-800 dark:bg-slate-900">
                <div
                    class="flex size-12 items-center justify-center rounded-xl bg-red-50 text-red-600 dark:bg-red-950 dark:text-red-400">
                    <Trash2 class="size-6" />
                </div>

                <h2 id="delete-task-title" class="mt-4 text-lg font-bold">Supprimer cette tâche ?</h2>
                <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">
                    La tâche « {{ taskToDelete.title }} » sera supprimée définitivement. Cette action ne peut pas être
                    annulée.
                </p>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="taskToDelete = null"
                        class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold dark:border-slate-700">
                        Annuler
                    </button>
                    <button type="button" @click="deleteTask"
                        class="rounded-xl bg-red-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-red-700">
                        Supprimer
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.form-input,
.form-select {
    width: 100%;
    border: 1px solid rgb(226 232 240);
    border-radius: 0.75rem;
    background-color: rgb(248 250 252);
    padding: 0.7rem 0.85rem;
    font-size: 0.875rem;
    outline: none;
    transition: border-color 150ms, box-shadow 150ms;
}

/* Dark mode de .form-input,
.form-select */
@media (prefers-color-scheme: dark) {

    .form-input,
    .form-select {
        border-color: rgb(51 65 85);
        background-color: rgb(2 6 23);
        color: rgb(241 245 249);
    }
}


.form-input:focus,
.form-select:focus {
    border-color: rgb(20 184 166);
    box-shadow: 0 0 0 2px rgb(20 184 166 / 15%);
}

:global(.dark) .form-input,
:global(.dark) .form-select {
    border-color: rgb(51 65 85);
    background-color: rgb(2 6 23);
    color: rgb(241 245 249);
}

:global(.dark) .form-input:focus,
:global(.dark) .form-select:focus {
    border-color: rgb(45 212 191);
}

@media (prefers-reduced-motion: reduce) {

    *,
    *::before,
    *::after {
        transition-duration: 0.01ms !important;
        animation-duration: 0.01ms !important;
    }
}
</style>