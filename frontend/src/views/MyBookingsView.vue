<script setup>
import MyBookingCard from '@/components/booking/MyBookingCard.vue';
import NavBar from '@/components/NavBar.vue';
import router from '@/router';
import { fetchMyBookings } from '@/services/booking.service';
import dayjs from 'dayjs';
import { computed, onMounted, ref } from 'vue';
import toast from '@/utils/toast';

const bookings = ref([]);
const filter = ref('all');

const filteredBookings = computed(() => {
  if (filter.value === 'all') return bookings.value;
  return bookings.value.filter(b => b.booking && b.booking.status === filter.value);
});

const handleFetchMyBookings = async () => {
  try {
    bookings.value = await fetchMyBookings();
    console.log('Bookings in My Bookings: ', bookings.value);
  } catch (error) {
    console.error('Error fetching bookings: ', error);
    toast.error('Failed to fetch bookings.');
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
  <div class="min-h-screen bg-gray-900 text-white">
    <NavBar />
    <div class="container mx-auto py-5 max-w-7xl space-y-5">
      <div class="flex justify-between items-center px-4 mt-10 mb-4">
        <div>
          <h1 class="text-5xl font-bold text-white">My Bookings</h1>
          <p class="text-gray-300 text-lg mt-3">Manage your gaming bookings</p>
        </div>
      </div>
      <div class="flex gap-4 mt-4 ml-4">
        <button @click="filter = 'all'"
          :class="{ 'font-bold shadow-none': filter === 'all', 'btn': true, 'btn-primary': filter !== 'all' }">All</button>
        <button @click="filter = 'in_progress'"
          :class="{ 'font-bold': filter === 'in_progress', 'btn bg-yellow-500 bg-opacity-30 border border-none shadow-none': true, 'text-yellow-300': true }">In
          Progress</button>
        <button @click="filter = 'completed'"
          :class="{ 'font-bold shadow-none': filter === 'completed', 'btn bg-green-500 bg-opacity-30 border border-none shadow-none text-green-300': true }">Completed</button>
      </div>
      <div class="w-fit sm:w-full flex flex-col items-start">
        <div v-if="filteredBookings.length === 0" class="w-full flex justify-center">
          <p class="text-center text-xl cursor-pointer hover:scale-105 duration-300 mt-20"
            @click="redirectToHome">
            <span v-if="filter === 'all'">You have not booked a service yet. Maybe try booking <span
                class="text-green-400">here</span>.</span>
            <span v-else>No {{ filter.replace('_', ' ') }} bookings found.</span>
          </p>
        </div>
        <div v-else
          class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 overflow-y-auto max-h-[calc(100vh-200px)] px-4">
          <MyBookingCard v-for="booking in filteredBookings" :key="booking.booking.id"
            :serviceTitle="booking.serviceTitle" :gameName="booking.gameTitle" :pilotName="booking.pilotName"
            :status="booking.booking.status" :bookedOn="dayjs(booking.created_at).format('MMMM D,YYYY')"
            :progress="booking.booking.progress" :serviceId="booking.booking.service_id"
            :bookingId="booking.booking.id" />
        </div>
      </div>
    </div>
  </div>
</template>
