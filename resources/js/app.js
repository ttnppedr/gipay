/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import VueCookies from 'vue-cookies';
import 'bulma/css/bulma.css';
import {
  library
} from '@fortawesome/fontawesome-svg-core';
import {
  faDollarSign
} from '@fortawesome/free-solid-svg-icons';
import {
  FontAwesomeIcon
} from '@fortawesome/vue-fontawesome';

window.Vue = require('vue');

library.add(faDollarSign);

Vue.component('font-awesome-icon', FontAwesomeIcon);

Vue.config.productionTip = false;


require('./bootstrap');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

// Vue.component('example-component', require('./views/ExampleComponent.vue').default);
Vue.component('admin-login', require('./views/AdminLogin.vue').default);
Vue.component('home', require('./views/AdminIndex.vue').default);
Vue.component('admin-users', require('./views/AdminUsers.vue').default);
Vue.component('admin-orders', require('./views/AdminOrders.vue').default);
Vue.component('navigation', require('./components/Navigation.vue').default);

Vue.use(VueCookies);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
  el: '#app'
});
