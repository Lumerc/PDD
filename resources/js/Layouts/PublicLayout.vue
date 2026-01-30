<template>
    <!-- Основной контент -->
    <main class="public-main">
      <slot />
    </main>


</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
// Явно импортируем объект Ziggy
import { Ziggy } from '../ziggy'
</script>

<style scoped>
.public-main
{
    background: #3b3b3b;
}
</style>