import './bootstrap';
import router from './routes.js';
import store from './store'
import Layout from './views/Layout.vue'

new Vue({

    el: '#app',
    store,
    router,
    components: {
        Layout
    }

});