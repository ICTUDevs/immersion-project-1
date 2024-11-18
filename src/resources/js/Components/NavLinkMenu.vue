<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: String,
    active: Boolean,
});

const classes = computed(() => {
    return props.active
        ? 'block p-3 rounded-lg bg-gray-200 dark:bg-gray-700'
        : 'block p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700';
});
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
