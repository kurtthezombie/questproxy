<script setup>
    import loginService from '@/services/login-service';
    import { useRouter } from 'vue-router';

    const router = useRouter();

    const callLogout = () => {
        console.log('LOGOUT function CALLED from DASHBOARD');
        loginService.logout();

        router.push({ name: 'login' });
    }

</script>
<template>
    <div class="dashboard-body">
        <h2>This is the dashboard!</h2>
        <button @click="callLogout">Logout</button>
    </div>

</template>