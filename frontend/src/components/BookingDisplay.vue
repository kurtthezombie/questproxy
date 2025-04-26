<template>
    <div>
      <h2 class="text-2xl font-bold text-white mb-4">My Bookings</h2>
      <div v-if="bookings.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <BookingCard v-for="booking in bookings" :key="booking.id" :booking="booking" />
      </div>
      <div v-else class="text-gray-400">No bookings found.</div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue'
  import axios from 'axios'
  import BookingCard from './BookingCard.vue'
  
  const bookings = ref([])
  
  onMounted(async () => {
    try {
      const token = localStorage.getItem('authToken')
      const response = await axios.get('http://127.0.0.1:8000/api/bookings', {
        headers: {
          Authorization: `Bearer ${token}`
        }
      })
      bookings.value = response.data
    } catch (error) {
      console.error('Failed to fetch bookings:', error)
    }
  })
  </script>
  