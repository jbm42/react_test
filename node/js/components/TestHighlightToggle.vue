<template>
  <section class="test-two">
    <header>
      <h2>Highlight Toggle</h2>
      <p>Toggle the highlight state to confirm reactivity.</p>
    </header>

    <ul :class="{ highlighted }">
      <li v-for="(item, index) in items" :key="index">{{ item }}</li>
    </ul>

    <TestButton
      :label="highlighted ? 'Remove highlight' : 'Highlight list'"
      @click="toggleHighlight"
    />
  </section>
</template>

<script setup>
import { ref } from 'vue'
import TestButton from './TestButton.vue'

const items = ref(['Alpha', 'Bravo', 'Charlie', 'Delta'])
const highlighted = ref(false)

function toggleHighlight() {
  highlighted.value = !highlighted.value
}
</script>

<style scoped>
.test-two {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

ul {
  margin: 0;
  padding: 1rem;
  list-style: square inside;
  border: 1px solid #d0d7de;
  border-radius: 12px;
  transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out;
}

ul.highlighted {
  background-color: #fff3d4;
  border-color: #d4a72c;
}
</style>

