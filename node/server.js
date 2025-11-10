import express from 'express'
import { fileURLToPath } from 'node:url'
import { dirname, resolve } from 'node:path'
import { readFileSync } from 'node:fs'
const __filename = fileURLToPath(import.meta.url)
const __dirname = dirname(__filename)
const isProd = process.env.NODE_ENV === 'production'

if (!isProd) {
  console.error('SSR server must run in production mode. Please build first and start with `npm run serve`.')
  process.exit(1)
}

const clientDist = resolve(__dirname, './dist/client')
const serverDist = resolve(__dirname, './dist/server')
const manifestPath = resolve(clientDist, '.vite/manifest.json')

const manifest = JSON.parse(readFileSync(manifestPath, 'utf-8'))
const { render } = await import(resolve(serverDist, 'entry-server.js'))

const app = express()

const DEFAULT_PAGE = 'test-one'

app.use('/assets', express.static(resolve(clientDist, 'assets')))

app.get('/health', (_req, res) => {
  res.type('text/plain').send('ok')
})

function resolveAssetPaths(modules = []) {
  const css = new Set()
  let entry = null

  for (const id of modules) {
    const manifestEntry = manifest[id]
    if (!manifestEntry) continue
    if (!entry && manifestEntry.isEntry) {
      entry = manifestEntry.file
    }
    manifestEntry.css?.forEach((href) => css.add(href))
    manifestEntry.imports?.forEach((importId) => {
      const imported = manifest[importId]
      imported?.css?.forEach((href) => css.add(href))
    })
  }

  if (!entry) {
    const clientEntry = manifest['entry-client.js']
    entry = clientEntry?.file
    clientEntry?.css?.forEach((href) => css.add(href))
    clientEntry?.imports?.forEach((importId) => {
      const imported = manifest[importId]
      imported?.css?.forEach((href) => css.add(href))
    })
  }

  return {
    entry: entry ? `/${entry}` : null,
    css: Array.from(css).map((href) => `/${href}`),
  }
}

app.get('/ssr', async (req, res) => {
  try {
    const requestedPage =
      typeof req.query.page === 'string' && req.query.page.trim().length > 0
        ? req.query.page.trim()
        : DEFAULT_PAGE

    const { html, modules } = await render(requestedPage)
    const assets = resolveAssetPaths(modules)
    res.json({
      html,
      entry: assets.entry,
      css: assets.css,
      page: requestedPage,
    })
  } catch (err) {
    console.error(err)
    res.status(500).json({ error: 'SSR rendering failed' })
  }
})

const port = process.env.PORT || 5173
app.listen(port, () => {
  console.log(`SSR server listening on port ${port}`)
})

