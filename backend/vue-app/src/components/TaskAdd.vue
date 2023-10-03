<template>
    <div class="task-card" v-if="currentTask">
      <div class="task-card-title">
        <span v-if="currentTask.parent_id === null">New root task</span>
        <span v-else-if="currentTask">New subtask of {{ parentName }}</span>
      </div>
  
      <div class="task-card-content">
        <div class="input-group">
          <label for="task-name">Name</label>
          <input 
            id="task-name"
            v-model="currentTask.name"
            type="text"
            :class="{ 'is-invalid': nameError }"
          />
          <div v-if="nameError" class="error-message">{{ nameError }}</div>
        </div>
  
        <div class="input-group">
          <label>
            <input type="checkbox" v-model="currentTask.completed" />
            Completed
          </label>
        </div>
  
        <div class="input-group">
            <label for="task-image">Image</label>
            <ImageUpload 
                :image="currentTask.image_path"
                @removeImage="handleRemoveImage"
                @newImage="handleNewImage"
                @error="handleImageError"
            />

            <div v-if="imageError" class="error-message">{{ imageError }}</div>
        </div>
  
        <div class="input-group">
          <label>Video URL</label>
          <VideoInput @video_link="handleVideoUrl" @error="handleVideoError" />

          <div v-if="videoUrlError" class="error-message">{{ videoUrlError }}</div>
        </div>
  
        <button 
          :disabled="!isValid || loading.value"
          @click="submitForm"
        >
          <span v-if="loading.value">🔄</span> <!-- Spinner icon -->
          Submit
        </button>
  
        <button @click="setPanelOff">Close</button>
      </div>
    </div>
    <div v-else>
        <Spinner :loading="loading"></Spinner>
    </div>
</template>  
  
<script>
import { ref, computed, inject } from 'vue';
import ImageUpload from './ImageUpload.vue';
import VideoInput from './VideoInput.vue';
import Spinner from './Spinner.vue';

export default {
  name: 'AddTaskForm',
  components: {
    ImageUpload,
    VideoInput,
    Spinner,
  },
  setup() {
    const { tasks, loading, setPanelOff, displayViewTask, addTask, currentTask } = inject('tasks');

    const nameError = ref('');
    const imageError = ref('');
    const videoUrlError = ref('');

    function validateName() {
        if (!currentTask.value.name) {
            nameError.value = 'Name is required';
            return false;
        }
        if (currentTask.value.name.length < 3 || currentTask.value.name.length > 255) {
            nameError.value = 'Name must be between 3 and 255 characters';
            return false;
        }
        nameError.value = '';
        return true;
    }

    function validateImage() {
        if (currentTask.newImage && currentTask.newImage.size >= 2000000) {
            imageError.value = 'Image size should be less than 2 MB';
            return false;
        }
        imageError.value = '';
        return true;
    }

    function validateVideoUrl() {
        if (currentTask.video_link && !/^(https:\/\/)?(www\.youtube\.com|youtu\.?be)\/.+$/g.test(currentTask.video_link)) {
            videoUrlError.value = 'Invalid YouTube URL';
            return false;
        }
        videoUrlError.value = '';
        return true;
    }

    const isValid = computed(() => {
        return validateName() && validateImage() && validateVideoUrl();
    });

    const parentName = computed(() => {
        const parentTask = tasks.value.find(task => task.id === currentTask.value.parent_id);
        return parentTask ? parentTask.name : '';
    });

    function handleRemoveImage(newValue) {
        currentTask.value.removeImage = newValue;
    }
    function handleNewImage(file) {
        currentTask.value.newImage = file;
    }
    function handleImageError(message) {
        imageError.value = message;
    }

    function handleVideoUrl(url) {
        currentTask.value.video_link = url;
    }
    function handleVideoError(message) {
        videoUrlError.value = message;
    }

    async function submitForm() {
        const newId = await addTask(currentTask.value);
        if (newId) {
            displayViewTask(newId);
        }
    }

    return {
        nameError,
        imageError,
        videoUrlError,
        currentTask,
        handleVideoUrl,
        handleVideoError,
        isValid,
        parentName,
        loading,
        setPanelOff,
        submitForm,
        handleRemoveImage,
        handleNewImage,
        handleImageError,
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

.task-card-title {
  font-weight: bold;
  margin-bottom: 16px;
  font-size: 1.5rem;
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
</style>


