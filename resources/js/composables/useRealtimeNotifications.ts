import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

type RealtimeNotification = {
    id?: string;
    type?: string;
    title?: string;
    message?: string;
    url?: string;
    [key: string]: unknown;
};

type EchoChannel = {
    notification: (
        callback: (notification: RealtimeNotification) => void,
    ) => EchoChannel;
};

type EchoInstance = {
    private: (channel: string) => EchoChannel;
    leave: (channel: string) => void;
};

// declare global {
//     interface Window {
//         Echo?: EchoInstance;
//     }
// }

const unreadCount = ref<number | null>(null);
const latestNotifications = ref<RealtimeNotification[]>([]);
const receivedIds = new Set<string>();

let activeUserId: string | null = null;
let activeSubscribers = 0;
let subscribedChannel: string | null = null;
let notificationHandler:
    | ((notification: RealtimeNotification) => void)
    | null = null;

function subscribeToUser(userId: string) {
    if (activeUserId === userId && subscribedChannel) {
        return;
    }

    if (subscribedChannel && window.Echo) {
        window.Echo.leave(subscribedChannel);
    }

    activeUserId = userId;
    subscribedChannel = null;
    notificationHandler = null;

    if (!window.Echo) {
        return;
    }

    const channelName = `App.Models.User.${userId}`;

    notificationHandler = (notification) => {
        const id = notification.id;

        if (typeof id === 'string' && receivedIds.has(id)) {
            return;
        }

        if (typeof id === 'string') {
            receivedIds.add(id);
        }

        unreadCount.value = (unreadCount.value ?? 0) + 1;
        latestNotifications.value = [
            notification,
            ...latestNotifications.value,
        ].slice(0, 20);
    };

    window.Echo.private(channelName).notification(notificationHandler);

    subscribedChannel = channelName;
}

export function useRealtimeNotifications() {
    const page = usePage();

    const auth = computed(() => page.props.auth as {
        user?: { id?: number | string } | null;
    });

    const sharedCount = computed(() => {
        const props = page.props as {
            unread_notifications_count?: number;
            unread_count?: number;
        };

        return props.unread_notifications_count
            ?? props.unread_count
            ?? 0;
    });

    onMounted(() => {
        activeSubscribers++;

        if (unreadCount.value === null) {
            unreadCount.value = sharedCount.value;
        }

        const userId = auth.value.user?.id;

        if (userId !== undefined && userId !== null) {
            subscribeToUser(String(userId));
        }
    });

    watch(sharedCount, (count) => {
        // Les données serveur restent la référence après une navigation
        // ou une action qui actualise les propriétés Inertia.
        unreadCount.value = count;
    });

    onUnmounted(() => {
        activeSubscribers = Math.max(0, activeSubscribers - 1);

        // On conserve l'abonnement tant qu'un composant l'utilise.
        if (activeSubscribers === 0 && subscribedChannel && window.Echo) {
            window.Echo.leave(subscribedChannel);
            subscribedChannel = null;
            notificationHandler = null;
            activeUserId = null;
        }
    });

    return {
        unreadCount: computed(() => unreadCount.value ?? sharedCount.value),
        latestNotifications: computed(() => latestNotifications.value),
    };
}