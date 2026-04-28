<script setup lang="ts">
import {
  GET_TEAM_MEMBERS,
  type GetTeamMembersResult,
} from '~/queries/getTeamMembers'

const { data, error } = await useGraphQL<GetTeamMembersResult>(
  GET_TEAM_MEMBERS,
  {},
  { key: 'team:list' },
)

const members = computed(() => data.value?.teamMembers.nodes ?? [])
</script>

<template>
  <main class="page">
    <div class="page__hero">
      <div class="container">
        <h1 class="page__title">Our Team</h1>
        <p class="page__subtitle">The people behind the work.</p>
      </div>
    </div>

    <div class="container page__content">
      <div v-if="error" class="page__state">
        <p>Failed to load team members.</p>
      </div>

      <ul v-else class="team-grid">
        <li v-for="member in members" :key="member.id">
          <TeamMemberCard :member="member" />
        </li>
      </ul>
    </div>
  </main>
</template>

<style scoped>
.page__subtitle {
  margin-top: 0.75rem;
  font-size: 1.125rem;
  color: rgba(255 255 255 / 0.75);
}

.team-grid {
  list-style: none;
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(18rem, 1fr));
  gap: 2rem;
  padding: 0;
}
</style>
