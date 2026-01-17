<template>
  <AdminLayout>
    <div class="page-form">
      <h1>Создание новой страницы</h1>
      
      <form @submit.prevent="submit" class="form">
        <div class="form-group">
          <label for="title">Заголовок *</label>
          <input 
            v-model="form.title" 
            id="title" 
            type="text" 
            class="form-control"
            :class="{ 'is-invalid': form.errors.title }"
          >
          <div v-if="form.errors.title" class="error">{{ form.errors.title }}</div>
        </div>

        <div class="form-group">
          <label for="slug">URL (slug) *</label>
          <input 
            v-model="form.slug" 
            id="slug" 
            type="text" 
            class="form-control"
            :class="{ 'is-invalid': form.errors.slug }"
            placeholder="пример-страницы"
          >
          <small>Только латинские буквы, цифры и дефисы</small>
          <div v-if="form.errors.slug" class="error">{{ form.errors.slug }}</div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="parent_id">Родительский элемент</label>
            <select v-model="form.parent_id" id="parent_id" class="form-control">
              <option value="">--- Глава (верхний уровень) ---</option>
              
              <!-- Главы (уровень 0) -->
              <optgroup label="Главы">
                <option v-for="chapter in chapters" :value="chapter.id">
                  {{ getChapterNumber(chapter) }}. {{ chapter.title }}
                </option>
              </optgroup>
              
              <!-- Пункты (уровень 1) -->
              <optgroup label="Пункты">
                <option v-for="point in points" :value="point.id">
                  {{ getPointNumber(point) }}. {{ point.title }}
                </option>
              </optgroup>
            </select>
            <small v-if="form.parent_id">
              Будет создан как 
              <span v-if="selectedParentLevel === 0">пункт</span>
              <span v-if="selectedParentLevel === 1">подпункт</span>
              выбранного элемента
            </small>
          </div>

          <div class="form-group">
            <label for="sort">Порядок (меньше = выше)</label>
            <input v-model="form.sort" type="number" min="0" class="form-control">
          </div>
        </div>

        <div class="form-group">
          <label for="content">Содержимое *</label>
          <Editor
              v-model="form.content"
              api-key="qztfxya46u353ocwf0hou6s42jbmnj5ulqtoxrritdmqy9f7"
              :init="{
                toolbar_mode: 'sliding',
                plugins: [
                  // Core editing features
                  'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'link', 'lists', 'media', 'searchreplace', 'table', 'visualblocks', 'wordcount',
                  
                ],
                toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography uploadcare | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
                tinycomments_mode: 'embedded',
                tinycomments_author: 'Author name',
                mergetags_list: [
                  { value: 'First.Name', title: 'First Name' },
                  { value: 'Email', title: 'Email' },
                ],
                ai_request: (request, respondWith) => respondWith.string(() => Promise.reject('See docs to implement AI Assistant')),
                uploadcare_public_key: 'd6e287e47637d3567605',
              }"
              />
          <div v-if="form.errors.content" class="error">{{ form.errors.content }}</div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="meta_title">Meta Title</label>
            <input 
              v-model="form.meta_title" 
              id="meta_title" 
              type="text" 
              class="form-control"
            >
          </div>

          <div class="form-group">
            <label for="meta_description">Meta Description</label>
            <textarea 
              v-model="form.meta_description" 
              id="meta_description" 
              rows="3" 
              class="form-control"
            ></textarea>
          </div>
        </div>

        <div class="form-group">
          <label class="checkbox">
            <input v-model="form.is_published" type="checkbox">
            Опубликовать сразу
          </label>
        </div>

        <div class="form-actions">
          <Link :href="route('admin.pages.index')" class="btn btn-secondary">
            Отмена
          </Link>
          <button type="submit" class="btn btn-primary" :disabled="form.processing">
            {{ form.processing ? 'Создание...' : 'Создать страницу' }}
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

import Editor from '@tinymce/tinymce-vue'

const form = useForm({
  title: '',
  slug: '',
  content: '',
  meta_title: '',
  meta_description: '',
  is_published: true
})

const submit = () => {
  form.post(route('admin.pages.store'))
}
</script>

<style scoped>
.page-form {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

.form {
  background: white;
  padding: 30px;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.form-group {
  margin-bottom: 20px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

.form-control {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 16px;
}

.form-control.is-invalid {
  border-color: #dc3545;
}

.error {
  color: #dc3545;
  font-size: 14px;
  margin-top: 5px;
}

small {
  color: #666;
  font-size: 12px;
  display: block;
  margin-top: 5px;
}

.checkbox {
  display: flex;
  align-items: center;
  gap: 10px;
  cursor: pointer;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 30px;
  padding-top: 20px;
  border-top: 1px solid #eee;
}
</style>