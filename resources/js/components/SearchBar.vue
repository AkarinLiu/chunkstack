<template>
    <form :action="route" method="GET" class="flex gap-2">
        <input
            v-model="query"
            type="text"
            name="q"
            placeholder="搜索链接..."
            class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-white"
            @keydown.enter="$emit('search', query)"
        >
        <button
            type="submit"
            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
        >
            搜索
        </button>
    </form>
</template>

<script setup>
import { ref } from 'vue';

defineProps({
    route: {
        type: String,
        required: true,
    },
    initialQuery: {
        type: String,
        default: '',
    },
});

const query = ref('');
</script>
