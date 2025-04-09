<template>
    <div class="flex flex-col justify-center items-center h-screen">
        <h1>Test Page</h1>
        <div v-if="notif">
            <div class="notif-message">
                <p>{{ notif.message }}</p>
                <p>User is: {{ notif.user_name }} with ID: {{ notif.user_id }}</p>
            </div>
        </div>
    </div>


</template>

<script setup>
import { ref, onMounted } from 'vue';
import useEcho from '@/echo';

// Initialize a ref to store the notification data
const notif = ref(null);

// Assume userId is passed as a prop or available from the store
const userId = 1; // Replace this with actual user ID or get it from your store, this is the user who will be sent with the notification.

onMounted(() => {
    console.log('OnMounted:');
    
    useEcho.channel(`App.Models.User.${userId}`)
    .listen('.notification.broadcast', (notification) => {
        notif.value = {
            message: notification.message,
            user_id: notification.user_id,
            user_name: notification.user_name
        };
        console.log(notification);
    });
});


</script>

<style scoped>
.notif-message {
    padding: 10px;
    background-color: #f4f4f9;
    border-radius: 5px;
    border: 1px solid #ddd;
    margin-top: 10px;
    font-size: 14px;
    color: #333;
}
</style>