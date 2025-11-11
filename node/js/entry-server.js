import { renderToString } from '@vue/server-renderer'
import { createApp } from './createApp.js'
import { DEFAULT_PAGE } from './pages/index.js'

export async function render(pageName = DEFAULT_PAGE, context = {}) {
  const { app } = createApp(pageName, context)
  const ctx = {}
  const html = await renderToString(app, ctx)
  const modules = ctx.modules ? Array.from(ctx.modules) : []
  return { html, modules }
}

