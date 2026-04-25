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
const uri = '/' + slugParts.filter(Boolean).join('/') + '/'

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
</script>

<template>
  <main>
    <div v-if="error" class="error-state">
      <p>Failed to load page content.</p>
    </div>

    <div v-else-if="!node" class="not-found">
      <h1>404 — Page not found</h1>
      <p>No content found at <code>{{ uri }}</code>.</p>
    </div>

    <template v-else>
      <BlockResolver :blocks="blocks" />
    </template>
  </main>
</template>

