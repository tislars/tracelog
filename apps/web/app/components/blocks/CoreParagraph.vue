<script setup lang="ts">
defineProps<{
  attributes: { content?: string }
}>()
</script>

<template>
  <!-- eslint-disable-next-line vue/no-v-html -->
  <p class="core-paragraph" v-html="attributes.content" />
</template>

<style scoped>
.core-paragraph {
  font-size: 1.0625rem;
  line-height: 1.8;
  color: #374151;
  margin-bottom: 1.25rem;
  max-width: 68ch;
}

.core-paragraph :deep(strong) {
  font-weight: 600;
  color: #1a1a2e;
}

.core-paragraph :deep(em) {
  font-style: italic;
}

.core-paragraph :deep(a) {
  color: #2563eb;
  text-underline-offset: 3px;
}
</style>
