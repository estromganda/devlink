<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

interface NotificationItem {
    id: string
    type: string
    data: {
        type?: string
        title?: string
        message?: string
        url?: string
        actor_name?: string
        question_title?: string
    }
    read_at: string | null
    created_at: string
}

interface PaginationLink {
    url: string | null
    label: string
    active: boolean
}

interface PaginatedNotifications {
    data: NotificationItem[]
    links: PaginationLink[]
}

const props = defineProps<{
    notifications: PaginatedNotifications
    unread_count: number
}>()

const processingId = ref<string | null>(null)
const processingAll = ref(false)

const unreadNotifications = computed(() =>
    props.notifications.data.filter((notification) => !notification.read_at),
)

function markAsRead(notification: NotificationItem) {
    if (notification.read_at || processingId.value) {
        return
    }

    processingId.value = notification.id

    router.patch(
        `/notifications/${notification.id}/read`,
        {},
        {
            preserveScroll: true,
            onFinish: () => {
                processingId.value = null
            },
        },
    )
}

function openNotification(notification: NotificationItem) {
    const destination = notification.data.url || '/notifications'

    if (notification.read_at) {
        router.visit(destination)
        return
    }

    processingId.value = notification.id

    router.patch(
        `/notifications/${notification.id}/read`,
        {},
        {
            preserveScroll: true,
            onSuccess: () => router.visit(destination),
            onFinish: () => {
                processingId.value = null
            },
        },
    )
}

function markAllAsRead() {
    if (props.unread_count === 0 || processingAll.value) {
        return
    }

    processingAll.value = true

    router.patch('/notifications/read-all', {}, {
        preserveScroll: true,
        onFinish: () => {
            processingAll.value = false
        },
    })
}

function formatDate(date: string): string {
    return new Intl.DateTimeFormat('fr-FR', {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(new Date(date))
}
</script>

<template>

    <Head title="Notifications" />

    <div class="min-h-screen bg-gray-50 py-8 dark:bg-gray-950">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <header class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-gray-100">
                        Notifications
                    </h1>

                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Reste au courant de ce qui se passe dans ta communauté.
                    </p>

                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ unread_count }} notification(s) non lue(s)
                    </p>
                </div>

                <button type="button" :disabled="unread_count === 0 || processingAll"
                    class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800"
                    @click="markAllAsRead">
                    {{ processingAll ? 'Traitement...' : 'Tout marquer comme lu' }}
                </button>
            </header>

            <section
                class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900"
                aria-label="Liste des notifications">
                <div v-if="notifications.data.length === 0" class="px-6 py-16 text-center">
                    <div class="text-4xl" aria-hidden="true">🔔</div>

                    <h2 class="mt-4 text-lg font-semibold text-gray-900 dark:text-gray-100">
                        Aucune notification
                    </h2>

                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Tes nouvelles notifications apparaîtront ici.
                    </p>
                </div>

                <ul v-else class="divide-y divide-gray-200 dark:divide-gray-800">
                    <li v-for="notification in notifications.data" :key="notification.id" class="transition" :class="notification.read_at
                        ? 'bg-white dark:bg-gray-900'
                        : 'bg-teal-50/70 dark:bg-teal-950/20'">
                        <div class="flex items-start gap-4 p-5 sm:p-6">
                            <span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full" :class="notification.read_at
                                ? 'bg-gray-300 dark:bg-gray-700'
                                : 'bg-teal-600'" :aria-label="notification.read_at ? 'Lue' : 'Non lue'" />

                            <button type="button" class="min-w-0 flex-1 text-left"
                                :disabled="processingId === notification.id" @click="openNotification(notification)">
                                <span class="block font-semibold text-gray-900 dark:text-gray-100">
                                    {{ notification.data.title || 'Nouvelle notification' }}
                                </span>

                                <span class="mt-1 block text-sm leading-6 text-gray-600 dark:text-gray-400">
                                    {{ notification.data.message || 'Tu as une nouvelle notification.' }}
                                </span>

                                <time :datetime="notification.created_at"
                                    class="mt-2 block text-xs text-gray-500 dark:text-gray-500">
                                    {{ formatDate(notification.created_at) }}
                                </time>
                            </button>

                            <button v-if="!notification.read_at" type="button"
                                class="shrink-0 text-sm font-medium text-teal-700 hover:text-teal-900 disabled:opacity-50 dark:text-teal-400 dark:hover:text-teal-300"
                                :disabled="processingId === notification.id" @click="markAsRead(notification)">
                                {{ processingId === notification.id ? '...' : 'Marquer comme lue' }}
                            </button>
                        </div>
                    </li>
                </ul>
            </section>

            <nav v-if="notifications.links.length > 3" aria-label="Pagination des notifications"
                class="mt-6 flex flex-wrap justify-center gap-2">
                <template v-for="(link, index) in notifications.links" :key="index">
                    <Link v-if="link.url" :href="link.url" preserve-scroll
                        class="rounded-lg border px-3 py-2 text-sm transition"
                        :class="link.active
                            ? 'border-teal-600 bg-teal-600 text-white'
                            : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800'"
                        v-html="link.label" />

                    <span v-else
                        class="cursor-not-allowed rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 text-sm text-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-600"
                        v-html="link.label" />
                </template>
            </nav>

            <div class="mt-6">
                <Link href="/"
                    class="text-sm font-medium text-teal-700 hover:text-teal-900 dark:text-teal-400 dark:hover:text-teal-300">
                    ← Retour à l'accueil
                </Link>
            </div>
        </div>
    </div>
</template>