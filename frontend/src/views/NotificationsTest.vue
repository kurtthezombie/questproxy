<template>
    <p>display notifs here</p>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import echo from '@/echo';

const notif = ref(null);

onMounted (() => {
    echo.channel('user.{id}')
        .listen('.message.sent', (event) => {
            console.log("Event received: ",event);
          notif.value = notif.message;
    });
});

</script>