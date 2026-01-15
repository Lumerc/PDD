<template>
  <AdminLayout>
    <div class="pages-index">
      <div class="page-header">
        <h1>Управление страницами</h1>
        <Link :href="route('admin.pages.create')" class="btn btn-primary">
          + Создать страницу
        </Link>
      </div>

      <div v-if="$page.props.flash.success" class="alert alert-success">
        {{ $page.props.flash.success }}
      </div>

      <div class="pages-list">
        <div v-if="pages.length === 0" class="empty-state">
          <p>Страниц пока нет. Создайте первую!</p>
        </div>

        <table v-else class="table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Заголовок</th>
              <th>URL</th>
              <th>Статус</th>
              <th>Действия</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="page in pages" :key="page.id">
              <td>{{ page.id }}</td>
              <td>{{ page.title }}</td>
              <td>/{{ page.slug }}</td>
              <td>
                <span :class="['badge', page.is_published ? 'badge-success' : 'badge-secondary']">
                  {{ page.is_published ? 'Опубликовано' : 'Черновик' }}
                </span>
              </td>
              <td class="actions">
                <Link :href="route('admin.pages.edit', page.id)" class="btn btn-sm btn-info">
                  Редактировать
                </Link>
                <button @click="confirmDelete(page)" class="btn btn-sm btn-danger">
                  Удалить
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import { defineProps } from 'vue'
import { route } from 'ziggy-js'
const props = defineProps({
  pages: Array
})

const confirmDelete = (page) => {
  if (confirm(`Удалить страницу "${page.title}"?`)) {
    router.delete(route('admin.pages.destroy', page.id))
  }
}
</script>

<style scoped>
.pages-index {
  padding: 20px;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
}

.table {
  width: 100%;
  border-collapse: collapse;
  background: white;
}

.table th, .table td {
  padding: 12px;
  border: 1px solid #ddd;
  text-align: left;
}

.table th {
  background: #f8f9fa;
  font-weight: 600;
}

.actions {
  display: flex;
  gap: 8px;
}

.badge {
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
}

.badge-success {
  background: #28a745;
  color: white;
}

.badge-secondary {
  background: #6c757d;
  color: white;
}

.empty-state {
  text-align: center;
  padding: 40px;
  background: #f8f9fa;
  border-radius: 8px;
}
</style>