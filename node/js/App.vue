<template>
  <main class="page">
    <h1>Test Page</h1>
    <TestComponent />
  </main>
</template>

<script setup>
import TestComponent from './components/TestComponent.vue'
</script>

<style scoped>
.page {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  padding: 2rem;
  max-width: 640px;
  margin: 0 auto;
  line-height: 1.5;
}

h1 {
  margin-bottom: 1.5rem;
}
</style>

