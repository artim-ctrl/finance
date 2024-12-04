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
                replacement: path.resolve(__dirname, 'frontend/Containers'),
            },
            {
                find: /^Components/,
                replacement: path.resolve(__dirname, 'frontend/Components'),
            },
            {
                find: /^Services/,
                replacement: path.resolve(__dirname, 'frontend/Services'),
            },
            {
                find: /^Constants/,
                replacement: path.resolve(__dirname, 'frontend/Constants'),
            },
            {
                find: /^Hooks/,
                replacement: path.resolve(__dirname, 'frontend/Hooks'),
            },
            {
                find: /^Contexts/,
                replacement: path.resolve(__dirname, 'frontend/Contexts'),
            },
        ],
    },
})
