<script setup lang="ts">
import {
  GET_TEAM_MEMBER_BY_SLUG,
  type GetTeamMemberBySlugResult,
} from '~/queries/getTeamMembers'

const route = useRoute()
const slug = route.params.slug as string

const { data, error } = await useGraphQL<GetTeamMemberBySlugResult>(
  GET_TEAM_MEMBER_BY_SLUG,
  { slug },
  { key: `team:${slug}` },
)

const member = computed(() => data.value?.teamMember ?? null)
const fields = computed(() => member.value?.teamMemberFields)
const seo = computed(() => member.value?.seo)

useSeoHead(seo)
</script>

<template>
  <main class="page">
    <div v-if="error || !member" class="page__state container">
      <h1 class="page__state-title">Member not found</h1>
      <NuxtLink to="/team" class="back-link">← Back to team</NuxtLink>
    </div>

    <template v-else>
      <div class="page__hero">
        <div class="container">
          <NuxtLink to="/team" class="back-link">← Our Team</NuxtLink>
          <h1 class="page__title">{{ member.title }}</h1>
          <p v-if="fields?.role" class="member-role">{{ fields.role }}</p>
        </div>
      </div>

      <div class="container page__content">
        <div class="member-profile">
          <div class="member-profile__photo">
            <img
              v-if="fields?.photo?.node"
              :src="fields.photo.node.sourceUrl"
              :alt="fields.photo.node.altText || member.title"
              :width="fields.photo.node.mediaDetails?.width"
              :height="fields.photo.node.mediaDetails?.height"
              class="member-photo"
            />
            <div v-else class="member-photo-placeholder" aria-hidden="true">
              {{ member.title.charAt(0) }}
            </div>
          </div>

          <div class="member-profile__content">
            <p v-if="fields?.bio" class="member-bio">{{ fields.bio }}</p>

            <a
              v-if="fields?.linkedinUrl"
              :href="fields.linkedinUrl"
              class="member-linkedin"
              target="_blank"
              rel="noopener noreferrer"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="currentColor"
                aria-hidden="true"
              >
                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
              </svg>
              LinkedIn
            </a>
          </div>
        </div>
      </div>
    </template>
  </main>
</template>

<style scoped>
.back-link {
  display: inline-block;
  color: rgba(255 255 255 / 0.7);
  text-decoration: none;
  font-size: 0.875rem;
  margin-bottom: 1.25rem;
  transition: color 0.15s;
}

.back-link:hover {
  color: #fff;
}

.member-role {
  margin-top: 0.5rem;
  font-size: 1.125rem;
  color: rgba(255 255 255 / 0.75);
  font-weight: 500;
}

.member-profile {
  display: grid;
  grid-template-columns: 16rem 1fr;
  gap: 3rem;
  align-items: start;
  padding-top: 1rem;
}

@media (max-width: 40rem) {
  .member-profile {
    grid-template-columns: 1fr;
  }
}

.member-photo {
  width: 100%;
  aspect-ratio: 1;
  object-fit: cover;
  border-radius: 1rem;
}

.member-photo-placeholder {
  width: 100%;
  aspect-ratio: 1;
  border-radius: 1rem;
  background: linear-gradient(135deg, #1a1a2e, #0f3460);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 5rem;
  font-weight: 800;
  color: rgba(255 255 255 / 0.4);
  user-select: none;
}

.member-bio {
  font-size: 1.0625rem;
  line-height: 1.8;
  color: #374151;
  margin-bottom: 1.5rem;
}

.member-linkedin {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1.25rem;
  background: #0a66c2;
  color: #fff;
  text-decoration: none;
  border-radius: 0.375rem;
  font-weight: 600;
  font-size: 0.9375rem;
  transition: background 0.15s;
}

.member-linkedin:hover {
  background: #004182;
}
</style>
