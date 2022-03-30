import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

const routes = [{
        path: '/',
        name: 'Login',
        component: () =>
            import ('../views/Login.vue')
    },
    {
        path: '/SingnIn',
        name: 'SingnIn',
        component: () =>
            import ('../views/SingnIn.vue')
    },
    {
        path: '/Accueil',
        name: 'Accueil',
        component: () =>
            import ('../views/Accueil.vue')
    },
    {
        path: '/FormEvent',
        name: 'FormEvent',
        component: () =>
            import ('../views/FormEvent.vue')
    },
    {
        path: '/ProfilUser',
        name: 'ProfilUser',
        component: () =>
            import ('../views/ProfilUser.vue')
    },
    {
        path: '/Invitations',
        name: 'Invitations',
        component: () =>
            import ('../views/Invitations.vue')
    },
    {
        path: '/DetailEvent/:id',
        name: 'DetailEvent',
        component: () =>
            import ('../views/DetailEvent.vue')
    },
    {
        path: '/LogOut/',
        name: 'LogOut',
        component: () =>
            import ('../views/LogOut.vue')
    },
    {
        path: '/BackOffice/',
        name: 'BackOffice',
        component: () =>
            import ('../views/BackOffice.vue')
    },
]

const router = new VueRouter({
    mode: 'history',
    base: process.env.BASE_URL,
    routes
})

export default router