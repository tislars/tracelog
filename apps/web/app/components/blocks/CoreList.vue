<script setup lang="ts">
defineProps<{
  attributes: { ordered?: boolean; values?: string }
}>()
</script>

<template>
  <!-- eslint-disable-next-line vue/no-v-html -->
  <component
    :is="attributes.ordered ? 'ol' : 'ul'"
    class="core-list"
    v-html="attributes.values"
  />
</template>

<style scoped>
.core-list {
  padding-left: 1.75rem;
  margin-bottom: 1.25rem;
  color: #374151;
  line-height: 1.8;
  font-size: 1.0625rem;
  max-width: 68ch;
}

.core-list :deep(li) {
  margin-bottom: 0.375rem;
}

.core-list :deep(li::marker) {
  color: #2563eb;
}
</style>
