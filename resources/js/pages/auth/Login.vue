<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';

import {
    ArrowRight,
    Code2,
    LockKeyhole,
    ShieldCheck,
    Sparkles,
    Users,
} from '@lucide/vue';

import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import PasskeyVerify from '@/components/PasskeyVerify.vue';

import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Connexion à DevColab',
        description: 'Connecte-toi pour retrouver la communauté des développeurs.',
    },
});

defineProps<{
    status?: string;
    error?: string;
    canResetPassword: boolean;
}>();
</script>

<template>

    <Head title="Connexion" />

    <main class="relative isolate w-full min-w-0 overflow-hidden">
        <!-- Background -->
        <div class="pointer-events-none absolute inset-0 -z-10 overflow-hidden" aria-hidden="true">
            <div class="absolute -left-32 -top-32 size-96 rounded-full bg-primary/10 blur-3xl" />
            <div class="absolute -bottom-40 -right-24 size-96 rounded-full bg-violet-500/10 blur-3xl" />

            <div class="absolute inset-0 opacity-[0.035] dark:opacity-[0.06]"
                style="background-image: radial-gradient(currentColor 1px, transparent 1px); background-size: 24px 24px;" />
        </div>

        <div
            class="mx-auto grid w-full min-w-0 grid-cols-1 items-center gap-8 px-4 py-8 sm:px-6 sm:py-12 lg:grid-cols-2 lg:gap-12 lg:px-10 xl:gap-16 xl:px-16 2xl:gap-24 2xl:px-24">
            <!-- Left panel: desktop -->
            <section class="hidden min-w-0 lg:block">
                <div
                    class="inline-flex items-center gap-2 rounded-full border border-primary/20 bg-primary/5 px-4 py-2 text-sm font-medium text-primary">
                    <Sparkles class="size-4" />
                    La communauté des développeurs
                </div>

                <h1
                    class="mt-8 max-w-3xl text-4xl font-extrabold leading-tight tracking-tight text-gray-950 dark:text-white xl:text-5xl 2xl:text-6xl">
                    Tes idées méritent
                    <span class="block text-primary">de prendre vie.</span>
                </h1>

                <p class="mt-6 max-w-2xl text-base leading-8 text-muted-foreground xl:text-lg">
                    Connecte-toi à DevColab pour partager tes connaissances,
                    découvrir des projets inspirants et collaborer avec
                    des développeurs qui partagent ta passion.
                </p>

                <div class="mt-10 grid grid-cols-1 gap-4 xl:grid-cols-2">
                    <article
                        class="flex min-w-0 items-start gap-4 rounded-2xl border bg-background/80 p-5 shadow-sm backdrop-blur transition hover:-translate-y-1 hover:shadow-md">
                        <div
                            class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400">
                            <Code2 class="size-6" />
                        </div>

                        <div class="min-w-0">
                            <h2 class="font-semibold">Partage tes connaissances</h2>
                            <p class="mt-2 text-sm leading-6 text-muted-foreground">
                                Pose tes questions, apporte tes réponses et progresse avec la communauté.
                            </p>
                        </div>
                    </article>

                    <article
                        class="flex min-w-0 items-start gap-4 rounded-2xl border bg-background/80 p-5 shadow-sm backdrop-blur transition hover:-translate-y-1 hover:shadow-md">
                        <div
                            class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-violet-500/10 text-violet-600 dark:text-violet-400">
                            <Users class="size-6" />
                        </div>

                        <div class="min-w-0">
                            <h2 class="font-semibold">Construis avec les autres</h2>
                            <p class="mt-2 text-sm leading-6 text-muted-foreground">
                                Trouve des collaborateurs et transforme tes idées en projets concrets.
                            </p>
                        </div>
                    </article>
                </div>

                <div class="mt-8 flex items-center gap-3 text-sm text-muted-foreground">
                    <ShieldCheck class="size-5 shrink-0 text-emerald-600 dark:text-emerald-400" />
                    <span>Retrouve ton espace, tes projets et tes conversations.</span>
                </div>
            </section>

            <!-- Right panel: login -->
            <section class="w-full min-w-0">
                <div
                    class="mx-auto w-full min-w-0 max-w-none overflow-hidden rounded-2xl border bg-card shadow-xl shadow-black/4 dark:shadow-black/20 sm:rounded-3xl">
                    <!-- Card header -->
                    <div class="border-b px-5 py-6 sm:px-8 sm:py-8 xl:px-10">
                        <div class="flex items-center gap-3 lg:hidden">
                            <div
                                class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-primary text-primary-foreground shadow-md shadow-primary/20">
                                <Code2 class="size-6" />
                            </div>

                            <div class="min-w-0">
                                <p class="text-lg font-extrabold tracking-tight">DevColab</p>
                                <p class="text-xs text-muted-foreground">La communauté des développeurs</p>
                            </div>
                        </div>

                        <div
                            class="mt-6 flex size-12 items-center justify-center rounded-xl bg-primary/10 text-primary lg:mt-0">
                            <LockKeyhole class="size-6" />
                        </div>

                        <h2 class="mt-5 text-2xl font-bold tracking-tight sm:text-3xl">
                            Bon retour !
                        </h2>

                        <p class="mt-2 max-w-xl text-sm leading-6 text-muted-foreground sm:text-base">
                            Connecte-toi à ton compte pour reprendre là où tu t'es arrêté.
                        </p>
                    </div>

                    <div class="p-5 sm:p-8 xl:p-10">
                        <!-- Status -->
                        <div v-if="status" role="status"
                            class="mb-5 rounded-xl border border-emerald-500/20 bg-emerald-500/5 px-4 py-3 text-sm font-medium text-emerald-700 dark:text-emerald-400">
                            {{ status }}
                        </div>
                        <!-- Erreur d'authentification -->
                        <div v-if="error" role="alert"
                            class="mb-5 rounded-xl border border-destructive/20 bg-destructive/5 px-4 py-3 text-sm font-medium text-destructive">
                            {{ error }}
                        </div>

                        <!-- Authentification sociale -->
                        <!-- Authentification Google et GitHub -->
                        <div class="space-y-3">
                            <!-- Google -->
                            <a href="/auth/google/redirect"
                                class="flex h-12 w-full items-center justify-center gap-3 rounded-xl border bg-background px-4 text-sm font-semibold transition-colors hover:bg-muted focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" class="size-5 shrink-0"
                                    aria-hidden="true">
                                    <path fill="#EA4335"
                                        d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5Z"
                                        transform="translate(0 4)" />
                                    <path fill="#4285F4"
                                        d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.76 7.18l7.73 6C44.42 37.99 46.98 31.8 46.98 24.55Z" />
                                    <path fill="#FBBC05"
                                        d="M10.53 28.59a14.4 14.4 0 0 1 0-9.18l-7.98-6.19a23.9 23.9 0 0 0 0 21.56l7.98-6.19Z"
                                        transform="translate(0 4)" />
                                    <path fill="#34A853"
                                        d="M24 48c6.48 0 11.93-2.13 15.91-5.8l-7.73-6c-2.14 1.44-4.89 2.3-8.18 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48Z"
                                        transform="translate(0 -4)" />
                                </svg>

                                <span>Continuer avec Google</span>
                            </a>

                            <!-- GitHub -->
                            <a href="/auth/github/login"
                                class="flex h-12 w-full items-center justify-center gap-3 rounded-xl border bg-background px-4 text-sm font-semibold transition-colors hover:bg-muted focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                    class="size-5 shrink-0 fill-current" aria-hidden="true">
                                    <path
                                        d="M12 .9a11.1 11.1 0 0 0-3.51 21.63c.56.1.76-.24.76-.54v-2.1c-3.1.67-3.76-1.32-3.76-1.32-.5-1.29-1.24-1.63-1.24-1.63-1.01-.69.08-.68.08-.68 1.12.08 1.71 1.15 1.71 1.15.99 1.7 2.6 1.21 3.23.93.1-.72.39-1.21.7-1.49-2.47-.28-5.07-1.24-5.07-5.5 0-1.21.43-2.2 1.15-2.98-.12-.28-.5-1.41.11-2.94 0 0 .94-.3 3.05 1.14a10.6 10.6 0 0 1 5.55 0c2.11-1.44 3.05-1.14 3.05-1.14.61 1.53.23 2.66.11 2.94.72.78 1.15 1.77 1.15 2.98 0 4.27-2.6 5.22-5.08 5.5.4.35.75 1.02.75 2.06v3.08c0 .3.2.65.77.54A11.1 11.1 0 0 0 12 .9Z" />
                                </svg>

                                <span>Continuer avec GitHub</span>
                            </a>
                        </div>

                        <!-- Séparateur -->
                        <div class="my-6 flex items-center gap-4">
                            <div class="h-px flex-1 bg-border" />

                            <span class="shrink-0 text-xs font-medium uppercase tracking-wider text-muted-foreground">
                                Ou avec ton e-mail
                            </span>

                            <div class="h-px flex-1 bg-border" />
                        </div>

                        <!-- Authentification par passkey -->
                        <PasskeyVerify />

                        <!-- Form -->
                        <Form v-bind="store.form()" :reset-on-success="['password']" v-slot="{ errors, processing }"
                            class="w-full min-w-0 space-y-5">
                            <div class="grid min-w-0 grid-cols-1 gap-5 xl:grid-cols-2 xl:gap-6">
                                <div class="min-w-0 space-y-2 xl:col-span-2">
                                    <Label for="email" class="text-sm font-semibold">
                                        Adresse e-mail
                                    </Label>

                                    <Input id="email" type="email" name="email" required v-focus :tabindex="1"
                                        autocomplete="email" placeholder="toi@exemple.com"
                                        class="h-12 w-full min-w-0 rounded-xl px-4" :aria-invalid="!!errors.email" />

                                    <InputError :message="errors.email" />
                                </div>

                                <div class="min-w-0 space-y-2 xl:col-span-2">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <Label for="password" class="text-sm font-semibold">
                                            Mot de passe
                                        </Label>

                                        <TextLink v-if="canResetPassword" :href="request()"
                                            class="text-sm font-medium text-primary hover:underline" :tabindex="5">
                                            Mot de passe oublié ?
                                        </TextLink>
                                    </div>

                                    <PasswordInput id="password" name="password" required :tabindex="2"
                                        autocomplete="current-password" placeholder="Ton mot de passe" />

                                    <InputError :message="errors.password" />
                                </div>
                            </div>

                            <div class="flex items-center justify-between gap-3">
                                <Label for="remember"
                                    class="flex cursor-pointer items-center gap-3 text-sm font-normal text-muted-foreground">
                                    <Checkbox id="remember" name="remember" :tabindex="3" />
                                    Se souvenir de moi
                                </Label>
                            </div>

                            <Button type="submit" class="mt-2 h-12 w-full rounded-xl text-sm font-semibold"
                                :tabindex="4" :disabled="processing" data-test="login-button">
                                <Spinner v-if="processing" />
                                <span>{{ processing ? 'Connexion en cours…' : 'Se connecter' }}</span>
                                <ArrowRight v-if="!processing" class="size-4" />
                            </Button>
                        </Form>

                        <!-- Register -->
                        <div class="mt-7 border-t pt-6 text-center">
                            <p class="text-sm leading-6 text-muted-foreground">
                                Tu n'as pas encore de compte ?
                                <TextLink :href="register()" :tabindex="6"
                                    class="font-semibold text-primary hover:underline">
                                    Créer un compte
                                </TextLink>
                            </p>
                        </div>
                    </div>

                    <div class="border-t bg-muted/30 px-5 py-4 sm:px-8">
                        <p class="flex items-center justify-center gap-2 text-center text-xs text-muted-foreground">
                            <ShieldCheck class="size-4 shrink-0" />
                            Connecte-toi et retrouve ta communauté.
                        </p>
                    </div>
                </div>
            </section>
        </div>
    </main>
</template>