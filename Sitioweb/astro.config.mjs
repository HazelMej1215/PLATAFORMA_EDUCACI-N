import { defineConfig } from 'astro/config';

export default defineConfig({
  vite: {
    server: {
      proxy: {
        '/api': {
          target: 'http://localhost/plataforma',
          changeOrigin: true,
          rewrite: (path) => path.replace(/^\/api/, '')
        }
      }
    }
  }
});