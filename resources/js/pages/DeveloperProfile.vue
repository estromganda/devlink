<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

interface DeveloperProfile {
    headline: string | null;
    bio: string | null;
    location: string | null;
    website: string | null;
    github_url: string | null;
    linkedin_url: string | null;
}

const props = defineProps<{
    profile: DeveloperProfile | null;
}>();

const form = useForm({
    headline: props.profile?.headline ?? '',
    bio: props.profile?.bio ?? '',
    location: props.profile?.location ?? '',
    website: props.profile?.website ?? '',
    github_url: props.profile?.github_url ?? '',
    linkedin_url: props.profile?.linkedin_url ?? '',
});

function submit() {
    form.patch('/settings/developer-profile');
}
</script>

<template>

    <Head title="Profil développeur" />

    <div class="mx-auto max-w-3xl space-y-8 p-6">
        <div class="space-y-2">
            <h1 class="text-3xl font-bold tracking-tight">
                Profil développeur
            </h1>

            <p class="text-sm text-muted-foreground">
                Présente-toi à la communauté DevColab et partage tes
                compétences, ton parcours et tes liens professionnels.
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div class="space-y-2">
                <Label for="headline">Titre professionnel</Label>

                <Input id="headline" v-model="form.headline" placeholder="Ex. : Développeur Laravel et Vue.js"
                    maxlength="150" />

                <InputError :message="form.errors.headline" />
            </div>

            <div class="space-y-2">
                <Label for="bio">À propos de moi</Label>

                <textarea id="bio" v-model="form.bio" rows="6" maxlength="5000"
                    placeholder="Présente ton parcours, tes intérêts et tes objectifs..."
                    class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm outline-none focus-visible:ring-2 focus-visible:ring-ring"></textarea>

                <InputError :message="form.errors.bio" />
            </div>

            <div class="space-y-2">
                <Label for="location">Localisation</Label>

                <Input id="location" v-model="form.location" placeholder="Ex. : Vienne, Autriche" maxlength="150" />

                <InputError :message="form.errors.location" />
            </div>

            <div class="space-y-2">
                <Label for="website">Site personnel</Label>

                <Input id="website" v-model="form.website" type="url" placeholder="https://monsite.com" />

                <InputError :message="form.errors.website" />
            </div>

            <div class="space-y-2">
                <Label for="github_url">Profil GitHub</Label>

                <Input id="github_url" v-model="form.github_url" type="url"
                    placeholder="https://github.com/mon-profil" />

                <InputError :message="form.errors.github_url" />
            </div>

            <div class="space-y-2">
                <Label for="linkedin_url">Profil LinkedIn</Label>

                <Input id="linkedin_url" v-model="form.linkedin_url" type="url"
                    placeholder="https://www.linkedin.com/in/mon-profil" />

                <InputError :message="form.errors.linkedin_url" />
            </div>

            <div class="flex items-center gap-4">
                <Button type="submit" :disabled="form.processing">
                    {{
                        form.processing
                            ? 'Enregistrement...'
                            : 'Enregistrer mon profil'
                    }}
                </Button>

                <span v-if="form.recentlySuccessful" class="text-sm text-green-600">
                    Profil enregistré.
                </span>
            </div>
        </form>
    </div>
</template>