<script setup lang="ts">
import {
  GET_NODE_BY_URI,
  type GetNodeByUriResult,
  type EditorBlock,
  type WpPage,
  type WpPost,
} from '~/queries/getNodeByUri'

const route = useRoute()

// Build URI from slug array: ['about', 'team'] → '/about/team/'
const slugParts = Array.isArray(route.params.slug)
  ? route.params.slug
  : [route.params.slug]
const parts = slugParts.filter(Boolean)
const uri = parts.length ? `/${parts.join('/')}/` : '/'

const { data, error } = await useGraphQL<GetNodeByUriResult>(
  GET_NODE_BY_URI,
  { uri },
  { key: `page:${uri}` },
)

const node = computed(() => data.value?.nodeByUri ?? null)

const blocks = computed<EditorBlock[]>(() => {
  const n = node.value
  if (!n || !('editorBlocks' in n)) return []
  return (n as WpPage | WpPost).editorBlocks
})

const title = computed(() => {
  const n = node.value
  return n && 'title' in n ? (n as WpPage | WpPost).title : ''
})

const seo = computed(() => {
  const n = node.value
  return n && 'seo' in n ? (n as WpPage | WpPost).seo : undefined
})

useSeoHead(seo)
</script>

<template>
  <main class="page">
    <div v-if="error" class="page__state container">
      <p class="page__state-message">Failed to load page content.</p>
    </div>

    <div v-else-if="!node" class="page__state container">
      <h1 class="page__state-title">404 — Page not found</h1>
      <p>No content found at <code>{{ uri }}</code>.</p>
    </div>

    <template v-else>
      <div v-if="title" class="page__hero">
        <div class="container">
          <h1 class="page__title">{{ title }}</h1>
        </div>
      </div>

      <div class="container page__content">
        <BlockResolver :blocks="blocks" />
      </div>
    </template>
  </main>
</template>

<style scoped>
.page {
  flex: 1;
}

.page__hero {
  background: linear-gradient(135deg, #1a1a2e 0%, #16213e 60%, #0f3460 100%);
  color: #fff;
  padding-block: 4rem 3rem;
}

.page__title {
  font-size: clamp(2rem, 5vw, 3.5rem);
  font-weight: 800;
  line-height: 1.1;
  letter-spacing: -0.03em;
  max-width: 48rem;
}

.page__content {
  padding-block: 3rem 5rem;
}

.page__state {
  padding-block: 5rem;
}

.page__state-title {
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 1rem;
}

.page__state-message {
  color: #6b7280;
}
</style>

