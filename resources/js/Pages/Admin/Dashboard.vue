<template>
  <AdminLayout>
    <div class="admin-dashboard">
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
              <Link :href="route('admin.pages.index')" class="action-card">
                <h4>📄 Управление страницами</h4>
                <p>Создание и редактирование страниц</p>
              </Link>
              <div class="action-card">
                <h4>👥 Пользователи</h4>
                <p>Управление администраторами (скоро)</p>
              </div>
              <div class="action-card">
                <h4>⚙️ Настройки</h4>
                <p>Настройки сайта (скоро)</p>
              </div>
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

    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { useForm, Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import { route } from 'ziggy-js'

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

.quick-links {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

/* Вместо .link используем .action-card */
.action-card {
  display: block;
  background: white;
  padding: 1.5rem;
  border-radius: 8px;
  text-decoration: none;
  color: inherit;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  transition: transform 0.2s, box-shadow 0.2s;
  border: 2px solid transparent;
}

.action-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0,0,0,0.15);
  border-color: #3498db;
}

.action-card h4 {
  margin: 0 0 0.5rem 0;
  color: #3498db;
}

.action-card p {
  margin: 0;
  color: #666;
  font-size: 0.9rem;
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

/* Анимация появления */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.card, .welcome-section, .recent-activity {
  animation: fadeIn 0.6s ease-out;
}
</style>