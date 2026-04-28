<script setup lang="ts">
import type { TeamMemberListItem } from '~/queries/getTeamMembers'

defineProps<{
  member: TeamMemberListItem
}>()
</script>

<template>
  <NuxtLink :to="`/team/${member.slug}`" class="team-card">
    <div class="team-card__photo">
      <img
        v-if="member.teamMemberFields.photo?.node"
        :src="member.teamMemberFields.photo.node.sourceUrl"
        :alt="member.teamMemberFields.photo.node.altText || member.title"
        :width="member.teamMemberFields.photo.node.mediaDetails?.width"
        :height="member.teamMemberFields.photo.node.mediaDetails?.height"
        class="team-card__img"
      />
      <div v-else class="team-card__placeholder" aria-hidden="true">
        {{ member.title.charAt(0) }}
      </div>
    </div>

    <div class="team-card__body">
      <h2 class="team-card__name">{{ member.title }}</h2>
      <p v-if="member.teamMemberFields.role" class="team-card__role">
        {{ member.teamMemberFields.role }}
      </p>
    </div>
  </NuxtLink>
</template>

<style scoped>
.team-card {
  display: block;
  text-decoration: none;
  border-radius: 1rem;
  overflow: hidden;
  background: #fff;
  border: 1px solid #e5e7eb;
  transition: transform 0.2s, box-shadow 0.2s;
}

.team-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 32px rgba(0 0 0 / 0.1);
}

.team-card__photo {
  aspect-ratio: 1;
  overflow: hidden;
  background: #f1f5f9;
}

.team-card__img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transition: transform 0.3s;
}

.team-card:hover .team-card__img {
  transform: scale(1.04);
}

.team-card__placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 4rem;
  font-weight: 800;
  color: rgba(30 41 59 / 0.25);
  background: linear-gradient(135deg, #f8fafc, #e2e8f0);
  user-select: none;
}

.team-card__body {
  padding: 1.25rem 1.5rem;
}

.team-card__name {
  font-size: 1.125rem;
  font-weight: 700;
  color: #1a1a2e;
  margin: 0 0 0.25rem;
}

.team-card__role {
  font-size: 0.875rem;
  color: #6b7280;
  font-weight: 500;
  margin: 0;
}
</style>
