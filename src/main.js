// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import './assets/css/normalize.css'

import Vue from 'vue'
import VueRouter from 'vue-router'
import App from './App'

Vue.use(VueRouter)

// We want to apply VueResource and VueRouter
// to our Vue instance

/* eslint-disable no-new */
new Vue({

  el: '#app',
  render: h => h(App)
})
