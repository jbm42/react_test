<template>
  <section class="test">
    <p>This is a simple test component. Click the button to increment the counter.</p>
    <TestButton :label="`Clicked ${count} times`" @click="increment" />
  </section>
</template>

<script setup>
import { ref } from 'vue'
import TestButton from './TestButton.vue'

const count = ref(0)

function increment() {
  count.value += 1
}
</script>

<style scoped>
.test {
  padding: 1.5rem;
  border-radius: 12px;
  border: 1px solid #d0d7de;
  background-color: #f6f8fa;
  color: #24292f;
}

p {
  margin-bottom: 1rem;
}
</style>

