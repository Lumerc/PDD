<template>
  <Link
    v-for="chapter in chapters"
    :key="chapter.id"
    :href="route('pages.show', chapter.slug)"
    :class="['chapter-item', { active: currentChapter && chapter.id === currentChapter.id }]"
  >
    <div class="chapter-number">Раздел {{ chapter.sort / 10 }}</div>
    <div class="separator">|</div>
    <div class="chapter-title">{{ chapter.title }}</div>
  </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
defineProps({
  chapters: Array,
  currentChapter: Object
})
</script>

<style>
.chapter-item
{
    background: #d1cdcd;
    width: 95%;
    margin: 5px;
    display: flex;
    padding: 10px;
    box-shadow: 4px 0 8px #000 inset;
    font-family: grassBody, sans-serif; 
    text-shadow: 1px 1px 0px white;
}

.separator
{
    flex: 0 0 calc(5% - 10px);
    font-size: 25px;
    font-weight: bold;
}

.chapter-number 
{
    font-size: 25px;
    font-weight: bold;
    flex: 0 0 calc(20% - 10px);
    text-align: center;
}

.chapter-item.active
{
    margin-bottom: 0;
}

.chapter-title
{
    flex: 0 0 calc(50% - 10px);
    font-size: 18px;
    line-height: 37px;
}
</style>