<template>
    <div class="task-card" v-if="currentTask">
        <div class="task-card-header">
            <div class="task-card-title">
              {{ currentTask.name }}
            </div>
            <button class="closing" @click="setPanelOff()">
                X
            </button>
        </div>
  
      <div class="task-card-content">
  
        <div class="input-group">
          <label>
            {{ currentTask.completed ? 'Completed' : 'TO DO' }}
          </label>
        </div>
  
        <div class="image-preview">
            <template v-if="currentTask.image_path">
                <img :src="currentTask.image_path" class="image-element" alt="Uploaded Image" />
            </template>
            <template v-else>
                No Image
            </template>
        </div>
  
        <div class="input-group">
            <label>Video URL</label>
            <div v-if="currentTask.video_link">
                <div>{{ currentTask.video_link }}</div>
                <LiteYouTubeEmbed :id="videoId"></LiteYouTubeEmbed>
            </div>
            <div v-else>
                No video
            </div>

          <div v-if="videoUrlError" class="error-message">{{ videoUrlError }}</div>
        </div>
      </div>
    </div>
    <div v-else>
        <Spinner :loading="loading"></Spinner>
    </div>
</template>  
  
<script>
import { inject, computed } from 'vue';
import Spinner from './Spinner.vue';
import LiteYouTubeEmbed from 'vue-lite-youtube-embed'
import 'vue-lite-youtube-embed/style.css'

export default {
  name: 'AddTaskForm',
  components: {
    Spinner, LiteYouTubeEmbed,
  },
  setup() {
    const { loading, setPanelOff, currentTask } = inject('tasks');

    const videoId = computed(() => {
        if(!currentTask.value.video_link) return '';
        const videoRegex = /(?:v=|\/)([0-9A-Za-z_-]{10,12})/;
        const match = currentTask.value.video_link.match(videoRegex);
        if (!match || !match[1]) return '';
        return match[1]
    });

    return {
        loading,
        setPanelOff,
        currentTask,
        videoId,
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
  max-width: 500px;
  min-width: 300px;
}
.task-card-header {
    display: flex;
    justify-content: space-between;
}
.task-card-title {
  font-weight: bold;
  margin-bottom: 16px;
  font-size: 1.3rem;
  color: #3F51B5; /* Material Design primary color */
}

.task-card-content {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
}

.input-group, 
.input-group label, 
.input-group input[type="checkbox"] + span {
  grid-column: span 2;
}

.input-group label {
  display: block;
  margin-bottom: 8px;
  color: #3F51B5;
}

.input-group input,
.input-group button {
  width: calc(100% - 32px);
  padding: 10px;
  border-radius: 4px;
  border: 1px solid #ccc;
  font-size: 1rem;
}

.input-group input[type="checkbox"] {
  width: auto;
}

.input-group input[type="checkbox"] + span {
  margin-left: 8px;
}

.input-group input:focus {
  outline: none;
  border-color: #3F51B5;
}

.error-message {
  color: #f44336; /* Material Design error color */
  font-size: 12px;
  margin-top: 4px;
}

.is-invalid {
  border-color: #f44336;
}

button {
  background-color: #3F51B5;
  color: #fff;
  cursor: pointer;
  transition: background-color 0.3s;
}

button:disabled {
  background-color: #ccc;
  cursor: not-allowed;
}

button:hover:not(:disabled) {
  background-color: #303F9F;
}

button + button {
  margin-left: 16px;
}

.image-element {
    object-fit: contain;
    width: 100%;
    max-height: 400px;
}
.image-preview {
    padding: 20px;
    border: 1px dashed #ccc;
    text-align: center;
    cursor: pointer;
}
</style>


