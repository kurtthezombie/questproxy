<template>
  <div class="min-h-screen bg-gray-900">
    <NavBar :username="username" :email="email" :role="role" :callLogout="callLogout" />
    <div>
      <UserProfile />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import UserProfile from '@/components/UserProfile.vue';
import UserDropdown from '@/components/UserDropdown.vue';
import loginService from '@/services/login-service';
import NavBar from '@/components/NavBar.vue';
import { useLoader } from '@/services/loader-service';

const { loadShow, loadHide } = useLoader();

const username = ref('');
const email = ref('');
const role = ref('');

const callLogout = () => {
  localStorage.removeItem('token');
};

const fetchUserData = async () => {
  const loader = loadShow();
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