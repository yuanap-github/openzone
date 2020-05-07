import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

// export default new Router({
//   routes: [
//     {
//       path: '/',
//       name: 'HelloWorld',
//       component: HelloWorld
//     }
//   ]
// })
export const constantRouterMap = [
  { path: '/', component: () => import('@/views/home'), hidden: true },
  { path: '/add', component: () => import('@/views/add'), hidden: true },
  { path: '/add', component: () => import('@/views/add'), hidden: true },
  { path: '/message', component: () => import('@/views/message'), hidden: true },
  { path: '/mycenter', component: () => import('@/views/mycenter'), hidden: true },
  { path: '/updateinfo', component: () => import('@/views/updateinfo'), hidden: true },
  { path: '/login', component: () => import('@/views/login'), hidden: true }
]

export default new Router({
  // mode: 'history', //后端支持可开
  scrollBehavior: () => ({ y: 0 }),
  routes: constantRouterMap
})
