<template>
    <div
        class="bg-white dark:bg-gray-800 dark:border dark:border-gray-600 rounded-lg p-4 shadow hover:shadow-lg transition cursor-pointer group relative"
        :class="bgClass"
        @click="$emit('navigate', link.slug || link.id)"
    >
        <div class="flex items-start gap-3">
            <span class="text-2xl" v-html="iconHtml"></span>
            <div class="flex-1 min-w-0">
                <h3 class="font-semibold text-gray-900 dark:text-white truncate pr-16">
                    {{ link.title }}
                </h3>
                <p
                    v-if="link.description"
                    class="text-sm text-gray-600 dark:text-gray-400 mt-1 line-clamp-2"
                >
                    {{ link.description }}
                </p>
                <div v-if="link.tags && link.tags.length" class="flex flex-wrap gap-1 mt-2">
                    <span
                        v-for="tag in link.tags"
                        :key="tag.id"
                        class="px-2 py-0.5 text-xs rounded-full text-white"
                        :style="{ backgroundColor: tag.color }"
                    >
                        {{ tag.name }}
                    </span>
                </div>
            </div>
        </div>

        <div class="absolute top-2 right-2 flex items-center gap-2 opacity-0 group-hover:opacity-100 transition">
            <span class="text-xs text-gray-500 dark:text-gray-400">
                {{ link.click_count ?? 0 }} 击 / {{ link.page_view_count ?? 0 }} 览
            </span>
            <a
                :href="link.url"
                target="_blank"
                class="px-2.5 py-1 text-xs bg-blue-600 text-white rounded-md hover:bg-blue-700 transition"
                @click.stop="recordClick"
            >
                前往
            </a>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import axios from 'axios';

const props = defineProps({
    link: {
        type: Object,
        required: true,
    },
    bgClass: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['navigate']);

function recordClick() {
    axios.post('/api/link-clicks/' + (props.link.slug || props.link.id)).catch(() => {});
}

const iconHtml = computed(() => {
    const type = props.link.icon_type || 'emoji';
    if (type === 'emoji') {
        return props.link.icon || '🔗';
    }
    if (type === 'font-awesome') {
        return '<i class="' + props.link.icon + ' dark:text-white"></i>';
    }
    if (type === 'image') {
        return '<img src="' + props.link.icon_url + '" alt="icon" class="w-6 h-6 object-contain">';
    }
    return '🔗';
});
</script>
