<script setup lang="ts">
import {
  GF_SUBMIT_FORM,
  type GfForm,
  type GfFormField,
  type GfSubmitResult,
} from '~/queries/getContactForm'

const props = defineProps<{
  form: GfForm
}>()

const emit = defineEmits<{
  submitted: []
}>()

// ── Form state ────────────────────────────────────────────────────────────────

const values = ref<Record<number, string>>({})
const errors = ref<Record<number, string>>({})
const submitError = ref<string | null>(null)
const submitting = ref(false)
const submitted = ref(false)

// Initialise values map from form fields
watchEffect(() => {
  props.form.formFields.nodes.forEach((field) => {
    if (!(field.id in values.value)) {
      values.value[field.id] = ''
    }
  })
})

// ── Validation ────────────────────────────────────────────────────────────────

function validate(): boolean {
  errors.value = {}
  let valid = true

  for (const field of props.form.formFields.nodes) {
    if (field.isRequired && !values.value[field.id]?.trim()) {
      errors.value[field.id] =
        field.errorMessage || `${field.label ?? 'This field'} is required.`
      valid = false
    }
    if (
      field.type === 'EMAIL' &&
      values.value[field.id] &&
      !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(values.value[field.id])
    ) {
      errors.value[field.id] = 'Please enter a valid email address.'
      valid = false
    }
  }

  return valid
}

// ── Submit ────────────────────────────────────────────────────────────────────

const config = useRuntimeConfig()

async function handleSubmit() {
  if (!validate()) return

  submitting.value = true
  submitError.value = null

  const fieldValues = Object.entries(values.value).map(([id, value]) => ({
    id: Number(id),
    value,
  }))

  try {
    const res = await $fetch<{ data: GfSubmitResult; errors?: { message: string }[] }>(
      config.public.graphqlEndpoint as string,
      {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: {
          query: GF_SUBMIT_FORM,
          variables: { formId: String(props.form.databaseId), fieldValues },
        },
      },
    )

    if (res.errors?.length) {
      throw new Error(res.errors.map((e) => e.message).join('; '))
    }

    const result = res.data?.submitGfForm
    if (result?.errors?.length) {
      // Field-level errors from GF
      result.errors.forEach((e) => {
        if (e.id) errors.value[e.id] = e.message
      })
      return
    }

    if (result?.entry?.id) {
      submitted.value = true
      emit('submitted')
    }
  }
  catch (err: unknown) {
    submitError.value =
      err instanceof Error ? err.message : 'Something went wrong. Please try again.'
  }
  finally {
    submitting.value = false
  }
}
</script>

<template>
  <!-- Success state -->
  <div v-if="submitted" class="gf-success">
    <svg
      xmlns="http://www.w3.org/2000/svg"
      width="40"
      height="40"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      stroke-width="2"
      aria-hidden="true"
    >
      <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
      <polyline points="22 4 12 14.01 9 11.01" />
    </svg>
    <h3>Message sent!</h3>
    <p>Thank you for reaching out. We'll get back to you as soon as possible.</p>
  </div>

  <!-- Form -->
  <form v-else class="gf-form" novalidate @submit.prevent="handleSubmit">
    <div
      v-for="field in form.formFields.nodes"
      :key="field.id"
      class="gf-field"
    >
      <label :for="`field-${field.id}`" class="gf-label">
        {{ field.label }}
        <span v-if="field.isRequired" class="gf-required" aria-hidden="true">*</span>
      </label>

      <p v-if="field.description" class="gf-description">
        {{ field.description }}
      </p>

      <!-- Text / Email / Phone -->
      <input
        v-if="['TEXT', 'EMAIL', 'PHONE'].includes(field.type)"
        :id="`field-${field.id}`"
        v-model="values[field.id]"
        :type="field.type === 'EMAIL' ? 'email' : field.type === 'PHONE' ? 'tel' : 'text'"
        :placeholder="field.placeholder ?? ''"
        :required="field.isRequired"
        :aria-invalid="errors[field.id] ? 'true' : undefined"
        :aria-describedby="errors[field.id] ? `error-${field.id}` : undefined"
        class="gf-input"
        :class="{ 'gf-input--error': errors[field.id] }"
      />

      <!-- Textarea -->
      <textarea
        v-else-if="field.type === 'TEXTAREA'"
        :id="`field-${field.id}`"
        v-model="values[field.id]"
        :placeholder="field.placeholder ?? ''"
        :required="field.isRequired"
        :rows="field.rows ?? 5"
        :aria-invalid="errors[field.id] ? 'true' : undefined"
        :aria-describedby="errors[field.id] ? `error-${field.id}` : undefined"
        class="gf-input gf-textarea"
        :class="{ 'gf-input--error': errors[field.id] }"
      />

      <!-- Select -->
      <select
        v-else-if="field.type === 'SELECT'"
        :id="`field-${field.id}`"
        v-model="values[field.id]"
        :required="field.isRequired"
        class="gf-input gf-select"
        :class="{ 'gf-input--error': errors[field.id] }"
      >
        <option value="">Choose an option…</option>
        <option
          v-for="choice in field.choices"
          :key="choice.value"
          :value="choice.value"
        >
          {{ choice.text }}
        </option>
      </select>

      <!-- Unsupported field type fallback -->
      <p v-else class="gf-unsupported">
        [{{ field.type }} field — not yet rendered]
      </p>

      <p
        v-if="errors[field.id]"
        :id="`error-${field.id}`"
        class="gf-error"
        role="alert"
      >
        {{ errors[field.id] }}
      </p>
    </div>

    <!-- Submit error -->
    <p v-if="submitError" class="gf-submit-error" role="alert">
      {{ submitError }}
    </p>

    <button
      type="submit"
      class="gf-submit"
      :disabled="submitting"
    >
      <span v-if="submitting" class="gf-submit__spinner" aria-hidden="true" />
      {{ submitting ? 'Sending…' : (form.submitButton?.text ?? 'Submit') }}
    </button>
  </form>
</template>

<style scoped>
/* ── Success state ─────────────────────────── */
.gf-success {
  text-align: center;
  padding: 3rem 2rem;
  color: #16a34a;
}

.gf-success svg {
  margin: 0 auto 1rem;
  stroke: #16a34a;
}

.gf-success h3 {
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
  color: #1a1a2e;
}

.gf-success p {
  color: #6b7280;
}

/* ── Form layout ───────────────────────────── */
.gf-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.gf-field {
  display: flex;
  flex-direction: column;
  gap: 0.375rem;
}

/* ── Labels ────────────────────────────────── */
.gf-label {
  font-size: 0.9375rem;
  font-weight: 600;
  color: #1a1a2e;
}

.gf-required {
  color: #ef4444;
  margin-left: 0.125rem;
}

.gf-description {
  font-size: 0.8125rem;
  color: #6b7280;
  margin: 0;
}

/* ── Inputs ────────────────────────────────── */
.gf-input {
  padding: 0.625rem 0.875rem;
  border: 1.5px solid #d1d5db;
  border-radius: 0.5rem;
  font-size: 1rem;
  font-family: inherit;
  color: #1a1a2e;
  background: #fff;
  transition: border-color 0.15s, box-shadow 0.15s;
  width: 100%;
}

.gf-input:focus {
  outline: none;
  border-color: #2563eb;
  box-shadow: 0 0 0 3px rgba(37 99 235 / 0.15);
}

.gf-input--error {
  border-color: #ef4444;
}

.gf-input--error:focus {
  box-shadow: 0 0 0 3px rgba(239 68 68 / 0.15);
}

.gf-textarea {
  resize: vertical;
  min-height: 8rem;
}

.gf-select {
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 0.75rem center;
  padding-right: 2.5rem;
  cursor: pointer;
}

/* ── Errors ────────────────────────────────── */
.gf-error {
  font-size: 0.8125rem;
  color: #ef4444;
  font-weight: 500;
  margin: 0;
}

.gf-submit-error {
  padding: 0.75rem 1rem;
  background: #fef2f2;
  border: 1px solid #fca5a5;
  border-radius: 0.5rem;
  font-size: 0.9375rem;
  color: #dc2626;
}

.gf-unsupported {
  font-size: 0.875rem;
  color: #9ca3af;
  font-style: italic;
}

/* ── Submit button ─────────────────────────── */
.gf-submit {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.75rem 2rem;
  background: #2563eb;
  color: #fff;
  border: none;
  border-radius: 0.5rem;
  font-size: 1rem;
  font-weight: 600;
  font-family: inherit;
  cursor: pointer;
  transition: background 0.15s;
  align-self: flex-start;
}

.gf-submit:hover:not(:disabled) {
  background: #1d4ed8;
}

.gf-submit:disabled {
  opacity: 0.65;
  cursor: not-allowed;
}

.gf-submit__spinner {
  width: 1rem;
  height: 1rem;
  border: 2px solid rgba(255 255 255 / 0.4);
  border-top-color: #fff;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}
</style>
