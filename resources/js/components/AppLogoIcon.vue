<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40" fill="none" :class="className" v-bind="$attrs">
        <!-- Contour géométrique -->
        <path d="M20 2.5L35.2 11.25V28.75L20 37.5L4.8 28.75V11.25L20 2.5Z" stroke="currentColor" stroke-width="2.5"
            stroke-linejoin="round" />

        <!-- Symbole de code : chevron gauche -->
        <path d="M15.5 14L10 20L15.5 26" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
            stroke-linejoin="round" />

        <!-- Symbole de code : chevron droit -->
        <path d="M24.5 14L30 20L24.5 26" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
            stroke-linejoin="round" />

        <!-- Lien central -->
        <path d="M22 16L18 24" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />

        <!-- Nœuds de connexion -->
        <circle cx="20" cy="5" r="1.5" fill="currentColor" />
        <circle cx="5.5" cy="12" r="1.5" fill="currentColor" />
        <circle cx="34.5" cy="12" r="1.5" fill="currentColor" />
    </svg>
</template>