<template>
  <div class="admin-enter-page">
    <h1>Вход в админ-панель</h1>

    <!-- Сообщения об ошибках, если они есть -->
    <div v-if="hasErrors" class="alert alert-error">
      <p>Не удалось войти. Проверьте email и пароль.</p>
    </div>

    <!-- Форма для входа -->
    <form @submit.prevent="submit" class="login-form">
      <div class="form-group">
        <label for="email">Email</label>
        <input
          v-model="form.email"
          id="email"
          type="email"
          required
          autofocus
          placeholder="admin@example.com"
        />
        <!-- Показ ошибок валидации для конкретного поля -->
        <p v-if="errors.email" class="error">{{ errors.email }}</p>
      </div>

      <div class="form-group">
        <label for="password">Пароль</label>
        <input
          v-model="form.password"
          id="password"
          type="password"
          required
          placeholder="Введите пароль"
        />
        <p v-if="errors.password" class="error">{{ errors.password }}</p>
      </div>

      <div class="form-group">
        <button type="submit" :disabled="form.processing">
          {{ form.processing ? 'Вход...' : 'Войти' }}
        </button>
      </div>
    </form>

    <hr>
    <p><small>Маршрут: <code>/adminenter</code> | Компонент: <code>Admin/Enter.vue</code></small></p>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import { route } from 'ziggy-js'
// Явно импортируем объект Ziggy
import { Ziggy } from '../../ziggy'

const form = useForm({
  email: '',
  password: '',
})

const props = defineProps({
  errors: {
    type: Object,
    default: () => ({})
  }
})

const hasErrors = computed(() => {
  const errorCount = Object.keys(props.errors || {}).length
  const formErrorCount = Object.keys(form.errors || {}).length
  return errorCount > 0 || formErrorCount > 0
})

const submit = () => {
  // Явно передаём объект Ziggy как третий аргумент (второй — параметры, у нас пусто)
  form.post(route('admin.login', {}, Ziggy), {
    onSuccess: () => {
      console.log('Успешный вход! Редирект...')
    },
    onError: (errors) => {
      console.log('Ошибка входа', errors)
    },
  })
}
</script>

<style scoped>
.admin-enter-page {
  max-width: 400px;
  margin: 0 auto;
  padding: 2rem;
  font-family: sans-serif;
}

.login-form {
  background: #f9f9f9;
  padding: 1.5rem;
  border-radius: 8px;
  margin-top: 1.5rem;
}

.form-group {
  margin-bottom: 1rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: bold;
  color: #333;
}

.form-group input {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  box-sizing: border-box;
  font-size: 1rem;
}

.form-group input:focus {
  outline: none;
  border-color: #4a90e2;
  box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.2);
}

.form-group button {
  width: 100%;
  padding: 0.75rem;
  background-color: #4a90e2;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  cursor: pointer;
  transition: background-color 0.2s;
}

.form-group button:hover:not(:disabled) {
  background-color: #3a7bc8;
}

.form-group button:disabled {
  background-color: #aaa;
  cursor: not-allowed;
}

.alert {
  padding: 1rem;
  border-radius: 4px;
  margin-bottom: 1rem;
}

.alert-error {
  background-color: #fee;
  border: 1px solid #f99;
  color: #c00;
}

.error {
  color: #c00;
  font-size: 0.875rem;
  margin-top: 0.25rem;
}

hr {
  margin: 2rem 0;
}
</style>