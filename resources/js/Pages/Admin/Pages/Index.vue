<template>
  <AdminLayout>
    <div class="pages-tree">
      <div class="page-header mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Управление страницами</h1>
        <Link :href="route('admin.pages.create')" class="btn btn-primary">
          + Создать раздел
        </Link>
      </div>

      <div v-if="$page.props.flash.success" class="alert alert-success mb-4">
        {{ $page.props.flash.success }}
      </div>

      <div v-if="tree.length === 0" class="empty-state">
        <p class="text-gray-500">Страниц пока нет. Создайте первую!</p>
      </div>

      <div v-else class="tree-container">
        <div v-for="(chapter, chapterIndex) in tree" :key="chapter.id" class="tree-item level-0 mb-2">
          
          <!-- Заголовок главы (кликабельный) -->
          <div class="tree-node chapter-header" @click="toggleChapter(chapter.id)">
            <div class="node-content">
              <div class="node-info">
                <span class="node-number">Раздел {{ chapterIndex + 1 }}.</span>
                <span class="node-title">{{ chapter.title }}</span>
                <span class="node-badge">Глава</span>
                <span class="toggle-icon ml-2">
                  {{ isChapterOpen(chapter.id) ? '▼' : '▶' }}
                </span>
              </div>
              <div class="node-status">
                <span :class="['status-badge', chapter.is_published ? 'published' : 'draft']">
                  {{ chapter.is_published ? 'Опубликовано' : 'Черновик' }}
                </span>
              </div>
            </div>
            <div class="node-actions">
              <Link :href="route('admin.pages.edit', chapter.id)" class="btn btn-sm btn-info" @click.stop>
                Редактировать
              </Link>
              <Link :href="route('admin.pages.create', { parent_id: chapter.id })" 
                    class="btn btn-sm btn-success" @click.stop>
                + Пункт
              </Link>
              <button @click="confirmDelete(chapter)" class="btn btn-sm btn-danger" @click.stop>
                Удалить
              </button>
            </div>
          </div>
          
          <!-- Пункты этой главы (показываются если глава открыта) -->
          <div v-if="isChapterOpen(chapter.id)" class="chapter-content ml-8 mt-2">
            <div v-for="point in chapter.children" :key="point.id" class="tree-item level-1 mb-2">
              
              <!-- Заголовок пункта (кликабельный) -->
              <div class="tree-node point-header" @click="togglePoint(point.id)" v-if="point.children && point.children.length > 0">
                <div class="node-content">
                  <div class="node-info">
                    <span class="node-number">{{ getPointNumber(chapterIndex, point) }}.</span>
                    <span class="node-title">{{ point.title }}</span>
                    <span class="node-badge">Пункт</span>
                    <span class="toggle-icon ml-2">
                      {{ isPointOpen(point.id) ? '▼' : '▶' }}
                    </span>
                  </div>
                  <div class="node-status">
                    <span :class="['status-badge', point.is_published ? 'published' : 'draft']">
                      {{ point.is_published ? 'Опубликовано' : 'Черновик' }}
                    </span>
                  </div>
                </div>
                <div class="node-actions">
                  <Link :href="route('admin.pages.edit', point.id)" class="btn btn-sm btn-info" @click.stop>
                    Редактировать
                  </Link>
                  <Link :href="route('admin.pages.create', { parent_id: point.id })" 
                        class="btn btn-sm btn-success" @click.stop>
                    + Подпункт
                  </Link>
                  <button @click="confirmDelete(point)" class="btn btn-sm btn-danger" @click.stop>
                    Удалить
                  </button>
                </div>
              </div>
              
              <!-- Если нет подпунктов, пункт не кликабельный -->
              <div v-else class="tree-node">
                <div class="node-content">
                  <div class="node-info">
                    <span class="node-number">{{ getPointNumber(chapterIndex, point) }}.</span>
                    <span class="node-title">{{ point.title }}</span>
                    <span class="node-badge">Пункт</span>
                  </div>
                  <div class="node-status">
                    <span :class="['status-badge', point.is_published ? 'published' : 'draft']">
                      {{ point.is_published ? 'Опубликовано' : 'Черновик' }}
                    </span>
                  </div>
                </div>
                <div class="node-actions">
                  <Link :href="route('admin.pages.edit', point.id)" class="btn btn-sm btn-info">
                    Редактировать
                  </Link>
                  <Link :href="route('admin.pages.create', { parent_id: point.id })" 
                        class="btn btn-sm btn-success">
                    + Подпункт
                  </Link>
                  <button @click="confirmDelete(point)" class="btn btn-sm btn-danger">
                    Удалить
                  </button>
                </div>
              </div>
              
              <!-- Подпункты этого пункта (показываются если пункт открыт) -->
              <div v-if="isPointOpen(point.id) && point.children && point.children.length > 0" class="point-content ml-8 mt-2">
                <div v-for="subpoint in point.children" :key="subpoint.id" class="tree-item level-2 mb-2">
                  <div class="tree-node">
                    <div class="node-content">
                      <div class="node-info">
                        <span class="node-number">{{ getSubpointNumber(chapterIndex, point, subpoint) }}.</span>
                        <span class="node-title">{{ subpoint.title }}</span>
                        <span class="node-badge">Подпункт</span>
                      </div>
                      <div class="node-status">
                        <span :class="['status-badge', subpoint.is_published ? 'published' : 'draft']">
                          {{ subpoint.is_published ? 'Опубликовано' : 'Черновик' }}
                        </span>
                      </div>
                    </div>
                    <div class="node-actions">
                      <Link :href="route('admin.pages.edit', subpoint.id)" class="btn btn-sm btn-info">
                        Редактировать
                      </Link>
                      <button @click="confirmDelete(subpoint)" class="btn btn-sm btn-danger">
                        Удалить
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>


import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import { defineProps, ref, reactive } from 'vue'
import { route } from 'ziggy-js'

const props = defineProps({
  tree: Array
})

const confirmDelete = (page) => {
  if (confirm(`Удалить страницу "${page.title}"?`)) {
    router.delete(route('admin.pages.destroy', page.id))
  }
}

// Состояние для открытых глав
const openChapters = ref(new Set())

// Состояние для открытых пунктов
const openPoints = ref(new Set())

// Переключение главы
const toggleChapter = (chapterId) => {
  if (openChapters.value.has(chapterId)) {
    openChapters.value.delete(chapterId)
    // Закрываем все пункты этой главы при закрытии главы
    const chapter = props.tree.find(c => c.id === chapterId)
    if (chapter && chapter.children) {
      chapter.children.forEach(point => {
        openPoints.value.delete(point.id)
      })
    }
  } else {
    openChapters.value.add(chapterId)
  }
}

// Проверка открыта ли глава
const isChapterOpen = (chapterId) => {
  return openChapters.value.has(chapterId)
}

// Переключение пункта
const togglePoint = (pointId) => {
  if (openPoints.value.has(pointId)) {
    openPoints.value.delete(pointId)
  } else {
    openPoints.value.add(pointId)
  }
}

// Проверка открыт ли пункт
const isPointOpen = (pointId) => {
  return openPoints.value.has(pointId)
}

// Методы для нумерации
const getPointNumber = (chapterIndex, point) => {
  // Находим индекс пункта в его главе
  const chapter = props.tree[chapterIndex]
  if (chapter && chapter.children) {
    const pointIndex = chapter.children.findIndex(p => p.id === point.id)
    if (pointIndex !== -1) {
      return `${chapterIndex + 1}.${pointIndex + 1}`
    }
  }
  return `${chapterIndex + 1}.?`
}

const getSubpointNumber = (chapterIndex, point, subpoint) => {
  const chapter = props.tree[chapterIndex]
  if (chapter && chapter.children) {
    const pointIndex = chapter.children.findIndex(p => p.id === point.id)
    if (pointIndex !== -1 && point.children) {
      const subpointIndex = point.children.findIndex(sp => sp.id === subpoint.id)
      if (subpointIndex !== -1) {
        return `${chapterIndex + 1}.${pointIndex + 1}.${subpointIndex + 1}`
      }
    }
  }
  return `${chapterIndex + 1}.?.?`
}
</script>

<style scoped>
.pages-tree {
  padding: 20px;
  max-width: 1200px;
  margin: 0 auto;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
}

.alert-success {
  background-color: #d1fae5;
  color: #065f46;
  padding: 12px 16px;
  border-radius: 6px;
  border: 1px solid #a7f3d0;
}

.empty-state {
  text-align: center;
  padding: 40px;
  background: #f8f9fa;
  border-radius: 8px;
  border: 1px dashed #dee2e6;
}

.tree-container {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.tree-node {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 14px 16px;
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  transition: all 0.2s ease;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.chapter-header, .point-header {
  cursor: pointer;
}

.chapter-header:hover, .point-header:hover {
  background-color: #f9fafb;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  border-color: #d1d5db;
}

.level-0 .tree-node {
  background: #f0f9ff;
  border-left: 4px solid #3b82f6;
  border-top-left-radius: 0;
  border-bottom-left-radius: 0;
}

.level-1 .tree-node {
  border-left: 4px solid #10b981;
}

.level-2 .tree-node {
  border-left: 4px solid #f59e0b;
}

.chapter-content, .point-content {
  transition: all 0.3s ease;
  overflow: hidden;
}

.node-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-grow: 1;
  margin-right: 16px;
}

.node-info {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-grow: 1;
}

.node-number {
  font-weight: 700;
  color: #374151;
  min-width: 60px;
  font-size: 0.95rem;
}

.node-title {
  flex-grow: 1;
  font-size: 0.95rem;
  color: #1f2937;
}

.node-badge {
  font-size: 0.7rem;
  padding: 3px 10px;
  border-radius: 12px;
  font-weight: 600;
  letter-spacing: 0.3px;
  text-transform: uppercase;
}

.level-0 .node-badge {
  background: #dbeafe;
  color: #1e40af;
}

.level-1 .node-badge {
  background: #d1fae5;
  color: #065f46;
}

.level-2 .node-badge {
  background: #fef3c7;
  color: #92400e;
}

.toggle-icon {
  color: #6b7280;
  font-size: 0.8rem;
  width: 16px;
  text-align: center;
}

.node-status {
  margin-left: 16px;
}

.status-badge {
  font-size: 0.75rem;
  padding: 4px 10px;
  border-radius: 12px;
  font-weight: 500;
}

.status-badge.published {
  background: #d1fae5;
  color: #065f46;
}

.status-badge.draft {
  background: #f3f4f6;
  color: #6b7280;
}

.node-actions {
  display: flex;
  gap: 8px;
  flex-shrink: 0;
}

.btn {
  padding: 6px 12px;
  font-size: 0.875rem;
  border-radius: 6px;
  text-decoration: none;
  border: 1px solid;
  cursor: pointer;
  transition: all 0.2s;
  font-weight: 500;
}

.btn-sm {
  padding: 5px 10px;
  font-size: 0.8rem;
}

.btn-primary {
  background: #3b82f6;
  color: white;
  border-color: #3b82f6;
}

.btn-primary:hover {
  background: #2563eb;
}

.btn-info {
  background: #0ea5e9;
  color: white;
  border-color: #0ea5e9;
}

.btn-info:hover {
  background: #0284c7;
}

.btn-success {
  background: #10b981;
  color: white;
  border-color: #10b981;
}

.btn-success:hover {
  background: #059669;
}

.btn-danger {
  background: #ef4444;
  color: white;
  border-color: #ef4444;
}

.btn-danger:hover {
  background: #dc2626;
}

.btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.mb-2 {
  margin-bottom: 8px;
}

.mb-4 {
  margin-bottom: 16px;
}

.mb-6 {
  margin-bottom: 24px;
}

.ml-2 {
  margin-left: 8px;
}

.ml-8 {
  margin-left: 32px;
}

.mt-2 {
  margin-top: 8px;
}
</style>