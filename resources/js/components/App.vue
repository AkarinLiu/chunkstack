<template>
    <router-view />
</template>

<script setup>
import { provide } from 'vue';

const props = defineProps({
    initialData: {
        type: Object,
        default: () => ({}),
    },
});

provide('initialData', props.initialData);
</script>
