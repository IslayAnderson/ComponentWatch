import { defineConfig } from 'vite'

export default defineConfig({
  build: {
    lib: {
      entry: 'src/index.js',
      name: 'ComponentWatcher',
      fileName: 'component-watcher',
      formats: ['es', 'umd'],
    },
    rollupOptions: {
      external: [],
    },
  },
})
