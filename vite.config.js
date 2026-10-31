import { defineConfig } from 'vite';

export default defineConfig({
  base: './',
  publicDir: false,
  build: {
    outDir: 'public/assets/build',
    emptyOutDir: true,
    minify: true,
    rollupOptions: {
      input: 'src/learning.jsx',
      output: {
        entryFileNames: 'learning.js',
        chunkFileNames: '[name].js',
        assetFileNames: '[name][extname]',
      },
    },
  },
});

