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

function resolveAssetPaths() {
  const entryChunk = manifest['entry-client.js']
  if (!entryChunk) {
    return { entry: null, css: [] }
  }

  const css = new Set(entryChunk.css ?? [])
  entryChunk.imports?.forEach((importId) => {
    const imported = manifest[importId]
    imported?.css?.forEach((href) => css.add(href))
  })

  return {
    entry: entryChunk.file ? `/${entryChunk.file}` : null,
    css: Array.from(css).map((href) => `/${href}`),
  }
}

app.get('/ssr', async (req, res) => {
  try {
    const requestedPage =
      typeof req.query.page === 'string' && req.query.page.trim().length > 0
        ? req.query.page.trim()
        : DEFAULT_PAGE

    let context = {}
    if (typeof req.query.context === 'string' && req.query.context.length > 0) {
      try {
        const parsed = JSON.parse(req.query.context)
        if (parsed && typeof parsed === 'object') {
          context = parsed
        }
      } catch (err) {
        console.warn('[ssr] failed to parse context payload', err)
      }
    }

    const { html } = await render(requestedPage, context)
    const assets = resolveAssetPaths()
    res.json({
      html,
      entry: assets.entry,
      css: assets.css,
      page: requestedPage,
      context,
    })
  } catch (err) {
    console.error(err)
    res.status(500).json({ error: 'SSR rendering failed' })
  }
})

const port = process.env.PORT || 5178
app.listen(port, () => {
  console.log(`Solid SSR server listening on port ${port}`)
})

