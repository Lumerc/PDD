<template>
    <div class="sidebarSubPoints">
        <Link
            v-for="subPoint in subPoints"
            :key="subPoint.id"
            :href="route('pages.show', subPoint.slug)"
            :class="['subpoint-item', { active: page.slug && page.slug === subPoint.slug }]"
        >
            <div class="subpoint-title">{{ subPoint.title }}</div>
        </Link>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
const props = defineProps({
  subPoints: Array,
  page: Object
})
</script>

<style>
.subpoint-item::before 
{
    content: "";
    background: url('@/images/left.png');
    height: 76px;
    background-position: 3px;
    width: 55px;
    background-size: auto;
    margin-top: -3px;
    display: block;
    background-repeat: no-repeat;
}
.subpoint-item::after 
{
    content: "";
    background: url('@/images/right.png');
    height: 81px;
    background-size: auto;
    background-position: left top;
    background-repeat: no-repeat;
    width: 33px;
}
a.subpoint-item.active, a.subpoint-item.active .subpoint-title:hover 
{
    color: black;
    font-weight: bold;
}
.subpoint-title:hover 
{
    color: gray;
}

.subpoint-title 
{
    display: flex;
    background: url('@/images/center.png');
    height: 69px;
    background-size: auto;
    line-height: 47px;
}
.sidebarSubPoints 
{
    margin: 10px 20px 0;
    padding-bottom: 12px;
}
a.subpoint-item 
{
    display: flex;
    float: left;
    margin-bottom: -54px;
}
</style>