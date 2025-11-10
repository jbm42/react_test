<template>
  <button class="button" type="button" @click="$emit('click')">
    {{ label }}
  </button>
</template>

<script setup>
defineProps({
  label: {
    type: String,
    default: 'Click me',
  },
})
</script>

<style scoped>
.button {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 8px;
  background-color: #2f81f7;
  color: white;
  font-size: 1rem;
  cursor: pointer;
  transition: background-color 0.2s ease-in-out;
}

.button:hover {
  background-color: #1b64d7;
}

.button:active {
  background-color: #124ea7;
}
</style>

