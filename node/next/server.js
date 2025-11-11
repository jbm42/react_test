import express from 'express'
import next from 'next'
import { fileURLToPath } from 'node:url'
import { dirname, join } from 'node:path'
import { readFileSync } from 'node:fs'

const __filename = fileURLToPath(import.meta.url)
const __dirname = dirname(__filename)

const isProd = process.env.NODE_ENV === 'production'
const app = next({ dev: !isProd, dir: __dirname })
const handle = app.getRequestHandler()

await app.prepare()

const buildManifestPath = join(__dirname, '.next', 'build-manifest.json')
const buildManifest = JSON.parse(readFileSync(buildManifestPath, 'utf-8'))

const PAGE_MAP = {
  'test-one': '/test-one',
  'test-two': '/test-two',
}

const DEFAULT_PAGE = 'test-one'

function resolveAssets(route) {
  const files = new Set([
    ...(buildManifest.pages['/_app'] ?? []),
    ...(buildManifest.pages[route] ?? []),
  ])

  const js = Array.from(files)
    .filter((file) => file.endsWith('.js'))
    .map((file) => `/_next/${file}`)

  const css = Array.from(files)
    .filter((file) => file.endsWith('.css'))
    .map((file) => `/_next/${file}`)

  return {
    entries: js,
    css,
  }
}

const server = express()

server.get('/health', (_req, res) => {
  res.type('text/plain').send('ok')
})

server.get('/ssr', async (req, res) => {
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

    const route = PAGE_MAP[requestedPage] ?? '/404'
    const html = await app.renderToHTML(req, res, route, { context })
    if (res.headersSent) {
      return
    }

    const assets = resolveAssets(route)
    res.json({
      html,
      entry: assets.entries,
      css: assets.css,
      page: requestedPage,
      context,
    })
  } catch (err) {
    console.error(err)
    res.status(500).json({ error: 'SSR rendering failed' })
  }
})

server.all('*', (req, res) => handle(req, res))

const port = process.env.PORT || 5175
server.listen(port, () => {
  console.log(`Next.js SSR server listening on port ${port}`)
})

