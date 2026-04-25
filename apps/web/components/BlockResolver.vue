<script setup lang="ts">
import { defineAsyncComponent, type Component } from 'vue'
import type { EditorBlock } from '~/queries/getNodeByUri'

defineProps<{
  blocks: EditorBlock[]
}>()

/**
 * Maps ACF block names to their Vue component implementations.
 * Add new entries here as more blocks are registered on the WordPress side.
 */
const blockMap: Record<string, Component> = {
  'acf/hero': defineAsyncComponent(
    () => import('~/components/blocks/HeroBlock.vue'),
  ),
  'acf/text-content': defineAsyncComponent(
    () => import('~/components/blocks/TextContentBlock.vue'),
  ),
  'acf/image-gallery': defineAsyncComponent(
    () => import('~/components/blocks/ImageGalleryBlock.vue'),
  ),
}

function resolveBlock(name: string): Component | null {
  return blockMap[name] ?? null
}
</script>

<template>
  <div class="block-resolver">
    <template v-for="block in blocks" :key="block.clientId">
      <component
        :is="resolveBlock(block.name)"
        v-if="resolveBlock(block.name)"
        v-bind="block"
      />
      <!-- Unrecognised blocks are silently skipped in production.
           In dev, a placeholder helps identify unmapped block types. -->
      <div
        v-else-if="$nuxt?.isDev ?? false"
        class="block-resolver__unknown"
        :data-block-name="block.name"
      >
        <p>⚠ Unmapped block: <code>{{ block.name }}</code></p>
      </div>
    </template>
  </div>
</template>

<style scoped>
.block-resolver__unknown {
  border: 2px dashed #f59e0b;
  background: #fffbeb;
  padding: 1rem;
  margin: 0.5rem 0;
  font-size: 0.875rem;
  color: #92400e;
}
</style>
