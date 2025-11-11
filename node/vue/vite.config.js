import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
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
    ? resolve(rootDir, './entry-server.js')
    : resolve(rootDir, './entry-client.js')

  return {
    plugins: [vue()],
    root: rootDir,
    define: {
      'process.env': {},
    },
    server: {
      port: 5173,
      host: '0.0.0.0',
      strictPort: true,
      allowedHosts: ['vue'],
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
    },
  }
})
