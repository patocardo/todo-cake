const { defineConfig } = require('@vue/cli-service');

module.exports = defineConfig({
  transpileDependencies: true,
  outputDir: '../webroot/js/vue-app/dist',
  publicPath: process.env.NODE_ENV === 'production'
    ? '/js/vue-app/dist/'
    : '/',
  filenameHashing: false,
});
