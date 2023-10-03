<template>
    <div>
        <!-- Unified Image Preview -->
        <div class="image-preview" @click="selectImage">
            <template v-if="selectedImage">
                <img :src="selectedImage" class="image-element" alt="Selected Image" />
            </template>
            <template v-else-if="image">
                <img :src="image" class="image-element" alt="Uploaded Image" />
            </template>
            <template v-else>
            No Image
            </template>
        </div>
    
        <!-- Controls under the preview -->
        <div class="controls">
            <div v-if="image">
                <input type="checkbox" v-model="removeImage" /> Delete
            </div>
            <div v-if="selectImage">
                <button v-if="selectedImage" @click="cancelImage">⎌</button>
            </div>
        </div>
        
        <!-- Hidden Image Input -->
        <input type="file" ref="fileInput" @change="handleImageChange" style="display: none" />
    </div>
</template>
  
<script>
  export default {
    props: {
      image: {
        type: String,
        default: ''
      }
    },
    data() {
      return {
        removeImage: false,
        selectedImage: null
      };
    },
    methods: {
      selectImage() {
        this.$refs.fileInput.click();
      },
      handleImageChange(event) {
        const file = event.target.files[0];
        if (file) {
          this.selectedImage = URL.createObjectURL(file);
          this.$emit('newImage', file);
        } else {
          this.$emit('error', 'Failed to load the image.');
        }
      },
      cancelImage() {
        this.selectedImage = null;
      }
    },
    watch: {
      removeImage(newValue) {
        this.$emit('removeImage', newValue);
      }
    }
  };
  </script>
  
  <style scoped>

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
  
  .controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 10px;
  }
  </style>
  