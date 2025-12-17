import { defineConfig } from 'vite';

export default defineConfig({
    publicDir: false, // Prevent copying public/ to outDir since outDir is inside public/
    build: {
        outDir: 'public/assets/build',
        emptyOutDir: true,
        rollupOptions: {
            input: 'resources/css/app.css',
            output: {
                entryFileNames: 'style.css',
                assetFileNames: '[name].[ext]'
            }
        }
    }
});
