import Vue from 'vue';
import Vuex from 'vuex'
import VueRouter from 'vue-router'
import { Loading } from 'element-ui'
import Notifications from 'vue-notification'
import axios from 'axios';
import debounce from 'debounce'

Vue.use(Vuex);
Vue.use(VueRouter);
Vue.use(Loading.directive);
Vue.use(Notifications);

Vue.prototype.$loading = Loading.service;

Vue.filter('formatDate', (timestamp) => {
    let date = new Date(timestamp * 1000);
    return date.toLocaleString();
});

window.Vue = Vue;

window.Vuex = Vuex;

window.axios = axios;

window.debounce = debounce;

window.axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'Content-Type': 'application/x-www-form-urlencoded'
};

require('./css/bootstrap.min.css');
require('./css/fontawesome-all.min.css');
require('./css/style.css');
// require('../css/style.css');