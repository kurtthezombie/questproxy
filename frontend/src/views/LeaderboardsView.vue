<script setup>
import { ref, onMounted } from 'vue';
import { leaderboards } from '@/services/rank-service';
import NavBar from '@/components/NavBar.vue';

const records = ref([]);

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
</script>

<template>
    <NavBar :username="username" :email="email" :role="role" :callLogout="callLogout" />
    <div class="min-h-screen bg-gray-900 flex flex-col font-poppins">
        <div class="mt-20 flex justify-center">
            <h2 class="text-5xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-green-400 to-blue-500 font-poppins">
                LEADERBOARD
            </h2>
        </div>
        <div class="mt-10 flex flex-col justify-center items-center space-y-4 font-poppins">
            <!-- Container for rows -->
            <div v-if="records.length > 0" class="w-2/6 font-poppins">
                <!-- Table Header with smaller text -->
                <div class="flex bg-gray-800 text-gray-100 text-sm py-3 px-15 mb-7 text-center font-poppins">
                    <div class="flex-1">Rank</div>
                    <div class="flex-1">Game Pilot</div>
                    <div class="flex-1">Points</div>
                </div>
                <!-- Data Rows -->
<div v-for="(record, index) in records" :key="index" 
    class="flex bg-gray-800 text-gray-100 text-center cursor-pointer 
        duration-300 shadow-xl hover:shadow-2xl p-1 mb-4 
        hover:bg-gradient-to-r from-green-400 to-blue-500 hover:text-gray-900 
        transform scale-100 hover:scale-105 transition-all ease-in-out">
    <div class="flex-1 py-3 px-10">{{ String(index + 1).padStart(2, '0') }}</div>
    <div class="flex-1 py-3 px-10">{{ record.pilot_username }}</div>
    <div class="flex-1 py-3 px-10">{{ record.points }}</div>
</div>


            </div>
            <p v-else class="text-gray-50 font-poppins">No rankings available.</p>
        </div>
    </div>
</template>


