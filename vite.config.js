import { defineConfig } from 'vite'

export default defineConfig({
    build: {
        outDir: './app/public',
        emptyOutDir: false,
        manifest: true,
        rollupOptions: {
            input: 'app/resources/js/app.js'
        }
    }
})
