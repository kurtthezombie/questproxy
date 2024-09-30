<template>
  <div class="gamer-view">
    <div class="gamer-header">
      <img class="gamer-icon" src="@/assets/img/qplogo2.png" alt="Gamer Icon" width="80" height="80" />
      <h1>Welcome, Gamer!</h1>
    </div>

    <p>This is the view for gamers. Here, you can manage your profile, view your game library, and more.</p>

    <DashboardView />

    <button class="logout-button" @click="callLogout">Logout</button>
  </div>
</template>

<script setup>
import Dashboard from '@/components/Dashboard.vue';
import loginService from '@/services/login-service';
import { useRouter } from 'vue-router';

const router = useRouter();

const callLogout = () => {
  console.log('LOGOUT function CALLED from GamerView');
  loginService.logout();

  router.push({ name: 'login' });
};
</script>

<style scoped>
/* Main Container */
.gamer-view {
  max-width: 600px;
  margin: 50px auto;
  padding: 40px;
  background-color: rgba(30, 40, 50, 0.9);
  border-radius: 10px;
  color: #fff;
  text-align: center;
  box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
}

/* Header Styling */
.gamer-header {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 20px;
}

.gamer-icon {
  margin-right: 15px;
}

h1 {
  font-size: 26px;
  text-transform: uppercase;
  font-family: 'Arial', sans-serif;
  letter-spacing: 1.2px;
  margin: 0;
}

/* Paragraph Styling */
p {
  font-size: 18px;
  margin: 20px 0;
  font-family: 'Verdana', sans-serif;
}

/* Logout Button Styling */
.logout-button {
  padding: 12px 25px;
  background-color: #FF4500;
  color: white;
  border: none;
  border-radius: 5px;
  font-size: 18px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.logout-button:hover {
  background-color: #FF6347;
}

/* Dashboard Placeholder */
.dashboard-body {
  margin-top: 30px;
  padding: 20px;
  background-color: rgba(0, 50, 70, 0.8);
  border-radius: 8px;
  color: white;
  font-size: 16px;
}
</style>