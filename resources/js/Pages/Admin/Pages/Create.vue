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
              <option :value="null">--- Глава (верхний уровень) ---</option>
              
              <!-- Главы (уровень 0) -->
              <optgroup label="Главы">
                <option v-for="chapter in chapters" :key="chapter.id" :value="chapter.id">
                  {{ getChapterNumber(chapter) }}. {{ chapter.title }}
                </option>
              </optgroup>
              
              <!-- Пункты (уровень 1) -->
              <optgroup label="Пункты">
                <option v-for="point in points" :key="point.id"  :value="point.id">
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
            :init="tinyMCEConfig"
            :api-key="tinymceKey"
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

          <div class="form-group">
            <label for="menu_html">Меню пункта</label>
            <textarea 
              v-model="form.menu_html" 
              id="menu_html" 
              rows="6" 
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
import { usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import Editor from '@tinymce/tinymce-vue'
import { ref, computed, watch, onMounted } from 'vue'

const page = usePage();

const props = defineProps({
  page: Object,
  chapters: Array,
  points: Array,
  parent_id: Number
})

const initialParentId = props.parent_id === 0 ? null : props.parent_id;

const tinymceKey = page.props.tinymce_key;

const tinyMCEConfig = ref({
  height: 600,
  menubar: true,
  toolbar: 'undo redo | formatselect | ' +
           'bold italic underline strikethrough | ' +
           'alignleft aligncenter alignright alignjustify | ' +
           'bullist numlist outdent indent | ' +
           'link image media table | ' +
           'removeformat preview code help',
  
  plugins: [
    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
    'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
    'insertdatetime', 'media', 'table', 'help', 'wordcount'
  ],
  
  images_upload_handler: function (blobInfo, success, failure) {
    const xhr = new XMLHttpRequest()
    const formData = new FormData()
    
    formData.append('file', blobInfo.blob(), blobInfo.filename())
    
    // CSRF токен для Laravel
    const token = document.querySelector('meta[name="csrf-token"]')
    if (token) {
      formData.append('_token', token.content)
    }
    
    xhr.open('POST', route('admin.upload'))
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest')
    
    xhr.onload = function() {
      if (xhr.status !== 200) {
        failure('HTTP Error: ' + xhr.status)
        return
      }
      
      let json
      try {
        json = JSON.parse(xhr.responseText)
      } catch (e) {
        failure('Invalid JSON response')
        return
      }
      
      if (json.location) {
        success(json.location)
      } else if (json.error) {
        failure(json.error)
      } else {
        failure('Unknown error')
      }
    }
    
    xhr.onerror = function() {
      failure('Network error')
    }
    
    xhr.send(formData)
  },
  

  automatic_uploads: true,
  images_upload_url: route('admin.upload'),
  file_picker_types: 'image',
  images_reuse_filename: false,
  
  content_style: `
    body { 
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
      font-size: 16px; 
      line-height: 1.6;
      color: #1a202c;
      padding: 10px;
    }
    h1, h2, h3, h4, h5, h6 {
      color: #2d3748;
      margin-top: 1.5em;
      margin-bottom: 0.5em;
    }
    h1 { font-size: 2em; }
    h2 { font-size: 1.5em; }
    h3 { font-size: 1.25em; }
    p { margin-bottom: 1em; }
    img { max-width: 100%; height: auto; }
    table { border-collapse: collapse; width: 100%; margin: 1em 0; }
    th { background-color: #f7fafc; font-weight: 600; }
    td, th { border: 1px solid #e2e8f0; padding: 8px 12px; }
    ul, ol { padding-left: 2em; margin: 1em 0; }
    a { color: #3182ce; text-decoration: underline; }
    blockquote { 
      border-left: 4px solid #e2e8f0; 
      margin: 1.5em 0; 
      padding: 0.5em 1em; 
      color: #4a5568;
    }
  `,
  
  // Дополнительные настройки
  branding: false,
  elementpath: false,
  statusbar: true,
  convert_urls: false,
  relative_urls: false,
  remove_script_host: false
})


const selectedParentLevel = computed(() => {
  if (!form.parent_id) return null
  
  // Ищем в главах
  const foundChapter = props.chapters.find(ch => ch.id == form.parent_id)
  if (foundChapter) return 0
  
  // Ищем в пунктах
  const foundPoint = props.points.find(pt => pt.id == form.parent_id)
  if (foundPoint) return 1
  
  return null
})

function getChapterNumber(chapter) {
  return chapter.sort ? chapter.sort.toString().padStart(2, '0') : '00'
}

function getPointNumber(point) {
  if (!point.parent) return '?'
  
  const chapterNum = getChapterNumber(point.parent)
  const pointNum = point.sort ? point.sort.toString().padStart(2, '0') : '00'
  return `${chapterNum}.${pointNum}`
}

const form = useForm({
  title: '',
  slug: '',
  content: '',
  meta_title: '',
  menu_html: '',
  parent_id: initialParentId,
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