<template>
  <NavBar/>
  <div class="min-h-screen bg-gray-900">
      <div class="flex items-center">
      </div>
    <div>
      <UserProfile />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import UserProfile from '@/components/UserProfile.vue';
import loginService from '@/services/login-service';
import { useLoader } from '@/services/loader-service';
import NavBar from '@/components/NavBar.vue';

const { loadShow, loadHide } = useLoader();

const username = ref('');
const email = ref('');
const role = ref('');

const callLogout = () => {
  userStore.clearUser();
  serviceStore.clearServices();
  localStorage.removeItem('authToken');
  localStorage.removeItem('tokenType');
  router.push({ name: 'login' });
};

const fetchUserData = async () => {
  try {
    const userData = await loginService.fetchUserData();
    username.value = userData.username;
    email.value = userData.email;
    role.value = userData.role;
  } catch (error) {
    console.error('Error fetching user data:', error);
  } finally {
    loadHide(loader);
  }
};

onMounted(() => {
  fetchUserData();
});
</script>