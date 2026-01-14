<template>
  <div class="admin-dashboard">
    <header class="dashboard-header">
      <h1>Панель управления</h1>
      <div class="user-info">
        <span>Привет, {{ auth.user.name }}!</span>
        <form @submit.prevent="logout" class="logout-form">
          <button type="submit" class="logout-btn">Выйти</button>
        </form>
      </div>
    </header>

    <main class="dashboard-content">
      <div class="welcome-section">
        <h2>Добро пожаловать в админ-панель PDD</h2>
        <p>Вы вошли как <strong>{{ auth.user.email }}</strong></p>
        <p class="user-id">ID пользователя: {{ auth.user.id }}</p>
      </div>

      <div class="stats-cards">
        <div class="card">
          <h3>👤 Ваш профиль</h3>
          <div class="profile-info">
            <p><strong>Имя:</strong> {{ auth.user.name }}</p>
            <p><strong>Email:</strong> {{ auth.user.email }}</p>
            <p><strong>Аккаунт создан:</strong> {{ auth.user.created_at }}</p>
          </div>
        </div>
        
        <div class="card">
          <h3>📊 Статистика</h3>
          <p class="count">0</p>
          <small>Всего страниц (скоро)</small>
        </div>
        
        <div class="card">
          <h3>⚡ Быстрые ссылки</h3>
          <div class="quick-links">
            <a href="#" class="link">Управление страницами</a>
            <a href="#" class="link">Добавить новую страницу</a>
            <a href="#" class="link">Настройки профиля</a>
          </div>
        </div>
      </div>

      <div class="recent-activity">
        <h3>Последние действия</h3>
        <div class="activity-list">
          <div class="activity-item">
            <span class="time">Сегодня, {{ currentTime }}</span>
            <span>Успешный вход в систему</span>
          </div>
        </div>
      </div>
    </main>

    <footer class="dashboard-footer">
      <p>© {{ currentYear }} Админ-панель "Правила дорожного движения"</p>
    </footer>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import { route } from 'ziggy-js'
import { Ziggy } from '../../ziggy'

const props = defineProps({
  auth: {
    type: Object,
    required: true
  }
})

const currentTime = computed(() => {
  return new Date().toLocaleTimeString('ru-RU', {
    hour: '2-digit',
    minute: '2-digit'
  })
})

const currentYear = computed(() => new Date().getFullYear())

const logout = () => {
  useForm({}).post(route('admin.logout'))
}
</script>

<style scoped>
.admin-dashboard {
  min-height: 100vh;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
}

.dashboard-header {
  background: white;
  padding: 1rem 2rem;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  display: flex;
  justify-content: space-between;
  align-items: center;
  position: sticky;
  top: 0;
  z-index: 100;
}

.dashboard-header h1 {
  margin: 0;
  color: #2c3e50;
  font-size: 1.5rem;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.user-info span {
  color: #34495e;
  font-weight: 500;
}

.logout-form {
  margin: 0;
}

.logout-btn {
  padding: 0.5rem 1.5rem;
  background: #e74c3c;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.3s ease;
}

.logout-btn:hover {
  background: #c0392b;
  transform: translateY(-1px);
  box-shadow: 0 4px 8px rgba(231, 76, 60, 0.3);
}

.dashboard-content {
  max-width: 1200px;
  margin: 2rem auto;
  padding: 0 1.5rem;
}

.welcome-section {
  background: white;
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
  margin-bottom: 2rem;
  text-align: center;
}

.welcome-section h2 {
  color: #2c3e50;
  margin-bottom: 1rem;
}

.welcome-section p {
  color: #7f8c8d;
  margin: 0.5rem 0;
}

.user-id {
  font-size: 0.9rem;
  color: #95a5a6;
}

.stats-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.card {
  background: white;
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 15px rgba(0,0,0,0.1);
}

.card h3 {
  color: #3498db;
  margin-top: 0;
  margin-bottom: 1rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.profile-info p {
  margin: 0.5rem 0;
  color: #2c3e50;
}

.profile-info strong {
  color: #34495e;
}

.count {
  font-size: 2.5rem;
  font-weight: bold;
  color: #2ecc71;
  margin: 1rem 0;
  text-align: center;
}

.quick-links {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.link {
  color: #3498db;
  text-decoration: none;
  padding: 0.5rem;
  border-radius: 6px;
  transition: all 0.2s ease;
}

.link:hover {
  background: #ebf5fb;
  color: #2980b9;
  padding-left: 1rem;
}

.recent-activity {
  background: white;
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.recent-activity h3 {
  color: #2c3e50;
  margin-top: 0;
}

.activity-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.activity-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem;
  background: #f8f9fa;
  border-radius: 8px;
  border-left: 4px solid #3498db;
}

.time {
  color: #7f8c8d;
  font-size: 0.9rem;
}

.dashboard-footer {
  text-align: center;
  padding: 2rem;
  color: #7f8c8d;
  margin-top: 3rem;
  border-top: 1px solid #ecf0f1;
}

/* Анимация появления */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.card, .welcome-section, .recent-activity {
  animation: fadeIn 0.6s ease-out;
}
</style>