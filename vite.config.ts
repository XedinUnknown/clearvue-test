import {defineConfig} from 'vite'
import vue from '@vitejs/plugin-vue'
import dotenv from 'dotenv'

dotenv.config()
const webPort = parseInt(process.env.FRONTEND_SERVER_PORT)
const hmrPort = parseInt(process.env.HMR_SERVER_PORT)

// https://vitejs.dev/config/
export default defineConfig({
    plugins: [vue()],
    server: {
        host: '0.0.0.0',
        port: webPort,
        hmr: {
            port: hmrPort
        }
    },
})
