<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Props {
    status: number;
}

const props = defineProps<Props>();

interface ErrorContent {
    title: string;
    description: string;
    icon: string;
}

const errors: Record<number, ErrorContent> = {
    403: {
        title: 'Accès refusé',
        description:
            "Tu n'as pas les autorisations nécessaires pour accéder à cette ressource. Vérifie tes permissions ou retourne à l'accueil.",
        icon: '🔒',
    },
    404: {
        title: 'Page introuvable',
        description:
            "La page que tu recherches n'existe pas, a été déplacée ou n'est plus disponible.",
        icon: '🔎',
    },
    419: {
        title: 'Session expirée',
        description:
            'Ta session a expiré. Retourne à la page précédente et actualise-la pour continuer.',
        icon: '⏳',
    },
    429: {
        title: 'Doucement !',
        description:
            "Tu as effectué trop de requêtes en peu de temps. Patiente un instant avant de réessayer.",
        icon: '⚡',
    },
    500: {
        title: 'Une erreur est survenue',
        description:
            "Un problème inattendu empêche le chargement de cette page. Nos équipes pourront examiner le problème.",
        icon: '🛠️',
    },
    503: {
        title: 'Devlink est en maintenance',
        description:
            'Nous effectuons quelques opérations de maintenance. Le service devrait être disponible prochainement.',
        icon: '🔧',
    },
};

const error = computed<ErrorContent>(() => {
    return (
        errors[props.status] ?? {
            title: 'Une erreur est survenue',
            description:
                "Nous n'avons pas pu traiter ta demande. Réessaie dans quelques instants.",
            icon: '⚠️',
        }
    );
});

const errorCode = computed(() => props.status || 500);

function goBack() {
    if (window.history.length > 1) {
        window.history.back();
    } else {
        window.location.href = '/';
    }
}
</script>

<template>

    <Head :title="`${errorCode} — ${error.title} | Devlink`" />

    <main class="flex min-h-screen flex-col items-center justify-center bg-background px-6 py-12 text-foreground">
        <div class="w-full max-w-xl text-center">
            <!-- Logo / nom de la plateforme -->
            <Link href="/" class="inline-flex items-center gap-2 text-xl font-bold tracking-tight">
                <span
                    class="flex size-10 items-center justify-center rounded-xl bg-primary text-lg text-primary-foreground">
                    D
                </span>

                <span>Devlink</span>
            </Link>

            <!-- Illustration -->
            <div class="mt-12">
                <div class="mx-auto flex size-28 items-center justify-center rounded-3xl border border-border bg-card text-5xl shadow-sm"
                    role="img" :aria-label="error.title">
                    {{ error.icon }}
                </div>

                <p class="mt-8 text-sm font-semibold uppercase tracking-[0.2em] text-muted-foreground">
                    Erreur {{ errorCode }}
                </p>

                <h1 class="mt-3 text-3xl font-bold tracking-tight sm:text-4xl">
                    {{ error.title }}
                </h1>

                <p class="mx-auto mt-4 max-w-md text-base leading-7 text-muted-foreground">
                    {{ error.description }}
                </p>
            </div>

            <!-- Actions -->
            <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                <button type="button"
                    class="inline-flex min-h-11 items-center justify-center rounded-xl border border-input bg-background px-5 py-3 text-sm font-medium transition-colors hover:bg-muted focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                    @click="goBack">
                    ← Retour
                </button>

                <Link href="/"
                    class="inline-flex min-h-11 items-center justify-center rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-primary-foreground transition-opacity hover:opacity-90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                    Retour à l'accueil
                </Link>
            </div>

            <!-- Aide -->
            <p class="mt-12 text-sm text-muted-foreground">
                Un problème persiste ?

                <Link href="/"
                    class="font-medium text-foreground underline underline-offset-4 transition-colors hover:text-primary">
                    Reviens sur Devlink
                </Link>
                et réessaie.
            </p>
        </div>

        <footer class="mt-16 text-center text-xs text-muted-foreground">
            © {{ new Date().getFullYear() }} Devlink.
            Par les développeurs, pour les développeurs.
        </footer>
    </main>
</template>
