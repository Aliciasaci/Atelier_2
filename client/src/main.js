import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import { Outils } from "./mixins/Outils.js";
Vue.mixin(Outils);
import axios from "axios";

Vue.prototype.$api = new axios.create({
    baseURL: "http://api.backoffice.local:62364/",
    params: {},
    headers: {}
});

Vue.prototype.$api.interceptors.request.use(function(config) {
    if (store.state.token) {
        config.headers.Authorization = `Bearer ${store.state.token}`
    }
    return config;
});

Vue.config.productionTip = false

new Vue({
    router,
    store,
    render: h => h(App)
}).$mount('#app')

Vue.component("Header", () =>
    import ("@/components/Header.vue"));


Vue.component("Footer", () =>
    import ("@/components/Footer.vue"));