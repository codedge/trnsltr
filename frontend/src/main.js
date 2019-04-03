import axios from 'axios'
import Vue from 'vue'
import './plugins/vuetify'
import App from './App.vue'
import router from './router'

Vue.config.productionTip = false

// Register axios globally under this.$api
Vue.use({
  install (Vue) {
    const baseUrl = process.env.VUE_APP_API_URL
    Vue.prototype.$api = axios.create({
      baseURL: baseUrl
    })

    const token = process.env.VUE_APP_API_TOKEN
    Vue.prototype.$api.defaults.headers['Authorization'] = 'Bearer ' + token
  }
})

new Vue({
  router,
  render: h => h(App)
}).$mount('#app')
