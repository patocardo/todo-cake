<template>
    <div>
      <input
        v-model="inputUrl"
        placeholder="Paste or write a YouTube URL here"
        @input="validateUrl"
        class="input"
      />   
      <LiteYouTubeEmbed v-if="isValid" :id="videoId"></LiteYouTubeEmbed>
    </div>
  </template>
  
  <script>
  import { ref, watch } from 'vue';
  import LiteYouTubeEmbed from 'vue-lite-youtube-embed'
  import 'vue-lite-youtube-embed/style.css'
  
  export default {
    name: 'VideoInput',
    components: {
      LiteYouTubeEmbed,
    },
    props: {
      video_link: {
        type: String,
        default: '',
      },
    },
    emits: ['video_link', 'error'],
    setup(props, { emit }) {
      const inputUrl = ref(props.video_link);
      const isValid = ref(false);
      const videoId = ref('');
      const errorMessage = ref('');
  
      const validateUrl = () => {
        const youtubeRegex = /^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.?be)\/.+$/;
        if (youtubeRegex.test(inputUrl.value)) {
          const videoRegex = /(?:v=|\/)([0-9A-Za-z_-]{10,12})/;
          const match = !inputUrl.value || inputUrl.value.match(videoRegex);
          if (match) {
            videoId.value = match[1];
            isValid.value = true;
            errorMessage.value = '';
            emit('video_link', inputUrl.value);
            emit('error', '');
          } else {
            isValid.value = false;
            errorMessage.value = 'Invalid YouTube video URL';
            emit('error', errorMessage.value);
          }
        } else {
          isValid.value = false;
          errorMessage.value = 'Invalid URL';
          emit('error', errorMessage.value);
        }
      };
  
      watch(
        () => props.video_link,
        (newUrl) => {
          inputUrl.value = newUrl;
          validateUrl();
        }
      );
  
      return {
        inputUrl,
        isValid,
        videoId,
        errorMessage,
        validateUrl,
      };
    },
  };
  </script>
  
<style scoped>
.input {
  border: 1px solid darkslategray;
  border-radius: 3px;
  padding: 4px;
  margin-bottom: 1rem;
}
</style>
  