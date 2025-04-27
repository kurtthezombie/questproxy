<script setup>
import MyBookingCard from '@/components/booking/MyBookingCard.vue';
import NavBar from '@/components/NavBar.vue';
import router from '@/router';
import { fetchMyBookings } from '@/services/booking.service';
import dayjs from 'dayjs';
import { computed, onMounted, ref } from 'vue';

const bookings = ref([]);
const filter = ref('all');

const filteredBookings = computed(() => {
  if (filter.value === 'all') return bookings.value;
  return bookings.value.filter(b => b.booking.status === filter.value);
});

const handleFetchMyBookings = async () => {
  try {
    bookings.value = await fetchMyBookings();
    console.log('Bookings in My Bookings: ', bookings.value);
  } catch (error) {
    console.error('Error fetching bookings: ', error);
    toast.error(error);
  }
}

const redirectToHome = () => {
  router.push('/home');
}

onMounted(() => {
  handleFetchMyBookings();
  console.log('Bookings in My Bookings: ', bookings.value);
})
</script>

<template>
  <NavBar />
  <div class="min-h-screen bg-gray-900 flex items-center justify-center text-white flex-col">
    <h2 class="mb-6 text-3xl font-bold text-white">My Bookings</h2>
    <div class="flex gap-4 mb-4">
      <button @click="filter = 'all'" :class="{ 'font-bold': filter === 'all', 'btn': true }">All</button>
      <button @click="filter = 'in_progress'" :class="{ 'font-bold': filter === 'in_progress', 'btn btn-warning': true }">In Progress</button>
      <button @click="filter = 'completed'" :class="{ 'font-bold': filter === 'completed', 'btn btn-success': true }">Completed</button>
    </div>
    <p v-if="filteredBookings.length === 0" class="text-center text-xl cursor-pointer hover:scale-105 duration-300" @click="redirectToHome">
      You have not booked a service yet. Maybe try booking <span class="text-green-400">here</span>.
    </p>
    <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 overflow-y-auto max-h-[calc(100vh-200px)]">
      <MyBookingCard v-for="booking in filteredBookings" :key="booking.booking.id" :serviceTitle="booking.serviceTitle"
        :gameName="booking.gameTitle" :pilotName="booking.pilotName" :status="booking.booking.status"
        :bookedOn="dayjs(booking.created_at).format('MMMM D, YYYY')" 
        :progress="booking.booking.progress"
        />
    </div>
  </div>
</template>