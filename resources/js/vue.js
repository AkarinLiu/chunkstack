import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import App from './components/App.vue';
import router from './router';

const app = createApp({});

export function mount(component, selector) {
    const el = document.querySelector(selector);
    if (el) {
        app.component(component.name || 'mounted-component', component);
        app.mount(el);
    }
}

export function mountComponent(component, selector) {
    document.querySelectorAll(selector).forEach((el) => {
        const props = { ...el.dataset };
        const instance = createApp(component, props);
        instance.mount(el);
    });
}

export function mountApp() {
    const el = document.getElementById('app');
    if (!el) return;

    const initialData = window.__INITIAL_DATA__ || {};

    const spaApp = createApp(App, {
        initialData,
    });

    spaApp.use(router);
    spaApp.mount('#app');
}

export default app;
