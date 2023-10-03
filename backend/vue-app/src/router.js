/* This approach was temporarly disables due problems with nginx */

import { createRouter, createWebHistory } from 'vue-router';
const HelloWorld = () => import('@/components/HelloWorld');
import TaskList from './views/TaskList.vue';

const routes = [
  {
    path: '/hello',
    name: 'helloworld',
    component: HelloWorld
  },
      {
      path: '/task/list',
      name: 'list-tasks',
      component: TaskList
    },
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

export default router;
