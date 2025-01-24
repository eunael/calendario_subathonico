// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2024-11-01',
  devtools: { enabled: true },
  css: ['~/assets/css/main.css'],
  postcss: {
    plugins: {
      tailwindcss: {},
      autoprefixer: {},
    },
  },
  runtimeConfig: {
    secretEndpoint: 'http://localhost:3000/api/time-left'
  },
  modules: ["@prisma/nuxt"],
  alias: {
    ".prisma/client/index-browser": `node_modules/.prisma/client/index-browser.js`,
  },
})
