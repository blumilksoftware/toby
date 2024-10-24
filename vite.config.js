import { defineConfig, loadEnv  } from 'vite'
import vue from '@vitejs/plugin-vue'
import laravel from 'laravel-vite-plugin'
import { networkInterfaces } from 'os'

export default ({ mode }) => {
  process.env = { ...process.env, ...loadEnv(mode, process.cwd()) }

  return defineConfig({
    build: {
      outDir: './public/build/',
    },
    server: {
      host: Object.values(networkInterfaces()).flat().find(i => i.family === 'IPv4' && !i.internal).address,
      port: process.env.VITE_PORT,
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
