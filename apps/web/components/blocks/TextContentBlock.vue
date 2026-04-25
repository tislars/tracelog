<script setup lang="ts">
interface TextContentBlockAcf {
  body?: string
}

defineProps<{
  acf: TextContentBlockAcf
}>()
</script>

<template>
  <section v-if="acf.body" class="text-content-block">
    <!-- eslint-disable-next-line vue/no-v-html -->
    <div class="text-content-block__body prose" v-html="acf.body" />
  </section>
</template>

<style scoped>
.text-content-block {
  max-width: 72ch;
  margin: 2rem auto;
  padding: 0 1.5rem;
  line-height: 1.75;
}

/* Basic prose styles — replace with Tailwind Typography or similar in production */
.text-content-block__body :deep(h1),
.text-content-block__body :deep(h2),
.text-content-block__body :deep(h3) {
  font-weight: 700;
  margin-top: 1.5em;
  margin-bottom: 0.5em;
}

.text-content-block__body :deep(p) {
  margin-bottom: 1em;
}

.text-content-block__body :deep(a) {
  color: #2563eb;
  text-decoration: underline;
}
</style>
