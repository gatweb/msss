import { defineConfig } from 'vite';

export default defineConfig({
    build: {
        outDir: 'public/assets/build',
        emptyOutDir: true,
        rollupOptions: {
            input: 'resources/css/app.css',
            output: {
                entryFileNames: 'app.css', // Force static name for simplicity in MVP
                assetFileNames: '[name].[ext]'
            }
        }
    }
});
