import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import { fileURLToPath } from 'node:url'
import { dirname, resolve } from 'node:path'

const __filename = fileURLToPath(import.meta.url)
const __dirname = dirname(__filename)
const rootDir = __dirname
const buildTarget = process.env.BUILD_TARGET
const isSsr = buildTarget === 'ssr'

export default defineConfig(() => {
  const outDir = isSsr
    ? resolve(__dirname, './dist/server')
    : resolve(__dirname, './dist/client')

  const input = isSsr
    ? resolve(rootDir, './entry-server.jsx')
    : resolve(rootDir, './entry-client.js')

  return {
    plugins: [react()],
    root: rootDir,
    define: {
      'process.env': {},
    },
    server: {
      port: 5176,
      host: '0.0.0.0',
      strictPort: true,
      allowedHosts: ['react'],
    },
    build: {
      target: 'esnext',
      outDir,
      emptyOutDir: true,
      manifest: !isSsr,
      ssr: isSsr,
      rollupOptions: {
        input,
      },
      cssCodeSplit: true,
    },
  }
})

