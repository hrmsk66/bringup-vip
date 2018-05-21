import VueRouter from 'vue-router'

let routes = [
  {
    path: '/',
    redirect: '/vedge'
  },
  {
    path: '/controllers',
    component: require('./views/Controllers')
  },
  {
    path: '/vedge',
    component: require('./views/vEdge')
  },
]

export default new VueRouter({
  routes,
  linkActiveClass: 'is-active'
})