// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: true },

  runtimeConfig: {
    public: {
      graphqlEndpoint:
        process.env.GRAPHQL_ENDPOINT ?? 'http://localhost:8080/wp/graphql',
    },
  },
})


