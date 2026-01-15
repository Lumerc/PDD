<template>
  <div class="admin-layout">
    <!-- Шапка -->
    <header class="admin-header">
      <div class="container">
        <div class="header-content">
          <div class="header-left">
            <Link :href="route('admin.dashboard')" class="logo">
              <span class="logo-icon">🚦</span>
              <span class="logo-text">PDD Админ</span>
            </Link>
            <nav class="nav">
              <Link 
                :href="route('admin.dashboard')" 
                :class="['nav-link', { active: $page.url === '/adminenter/dashboard' }]"
              >
                Дашборд
              </Link>
              <Link 
                :href="route('admin.pages.index')" 
                :class="['nav-link', { active: $page.url.startsWith('/adminenter/pages') }]"
              >
                Страницы
              </Link>
            </nav>
          </div>
          
          <div class="header-right">
            <div class="user-info">
              <span class="user-name">{{ $page.props.auth.user?.name || 'Администратор' }}</span>
              <form @submit.prevent="logout" class="logout-form">
                <button type="submit" class="logout-btn">
                  <span class="logout-icon">🚪</span>
                  Выйти
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Контент -->
    <main class="admin-main">
      <div class="container">
        <slot />
      </div>
    </main>

    <!-- Футер -->
    <footer class="admin-footer">
      <div class="container">
        <p>© {{ new Date().getFullYear() }} Админ-панель "Правила дорожного движения"</p>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
const logout = () => {
  useForm({}).post(route('admin.logout'))
}
</script>

<style scoped>
.admin-layout {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background: #f5f7fa;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

/* Шапка */
.admin-header {
  background: white;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  position: sticky;
  top: 0;
  z-index: 1000;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 0;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 40px;
}

.logo {
  display: flex;
  align-items: center;
  gap: 10px;
  text-decoration: none;
  font-weight: bold;
  font-size: 20px;
  color: #2c3e50;
}

.logo-icon {
  font-size: 24px;
}

.nav {
  display: flex;
  gap: 20px;
}

.nav-link {
  padding: 8px 16px;
  text-decoration: none;
  color: #555;
  border-radius: 6px;
  transition: all 0.2s;
}

.nav-link:hover {
  background: #f0f0f0;
  color: #2c3e50;
}

.nav-link.active {
  background: #3498db;
  color: white;
}

/* Правая часть шапки */
.user-info {
  display: flex;
  align-items: center;
  gap: 20px;
}

.user-name {
  color: #2c3e50;
  font-weight: 500;
}

.logout-form {
  margin: 0;
}

.logout-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  background: #e74c3c;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  transition: background 0.2s;
}

.logout-btn:hover {
  background: #c0392b;
}

.logout-icon {
  font-size: 16px;
}

/* Основной контент */
.admin-main {
  flex: 1;
  padding: 30px 0;
}

/* Футер */
.admin-footer {
  background: #2c3e50;
  color: white;
  padding: 20px 0;
  text-align: center;
  margin-top: auto;
}

.admin-footer p {
  margin: 0;
  color: #95a5a6;
}
</style>