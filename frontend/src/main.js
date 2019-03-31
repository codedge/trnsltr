import axios from 'axios'
import Vue from 'vue'
import './plugins/vuetify'
import App from './App.vue'
import router from './router'

Vue.config.productionTip = false

// Register axios globally under this.$api
Vue.use({
  install (Vue) {
    Vue.prototype.$api = axios.create({
      baseURL: 'http://localhost:8081/api/v1/'
    })
  }
})

new Vue({
  router,
  render: h => h(App)
}).$mount('#app')
