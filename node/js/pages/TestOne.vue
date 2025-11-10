<template>
  <section class="test-one">
    <header>
      <h2>Counter Demo</h2>
      <p>Use the interactive counter below to verify hydration.</p>
    </header>

    <TestComponent />
  </section>
</template>

<script setup>
import TestComponent from '../components/TestComponent.vue'
</script>

<style scoped>
.test-one {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}
</style>

