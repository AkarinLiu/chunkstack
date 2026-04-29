import { createRouter, createWebHistory } from 'vue-router';
import LinkList from './components/LinkList.vue';
import LinkDetail from './components/LinkDetail.vue';

const routes = [
    {
        path: '/',
        name: 'home',
        component: LinkList,
    },
    {
        path: '/link/:slug',
        name: 'link.show',
        component: LinkDetail,
        props: true,
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
