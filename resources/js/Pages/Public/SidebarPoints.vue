<template>
  <aside class="sidebar-points" v-if="hasPoints">    
    <nav class="points-list">
      <Link
        v-for="(point, index) in safePoints"
        :key="point.id"
        :href="getPointUrl(point)"
        :class="['point-item', { active: isActive(index + 1) }]"
      >
        <div class="point-number">{{ (currentChapter.sort/10) + '.' + (index + 1) }}</div>
        <div class="point-title">{{ point.title || 'Без названия' }}</div>
      </Link>
    </nav>
  </aside>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import { route } from 'ziggy-js'

const props = defineProps({
  points: {
    type: Array,
    default: () => []  // Значение по умолчанию - пустой массив
  },
  currentPage: Object,
  currentPoint: Number,
  currentChapter: Object
})
console.debug(props);
// Безопасный доступ к точкам
const safePoints = computed(() => {
  return Array.isArray(props.points) ? props.points : []
})

// Проверка наличия точек
const hasPoints = computed(() => {
  return safePoints.value.length > 0
})

// Получение заголовка главы
const chapterTitle = computed(() => {
  if (!hasPoints.value) return ''
  
  const firstPoint = safePoints.value[0]
  if (firstPoint && firstPoint.parent) {
    return firstPoint.parent.title || ''
  }
  
  return ''
})

// Генерация URL для пункта
const getPointUrl = (point) => {
  if (!point || !point.slug) return '#'
  return route('pages.show', point.slug)
}

// Проверка активного пункта
const isActive = (point) => {
  return point === props.currentPoint / 10
}
</script>

<style>
.sidebar-points
{
    flex: 0 0 20%;
    padding: 0px 15px;
}
.point-number 
{
    font-size: 35px;
    line-height: 45px;
    padding: 5px;
    min-width: 60px;
    text-align: center;
    font-weight: bold;
}
a.point-item
{
    background: #1e4ee9;
    display: flex;
    color: white;
    align-items: center;
    border-bottom: 3px solid black;
}
a.point-item:last-child
{
    border-bottom: none;
}
a.point-item.active, a.point-item.active:hover 
{
    background: #e0e0e0;
    color: black;
    cursor: default;
    box-shadow: 2px 1px 5px black inset;
}
a.point-item:hover 
{
    background: #5375e3;
    color: white;
}
.points-list
{
    box-shadow: 2px 2px 2px black;
}
.point-title
{
    padding: 8px 12px 8px 8px;
    font-size: 13px;
    line-height: 15px;
}
</style>