import App from './App.svelte'
import { DEFAULT_PAGE } from './pages/index.js'

export async function render(pageName = DEFAULT_PAGE, context = {}) {
  const result = App.render({ pageName, context })
  return {
    html: result.html,
    head: result.head,
    css: result.css?.code ?? '',
    page: pageName,
    context,
  }
}

