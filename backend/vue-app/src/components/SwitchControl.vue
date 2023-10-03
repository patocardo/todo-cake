<template>
    <div class="switch-container" :title="isOn ? stringOn : stringOff">
      <input 
        type="checkbox" 
        v-model="isOn" 
        class="switch" 
        id="switch"
        @change="toggleSwitch"
      >
      <label :class="{ 'switch-on': isOn }" for="switch"></label>
    </div>
  </template>
  
  <script>
  export default {
    name: 'SwitchControl',
    props: {
      stringOn: {
        type: String,
        default: 'On'
      },
      stringOff: {
        type: String,
        default: 'Off'
      },
      value: {
        type: Boolean,
        default: false
      }
    },
    data() {
      return {
        isOn: this.value
      }
    },
    methods: {
      toggleSwitch() {
        this.$emit('input', this.isOn);
      }
    }
  }
  </script>
  
  <style scoped>
  .switch-container {
    position: relative;
    width: 60px;
    height: 34px;
  }
  
  .switch {
    opacity: 0;
    width: 0;
    height: 0;
  }
  
  label {
    position: absolute;
    cursor: pointer;
    text-indent: -9999px;
    top: 0;
    left: 0;
    width: 24px;
    height: 8px;
    background: grey;
    display: block;
    border-radius: 100px;
    position: relative;
    transition: background-color 0.3s;
  }
  
  label:before {
    content: '';
    position: absolute;
    top: -2px;
    left: 2px;
    width: 11px;
    height: 11px;
    background: #fff;
    border-radius: 90px;
    border: 1px solid gray;
    transition: 0.3s;
  }
  
  input:checked + label {
    background: #3498db;
  }
  
  input:checked + label:before {
    left: calc(100% - 2px);
    background: #3498db;
    border: 2px solid black;
    transform: translateX(-100%);
  }
  
  label:active:before {
    width: 35px;
  }
  
  .switch-on {
    background-color: #3498db;
  }
  </style>
  