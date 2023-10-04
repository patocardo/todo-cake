<template>
    <div v-if="loading" class="spinner"></div>
</template>
  
  <script>
  export default {
    name: 'RotationSpinner',
    props: {
      loading: {
        type: Boolean,
        default: true
      }
    }
  }
  </script>
  
  <style scoped>
  .spinner {
    border: 8px solid #f3f3f3;
    border-top: 8px solid #3498db;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    animation: spin 2s linear infinite;
  }
  
  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
  </style>
  