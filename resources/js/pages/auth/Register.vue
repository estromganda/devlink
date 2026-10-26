<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import {
    ArrowRight,
    Check,
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
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { store } from '@/routes/register';

defineProps<{
    passwordRules: string;
}>();

defineOptions({
    layout: {
        title: 'Créer un compte DevLink',
        description: 'Rejoins la communauté des développeurs et construis de nouveaux projets ensemble.',
    },
});
</script>

<template>

    <Head title="Créer un compte" />

    <main class="relative isolate w-full min-w-0 overflow-hidden bg-background">
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
                        <p class="text-xs text-slate-400">La communauté des développeurs</p>
                    </div>
                </div>

                <div class="relative my-12 max-w-2xl">
                    <div
                        class="mb-6 inline-flex items-center gap-2 rounded-full border border-indigo-400/25 bg-indigo-400/10 px-3 py-1.5 text-sm text-indigo-200">
                        <Sparkles class="size-4" />
                        <span>Ton prochain chapitre commence ici</span>
                    </div>

                    <h1 class="text-4xl font-bold leading-tight tracking-tight xl:text-5xl 2xl:text-6xl">
                        Le code est
                        <span class="block text-indigo-300">meilleur ensemble.</span>
                    </h1>

                    <p class="mt-6 max-w-xl text-base leading-7 text-slate-300 xl:text-lg">
                        Rejoins une communauté où les développeurs partagent leurs
                        connaissances, présentent leurs projets et trouvent des partenaires
                        pour aller plus loin.
                    </p>

                    <div class="mt-10 grid gap-4 sm:grid-cols-2">
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur-sm">
                            <div
                                class="mb-3 flex size-10 items-center justify-center rounded-xl bg-indigo-400/15 text-indigo-300">
                                <Users class="size-5" />
                            </div>
                            <h2 class="font-semibold">Rencontre des développeurs</h2>
                            <p class="mt-1 text-sm leading-6 text-slate-400">
                                Échange, apprends et construis ton réseau.
                            </p>
                        </div>

                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur-sm">
                            <div
                                class="mb-3 flex size-10 items-center justify-center rounded-xl bg-emerald-400/15 text-emerald-300">
                                <Code2 class="size-5" />
                            </div>
                            <h2 class="font-semibold">Fais grandir tes projets</h2>
                            <p class="mt-1 text-sm leading-6 text-slate-400">
                                Partage tes idées et collabore avec d'autres talents.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="relative flex items-center gap-2 text-sm text-slate-400">
                    <ShieldCheck class="size-4 shrink-0 text-emerald-400" />
                    <span>Un espace pour apprendre, partager et progresser ensemble.</span>
                </div>
            </section>

            <!-- Formulaire d'inscription -->
            <section
                class="flex min-w-0 items-center justify-center px-4 py-8 sm:px-8 sm:py-12 lg:px-10 xl:px-16 2xl:px-24">
                <div class="w-full min-w-0 max-w-xl">
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
                            class="mb-4 inline-flex size-12 items-center justify-center rounded-2xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-300">
                            <Users class="size-6" />
                        </div>

                        <p
                            class="mb-2 text-sm font-semibold uppercase tracking-[0.18em] text-indigo-600 dark:text-indigo-300">
                            Rejoins DevLink
                        </p>

                        <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">
                            Crée ton compte
                        </h2>

                        <p class="mt-3 max-w-lg text-sm leading-6 text-muted-foreground sm:text-base">
                            Quelques informations suffisent pour rejoindre la communauté
                            et commencer à échanger avec d'autres développeurs.
                        </p>
                    </div>

                    <Form v-bind="store.form()" :reset-on-success="['password', 'password_confirmation']"
                        v-slot="{ errors, processing }" class="w-full min-w-0 space-y-5">
                        <div class="grid gap-2">
                            <Label for="name">Nom complet</Label>
                            <Input id="name" type="text" required v-focus :tabindex="1" autocomplete="name" name="name"
                                placeholder="Ex. Alex Martin" class="h-12 w-full min-w-0 rounded-xl"
                                :aria-invalid="!!errors.name" />
                            <InputError :message="errors.name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="email">Adresse e-mail</Label>
                            <Input id="email" type="email" required :tabindex="2" autocomplete="email" name="email"
                                placeholder="toi@exemple.com" class="h-12 w-full min-w-0 rounded-xl"
                                :aria-invalid="!!errors.email" />
                            <InputError :message="errors.email" />
                        </div>

                        <div class="grid gap-2">
                            <div class="flex items-center justify-between gap-3">
                                <Label for="password">Mot de passe</Label>
                                <span class="flex shrink-0 items-center gap-1 text-xs text-muted-foreground">
                                    <LockKeyhole class="size-3.5" />
                                    Sécurisé
                                </span>
                            </div>

                            <PasswordInput id="password" required :tabindex="3" autocomplete="new-password"
                                name="password" placeholder="Crée un mot de passe" :passwordrules="passwordRules" />
                            <InputError :message="errors.password" />

                            <p class="text-xs leading-5 text-muted-foreground">
                                Choisis un mot de passe qui respecte les règles de sécurité
                                de l'application.
                            </p>
                        </div>

                        <div class="grid gap-2">
                            <Label for="password_confirmation">Confirmer le mot de passe</Label>
                            <PasswordInput id="password_confirmation" required :tabindex="4" autocomplete="new-password"
                                name="password_confirmation" placeholder="Saisis à nouveau ton mot de passe"
                                :passwordrules="passwordRules" />
                            <InputError :message="errors.password_confirmation" />
                        </div>

                        <div class="flex items-start gap-3 rounded-xl border border-border/70 bg-muted/30 p-3.5">
                            <div
                                class="mt-0.5 flex size-7 shrink-0 items-center justify-center rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                                <Check class="size-4" />
                            </div>
                            <p class="text-xs leading-5 text-muted-foreground">
                                En créant ton compte, tu pourras découvrir des communautés,
                                poser des questions et présenter tes projets.
                            </p>
                        </div>

                        <Button type="submit"
                            class="mt-2 h-12 w-full rounded-xl text-sm font-semibold shadow-lg shadow-primary/15 transition-all hover:-translate-y-0.5"
                            tabindex="5" :disabled="processing" data-test="register-user-button">
                            <Spinner v-if="processing" />
                            <span>{{ processing ? 'Création du compte…' : 'Créer mon compte' }}</span>
                            <ArrowRight v-if="!processing" class="ml-1 size-4" />
                        </Button>

                        <div class="relative py-1">
                            <div class="absolute inset-0 flex items-center">
                                <span class="w-full border-t border-border/70" />
                            </div>
                            <div class="relative flex justify-center">
                                <span class="bg-background px-3 text-xs text-muted-foreground">
                                    Déjà membre de DevLink ?
                                </span>
                            </div>
                        </div>

                        <p class="text-center text-sm text-muted-foreground">
                            Tu as déjà un compte ?
                            <TextLink :href="login()"
                                class="font-semibold text-indigo-600 underline-offset-4 hover:underline dark:text-indigo-300"
                                :tabindex="6">
                                Connecte-toi
                            </TextLink>
                        </p>
                    </Form>

                    <div class="mt-8 flex items-center justify-center gap-2 text-xs text-muted-foreground">
                        <ShieldCheck class="size-4 shrink-0" />
                        <span>Tes informations sont utilisées pour gérer ton compte DevLink.</span>
                    </div>
                </div>
            </section>
        </div>
    </main>
</template>