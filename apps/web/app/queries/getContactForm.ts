/**
 * Gravity Forms — GraphQL queries and mutation.
 *
 * Requires:
 *   - Gravity Forms plugin (installed + activated, license entered)
 *   - WPGraphQL for Gravity Forms (AxeWP/wp-graphql-gravity-forms) — already installed
 *
 * After installing GF:
 *   1. Create a form in WP Admin → Forms → Add New
 *   2. Note the form's database ID (visible in the URL: ?id=1)
 *   3. Pass that ID as the `formId` variable
 */

// ── Form schema query ─────────────────────────────────────────────────────────

export const GF_GET_FORM = /* GraphQL */ `
  query GetGfForm($formId: ID!) {
    gfForm(id: $formId, idType: DATABASE_ID) {
      databaseId
      title
      description
      submitButton {
        text
      }
      formFields {
        nodes {
          id
          type
          layoutGridColumnSpan

          ... on TextField {
            label
            isRequired
            description
            placeholder
            errorMessage
          }

          ... on EmailField {
            label
            isRequired
            description
            placeholder
            errorMessage
          }

          ... on TextAreaField {
            label
            isRequired
            description
            placeholder
            errorMessage
            rows: maxLength
          }

          ... on PhoneField {
            label
            isRequired
            description
            placeholder
          }

          ... on SelectField {
            label
            isRequired
            description
            choices {
              text
              value
            }
          }
        }
      }
    }
  }
`

// ── Submit mutation ───────────────────────────────────────────────────────────

export const GF_SUBMIT_FORM = /* GraphQL */ `
  mutation SubmitGfForm($formId: ID!, $fieldValues: [FormFieldValuesInput]!) {
    submitGfForm(input: {
      id: $formId
      fieldValues: $fieldValues
    }) {
      errors {
        id
        message
      }
      entry {
        id
      }
    }
  }
`

// ── TypeScript types ──────────────────────────────────────────────────────────

export type GfFieldType =
  | 'TEXT'
  | 'EMAIL'
  | 'TEXTAREA'
  | 'PHONE'
  | 'SELECT'
  | 'CHECKBOX'
  | 'RADIO'
  | string

export interface GfSelectChoice {
  text: string
  value: string
}

export interface GfFormField {
  id: number
  type: GfFieldType
  label?: string
  isRequired?: boolean
  description?: string
  placeholder?: string
  errorMessage?: string
  rows?: number
  choices?: GfSelectChoice[]
}

export interface GfForm {
  databaseId: number
  title: string
  description?: string
  submitButton?: { text: string }
  formFields: { nodes: GfFormField[] }
}

export interface GfSubmitError {
  id: number
  message: string
}

export interface GfSubmitResult {
  submitGfForm: {
    errors: GfSubmitError[] | null
    entry: { id: string } | null
  }
}

export interface GetGfFormResult {
  gfForm: GfForm | null
}

export interface FormFieldValue {
  id: number
  value: string
}
