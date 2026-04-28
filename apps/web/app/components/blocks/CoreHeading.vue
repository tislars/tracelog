<script setup lang="ts">
const props = defineProps<{
  attributes: { content?: string; level?: number; textAlign?: string }
}>()

const tag = computed(() => `h${props.attributes.level ?? 2}`)
const levelClass = computed(() => `level-${props.attributes.level ?? 2}`)
</script>

<template>
  <!-- eslint-disable-next-line vue/no-v-html -->
  <component
    :is="tag"
    class="core-heading"
    :class="levelClass"
    :style="attributes.textAlign ? `text-align: ${attributes.textAlign}` : undefined"
    v-html="attributes.content"
  />
</template>

<style scoped>
.core-heading {
  font-weight: 800;
  line-height: 1.2;
  letter-spacing: -0.02em;
  color: #1a1a2e;
  margin-top: 2rem;
  margin-bottom: 0.75rem;
}

.core-heading:first-child {
  margin-top: 0;
}

.core-heading.level-1 { font-size: clamp(2rem, 4vw, 3rem); }
.core-heading.level-2 { font-size: clamp(1.5rem, 3vw, 2rem); }
.core-heading.level-3 { font-size: clamp(1.25rem, 2.5vw, 1.5rem); }
.core-heading.level-4 { font-size: 1.25rem; }
.core-heading.level-5 { font-size: 1.125rem; }
.core-heading.level-6 { font-size: 1rem; }
</style>
