<template>
    <div v-if="notif">
        <div class="notif-message">
            <p>{{ notif.message }}</p>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import echo from '@/echo';

const notif = ref(null); // Stores the notification message

// Assume userId is passed as a prop or available from the store
const userId = 1; // Replace this with actual user ID or get it from your store

onMounted(() => {
    echo.private(`App.Models.User.${userId}`)
        .listen('PilotMatchedNotification', (event) => {
            console.log('Received notification:', event);
            //Store the notification message to display it in the UI
            notif.value = {
                message: event.message, // You can customize this based on the event data
            };
        });
});
</script>