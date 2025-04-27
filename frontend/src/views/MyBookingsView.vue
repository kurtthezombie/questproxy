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
  <div class="min-h-screen bg-gray-900 flex items-center justify-start text-white flex-col">
    <div class="fixed top-0 left-0 w-full bg-gray-900 z-10 py-4 flex flex-col items-center" style="margin-top: 80px;">
      <h2 class="mb-4 text-3xl font-bold text-white">My Bookings</h2>
      <div class="flex gap-4">
        <button @click="filter = 'all'" :class="{ 'font-bold': filter === 'all', 'btn': true }">All</button>
        <button @click="filter = 'in_progress'" :class="{ 'font-bold': filter === 'in_progress', 'btn btn-warning': true }">In Progress</button>
        <button @click="filter === 'completed'" :class="{ 'font-bold': filter === 'completed', 'btn btn-success': true }">Completed</button>
      </div>
    </div>

    <div class="pt-40 w-full flex flex-col items-center">
      <p v-if="filteredBookings.length === 0" class="text-center text-xl cursor-pointer hover:scale-105 duration-300" @click="redirectToHome">
        You have not booked a service yet. Maybe try booking <span class="text-green-400">here</span>.
      </p>
      <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 overflow-y-auto max-h-[calc(100vh-200px)] px-4">
        <MyBookingCard v-for="booking in filteredBookings" :key="booking.booking.id" :serviceTitle="booking.serviceTitle"
          :gameName="booking.gameTitle" :pilotName="booking.pilotName" :status="booking.booking.status"
          :bookedOn="dayjs(booking.created_at).format('MMMM D,YYYY')"
          :progress="booking.booking.progress"
          />
      </div>
    </div>
  </div>
</template>