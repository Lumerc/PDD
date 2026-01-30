<template>
  <PublicLayout>
    <div class="public-page-container">

      <SidebarChapters 
        :chapters="previousChapters"
        :currentChapter="currentChapter"
      />
      
      <!-- Центральный контент -->
      <div class="content-wrapper">
      <!-- Левое меню - Пункты текущей главы -->
      <SidebarPoints 
        :points="points"
        :page="page"
        :currentChapter="currentChapter"
        :currentPoint="currentPoint"
      />

      <div class="middle">
      <SidebarSubPoints 
        :page="page"
        :subPoints="subPoints"
      />
        <article class="pdd-content">
          <h1><span>{{ mainTitle }}</span><span></span>{{ subTitle }}</h1>
          <div class="content" v-html="page.content"></div>
          
          <!-- Навигация внутри пунктов -->
          <div class="page-navigation">
            <div>
            <Link 
              v-if="prevPage" 
              :href="route('pages.show', prevPage.slug)"
              class="nav-link prev"
            >
              ← {{ prevPage.title }}
            </Link>
            </div>
            <div>
            <Link 
              v-if="nextPage" 
              :href="route('pages.show', nextPage.slug)"
              class="nav-link next"
            >
              {{ nextPage.title }} →
            </Link>
            </div>
          </div>
        </article>
        </div>
      </div>
      
      <SidebarChapters 
        :chapters="nextChapters"
        :currentChapter="currentChapter"
      />
    </div>
  </PublicLayout>
</template>

<script setup>
import { computed } from 'vue'
import { route } from 'ziggy-js'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import SidebarChapters from './SidebarChapters.vue'
import SidebarPoints from './SidebarPoints.vue'
import SidebarSubPoints from './SidebarSubPoints.vue'
import { Link } from '@inertiajs/vue3'


const props = defineProps({
  page: Object,
  chapters: Array,           // Все главы (уровень 1)
  currentChapter: Object,    // Текущая глава
  currentPoint: Number,
  points: Array, // Пункты текущей главы
  subPoints: Array,
  prevPage: Object,
  nextPage: Object,
  mainTitle: String,
  subTitle: String
})

// Главы до текущей включительно
const previousChapters = computed(() => {
  if (!props.currentChapter) return []
  
  // Находим индекс текущей главы
  const currentIndex = props.chapters.findIndex(
    chapter => chapter.id === props.currentChapter.id
  )

  console.debug(props);
  
  // Если не нашли, возвращаем все главы
  if (currentIndex === -1) return props.chapters
  
  // Возвращаем главы от начала до текущей включительно
  return props.chapters.slice(0, currentIndex + 1)
})

// Главы после текущей
const nextChapters = computed(() => {
  if (!props.currentChapter) return []
  
  // Находим индекс текущей главы
  const currentIndex = props.chapters.findIndex(
    chapter => chapter.id === props.currentChapter.id
  )
  
  // Если не нашли или это последняя глава, возвращаем пустой массив
  if (currentIndex === -1 || currentIndex === props.chapters.length - 1) {
    return []
  }
  
  // Возвращаем главы после текущей
  return props.chapters.slice(currentIndex + 1)
})
</script>

<style>
.content-wrapper
{
    background: #dadada;
    width: 94%;
    margin-bottom: 10px;
    box-shadow: 0px 1px 3px black inset;
    display: flex;
}

.public-page-container
{
    display: flex;
    flex-direction: column;
    align-items: center;
}

@font-face 
{  
    font-family: 'grassBody';  
    src: url('/resources/css/ElegantGrassBodyS (kerning).otf') format('opentype');  
}  

@font-face 
{  
    font-family: 'bw';  
    src: url('/resources/css/BwSurco-Regular.ttf') format('truetype');  
}

body *
{
    font-family: bw, sans-serif; 
}

svg
{
    display: inline-block;
    width: 150px;
    height: 150px;
}

article.pdd-content 
{
    width: 96%;
    background: #f9f9f9;
    margin: 0 20px 20px;
    box-shadow: 2px 2px 5px #545454;
    padding: 60px 30px;
}

article.pdd-content h1 
{
    width: 100%;
    border-bottom: 1px solid black;
    font-size: 20px;
    text-align: right;
    line-height: 14px;
    margin-bottom: 60px;
    display: flex;
    justify-content: space-between;
}

td 
{
    padding: 5px;
}

table
{
    text-align: center;
    width: 100%;
}

.middle
{
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 100%;
}

.page-navigation 
{
    margin-top: 45px;
    display: flex;
    justify-content: space-between;
    color: #1e4ee9;
    font-size: 16px;
    font-weight: bold;
}
</style>