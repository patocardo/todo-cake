<template>
  <TaskPage />
</template>

<script>
import TaskPage from './views/TaskPage.vue';
import useTasks from './store/useTasks';
import useErrors from './store/useErrors';
import { useConfirm } from './store/useConfirm';

export default {
  name: 'App',
  components: {
    TaskPage
  },
  setup() {
    const { handleError} = useErrors();
    const { displayConfirmation } = useConfirm();
    useTasks(handleError, displayConfirmation);
  }
}
</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  color: #2c3e50;
  margin-top: 60px;
}
</style>
