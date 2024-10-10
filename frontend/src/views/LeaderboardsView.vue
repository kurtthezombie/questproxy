<script setup>
import { ref, onMounted } from 'vue';
import { leaderboards } from '@/services/rank-service';

const records = ref([]);

onMounted(async () => {
    const response = await leaderboards();
    console.log(response)
    // Check if response is defined and contains rankings
    if (response && response.rankings) {
        records.value = response.rankings;  // Assign the rankings array to records
    } else {
        console.error('Response is undefined or missing rankings:', response);
    }
});
</script>

<template>
    <!-- INSERT NAVBAR HERE ->  -->
    <div class="flex flex-col justify-between">
        <div class="mt-40 flex justify-center">
            <h2 class="text-5xl text-white">Leaderboards</h2>
        </div>
        <div class="mt-10 flex flex-col justify-center items-center" v-if="records">
            <table v-if="records"
                class="w-2/6 shadow-2xl font-[Poppins] border-2 border-red-100 rounded-lg bg-purple-300/5 overflow-hidden">
                <tbody v-for="(record, index) in records" :key="index">
                    <tr
                        class="text-2xl text-white hover:scale-105 hover:bg-purple-200 hover:scale-105 hover:rounded-lg hover:text-black text-center cursor-pointer duration-300">
                        <td class="py-3 px-10">{{ index + 1 }}</td>
                        <td class="py-3 px-10">{{ record.pilot_username }}</td>
                        <td class="py-3 px-10">{{ record.points }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>


</template>