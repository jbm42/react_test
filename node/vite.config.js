import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { fileURLToPath } from 'node:url'
import { dirname, resolve } from 'node:path'

const __filename = fileURLToPath(import.meta.url)
const __dirname = dirname(__filename)
const rootDir = resolve(__dirname, './js')
const outDir = resolve(__dirname, process.env.VITE_OUT_DIR ?? '../php/public/build')

export default defineConfig({
  plugins: [vue()],
  root: rootDir,
  define: {
    'process.env': {},
  },
  resolve: {
    alias: {
      vue: 'vue/dist/vue.esm-bundler.js',
    },
  },
  server: {
    port: 5173,
    host: '0.0.0.0',
    strictPort: true,
    allowedHosts: ['node'],
  },
  build: {
    outDir,
    emptyOutDir: true,
    lib: {
      entry: './inject.js',
      name: 'InjectApp',
      formats: ['es'],
      fileName: () => 'inject.js',
    },
    rollupOptions: {
      external: [],
      output: {
        inlineDynamicImports: true,
      },
    },
    minify: false,
  }
})
