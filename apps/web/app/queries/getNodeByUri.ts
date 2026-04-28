/**
 * Fetches a WordPress page or post by its URI, including all editor blocks.
 * Core blocks with typed attributes: CoreParagraph, CoreHeading, CoreList.
 * All other blocks fall back to renderedHtml.
 */
export const GET_NODE_BY_URI = /* GraphQL */ `
  query GetNodeByUri($uri: String!) {
    nodeByUri(uri: $uri) {
      __typename
      uri

      ... on Page {
        id
        title
        seo {
          ...SeoFields
        }
        editorBlocks(flat: false) {
          ...BlockFields
        }
      }

      ... on Post {
        id
        title
        seo {
          ...SeoFields
        }
        editorBlocks(flat: false) {
          ...BlockFields
        }
      }
    }
  }

  fragment SeoFields on SeoMeta {
    title
    description
    robots
    canonical
    ogTitle
    ogDescription
  }

  fragment BlockFields on EditorBlock {
    __typename
    name
    clientId
    parentClientId
    isDynamic
    renderedHtml

    ... on CoreParagraph {
      attributes {
        content
      }
    }

    ... on CoreHeading {
      attributes {
        content
        level
        textAlign
      }
    }

    ... on CoreList {
      attributes {
        ordered
        values
      }
    }
  }
`

// ── Response shape ──────────────────────────────────────────────────────────

export interface SeoMeta {
  title?: string
  description?: string
  robots?: string
  canonical?: string
  ogTitle?: string
  ogDescription?: string
}

// Core blocks
export interface CoreParagraphBlockData {
  __typename: 'CoreParagraph'
  name: string
  clientId: string
  parentClientId: string | null
  isDynamic: boolean
  renderedHtml?: string
  attributes: { content?: string }
}

export interface CoreHeadingBlockData {
  __typename: 'CoreHeading'
  name: string
  clientId: string
  parentClientId: string | null
  isDynamic: boolean
  renderedHtml?: string
  attributes: { content?: string; level?: number; textAlign?: string }
}

export interface CoreListBlockData {
  __typename: 'CoreList'
  name: string
  clientId: string
  parentClientId: string | null
  isDynamic: boolean
  renderedHtml?: string
  attributes: { ordered?: boolean; values?: string }
}

export type EditorBlock =
  | CoreParagraphBlockData
  | CoreHeadingBlockData
  | CoreListBlockData
  | { __typename: string; name: string; clientId: string; renderedHtml?: string; [key: string]: unknown }

export interface WpNodeBase {
  __typename: string
  uri: string
}

export interface WpPage extends WpNodeBase {
  __typename: 'Page'
  id: string
  title: string
  seo?: SeoMeta
  editorBlocks: EditorBlock[]
}

export interface WpPost extends WpNodeBase {
  __typename: 'Post'
  id: string
  title: string
  seo?: SeoMeta
  editorBlocks: EditorBlock[]
}

export type WpNode = WpPage | WpPost | WpNodeBase

export interface GetNodeByUriResult {
  nodeByUri: WpNode | null
}
