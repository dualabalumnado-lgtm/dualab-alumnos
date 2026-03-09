import { defineStore } from 'pinia'
import axios from 'axios'

const apiUrl = 'http://127.0.0.1:8000/api'

export const useOnboardingStore = defineStore('onboarding', {
  state: () => ({
    sections: [],
    loading: false,
    error: null
  }),
  actions: {
    async fetchSections() {
      this.loading = true
      this.error = null
      try {
        const response = await axios.get(`${apiUrl}/sections`)
        this.sections = response.data
      } catch (error) {
        this.error = error.message
      } finally {
        this.loading = false
      }
    },
    async fetchSection(id) {
      this.loading = true
      try {
        const response = await axios.get(`${apiUrl}/sections/${id}`)
        return response.data
      } catch (error) {
        this.error = error.message
        return null
      } finally {
        this.loading = false
      }
    }
  }
})
