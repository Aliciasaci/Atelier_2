import Vue from 'vue'
import Vuex from 'vuex'
import VuexPersistence from 'vuex-persist'

Vue.use(Vuex)

const vuexLocal = new VuexPersistence({
    storage: window.localStorage
})

export default new Vuex.Store({
    plugins: [vuexLocal.plugin],
    state: {
        ready: false,
        token: "",
        member: false,
        invitations: [],
        token_visiteur: ""
    },
    getters: {},
    mutations: {
        setReady(state, ready) {
            state.ready = ready;
        },
        setToken(state, token) {
            state.token = token;
        },
        setTokenVisiteur(state, token_visiteur) {
            state.token_visiteur = token_visiteur;
        },
        setMember(state, member) {
            state.member = member;
        },
        setInvitations(state, invitations) {
            state.invitations = invitations;
        },
    },
    actions: {},
    modules: {}
})