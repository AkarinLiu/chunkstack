<template>
    <div class="max-w-3xl mx-auto">
        <div v-if="loading" class="text-center py-12">
            <p class="text-gray-600 dark:text-gray-400">加载中...</p>
        </div>

        <div v-else-if="!link" class="text-center py-12">
            <p class="text-gray-600 dark:text-gray-400">链接不存在</p>
            <router-link
                :to="{ name: 'home' }"
                class="inline-block mt-4 text-blue-600 hover:text-blue-700"
            >
                返回首页
            </router-link>
        </div>

        <div v-else class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8">
            <div class="flex items-start gap-4 mb-6">
                <span class="text-4xl" v-html="iconHtml"></span>
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ link.title }}
                    </h1>
                    <a
                        :href="link.url"
                        target="_blank"
                        class="text-sm text-blue-600 dark:text-blue-400 hover:underline break-all"
                    >
                        {{ link.url }}
                    </a>
                </div>
            </div>

            <div class="flex flex-wrap gap-2 mb-6">
                <span
                    v-for="tag in link.tags"
                    :key="tag.id"
                    class="px-3 py-1 text-sm rounded-full text-white"
                    :style="{ backgroundColor: tag.color }"
                >
                    {{ tag.name }}
                </span>
            </div>

            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">介绍</h3>
                <p class="text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-wrap">
                    {{ link.description || '暂无介绍' }}
                </p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                        {{ formatCount(link.click_count) }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">点击量</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                        {{ formatCount(link.page_view_count) }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">页面浏览量</div>
                </div>
                <div class="text-center">
                    <div class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        {{ categoryName }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">所属分类</div>
                </div>
                <div class="text-center">
                    <div class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        {{ createdDate }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">创建日期</div>
                </div>
            </div>

            <div class="flex gap-3">
                <a
                    :href="link.url"
                    target="_blank"
                    class="flex-1 px-8 py-3 bg-blue-600 text-white text-center text-lg font-semibold rounded-lg hover:bg-blue-700 transition shadow-md"
                    @click="recordClick"
                >
                    直链前往
                </a>
                <router-link
                    :to="{ name: 'home' }"
                    class="px-6 py-3 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition"
                >
                    返回首页
                </router-link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, inject } from 'vue';
import axios from 'axios';

const props = defineProps({
    slug: {
        type: String,
        default: null,
    },
});

const initialData = inject('initialData', {});

const link = ref(null);
const loading = ref(true);

const iconHtml = computed(() => {
    if (!link.value) return '🔗';
    const type = link.value.icon_type || 'emoji';
    if (type === 'emoji') {
        return link.value.icon || '🔗';
    }
    if (type === 'font-awesome') {
        return '<i class="' + link.value.icon + ' dark:text-white"></i>';
    }
    if (type === 'image') {
        return '<img src="' + link.value.icon_url + '" alt="icon" class="w-10 h-10 object-contain">';
    }
    return '🔗';
});

const categoryName = computed(() => {
    return link.value?.category?.name || '-';
});

const createdDate = computed(() => {
    if (!link.value?.created_at) return '-';
    return new Date(link.value.created_at).toLocaleDateString('zh-CN');
});

function formatCount(num) {
    if (!num && num !== 0) return '-';
    if (num >= 10000) {
        return (num / 10000).toFixed(1) + '万';
    }
    return num.toString();
}

function recordClick() {
    if (!link.value) return;
    axios.post('/api/link-clicks/' + (link.value.slug || link.value.id)).catch(() => {});
}

onMounted(() => {
    const initialLink = initialData?.link || null;

    if (initialLink) {
        link.value = initialLink;
        loading.value = false;
        axios.post('/api/link-views/' + (initialLink.slug || initialLink.id)).catch(() => {});
        return;
    }

    if (props.slug) {
        axios.get('/api/links/' + props.slug).then((res) => {
            link.value = res.data.link;
            loading.value = false;
        }).catch(() => {
            loading.value = false;
        });
    } else {
        loading.value = false;
    }
});
</script>
