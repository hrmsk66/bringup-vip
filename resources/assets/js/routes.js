import VueRouter from 'vue-router'

let routes = [
  {
    path: '/',
    redirect: '/rootcert'
  },
  {
    path: '/rootcert',
    component: require('./views/RootCert')
  },
  {
    path: '/csr',
    component: require('./views/CSR')
  },
  {
    path: '/activate',
    component: require('./views/Activation')
  },
]

export default new VueRouter({
  routes,
  linkActiveClass: 'is-active'
})