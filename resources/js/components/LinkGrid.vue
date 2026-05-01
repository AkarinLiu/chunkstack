<template>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <LinkCard
            v-for="link in links"
            :key="link.id"
            :link="link"
            :bg-class="bgClass"
            @navigate="handleNavigate"
        />
    </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import LinkCard from './LinkCard.vue';
import axios from 'axios';

defineProps({
    links: {
        type: Array,
        required: true,
    },
    bgClass: {
        type: String,
        default: '',
    },
});

const router = useRouter();

function handleNavigate(slug) {
    axios.post('/api/link-views/' + slug).catch(() => {});
    router.push({ name: 'link.show', params: { slug } });
}
</script>
