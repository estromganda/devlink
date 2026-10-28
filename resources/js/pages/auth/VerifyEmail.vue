<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import {
    ArrowLeft,
    CheckCircle2,
    Code2,
    Mail,
    RefreshCw,
    ShieldCheck,
} from '@lucide/vue';

import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { logout } from '@/routes';
import { send } from '@/routes/verification';

defineOptions({
    layout: {
        title: 'Vérification de l’adresse e-mail',
        description:
            'Vérifie ton adresse e-mail pour profiter pleinement de la communauté DevLink.',
    },
});

defineProps<{
    status?: string;
}>();
</script>

<template>

    <Head title="Vérification de l’adresse e-mail" />

    <main
        class="relative isolate flex min-h-[calc(100vh-4rem)] w-full min-w-0 items-center justify-center overflow-hidden bg-background px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
        <!-- Décoration d'arrière-plan -->
        <div class="pointer-events-none absolute -left-32 top-0 -z-10 size-72 rounded-full bg-indigo-500/10 blur-3xl sm:size-96"
            aria-hidden="true" />

        <div class="pointer-events-none absolute -bottom-32 -right-24 -z-10 size-72 rounded-full bg-violet-500/10 blur-3xl sm:size-96"
            aria-hidden="true" />

        <!-- Carte de vérification -->
        <section
            class="w-full min-w-0 max-w-lg overflow-hidden rounded-2xl border border-border/70 bg-card shadow-xl shadow-black/5 sm:rounded-3xl"
            aria-labelledby="verification-title">
            <!-- Bandeau supérieur -->
            <div class="relative overflow-hidden border-b border-border/60 bg-muted/30 px-5 py-6 sm:px-8 sm:py-8">
                <div class="pointer-events-none absolute -right-8 -top-12 size-40 rounded-full bg-indigo-500/10 blur-2xl"
                    aria-hidden="true" />

                <div class="relative flex items-center gap-3">
                    <div
                        class="flex size-11 shrink-0 items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-lg shadow-indigo-600/20">
                        <Code2 class="size-6" />
                    </div>

                    <div class="min-w-0">
                        <p class="truncate text-lg font-bold tracking-tight">
                            DevLink
                        </p>

                        <p class="text-xs text-muted-foreground sm:text-sm">
                            La communauté des développeurs
                        </p>
                    </div>
                </div>
            </div>

            <!-- Contenu -->
            <div class="px-5 py-7 sm:px-8 sm:py-9">
                <!-- Icône principale -->
                <div
                    class="mb-6 flex size-16 items-center justify-center rounded-2xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-300 sm:size-20">
                    <Mail class="size-8 sm:size-10" />
                </div>

                <p
                    class="mb-2 text-xs font-semibold uppercase tracking-[0.18em] text-indigo-600 dark:text-indigo-300 sm:text-sm">
                    Encore une petite étape
                </p>

                <h1 id="verification-title" class="text-2xl font-bold tracking-tight sm:text-3xl">
                    Vérifie ton adresse e-mail
                </h1>

                <p class="mt-4 text-sm leading-6 text-muted-foreground sm:text-base sm:leading-7">
                    Merci d'avoir rejoint DevLink ! Avant de commencer à
                    explorer les communautés et à partager tes projets,
                    confirme ton adresse e-mail en cliquant sur le lien que
                    nous t'avons envoyé.
                </p>

                <!-- Confirmation de renvoi -->
                <div v-if="status === 'verification-link-sent'" role="status" aria-live="polite"
                    class="mt-6 flex items-start gap-3 rounded-xl border border-emerald-500/20 bg-emerald-500/10 p-4 text-sm text-emerald-700 dark:text-emerald-300">
                    <CheckCircle2 class="mt-0.5 size-5 shrink-0" aria-hidden="true" />

                    <p class="min-w-0 leading-6">
                        Un nouveau lien de vérification vient d'être envoyé à
                        l'adresse e-mail indiquée lors de ton inscription.
                        Vérifie également ton dossier de courriers indésirables.
                    </p>
                </div>

                <!-- Instructions -->
                <div class="mt-6 rounded-xl border border-border/70 bg-muted/30 p-4 sm:p-5">
                    <div class="flex items-start gap-3">
                        <div
                            class="mt-0.5 flex size-9 shrink-0 items-center justify-center rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-300">
                            <ShieldCheck class="size-5" />
                        </div>

                        <div class="min-w-0">
                            <h2 class="text-sm font-semibold">
                                Tu n'as pas reçu le message ?
                            </h2>

                            <p class="mt-1 text-sm leading-6 text-muted-foreground">
                                Vérifie ton dossier spam ou courrier
                                indésirable. Tu peux aussi demander l'envoi
                                d'un nouveau lien ci-dessous.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Formulaire de renvoi -->
                <Form v-bind="send.form()" v-slot="{ processing }" class="mt-6 space-y-4">
                    <Button type="submit"
                        class="h-12 w-full rounded-xl text-sm font-semibold shadow-lg shadow-primary/15 transition-all hover:-translate-y-0.5"
                        :disabled="processing">
                        <Spinner v-if="processing" />

                        <RefreshCw v-else class="size-4 shrink-0" aria-hidden="true" />

                        <span>
                            {{
                                processing
                                    ? 'Envoi en cours…'
                                    : 'Renvoyer le lien de vérification'
                            }}
                        </span>
                    </Button>

                    <TextLink :href="logout()" as="button"
                        class="flex min-h-11 w-full items-center justify-center gap-2 rounded-xl px-4 text-sm font-medium text-muted-foreground transition-colors hover:bg-muted hover:text-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                        <ArrowLeft class="size-4 shrink-0" />
                        Se déconnecter
                    </TextLink>
                </Form>
            </div>

            <!-- Pied de carte -->
            <div class="border-t border-border/60 bg-muted/20 px-5 py-4 text-center sm:px-8">
                <p class="text-xs leading-5 text-muted-foreground">
                    Tes informations restent protégées.
                    <span class="font-medium">Bienvenue sur DevLink.</span>
                </p>
            </div>
        </section>
    </main>
</template>
