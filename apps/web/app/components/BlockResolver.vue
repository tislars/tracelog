<script setup lang="ts">
import { defineAsyncComponent, type Component } from 'vue'
import type { EditorBlock } from '~/queries/getNodeByUri'

defineProps<{
  blocks: EditorBlock[]
}>()

const blockMap: Record<string, Component> = {
  'core/paragraph': defineAsyncComponent(
    () => import('~/components/blocks/CoreParagraph.vue'),
  ),
  'core/heading': defineAsyncComponent(
    () => import('~/components/blocks/CoreHeading.vue'),
  ),
  'core/list': defineAsyncComponent(
    () => import('~/components/blocks/CoreList.vue'),
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
      <!-- Fallback: render server HTML for unmapped blocks (e.g. core/media-text, core/group) -->
      <div
        v-else-if="block.renderedHtml"
        class="block-fallback"
        v-html="block.renderedHtml"
      />
    </template>
  </div>
</template>

<style>
.block-resolver {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

/* ── Fallback rendered HTML from WordPress ──────────────────── */
.block-fallback {
  margin-block: 1rem;
}

.block-fallback :deep(img) {
  max-width: 100%;
  height: auto;
  border-radius: 0.5rem;
}

.block-fallback :deep(p) {
  font-size: 1.0625rem;
  line-height: 1.8;
  color: #374151;
  margin-bottom: 1.25rem;
  max-width: 68ch;
}

.block-fallback :deep(h1),
.block-fallback :deep(h2),
.block-fallback :deep(h3),
.block-fallback :deep(h4),
.block-fallback :deep(h5),
.block-fallback :deep(h6) {
  font-weight: 800;
  line-height: 1.2;
  letter-spacing: -0.02em;
  color: #1a1a2e;
  margin-top: 1.5rem;
  margin-bottom: 0.625rem;
}

.block-fallback :deep(h1) { font-size: clamp(2rem, 4vw, 3rem); }
.block-fallback :deep(h2) { font-size: clamp(1.5rem, 3vw, 2rem); }
.block-fallback :deep(h3) { font-size: clamp(1.25rem, 2.5vw, 1.5rem); }
.block-fallback :deep(h4) { font-size: 1.25rem; }
.block-fallback :deep(h5) { font-size: 1.125rem; }
.block-fallback :deep(h6) { font-size: 1rem; }

.block-fallback :deep(ul),
.block-fallback :deep(ol) {
  padding-left: 1.75rem;
  margin-bottom: 1.25rem;
  color: #374151;
  line-height: 1.8;
  font-size: 1.0625rem;
  max-width: 68ch;
}

.block-fallback :deep(li) {
  margin-bottom: 0.375rem;
}

.block-fallback :deep(a) {
  color: #2563eb;
  text-underline-offset: 3px;
}

.block-fallback :deep(figure) {
  margin-block: 1.5rem;
}

.block-fallback :deep(figcaption) {
  margin-top: 0.5rem;
  font-size: 0.875rem;
  color: #6b7280;
  text-align: center;
}
</style>
