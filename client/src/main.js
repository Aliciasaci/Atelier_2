import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import { Outils } from "./mixins/Outils.js";
Vue.mixin(Outils);
import axios from "axios";

Vue.prototype.$api = new axios.create({
    baseURL: "http://docketu.iutnc.univ-lorraine.fr:62364/",
    params: {},
    headers: {}
});

Vue.prototype.$api.interceptors.request.use(function(config) {
    //Authorization d'un user connecté
    if (store.state.token && (store.state.member.role == 100 || store.state.member.role == 200)) {
        config.headers.Authorization = `Bearer ${store.state.token}`
    }
    //Authorization d'un visiteur
    if (store.state.token_visiteur) {
        config.headers.Authorization = `Bearer ${store.state.token_visiteur}`
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