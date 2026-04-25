<script setup lang="ts">
interface BackgroundImage {
  sourceUrl: string
  altText: string
  mediaDetails?: { width: number; height: number }
}

interface HeroBlockAcf {
  heading?: string
  subheading?: string
  backgroundImage?: BackgroundImage
}

const props = defineProps<{
  acf: HeroBlockAcf
}>()
</script>

<template>
  <section
    class="hero-block"
    :style="acf.backgroundImage?.sourceUrl
      ? `background-image: url('${acf.backgroundImage.sourceUrl}')`
      : undefined"
  >
    <div class="hero-block__content">
      <h1 v-if="acf.heading" class="hero-block__heading">
        {{ acf.heading }}
      </h1>
      <p v-if="acf.subheading" class="hero-block__subheading">
        {{ acf.subheading }}
      </p>
    </div>
    <!-- Hidden img for SEO/accessibility when background image is present -->
    <img
      v-if="acf.backgroundImage"
      :src="acf.backgroundImage.sourceUrl"
      :alt="acf.backgroundImage.altText"
      :width="acf.backgroundImage.mediaDetails?.width"
      :height="acf.backgroundImage.mediaDetails?.height"
      class="hero-block__bg-img sr-only"
    />
  </section>
</template>

<style scoped>
.hero-block {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 60vh;
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  padding: 4rem 2rem;
  text-align: center;
  color: #fff;
}

.hero-block__content {
  position: relative;
  z-index: 1;
  max-width: 48rem;
}

.hero-block__heading {
  font-size: clamp(2rem, 5vw, 4rem);
  font-weight: 700;
  margin: 0 0 1rem;
}

.hero-block__subheading {
  font-size: clamp(1rem, 2.5vw, 1.5rem);
  margin: 0;
  opacity: 0.9;
}

.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  overflow: hidden;
  clip: rect(0 0 0 0);
  white-space: nowrap;
}
</style>
