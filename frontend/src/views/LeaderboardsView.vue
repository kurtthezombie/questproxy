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
    <h2 style="color: white;">Leaderboards</h2>
    <ol>
        list apears here dapat:
        <li v-for="record in records">
            {{ record.pilot_username }} - {{ record.points }}
        </li>
    </ol>
</template>