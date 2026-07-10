import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue2';

// The JS bundle is built by Vite; the stylesheet is compiled separately with
// the sass CLI and the static assets (tinymce, ace) are copied with
// scripts/copy-static.mjs (see the npm scripts) so that url() references are
// left untouched, like laravel-mix's processCssUrls: false did.
export default defineConfig({
    plugins: [
        vue(),
    ],
    resolve: {
        alias: {
            // The sidebar menu uses an in-DOM template, which needs the full
            // build with the template compiler.
            vue: 'vue/dist/vue.esm.js',
        },
    },
    build: {
        outDir: 'publishable/assets',
        // fonts/ and images/ already live in publishable/assets
        emptyOutDir: false,
        sourcemap: false,
        cssCodeSplit: false,
        rollupOptions: {
            input: 'resources/assets/js/app.js',
            output: {
                // Views load the bundle with a plain <script> tag by fixed
                // name, so: classic IIFE, no hashes.
                format: 'iife',
                inlineDynamicImports: true,
                entryFileNames: 'js/app.js',
                assetFileNames: 'js/[name][extname]',
            },
        },
    },
});
