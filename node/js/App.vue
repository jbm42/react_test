<template>
  <main class="page">
    <component :is="resolvedComponent" />
  </main>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { resolvePage, DEFAULT_PAGE } from './pages/index.js'

const props = defineProps({
  pageName: {
    type: String,
    default: DEFAULT_PAGE,
  },
})

if (import.meta.env.SSR) {
  console.info('[ssr] rendering page', props.pageName)
}

onMounted(() => {
  const container = document.getElementById('reactive')
  const containerPage = container?.dataset.page ?? null
  console.info('[hydrate] props.pageName', props.pageName, 'container dataset', containerPage)
})

const resolvedComponent = computed(() => resolvePage(props.pageName))
</script>

<style scoped>
.page {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  padding: 2rem;
  max-width: 720px;
  margin: 0 auto;
  line-height: 1.5;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

:deep(h1),
:deep(h2) {
  margin: 0;
}
</style>

