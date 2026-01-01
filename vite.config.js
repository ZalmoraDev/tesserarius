import { defineConfig } from 'vite'

export default defineConfig({
    build: {
        outDir: './app/public/assets',
        emptyOutDir: true,
        manifest: true,
        rollupOptions: {
            input: 'app/resources/js/app.js'
        }
    }
})
