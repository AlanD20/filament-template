import { defineConfig } from 'vite';
import laravel, { refreshPaths } from 'laravel-vite-plugin';

function has(name) {
  return [
    'chart',
    'imask',
    'sortable',
    'dayjs',
    'forms',
    'notifications',
    'focus',
    'marked',
    'markdown',
    'choices',
    'trix',
  ].find((w) => name.includes(w));
}

export default defineConfig({
  build: {
    rollupOptions: {
      output: {
        manualChunks(id) {
          if (id.includes('filepond')) {
            if (id.includes('image')) return 'fp-image';
            return 'fp';
          } else if ((exists = has(id))) {
            return exists;
          }

          console.log(id);
          return 'modules';
        },
      },
    },
  },
  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js'],
      refresh: [
        ...refreshPaths,
        'app/Http/Livewire/**',
        'app/Tables/Columns/**',
      ],
    }),
  ],
});
