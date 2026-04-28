// ── List query (team index page) ─────────────────────────────────────────────

export const GET_TEAM_MEMBERS = /* GraphQL */ `
  query GetTeamMembers {
    teamMembers(first: 100) {
      nodes {
        id
        slug
        uri
        title
        teamMemberFields {
          role
          photo {
            node {
              sourceUrl
              altText
              mediaDetails {
                width
                height
              }
            }
          }
        }
      }
    }
  }
`

// ── Detail query (team member profile page) ──────────────────────────────────

export const GET_TEAM_MEMBER_BY_SLUG = /* GraphQL */ `
  query GetTeamMemberBySlug($slug: ID!) {
    teamMember(id: $slug, idType: SLUG) {
      id
      slug
      uri
      title
      seo {
        title
        description
        robots
        canonical
        ogTitle
        ogDescription
      }
      teamMemberFields {
        role
        bio
        linkedinUrl
        photo {
          node {
            sourceUrl
            altText
            mediaDetails {
              width
              height
            }
          }
        }
      }
    }
  }
`

import type { SeoMeta } from '~/queries/getNodeByUri'

// ── TypeScript types ──────────────────────────────────────────────────────────

export interface TeamMemberPhoto {
  node: {
    sourceUrl: string
    altText: string
    mediaDetails?: { width: number; height: number }
  }
}

export interface TeamMemberFields {
  role?: string
  bio?: string
  linkedinUrl?: string
  photo?: TeamMemberPhoto
}

export interface TeamMemberListItem {
  id: string
  slug: string
  uri: string
  title: string
  teamMemberFields: TeamMemberFields
}

export interface TeamMemberDetail extends TeamMemberListItem {
  seo?: SeoMeta
  teamMemberFields: Required<Pick<TeamMemberFields, 'role'>> & TeamMemberFields
}

export interface GetTeamMembersResult {
  teamMembers: { nodes: TeamMemberListItem[] }
}

export interface GetTeamMemberBySlugResult {
  teamMember: TeamMemberDetail | null
}
