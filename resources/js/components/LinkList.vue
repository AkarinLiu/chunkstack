<template>
    <div class="space-y-8">
        <div class="flex flex-col sm:flex-row gap-2">
            <input
                v-model="searchQuery"
                type="text"
                placeholder="搜索链接..."
                class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-white"
                @keydown.enter="doSearch"
            >
            <div class="flex gap-2">
                <select
                    v-model="sortField"
                    class="px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-white"
                    @change="handleSortChange"
                >
                    <option value="sort_order">默认排序</option>
                    <option value="click_count">点击量</option>
                    <option value="page_view_count">浏览量</option>
                    <option value="title">名称</option>
                    <option value="created_at">创建时间</option>
                </select>
                <button
                    class="px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition dark:text-white"
                    @click="toggleDirection"
                >
                    <i class="fa-solid" :class="sortDirection === 'asc' ? 'fa-arrow-up' : 'fa-arrow-down'"></i>
                </button>
                <button
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                    @click="doSearch"
                >
                    搜索
                </button>
                <button
                    v-if="searching"
                    class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition"
                    @click="clearSearch"
                >
                    清除
                </button>
            </div>
        </div>

        <div v-if="searching">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                搜索结果: "{{ currentQuery }}"
            </h2>

            <div v-if="!sortedResults.length" class="text-center py-12">
                <p class="text-gray-600 dark:text-gray-400">没有找到相关结果</p>
            </div>

            <LinkGrid v-else :links="sortedResults" @navigate="handleNavigate" />
        </div>

        <div v-else class="space-y-10">
            <div v-if="!sortedCategories.length" class="text-center py-12">
                <p class="text-gray-600 dark:text-gray-400">暂无分类</p>
            </div>

            <div
                v-for="category in sortedCategories"
                :key="category.id"
                class="bg-white dark:bg-gray-800/80 dark:border dark:border-gray-700 rounded-lg shadow p-6"
            >
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <span v-html="categoryIcon(category)"></span>
                    {{ category.name }}
                    <span
                        v-if="category.description"
                        class="text-sm font-normal text-gray-600 dark:text-gray-400"
                    >
                        - {{ category.description }}
                    </span>
                </h2>

                <div v-if="!category.active_links || !category.active_links.length">
                    <p class="text-gray-600 dark:text-gray-400">暂无链接</p>
                </div>

                <LinkGrid
                    v-else
                    :links="category.active_links"
                    :bg-class="'bg-gray-50 dark:bg-gray-900 dark:border-gray-700'"
                    @navigate="handleNavigate"
                />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, inject, computed } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import LinkGrid from './LinkGrid.vue';

const router = useRouter();
const initialData = inject('initialData', {});

const searchQuery = ref('');
const currentQuery = ref('');
const searching = ref(false);
const results = ref([]);
const categories = ref([]);
const sortField = ref('sort_order');
const sortDirection = ref('asc');

const sortedResults = computed(() => {
    if (!results.value.length) return [];
    return sortItems([...results.value]);
});

const sortedCategories = computed(() => {
    if (!categories.value.length) return [];
    if (sortField.value === 'sort_order' && sortDirection.value === 'asc') {
        return categories.value;
    }
    return categories.value.map((category) => ({
        ...category,
        active_links: sortItems([...(category.active_links || [])]),
    }));
});

function sortItems(items) {
    const field = sortField.value;
    const dir = sortDirection.value === 'desc' ? -1 : 1;
    return items.sort((a, b) => {
        let va = a[field];
        let vb = b[field];
        if (typeof va === 'string') {
            va = va.toLowerCase();
            vb = vb.toLowerCase();
        }
        if (va < vb) return -1 * dir;
        if (va > vb) return 1 * dir;
        return 0;
    });
}

function handleSortChange() {
    if (searching.value) {
        doSearch();
    }
}

function toggleDirection() {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    if (searching.value) {
        doSearch();
    }
}

onMounted(() => {
    if (initialData && Object.keys(initialData).length) {
        sortField.value = initialData.sort || 'sort_order';
        sortDirection.value = initialData.direction || 'asc';
        if (initialData.query) {
            searchQuery.value = initialData.query;
            currentQuery.value = initialData.query;
            searching.value = true;
            results.value = initialData.links || [];
        } else {
            categories.value = initialData.categories || [];
        }
    }
});

function doSearch() {
    const q = searchQuery.value.trim();
    if (!q) return;

    axios.get('/api/links', {
        params: {
            q,
            sort: sortField.value,
            direction: sortDirection.value,
        },
    }).then((res) => {
        currentQuery.value = q;
        searching.value = true;
        results.value = res.data.links || [];
    }).catch(() => {
        results.value = [];
    });
}

function clearSearch() {
    searchQuery.value = '';
    searching.value = false;
    currentQuery.value = '';
    results.value = [];
}

function handleNavigate(slug) {
    axios.post('/api/link-views/' + slug).catch(() => {});
    router.push({ name: 'link.show', params: { slug } });
}

function categoryIcon(category) {
    const type = category.icon_type || 'emoji';
    if (type === 'emoji') {
        return category.icon || '📁';
    }
    if (type === 'font-awesome') {
        return '<i class="' + category.icon + ' dark:text-white"></i>';
    }
    if (type === 'image') {
        return '<img src="' + category.icon_url + '" alt="icon" class="w-6 h-6 object-contain">';
    }
    return '📁';
}
</script>
