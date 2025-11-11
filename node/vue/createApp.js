import { createSSRApp } from 'vue'
import App from './App.vue'
import { DEFAULT_PAGE } from './pages/index.js'

export function createApp(pageName = DEFAULT_PAGE, context = {}) {
  const app = createSSRApp(App, { pageName, context })
  return { app }
}

