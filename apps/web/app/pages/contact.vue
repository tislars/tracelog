<script setup lang="ts">
import {
  GF_GET_FORM,
  type GetGfFormResult,
} from '~/queries/getContactForm'

// Form ID — the Contact form is always ID 1 on a fresh seed (see apps/cms/bin/seed-content.php)
const FORM_ID = '1'

const { data } = await useGraphQL<GetGfFormResult>(
  GF_GET_FORM,
  { formId: FORM_ID },
  { key: 'gf-form:contact' },
)

const form = computed(() => data.value?.gfForm ?? null)

useSeoHead(
  computed(() => ({
    title: 'Contact Us',
    description: "Get in touch \u2014 we'd love to hear from you.",
    robots: 'index,follow',
    canonical: 'http://localhost:8080/contact/',
    ogTitle: 'Contact Us',
    ogDescription: "Get in touch \u2014 we'd love to hear from you.",
  })),
)
</script>

<template>
  <main class="page">
    <div class="page__hero">
      <div class="container">
        <h1 class="page__title">Contact Us</h1>
        <p class="page__subtitle">We'd love to hear from you.</p>
      </div>
    </div>

    <div class="container page__content">
      <div class="contact-layout">
        <div class="contact-info">
          <h2 class="contact-info__title">Get in touch</h2>
          <p class="contact-info__text">
            Fill in the form and we'll get back to you as soon as possible.
          </p>

          <ul class="contact-info__list">
            <li>
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
              hello@wordpress-headless-poc.dev
            </li>
          </ul>
        </div>

        <div class="contact-form-wrapper">
          <GravityForm v-if="form" :form="form" />
        </div>
      </div>
    </div>
  </main>
</template>

<style scoped>
.page__subtitle {
  margin-top: 0.75rem;
  font-size: 1.125rem;
  color: rgba(255 255 255 / 0.75);
}

/* ── Contact layout ────────────────────────── */
.contact-layout {
  display: grid;
  grid-template-columns: 18rem 1fr;
  gap: 4rem;
  align-items: start;
}

@media (max-width: 48rem) {
  .contact-layout {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
}

.contact-info__title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1a1a2e;
  margin-bottom: 0.75rem;
}

.contact-info__text {
  color: #6b7280;
  line-height: 1.75;
  margin-bottom: 1.5rem;
}

.contact-info__list {
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.contact-info__list li {
  display: flex;
  align-items: center;
  gap: 0.625rem;
  font-size: 0.9375rem;
  color: #374151;
}

.contact-info__list svg {
  flex-shrink: 0;
  color: #2563eb;
}

.contact-form-wrapper {
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 1rem;
  padding: 2rem;
}
</style>
