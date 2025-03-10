<template>
    <div class="min-h-screen bg-gray-900 text-white">
      <header class="bg-gray-800 p-4 flex justify-between items-center shadow-lg border-b-4 border-green-500">
        <h1 class="text-3xl font-bold">{{ categoryTitle }} Services</h1>
        <UserDropdown 
          :username="username" 
          :email="email" 
          :role="role" 
          :callLogout="callLogout"
        />
      </header>
      
      <div class="container mx-auto py-10">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
          <div 
            v-for="service in services" 
            :key="service.id"
            class="bg-gray-800 p-4 rounded-lg shadow-lg hover:bg-green-500 transition-colors duration-300"
          >
            <h3 class="text-xl font-semibold">{{ service.title }}</h3>
            <p>{{ service.description }}</p> 
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  import { useRouter } from 'vue-router';
  import loginService from '@/services/login-service';
  
  const router = useRouter();
  const services = ref([]);
  const categoryTitle = ref('');
  const username = ref('');
  const role = ref('');
  
  const fetchServices = async () => {
    const title = router.currentRoute.value.params.title;
    categoryTitle.value = title;
    try {
      const response = await axios.get(`http://127.0.0.1:8000/api/services/${title}`);
      services.value = response.data.services; 
    } catch (error) {
      console.error('Error fetching services:', error);
    }
  };
  
  const fetchUserData = async () => {
    try {
      const userData = await loginService.fetchUserData(); 
      username.value = userData.username;
      role.value = userData.role || 'User';  
    } catch (error) {
      console.error('Error fetching user data:', error);
      router.push({ name: 'login' });
    }
  };
  
  onMounted(() => {
    fetchUserData();
    fetchServices();
  });
  
  const callLogout = () => {
    loginService.logout();
    router.push({ name: 'login' });
  };
  </script>
  