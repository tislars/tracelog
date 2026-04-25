<script setup lang="ts">
interface GalleryImage {
  sourceUrl: string
  altText: string
  mediaDetails?: { width: number; height: number }
}

interface ImageGalleryBlockAcf {
  images?: GalleryImage[]
  caption?: string
}

defineProps<{
  acf: ImageGalleryBlockAcf
}>()
</script>

<template>
  <figure v-if="acf.images?.length" class="image-gallery-block">
    <div class="image-gallery-block__grid">
      <img
        v-for="(image, index) in acf.images"
        :key="index"
        :src="image.sourceUrl"
        :alt="image.altText"
        :width="image.mediaDetails?.width"
        :height="image.mediaDetails?.height"
        class="image-gallery-block__img"
        loading="lazy"
        decoding="async"
      />
    </div>
    <figcaption v-if="acf.caption" class="image-gallery-block__caption">
      {{ acf.caption }}
    </figcaption>
  </figure>
</template>

<style scoped>
.image-gallery-block {
  margin: 2rem auto;
  padding: 0 1.5rem;
  max-width: 80rem;
}

.image-gallery-block__grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
  gap: 0.75rem;
}

.image-gallery-block__img {
  width: 100%;
  height: 200px;
  object-fit: cover;
  border-radius: 0.25rem;
  display: block;
}

.image-gallery-block__caption {
  margin-top: 0.75rem;
  text-align: center;
  font-size: 0.875rem;
  color: #6b7280;
  font-style: italic;
}
</style>
