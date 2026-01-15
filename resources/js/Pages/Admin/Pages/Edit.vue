<template>
  <AdminLayout>
    <div class="page-form">
      <h1>Редактирование страницы</h1>
      
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

        <div class="form-group">
          <label for="content">Содержимое *</label>
          <textarea 
            v-model="form.content" 
            id="content" 
            rows="10" 
            class="form-control"
            :class="{ 'is-invalid': form.errors.content }"
            placeholder="Текст страницы..."
          ></textarea>
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
            {{ form.processing ? 'Сохранение...' : 'Сохранить изменения' }}
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

const props = defineProps({
  page: Object
})

const form = useForm({
  title: props.page.title,
  slug: props.page.slug,
  content: props.page.content,
  meta_title: props.page.meta_title,
  meta_description: props.page.meta_description,
  is_published: props.page.is_published
})

const submit = () => {
  form.put(route('admin.pages.update', props.page.id))
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