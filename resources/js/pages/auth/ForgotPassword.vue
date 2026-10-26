<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import {
    ArrowLeft,
    ArrowRight,
    CheckCircle2,
    Code2,
    KeyRound,
    Mail,
    ShieldCheck,
    Sparkles,
} from '@lucide/vue';

import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { email } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Mot de passe oublié',
        description: 'Réinitialise ton mot de passe pour retrouver ton compte DevLink.',
    },
});

defineProps<{
    status?: string;
}>();
</script>

<template>

    <Head title="Mot de passe oublié" />

    <main class="relative isolate w-full min-w-0 overflow-hidden bg-background">
        <!-- Décorations d'arrière-plan -->
        <div class="pointer-events-none absolute -left-40 top-0 -z-10 size-96 rounded-full bg-indigo-500/10 blur-3xl"
            aria-hidden="true" />
        <div class="pointer-events-none absolute -bottom-40 right-0 -z-10 size-96 rounded-full bg-violet-500/10 blur-3xl"
            aria-hidden="true" />

        <div class="mx-auto grid w-full min-w-0 grid-cols-1 lg:min-h-[calc(100vh-4rem)] lg:grid-cols-2">
            <!-- Panneau de présentation -->
            <section
                class="relative hidden min-w-0 flex-col justify-between overflow-hidden bg-slate-950 px-8 py-10 text-white sm:px-10 lg:flex lg:px-12 xl:px-16 2xl:px-24">
                <div class="pointer-events-none absolute -right-24 top-1/4 size-80 rounded-full bg-indigo-500/20 blur-3xl"
                    aria-hidden="true" />
                <div class="pointer-events-none absolute -bottom-20 -left-20 size-72 rounded-full bg-violet-500/20 blur-3xl"
                    aria-hidden="true" />

                <div class="relative flex items-center gap-3">
                    <div
                        class="flex size-11 items-center justify-center rounded-2xl border border-white/15 bg-white/10">
                        <Code2 class="size-6 text-indigo-300" />
                    </div>

                    <div>
                        <p class="text-xl font-bold tracking-tight">DevLink</p>
                        <p class="text-xs text-slate-400">
                            La communauté des développeurs
                        </p>
                    </div>
                </div>

                <div class="relative my-12 max-w-2xl">
                    <div
                        class="mb-6 inline-flex items-center gap-2 rounded-full border border-indigo-400/25 bg-indigo-400/10 px-3 py-1.5 text-sm text-indigo-200">
                        <Sparkles class="size-4" />
                        <span>Retrouve ton espace</span>
                    </div>

                    <h1 class="text-4xl font-bold leading-tight tracking-tight xl:text-5xl 2xl:text-6xl">
                        Ton compte,
                        <span class="block text-indigo-300">en toute sécurité.</span>
                    </h1>

                    <p class="mt-6 max-w-xl text-base leading-7 text-slate-300 xl:text-lg">
                        Un mot de passe oublié n'est pas un problème.
                        Quelques étapes suffisent pour retrouver l'accès à ton compte
                        et reprendre là où tu t'étais arrêté.
                    </p>

                    <div class="mt-10 space-y-4">
                        <div class="flex items-start gap-4">
                            <div
                                class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-indigo-400/15 text-indigo-300">
                                <Mail class="size-5" />
                            </div>
                            <div>
                                <h2 class="font-semibold">Un lien par e-mail</h2>
                                <p class="mt-1 text-sm leading-6 text-slate-400">
                                    Reçois les instructions pour réinitialiser ton mot de passe.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div
                                class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-emerald-400/15 text-emerald-300">
                                <ShieldCheck class="size-5" />
                            </div>
                            <div>
                                <h2 class="font-semibold">Un processus sécurisé</h2>
                                <p class="mt-1 text-sm leading-6 text-slate-400">
                                    Utilise le lien reçu pour choisir un nouveau mot de passe.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative flex items-center gap-2 text-sm text-slate-400">
                    <KeyRound class="size-4 shrink-0 text-emerald-400" />
                    <span>La sécurité de ton compte compte.</span>
                </div>
            </section>

            <!-- Formulaire -->
            <section
                class="flex min-w-0 items-center justify-center px-4 py-8 sm:px-8 sm:py-12 lg:px-10 xl:px-16 2xl:px-24">
                <div class="w-full min-w-0 max-w-xl">
                    <!-- Logo sur mobile -->
                    <div class="mb-8 lg:hidden">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex size-11 items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-lg shadow-indigo-600/20">
                                <Code2 class="size-6" />
                            </div>

                            <div>
                                <p class="text-xl font-bold tracking-tight">DevLink</p>
                                <p class="text-sm text-muted-foreground">
                                    La communauté des développeurs
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-8">
                        <div
                            class="mb-4 flex size-12 items-center justify-center rounded-2xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-300">
                            <KeyRound class="size-6" />
                        </div>

                        <p
                            class="mb-2 text-sm font-semibold uppercase tracking-[0.18em] text-indigo-600 dark:text-indigo-300">
                            Récupération du compte
                        </p>

                        <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">
                            Mot de passe oublié ?
                        </h2>

                        <p class="mt-3 max-w-lg text-sm leading-6 text-muted-foreground sm:text-base">
                            Indique l'adresse e-mail associée à ton compte. Nous t'enverrons
                            un lien pour définir un nouveau mot de passe.
                        </p>
                    </div>

                    <!-- Confirmation -->
                    <div v-if="status" role="status" aria-live="polite"
                        class="mb-6 flex items-start gap-3 rounded-xl border border-emerald-500/25 bg-emerald-500/10 p-4 text-sm text-emerald-700 dark:text-emerald-300">
                        <CheckCircle2 class="mt-0.5 size-5 shrink-0" />
                        <div class="min-w-0">
                            <p class="font-semibold">Demande prise en compte</p>
                            <p class="mt-1 leading-6">{{ status }}</p>
                        </div>
                    </div>

                    <Form v-bind="email.form()" v-slot="{ errors, processing }" class="w-full min-w-0 space-y-5">
                        <div class="grid gap-2">
                            <Label for="email">Adresse e-mail</Label>

                            <div class="relative">
                                <Mail
                                    class="pointer-events-none absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />

                                <Input id="email" type="email" name="email" autocomplete="email" required v-focus
                                    placeholder="toi@exemple.com" class="h-12 w-full min-w-0 rounded-xl pl-10"
                                    :aria-invalid="!!errors.email"
                                    :aria-describedby="errors.email ? 'email-error' : undefined" />
                            </div>

                            <InputError id="email-error" :message="errors.email" />
                        </div>

                        <Button type="submit"
                            class="h-12 w-full rounded-xl text-sm font-semibold shadow-lg shadow-primary/15 transition-all hover:-translate-y-0.5"
                            :disabled="processing" data-test="email-password-reset-link-button">
                            <Spinner v-if="processing" />
                            <span>
                                {{
                                    processing
                                        ? 'Envoi en cours…'
                                        : 'Envoyer le lien de réinitialisation'
                                }}
                            </span>
                            <ArrowRight v-if="!processing" class="ml-1 size-4" />
                        </Button>

                        <div class="rounded-xl border border-border/70 bg-muted/30 p-4">
                            <div class="flex items-start gap-3">
                                <div
                                    class="mt-0.5 flex size-8 shrink-0 items-center justify-center rounded-lg bg-indigo-500/10 text-indigo-600 dark:text-indigo-300">
                                    <ShieldCheck class="size-4" />
                                </div>

                                <div>
                                    <p class="text-sm font-medium">
                                        Tu ne reçois pas l'e-mail ?
                                    </p>
                                    <p class="mt-1 text-xs leading-5 text-muted-foreground">
                                        Vérifie ton adresse et pense à consulter ton dossier
                                        de courriers indésirables.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </Form>

                    <div class="my-8 flex items-center gap-4">
                        <div class="h-px flex-1 bg-border/70" />
                        <span class="text-xs text-muted-foreground">OU</span>
                        <div class="h-px flex-1 bg-border/70" />
                    </div>

                    <div class="text-center">
                        <TextLink :href="login()"
                            class="inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 underline-offset-4 hover:underline dark:text-indigo-300">
                            <ArrowLeft class="size-4" />
                            Retour à la connexion
                        </TextLink>
                    </div>

                    <div class="mt-8 flex items-center justify-center gap-2 text-xs text-muted-foreground">
                        <ShieldCheck class="size-4 shrink-0" />
                        <span>Protège ton compte et ne partage jamais ton mot de passe.</span>
                    </div>
                </div>
            </section>
        </div>
    </main>
</template>