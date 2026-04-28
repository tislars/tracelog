import type { SeoMeta } from '~/queries/getNodeByUri'

/**
 * Applies SEO metadata from WPGraphQL/RankMath to the current page's <head>.
 *
 * Usage:
 *   useSeoHead(computed(() => page.value?.seo))
 */
export function useSeoHead(seo: Ref<SeoMeta | undefined | null>) {
  const config = useRuntimeConfig()
  const siteUrl = config.public.graphqlEndpoint.replace(/\/wp\/graphql$/, '')

  useHead(() => {
    const s = seo.value
    if (!s) return {}

    const title = s.title || undefined
    const description = s.description || undefined

    // Build <meta robots> value
    const robotsContent = s.robots ?? 'index,follow'
    const noindex = robotsContent.includes('noindex')
    const nofollow = robotsContent.includes('nofollow')

    const metaTags: Record<string, string>[] = []

    if (description) {
      metaTags.push({ name: 'description', content: description })
    }
    metaTags.push({
      name: 'robots',
      content: robotsContent,
    })

    // Open Graph
    const ogTitle = s.ogTitle || title
    const ogDescription = s.ogDescription || description

    if (ogTitle) metaTags.push({ property: 'og:title', content: ogTitle })
    if (ogDescription) metaTags.push({ property: 'og:description', content: ogDescription })
    if (s.canonical) metaTags.push({ property: 'og:url', content: s.canonical })

    return {
      title,
      meta: metaTags,
      link: s.canonical
        ? [{ rel: 'canonical', href: s.canonical }]
        : [],
    }
  })

  // Warn in dev if indexing is blocked
  if (import.meta.dev) {
    watchEffect(() => {
      const robots = seo.value?.robots ?? ''
      if (robots.includes('noindex')) {
        console.warn('[useSeoHead] Page is set to noindex — check RankMath settings')
      }
    })
  }
}
