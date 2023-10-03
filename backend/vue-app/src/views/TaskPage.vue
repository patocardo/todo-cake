<template>
  <div class="page-container">
    <TaskList :parentId="null" />
    <TaskAdd v-if="['add', 'edit'].includes(displayPanel)" />
    <TaskView v-if="displayPanel == 'view'" />
  </div>
  <ConfirmDialog></ConfirmDialog>
</template>

<script>
import { inject } from 'vue';
import TaskList from '@/components/TaskList.vue';
import TaskAdd from '@/components/TaskAdd.vue';
import TaskView from '@/components/TaskView.vue';
import ConfirmDialog from '@/components/ConfirmDialog.vue';

export default {
  components: {
    TaskList, TaskAdd, ConfirmDialog, TaskView,
  },
  setup() {
    const {displayPanel} = inject('tasks');
    return {
      displayPanel,
    }
  }
};
</script>

<style scoped>
.page-container {
  display: flex;
}
</style>