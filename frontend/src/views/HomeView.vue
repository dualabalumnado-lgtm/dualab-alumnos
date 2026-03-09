<template>
  <div>
    <div class="text-center mb-12">
      <h2 class="text-4xl font-bold text-gray-900 mb-4">Guía de Bienvenida</h2>
      <p class="text-xl text-gray-600 max-w-2xl mx-auto">
        Descubre cómo funciona la empresa, nuestras políticas y normas.
      </p>
    </div>

    <div v-if="store.loading" class="text-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
    </div>

    <div v-else-if="store.error" class="text-center py-12 text-red-600">
      Error: {{ store.error }}. <button @click="store.fetchSections" class="underline">Reintentar</button>
    </div>

    <div v-else-if="store.sections.length === 0" class="text-center py-12 text-gray-500">
      No hay secciones disponibles aún.
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="section in store.sections" :key="section.id" 
           class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden group">
        <div class="p-8">
          <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-indigo-600 transition-colors">
            {{ section.title }}
          </h3>
          <p v-if="section.description" class="text-gray-600 mb-4 line-clamp-3">
            {{ section.description }}
          </p>
          <router-link :to="{ name: 'section', params: { id: section.id } }"
                      class="inline-flex items-center text-indigo-600 hover:text-indigo-700 font-medium">
            Leer más →
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useOnboardingStore } from '../stores/onboarding.js'

const store = useOnboardingStore()
onMounted(() => store.fetchSections())
</script>
