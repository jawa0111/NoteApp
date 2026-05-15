<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const apiUrl = 'http://127.0.0.1:8000/api/notes'

const notes = ref([])
const loading = ref(false)
const saving = ref(false)
const archivingId = ref(null)
const error = ref('')
const filter = ref('')
const toast = ref('')

const form = ref({
  title: '',
  content: '',
  priority: 'low'
})

const fetchNotes = async () => {
  error.value = ''
  loading.value = true

  try {
    let url = apiUrl
    if (filter.value) {
      url += `?priority=${filter.value}`
    }

    const res = await axios.get(url)
    const payload = res.data

    // support both array responses and Laravel paginator shape
    if (Array.isArray(payload)) {
      notes.value = payload
    } else if (payload && Array.isArray(payload.data)) {
      notes.value = payload.data
    } else {
      notes.value = []
    }
  } catch (err) {
    error.value = 'Unable to load notes.'
  } finally {
    loading.value = false
  }
}

const createNote = async () => {
  error.value = ''

  if (!form.value.title.trim() || !form.value.content.trim()) {
    error.value = 'Title and content are required.'
    return
  }

  saving.value = true

  try {
    await axios.post(apiUrl, {
      ...form.value,
      is_archived: false
    })

    form.value = {
      title: '',
      content: '',
      priority: 'low'
    }

    await fetchNotes()
    toast.value = 'Note created'
    setTimeout(() => (toast.value = ''), 3000)
  } catch (err) {
    error.value = 'Unable to create note.'
  } finally {
    saving.value = false
  }
}

const archiveNote = async (id) => {
  error.value = ''
  archivingId.value = id

  try {
    await axios.patch(`${apiUrl}/${id}/archive`)
    await fetchNotes()
    toast.value = 'Note archived'
    setTimeout(() => (toast.value = ''), 3000)
  } catch (err) {
    error.value = 'Unable to archive note.'
  } finally {
    archivingId.value = null
  }
}

onMounted(fetchNotes)
</script>

<template>
  <div class="page-shell">
    <section class="panel">
      <div class="header-row">
        <div>
          <h1>Student Notes</h1>
          <p class="subtitle">Create notes, filter by priority, and archive completed items.</p>
        </div>
      </div>

      <div class="form-card">
        <h2>Create a note</h2>
        <form @submit.prevent="createNote" class="form-grid">
          <label>
            Title
            <input v-model="form.title" placeholder="Note title" />
          </label>

          <label>
            Content
            <textarea v-model="form.content" placeholder="Note content"></textarea>
          </label>

          <label>
            Priority
            <select v-model="form.priority">
              <option value="low">Low</option>
              <option value="medium">Medium</option>
              <option value="high">High</option>
            </select>
          </label>

          <button type="submit" class="primary-button" :disabled="saving">
            {{ saving ? 'Saving...' : 'Create Note' }}
          </button>
        </form>
      </div>

      <div class="filter-row">
        <label>
          Filter by priority:
          <select v-model="filter" @change="fetchNotes">
            <option value="">All</option>
            <option value="low">Low</option>
            <option value="medium">Medium</option>
            <option value="high">High</option>
          </select>
        </label>
      </div>

      <div class="status-row">
        <div v-if="error" class="status error">{{ error }}</div>
        <div v-else-if="loading" class="status loading">Loading notes...</div>
      </div>

      <div v-if="!loading && notes.length === 0" class="empty-state">
        No notes found. Create one to get started.
      </div>

      <div class="notes-grid">
        <article v-for="note in notes" :key="note.id" class="note-card">
          <div class="note-header">
            <div>
              <h3>{{ note.title }}</h3>
              <small class="meta">{{ note.created_at ? new Date(note.created_at).toLocaleString() : '' }}</small>
            </div>
            <span class="badge" :class="note.priority">{{ note.priority }}</span>
          </div>

          <p class="note-content">{{ note.content }}</p>

          <div class="note-footer">
            <span class="status-chip" :class="note.is_archived ? 'archived' : 'active'">
              {{ note.is_archived ? 'Archived' : 'Active' }}
            </span>

            <button
              v-if="!note.is_archived"
              @click="archiveNote(note.id)"
              :disabled="archivingId === note.id"
            >
              {{ archivingId === note.id ? 'Archiving...' : 'Archive' }}
            </button>
          </div>
        </article>
      </div>
    </section>
  </div>
</template>

<style>
.page-shell {
  max-width: 900px;
  margin: 0 auto;
  padding: 20px;
  font-family: Inter, system-ui, sans-serif;
  color: #1f2937;
}

.header-row {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 1rem;
  margin-bottom: 20px;
}

h1 {
  margin: 0 0 6px;
  font-size: 2rem;
}

.subtitle {
  margin: 0;
  color: #4b5563;
}

.panel {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 14px;
  padding: 24px;
  box-shadow: 0 12px 24px rgba(15, 23, 42, 0.04);
}

.form-card,
.filter-row,
.notes-grid {
  margin-top: 24px;
}

.form-grid {
  display: grid;
  gap: 14px;
}

.form-grid label {
  display: flex;
  flex-direction: column;
  gap: 8px;
  font-weight: 500;
  color: #374151;
}

input,
textarea,
select {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #d1d5db;
  border-radius: 10px;
  font: inherit;
  color: #111827;
}

textarea {
  min-height: 100px;
  resize: vertical;
}

.primary-button {
  width: fit-content;
  padding: 10px 18px;
  background: #2563eb;
  border: none;
  color: white;
  border-radius: 10px;
  cursor: pointer;
}

.primary-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.filter-row {
  display: flex;
  align-items: center;
  justify-content: flex-start;
  gap: 16px;
}

.status-row {
  margin-top: 18px;
}

.status {
  padding: 12px 16px;
  border-radius: 10px;
  font-weight: 500;
}

.status.loading {
  background: #eff6ff;
  color: #1d4ed8;
}

.status.error {
  background: #fef2f2;
  color: #b91c1c;
}

.empty-state {
  padding: 18px;
  background: #f8fafc;
  border: 1px dashed #cbd5e1;
  border-radius: 12px;
  margin-top: 16px;
  color: #475569;
}

.notes-grid {
  display: grid;
  gap: 16px;
}

.note-card {
  padding: 18px;
  border: 1px solid #e5e7eb;
  border-radius: 14px;
}

.note-header {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
  align-items: flex-start;
  margin-bottom: 12px;
}

.note-header h3 {
  margin: 0;
}

.meta {
  color: #6b7280;
  font-size: 0.92rem;
}

.badge {
  padding: 6px 10px;
  border-radius: 9999px;
  text-transform: capitalize;
  font-size: 0.85rem;
  font-weight: 600;
}

.badge.low {
  background: #eef2ff;
  color: #4338ca;
}

.badge.medium {
  background: #fef3c7;
  color: #92400e;
}

.badge.high {
  background: #fee2e2;
  color: #991b1b;
}

.note-content {
  margin: 0 0 14px;
  color: #374151;
}

.note-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
}

.status-chip {
  padding: 6px 10px;
  border-radius: 9999px;
  font-size: 0.9rem;
}

.status-chip.active {
  background: #dcfce7;
  color: #166534;
}

.status-chip.archived {
  background: #e5e7eb;
  color: #374151;
}

button {
  border: 1px solid #d1d5db;
  border-radius: 10px;
  background: white;
  padding: 10px 14px;
  cursor: pointer;
}

button:disabled {
  opacity: 0.65;
  cursor: not-allowed;
}
</style>