import { defineConfig, loadEnv } from 'vite'
import vue from '@vitejs/plugin-vue'
import laravel from 'laravel-vite-plugin'

export default ({ mode }) => {
  process.env = { ...process.env, ...loadEnv(mode, process.cwd()) }

  return defineConfig({
    build: {
      outDir: './public/build/',
    },
    server: {
      host: true,
      port: 5173,
      strictPort: true,
      origin: 'https://' + process.env.VITE_DEV_SERVER_DOCKER_HOST_NAME,
      cors: true, // Allow any origin
    },
    resolve: {
      alias: {
        '@': '/resources/js',
      },
    },
    plugins: [
      laravel({
        input: ['resources/css/app.css', 'resources/js/app.js'],
        refresh: true,
      }),
      vue({
        template: {
          transformAssetUrls: {
            base: null,
            includeAbsolute: false,
          },
        },
      }),
    ],
  })
}
