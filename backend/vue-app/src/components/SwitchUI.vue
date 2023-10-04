<template>
    <div class="switch">
        <div class="switch-path" :class="active ? 'active' : ''"></div>
        <div class="switch-knob" :class="active ? 'active' : ''"></div>
    </div>
</template>

<script>
export default {
    name: 'SwitchUI',
    props: {
      active: {
        type: Boolean,
        required: true
      }
    },
}

</script>

<style scoped>

.switch {
    position: relative;
    width: 50px;
    height: 24px;
}
  
.switch-path {
  border: 1px solid gray;
  background-color: #ccc;
  border-radius: 3px;
  position: absolute;
  width: 25px;
  height: 6px;
  top: 6px;
  transition: background-color .3s;
}
.switch-path.active {
  background: #3498db;
}

.switch-knob {
  background-color: #f0f0f0;
  border: 2px solid black;
  border-radius: 50%;
  height: 10px;
  width: 10px;
  position: absolute;
  top: 3px;
  left: 0;
  transition: left 0.3s;
}
.switch-knob.active {
  background: #3498db;
  left: 13px;
}
</style>