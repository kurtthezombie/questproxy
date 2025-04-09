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
      <div class="min-h-screen bg-gray-900 flex flex-col">
        <div class="mt-40 flex justify-center">
            <h2 class="text-5xl text-gray-500">Leaderboards</h2>
        </div>
        <div class="mt-10 flex flex-col justify-center items-center">
            <table v-if="records.length > 0"
                class="w-2/6 shadow-2xl font-[Poppins] border-2 border-red-100 rounded-lg bg-purple-300/5 overflow-hidden">
                <tbody>
                    <tr
                        class="text-2xl text-gray-500 hover:scale-105 hover:bg-purple-200 hover:rounded-lg hover:text-black text-center cursor-pointer duration-300"
                        v-for="(record, index) in records" :key="index">
                        <td class="py-3 px-10">{{ index + 1 }}</td>
                        <td class="py-3 px-10">{{ record.pilot_username }}</td>
                        <td class="py-3 px-10">{{ record.points }}</td>
                    </tr>
                </tbody>
            </table>
            <p v-else class="text-gray-50">No rankings available.</p>
        </div>
    </div>
</template>
