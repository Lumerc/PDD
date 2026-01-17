<template>
  <AdminLayout>
    <div class="pages-tree">
      <!-- Кнопка создания главы -->
      <div class="mb-4">
        <Link :href="route('admin.pages.create')" class="btn btn-primary">
          + Создать главу
        </Link>
      </div>

      <!-- Дерево страниц -->
      <div v-for="chapter in tree" :key="chapter.id" class="tree-item level-0">
        <div class="tree-node">
          <div class="node-content">
            <span class="node-number">{{ chapter.auto_number }}.</span>
            <span class="node-title">{{ chapter.title }}</span>
            <span class="node-badge">Глава</span>
          </div>
          <div class="node-actions">
            <Link :href="route('admin.pages.edit', chapter.id)" class="btn btn-sm">
              Редактировать
            </Link>
            <Link :href="route('admin.pages.create', { parent_id: chapter.id })" 
                  class="btn btn-sm btn-success">
              + Пункт
            </Link>
          </div>
        </div>
        
        <!-- Пункты этой главы -->
        <div v-for="point in chapter.children" :key="point.id" class="tree-item level-1">
          <div class="tree-node">
            <div class="node-content">
              <span class="node-number">{{ point.auto_number }}.</span>
              <span class="node-title">{{ point.title }}</span>
              <span class="node-badge">Пункт</span>
            </div>
            <div class="node-actions">
              <Link :href="route('admin.pages.edit', point.id)" class="btn btn-sm">
                Редактировать
              </Link>
              <Link :href="route('admin.pages.create', { parent_id: point.id })" 
                    class="btn btn-sm btn-success">
                + Подпункт
              </Link>
            </div>
          </div>
          
          <!-- Подпункты этого пункта -->
          <div v-for="subpoint in point.children" :key="subpoint.id" class="tree-item level-2">
            <div class="tree-node">
              <div class="node-content">
                <span class="node-number">{{ subpoint.auto_number }}.</span>
                <span class="node-title">{{ subpoint.title }}</span>
                <span class="node-badge">Подпункт</span>
              </div>
              <div class="node-actions">
                <Link :href="route('admin.pages.edit', subpoint.id)" class="btn btn-sm">
                  Редактировать
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

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
        <div v-if="tree.length === 0" class="empty-state">
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
            <tr v-for="page in tree" :key="page.id">
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
  tree: Array
})

const confirmDelete = (page) => {
  if (confirm(`Удалить страницу "${page.title}"?`)) {
    router.delete(route('admin.pages.destroy', page.id))
  }
}
</script>

<style scoped>
.tree-item {
  margin-left: 0;
}

.level-1 {
  margin-left: 30px;
  border-left: 2px solid #e0e0e0;
  padding-left: 15px;
}

.level-2 {
  margin-left: 60px;
  border-left: 2px solid #f0f0f0;
  padding-left: 15px;
}

.tree-node {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px;
  background: white;
  margin-bottom: 5px;
  border-radius: 6px;
  border: 1px solid #eee;
}

.node-content {
  display: flex;
  align-items: center;
  gap: 10px;
}

.node-number {
  font-weight: bold;
  color: #2c3e50;
  min-width: 40px;
}

.node-badge {
  font-size: 11px;
  padding: 2px 8px;
  border-radius: 10px;
  background: #f8f9fa;
  color: #666;
}

.node-actions {
  display: flex;
  gap: 5px;
}

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