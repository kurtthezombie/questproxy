<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { leaderboards } from '@/services/rank-service';
import NavBar from '@/components/NavBar.vue';
import dayjs from 'dayjs';

const records = ref([]);
const router = useRouter();

onMounted(async () => {
    try {
        const response = await leaderboards();
        console.log(response);
        if (response && response.rankings) {
            records.value = response.rankings;  
        } else {
            console.error('Response is undefined or missing rankings:', response);
        }
    } catch (error) {
        console.error('Error occurred while fetching leaderboard data:', error);
    }
});

const isLoggedIn = () => {
  return !!localStorage.getItem('authToken');
};

if (!isLoggedIn()) {
  router.push({ name: 'login' });
}

const getInitial = (username) => {
  if (!username) return '';
  return username.trim().charAt(0).toUpperCase();
};

const getMemberSince = (createdAt) => {
  if (!createdAt) return '';
  const date = dayjs(createdAt);
  return date.format('MMMM YYYY');
};
</script>

<template>
  <NavBar :username="username" :email="email" :role="role" :callLogout="callLogout" />
  <div class="min-h-screen bg-gray-900 flex flex-col">
    <!-- Title -->
    <div class="mt-20 flex justify-center items-center">
      <h2 class="text-6xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-green-500 to-blue-400">
        LEADERBOARD
      </h2>
    </div>
    <!-- Description -->
    <div class="mt-5 flex justify-center items-center">
      <h5 class="text-lg text-gray-300 text-center px-4">
        Top gaming pilots ranked by their Quest Points
      </h5>
    </div>

    <!-- Leaderboard Container -->
    <div class="mt-20 flex flex-col justify-center items-center space-y-4 px-4">
      <div
        v-if="records.length > 0"
        class="w-full min-w-[260px] max-w-full sm:w-[90%] md:w-[850px] rounded-xl shadow-md shadow-green-900 border border-gray-700"
      >
        <!-- Table Header -->
        <div class="flex font-semibold text-gray-300 bg-gray-800 py-3 px-10 text-center rounded-t-xl">
          <div class="w-1/4 text-center">RANK</div>
          <div class="w-3/4 text-left">GAME PILOT</div>
          <div class="w-1/4 text-center">POINTS</div>
        </div>

        <!-- Data Rows -->
        <div
          v-for="(record, index) in records"
          :key="index"
          class="flex items-center text-gray-100 text-sm cursor-pointer 
                 duration-300 hover:shadow-lg p-5 px-10
                 hover:bg-gray-800 hover:text-green-500 
                 border-t border-gray-700 transition-all ease-in-out"
        >
          <!-- Rank -->
          <div class="w-1/4 text-center text-xl font-bold">{{ index + 1 }}</div>

          <!-- Avatar + Username -->
          <div class="w-3/4 flex items-center gap-3">
            <div class="bg-white bg-opacity-20 text-2xl text-green-500 font-bold rounded-full w-10 h-10 flex items-center justify-center">
              {{ getInitial(record.pilot_username) }}
            </div>
            <div class="flex flex-col text-left">
              <span class="font-semibold text-lg truncate">{{ record.pilot_username }}</span>
              <div class="text-gray-400 text-xs leading-tight">Member since {{ getMemberSince(record.created_at) }}</div>
            </div>
          </div>

          <!-- Points -->
          <div class="w-1/4 bg-clip-text text-transparent bg-gradient-to-r from-green-400 to-blue-500 font-bold text-center">
            {{ record.points }}
          </div>
        </div>
      </div>

      <p v-else class="text-gray-50 text-sm">No rankings available.</p>
    </div>
  </div>
</template>
