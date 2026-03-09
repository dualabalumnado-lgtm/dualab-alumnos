<template>
  <div v-if="loading" class="text-center py-12">
    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
  </div>

  <div v-else-if="section" class="space-y-8">
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-2xl p-12 text-center">
      <h1 class="text-4xl font-bold mb-4">{{ section.title }}</h1>
      <p v-if="section.description" class="text-xl opacity-90">{{ section.description }}</p>
    </div>

    <div v-if="section.articles?.length" class="space-y-8">
      <h2 class="text-3xl font-bold text-gray-900">Contenido</h2>
      <div v-for="article in section.articles" :key="article.id" 
           class="bg-white rounded-xl shadow-md p-8">
        <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ article.title }}</h3>
        <div class="prose max-w-none text-gray-700" v-html="article.body"></div>
      </div>
    </div>

    <router-link to="/" 
                 class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700">
      ← Volver al inicio
    </router-link>
  </div>

  <div v-else class="text-center py-12 text-red-600">Sección no encontrada.</div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useOnboardingStore } from '../stores/onboarding.js'

const route = useRoute()
const store = useOnboardingStore()
const section = ref(null)
const loading = ref(true)

const loadSection = async () => {
  loading.value = true
  section.value = await store.fetchSection(route.params.id)
  loading.value = false
}

onMounted(loadSection)
watch(() => route.params.id, loadSection)
</script>
