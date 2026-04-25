/**
 * Fetches a WordPress page or post by its URI, including all ACF editor blocks.
 *
 * Inline fragments cover the three PoC block types:
 *   - AcfHeroBlock
 *   - AcfTextContentBlock
 *   - AcfImageGalleryBlock
 *
 * Add new block fragments here as blocks are registered in WordPress.
 */
export const GET_NODE_BY_URI = /* GraphQL */ `
  query GetNodeByUri($uri: String!) {
    nodeByUri(uri: $uri) {
      __typename
      uri

      ... on Page {
        id
        title
        editorBlocks(flat: false) {
          ...BlockFields
        }
      }

      ... on Post {
        id
        title
        editorBlocks(flat: false) {
          ...BlockFields
        }
      }
    }
  }

  fragment BlockFields on EditorBlock {
    __typename
    name
    clientId
    parentClientId
    isDynamic

    ... on AcfHeroBlock {
      acf {
        heading
        subheading
        backgroundImage {
          sourceUrl
          altText
          mediaDetails {
            width
            height
          }
        }
      }
    }

    ... on AcfTextContentBlock {
      acf {
        body
      }
    }

    ... on AcfImageGalleryBlock {
      acf {
        caption
        images {
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
`

// ── Response shape ──────────────────────────────────────────────────────────

export interface BackgroundImage {
  sourceUrl: string
  altText: string
  mediaDetails?: { width: number; height: number }
}

export interface AcfHeroBlockData {
  __typename: 'AcfHeroBlock'
  name: string
  clientId: string
  parentClientId: string | null
  isDynamic: boolean
  acf: {
    heading?: string
    subheading?: string
    backgroundImage?: BackgroundImage
  }
}

export interface AcfTextContentBlockData {
  __typename: 'AcfTextContentBlock'
  name: string
  clientId: string
  parentClientId: string | null
  isDynamic: boolean
  acf: {
    body?: string
  }
}

export interface AcfImageGalleryBlockData {
  __typename: 'AcfImageGalleryBlock'
  name: string
  clientId: string
  parentClientId: string | null
  isDynamic: boolean
  acf: {
    caption?: string
    images?: BackgroundImage[]
  }
}

export type EditorBlock =
  | AcfHeroBlockData
  | AcfTextContentBlockData
  | AcfImageGalleryBlockData
  | { __typename: string; name: string; clientId: string; [key: string]: unknown }

export interface WpNodeBase {
  __typename: string
  uri: string
}

export interface WpPage extends WpNodeBase {
  __typename: 'Page'
  id: string
  title: string
  editorBlocks: EditorBlock[]
}

export interface WpPost extends WpNodeBase {
  __typename: 'Post'
  id: string
  title: string
  editorBlocks: EditorBlock[]
}

export type WpNode = WpPage | WpPost | WpNodeBase

export interface GetNodeByUriResult {
  nodeByUri: WpNode | null
}
