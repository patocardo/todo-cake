<template>
  <div class="task-card">
    <div class="filter-bar">
      <!-- Search Input -->
      <label for="taskSearch">Search:</label>
      <input 
        id="taskSearch"
        v-model.trim="search" 
        placeholder="Search tasks..." 
        aria-label="Search tasks"
      />

      <!-- Status Filter Dropdown -->
      <label for="taskStatusFilter">✔:</label>
      <select v-model="completion" id="taskStatusFilter" aria-label="Filter tasks by status">
          <option :value="null">All</option>
          <option :value="true">Completed</option>
          <option :value="false">Uncompleted</option>
      </select>

      <!-- Filter Button -->
      <button @click="applyFilter" aria-label="Apply filter">
          <i class="filter-icon">🔍</i> <!-- TODO SVG icon -->
      </button>

      <!-- Add Button -->
      <button @click="displayAddTask(parentId)" aria-label="Add a root task">
          <i class="filter-icon">+📋</i> <!-- TODO SVG icon -->
      </button>
      <!-- Remove Selected Button -->
      <button 
          @click="displayDeleteConfirm()" 
          :disabled="!anyTaskSelected"
          aria-label="Remove selected tasks"
      >
        🗑
      </button>
    </div>

    <TaskItem
      v-for="task in taskTree"
      :key="task.id"
      :task="task"
    />
    <!-- 
Lazy Loading 
    <div v-if="loading" class="loading">Loading...</div>
    <v-dialog v-model="dialogVisible" max-width="290">
      <v-card>
        <v-card-title class="headline">Are you sure?</v-card-title>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="green darken-1" text @click="confirmAction">Yes</v-btn>
          <v-btn color="red darken-1" text @click="dialogVisible = false">No</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
-->
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { inject } from 'vue'
import TaskItem from './TaskItem.vue';

export default {
  props: {
    parentId: {
      type: Number,
      default: null
    }
  },
  components: {
    TaskItem,
  },
  setup(props) {
    const {         
      tasks,
      loading,
      displayAddTask,
      fetchTasks,
      displayDeleteConfirm,
      displayCompletedConfirm,
    } = inject('tasks');

    const search = ref('');
    const prevSearch = '';
    const completion = ref(null);
    const prevCompletion = null;
    const page = ref(1);

    onMounted(() => {
      fetchTasks({ 
        search: search.value,
        parentId: props.parentId,
        page: page.value,
        ...(completion.value !== null ? { completed: completion.value } : {}),
      });
    });

    const taskTree = computed(() => {

      const buildTree = (parentId) => {
        return tasks.value
          .filter(task => task.parent_id === parentId)
          .map(task => {
            const children = buildTree(task.id);
            return children.length ? { ...task, children } : task;
          });
      };
      return buildTree(props.parentId);
    });

    const anyTaskSelected = computed(() => {
      return tasks.value.some(task => task.selected);
    });

    const applyFilter = () => {
      if(props.parentId) return null; // filtering only applied to root level.
      fetchTasks({
        search,
        parentId: null,
        ...(completion.value !== null ? { completed: completion.value } : {}),
        // new filter condition will refresh the list
        page: (search.value !== prevSearch || completion.value !== prevCompletion) ? 1 : page.value,
      });
    };

    return {
      search,
      completion,
      loading,
      taskTree,
      tasks,
      anyTaskSelected,
      applyFilter,
      displayAddTask,
      displayDeleteConfirm,
      displayCompletedConfirm,
    };
  }
};
</script>

<style scoped>
  .task-card {
    border: 1px solid #ccc;
    padding: 16px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    max-width: 450px;
  }
</style>
