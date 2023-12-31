<template>
  <div class="task-container">
    <div class="task-row mb-2">
      <div class="task-col-1">
        <input 
            type="checkbox" 
            :checked="task.selected" 
            @change="toggleTaskSelected(task.id, $event.target.checked)"
        />
      </div>
      <div class="task-col-8" @click="displayViewTask(task.id)">
        {{ task.name }}
      </div>
      <Spinner v-if="loading" :loading="true" />
      <div class="task-col-3 task-actions">
          <button class="icon-btn" @click="displayDeleteConfirm(task.id)">🗑</button>
          <button class="icon-btn" @click="displayAddTask(task.id)">⨢📋</button>
          <button class="icon-btn" @click="displayEditTask(task.id)">✎</button>
          <SwitchUI @click="toggleCompleted()" :active="task.completed" />
          <button class="icon-btn" @click="toggleTaskFold(task.id)">
              {{ task.folded ? '⏷' : '⏶' }}
          </button>
      </div>
    </div>
    <div v-if="!task.folded" class="margin">
      <TaskItem
          v-for="subtask in task.children"
          :key="subtask.id"
          :task="subtask"
      />
    </div>
  </div>
</template>

<script>
  import { onUnmounted, inject } from 'vue';
  import SwitchUI from './SwitchUI.vue';
  import Spinner from './Spinner.vue';
  
  export default {
    name: 'TaskItem',
    components: {
    Spinner, SwitchUI,
},
    props: {
      task: {
        type: Object,
        required: true
      }
    },
    setup(props) {
      const {
        loading,
        toggleTaskSelected,
        displayAddTask,
        displayEditTask,
        displayViewTask,
        toggleTaskFold,
        displayDeleteConfirm,
        displayCompletedConfirm,
        setSubtasksSelected,
        updateTask,
      } = inject('tasks');

      const toggleCompleted = () => {
        if(props.task.completed) {
          updateTask({...props.task, completed: false});
          return;
        }
        displayCompletedConfirm(props.task.id);
      }
  
      onUnmounted(() => {
        setSubtasksSelected(props.task.id, false);
        toggleTaskSelected(props.task.id, false);
        toggleTaskFold(props.task.id, false);
      });
  
      return {
        toggleTaskSelected,
        displayAddTask,
        displayEditTask,
        displayViewTask,
        toggleTaskFold,
        displayDeleteConfirm,
        displayCompletedConfirm,
        loading,
        toggleCompleted,
      };
    }
  };
  </script>
  
  <style scoped>
  .task-container {
    font-family: 'Arial', sans-serif;
  }
  
  .task-row {
    display: flex;
    align-items: center;
  }
  
  .task-col-1, .task-col-8, .task-col-3 {
    padding: 0.5rem;
  }
  
  .task-col-1 {
    flex: 1;
  }
  
  .task-col-8 {
    flex: 8;
  }
  
  .task-col-3 {
    flex: 3;
    display: flex;
    justify-content: space-between;
  }
  
  .icon-btn {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 1.2rem;
    padding: 0.5rem;
    transition: background-color 0.3s;
    text-wrap: nowrap;
  }
  
  .icon-btn:hover {
    background-color: #f0f0f0;
  }
  
  .mb-2 {
    margin-bottom: 0.5rem;
  }
  
  .task-actions {
    display: flex;
    gap: 0.5rem;
    align-items: center;
  }

  .margin {
    margin-left: 2rem;
  }

  </style>
  