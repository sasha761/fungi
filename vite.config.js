import { defineConfig } from 'vite';

export default defineConfig({
  base: './',
  css: {
    preprocessorOptions: {
      scss: {
        // additionalData: `@import "@/styles/variables.scss";`, // Импорт дополнительных файлов SCSS
        sourceMap: true,
      },
    },
  },
  resolve: {
    alias: {
      '@': '/src',
    },
  },
  build: {
    rollupOptions: {
      output: {
        entryFileNames: "js/[name].js",   // ✅ Главные файлы в папку js/
        chunkFileNames: "js/[name].js",

        assetFileNames: ({ name }) => {
          if (/\.(gif|jpe?g|png|svg|webp)$/.test(name ?? "")) {
            return "images/[name][extname]";
          }

          if (/\.(ttf|otf|eot|fnt|woff)$/.test(name ?? "")) {
            return "fonts/[name][extname]";
          }

          if (/\.css$/.test(name ?? "")) {
            return "css/[name][extname]";
          }

          if (/\.js$/.test(name ?? "")) {
            return "js/[name][extname]";
          }

          return "[name][extname]";
        },
      },
      input: {
        main: 'src/js/index.js', // Главная точка входа
      },
    }
  },
});