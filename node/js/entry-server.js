import { renderToString } from '@vue/server-renderer'
import { createApp } from './createApp.js'

export async function render() {
  const { app } = createApp()
  const ctx = {}
  const html = await renderToString(app, ctx)
  const modules = ctx.modules ? Array.from(ctx.modules) : []
  return { html, modules }
}

