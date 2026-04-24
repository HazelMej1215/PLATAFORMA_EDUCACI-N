import { defineConfig } from 'astro/config';

export default defineConfig({
  server: {
    port: 4321
  },
  vite: {
    server: {
      proxy: {
        '/php': {
          target: 'http://localhost/Plataforma',
          changeOrigin: true,
          rewrite: (path) => path
        }
      }
    }
  }
});