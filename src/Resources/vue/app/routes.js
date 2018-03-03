import VueRouter from 'vue-router';
import List from './views/pages/List.vue'
import View from './views/pages/View.vue'

let routes = [
    {
        path: '*',
        redirect: '/list'
    },
    {
        path: '/list',
        name: 'list',
        component: List
    },
    {
        path: '/view/:id',
        name: 'view',
        component: View
    }
];

let router = new VueRouter({
    // mode: 'history', // << do not use truncates to base url
    routes
});

export default router;