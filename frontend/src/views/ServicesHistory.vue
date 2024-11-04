<template>
  <div class="min-h-screen bg-gray-900 text-white">
    <NavBar
      :username="username"
      :email="email"
      :role="role"
      :callLogout="callLogout"
    />

    <!-- Loading State -->
    <div v-if="serviceStore.loading" class="flex justify-center items-center mt-10">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-500"></div>
    </div>

    <!-- Error State -->
    <div v-else-if="serviceStore.error" class="text-red-500 text-center mt-10">
      {{ serviceStore.error }}
    </div>

    <!-- Content -->
    <template v-else>
      <h1 class="text-2xl font-bold text-green-500 text-center mt-10 mb-5">
        My Services History
      </h1>

      <!-- No Services Message -->
      <div v-if="!filteredUserServices.length" class="text-center mt-10 text-gray-400">
        No services available.
      </div>

      <div v-else class="container mx-auto py-10">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          <ServiceDisplay
            v-for="service in filteredUserServices"
            :key="service.id"
            :service="service"
            :categories="serviceStore.categories"
            :isServiceHistory="true"
          />
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useServiceStore } from '@/stores/serviceStore';
import { useUserStore } from '@/stores/userStore';
import NavBar from '@/components/NavBar.vue';
import ServiceDisplay from '@/components/ServiceDisplay.vue';

const router = useRouter();
const serviceStore = useServiceStore();
const userStore = useUserStore();

const username = computed(() => userStore.userData?.username || '');
const email = computed(() => userStore.userData?.email || '');
const role = computed(() => userStore.userData?.role || '');

const filteredUserServices = computed(() => {
  const userId = userStore.userData?.id;
  return userId ? serviceStore.services.filter(service => service.pilot_id === userId) : [];
});


const checkAuth = () => {
  if (!localStorage.getItem('authToken')) {
    router.push({ name: 'login' });
    return false;
  }
  return true;
};

const fetchData = async () => {
  try {
    await serviceStore.fetchServicesByPilot();
    await serviceStore.fetchCategories(); 
    await serviceStore.fetchUserServices(); 
    console.log('Fetched services:', serviceStore.services); 
  } catch (error) {
    console.error('Error fetching data:', error);
  }
};

const callLogout = () => {
  userStore.clearUser();
  serviceStore.clearServices();
  localStorage.removeItem('authToken');
  localStorage.removeItem('tokenType');
  router.push({ name: 'login' });
};

onMounted(async () => {
  if (checkAuth()) {
    await fetchData();
    await fetchServices();
  }
});
</script>
