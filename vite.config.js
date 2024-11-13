// Change from CommonJS to ESM
import { defineConfig } from 'vite';  // Import Vite configuration
import laravel from 'laravel-vite-plugin'; // Import the Laravel Vite plugin

export default defineConfig({
  plugins: [
    laravel({
      input: [
        'resources/css/app.css',
        'resources/js/app.js',
      ],
    }),
  ],
});
