<template>
  <main class="page">
    <div class="renderer-label">Vue Renderer</div>
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
  context: {
    type: Object,
    default: () => ({}),
  },
})

if (import.meta.env.SSR) {
  console.info('[ssr] rendering page', props.pageName)
}

onMounted(() => {
  console.info('[hydrate] props.pageName', props.pageName, 'context', props.context)
})

const resolvedComponent = computed(() => resolvePage(props.pageName))
</script>

<style scoped>
.page {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  text-transform: none;
  letter-spacing: normal;
  min-height: 100%;
  padding: 2.5rem;
  max-width: 820px;
  margin: 0 auto;
  line-height: 1.6;
  display: flex;
  flex-direction: column;
  gap: 1.75rem;
  border-radius: 28px;
  background: #0d1b2a;
  box-shadow:
    0 20px 45px rgba(8, 47, 73, 0.45),
    inset 0 0 0 1px rgba(255, 255, 255, 0.15);
  color: #e2f1ff;
}

.renderer-label {
  align-self: flex-end;
  background: rgba(148, 163, 184, 0.18);
  border: 1px solid rgba(148, 163, 184, 0.35);
  border-radius: 999px;
  color: #93c5fd;
  font-size: 0.75rem;
  letter-spacing: 0.12em;
  padding: 0.35rem 0.85rem;
  text-transform: uppercase;
}

:deep(h1),
:deep(h2) {
  margin: 0;
}

:deep(p) {
  color: rgba(226, 241, 255, 0.85);
}
</style>

