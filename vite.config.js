import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    build: {
        outDir: "public/dist", // Output directory for compiled assets
        manifest: true, // Generate manifest file
        rollupOptions: {
            // Other rollup options...
        },
    },
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
    ],
});
