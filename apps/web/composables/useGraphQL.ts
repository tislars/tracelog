/**
 * Lightweight GraphQL client composable for Nuxt 4.
 *
 * Wraps $fetch so GraphQL queries behave like useAsyncData calls:
 * SSR-safe, automatically deduped, and cached by key.
 *
 * Usage:
 *   const { data, error, pending } = await useGraphQL<MyQuery>(MY_QUERY, { id: '1' })
 */

interface GraphQLResponse<T> {
  data: T
  errors?: Array<{ message: string; locations?: unknown[]; path?: string[] }>
}

export function useGraphQL<T = unknown>(
  query: string,
  variables?: Record<string, unknown>,
  options?: { key?: string; lazy?: boolean },
) {
  const config = useRuntimeConfig()
  const endpoint = config.public.graphqlEndpoint as string

  // Use query + variable hash as the cache key so identical queries share state
  const cacheKey =
    options?.key ?? `gql:${btoa(query).slice(0, 16)}:${JSON.stringify(variables ?? {})}`

  return useAsyncData<T>(cacheKey, () =>
    $fetch<GraphQLResponse<T>>(endpoint, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: { query, variables },
    }).then((res) => {
      if (res.errors?.length) {
        throw new Error(res.errors.map((e) => e.message).join('; '))
      }
      return res.data
    }), { lazy: options?.lazy ?? false })
}
