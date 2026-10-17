<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen bg-background text-foreground">
        <header class="border-b bg-background">
            <nav class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4"
                aria-label="Navigation principale">
                <Link href="/" class="text-xl font-bold tracking-tight">
                    DevLink
                </Link>

                <div class="flex items-center gap-3">
                    <Link href="/login" class="rounded-md px-4 py-2 text-sm font-medium transition hover:bg-muted">
                        Se connecter
                    </Link>

                    <Link href="/register"
                        class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground transition hover:opacity-90">
                        S'inscrire
                    </Link>
                </div>
            </nav>
        </header>

        <main>
            <slot />
        </main>

        <footer class="mt-12 border-t">
            <div class="mx-auto max-w-7xl px-6 py-6 text-center text-sm text-muted-foreground">
                © {{ new Date().getFullYear() }} DevLink.
                La communauté des développeurs.
            </div>
        </footer>
    </div>
</template>