import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

export default new Router({
  mode: 'history',
  base: process.env.BASE_URL,
  routes: [
    {
      path: '/',
      name: 'home',
      redirect: {
        name: 'new-translation'
      }
    },
    {
      path: '/translation/new',
      name: 'new-translation',
      component: () => import(/* webpackChunkName: "new-translation" */ './views/NewTranslation.vue')
    },
    {
      path: '/translation/list',
      name: 'list-translation',
      component: () => import(/* webpackChunkName: "list-translation" */ './views/ListTranslation.vue')
    }
  ]
})
