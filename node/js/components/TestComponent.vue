<template>
  <section class="test">
    <p>This is a simple test component. Click the button to increment the counter.</p>
    <TestButton :label="`Clicked ${count} times`" @click="increment" />
  </section>
</template>

<script setup>
import { onMounted, onUnmounted, ref } from 'vue'
import TestButton from './TestButton.vue'

const count = ref(0)

function increment(event) {
  console.info('[TestComponent] increment handler fired', {
    current: count.value,
    next: count.value + 1,
    eventType: event?.type ?? null,
    eventDetail: event?.detail ?? null,
    timeStamp: event?.timeStamp ?? null,
    isTrusted: event?.isTrusted ?? null,
  })
  count.value += 1
}

let captureListener = null

onMounted(() => {
  console.info('[TestComponent] mounted', {
    componentId: Math.random().toString(36).slice(2, 8),
    initial: count.value,
  })
  captureListener = (evt) => {
    if (!evt.isTrusted) {
      console.info('[TestComponent] observed non-trusted click on window', {
        type: evt.type,
        detail: evt.detail,
        timeStamp: evt.timeStamp,
      })
    }
  }
  window.addEventListener('click', captureListener, { capture: true })
})

onUnmounted(() => {
  if (captureListener) {
    window.removeEventListener('click', captureListener, { capture: true })
    captureListener = null
  }
})
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

