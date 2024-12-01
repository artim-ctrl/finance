import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import path from 'path'

// https://vite.dev/config/
export default defineConfig({
    plugins: [react()],
    resolve: {
        alias: [
            {
                find: /^Containers/,
                replacement: path.resolve(__dirname, 'src/Containers'),
            },
            {
                find: /^Components/,
                replacement: path.resolve(__dirname, 'src/Components'),
            },
        ],
    },
})
