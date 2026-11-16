import react from "@vitejs/plugin-react";
import { defineConfig } from "vite";

const API_PORT = process.env.API_PORT ?? 4000;

export default defineConfig({
    plugins: [react()],
    server: {
        proxy: {
            "/api": `http://localhost:${API_PORT}`,
        },
    },
    test: {
        environment: "jsdom",
        setupFiles: "./src/setupTests.js",
    },
});
