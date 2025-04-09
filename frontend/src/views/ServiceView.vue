<template>
  <div class="min-h-screen bg-gray-900 text-white">
    <NavBar
      :username="username"
      :email="email"
      :role="role"
      :callLogout="callLogout"
    />
    
    <!-- Loading State -->
    <div v-if="serviceStore.loading" 
         class="flex justify-center items-center mt-10">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-500"></div>
    </div>

    <!-- Error State -->
    <div v-else-if="serviceStore.error" 
         class="text-red-500 text-center mt-10">
      {{ serviceStore.error }}
    </div>

    <!-- Content -->
    <template v-else>
      <h1 class="text-2xl font-bold text-green-500 text-center mt-10 mb-5">
        {{ getDisplayTitle }} Services
      </h1>

      <!-- No Services Message -->
      <div v-if="!filteredServices.length" 
           class="text-center mt-10 text-gray-400">
        No services available for this category.
      </div>

      <div v-else class="container mx-auto py-10">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          <ServiceDisplay
            v-for="service in filteredServices"
            :key="service.id"
            :service="service"
            :categories="serviceStore.categories"
          />
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
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

const categoryGame = ref(router.currentRoute.value.params.title || 'all');

const getDisplayTitle = computed(() => {
  if (!categoryGame.value || categoryGame.value.toLowerCase() === 'all') {
    return 'All';
  }
  const category = serviceStore.categories?.find(
    cat => cat.game?.toLowerCase() === categoryGame.value.toLowerCase()
  );
  return category ? category.title : categoryGame.value;
});


const filteredServices = computed(() => {
  if (!categoryGame.value || categoryGame.value.toLowerCase() === 'all') {
    return serviceStore.services;
  }
  return serviceStore.services.filter(service => 
    service && service.game && service.game.toLowerCase() === categoryGame.value.toLowerCase()
  );
});


const checkAuth = () => {
  if (!localStorage.getItem('authToken')) {
    router.push({ name: 'login' });
  }
};

const fetchServices = async () => {
  try {
    await serviceStore.fetchUserServices();
    console.log('Fetched services:', serviceStore.services); 
  } catch (error) {
    console.error('Error fetching services:', error);
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
  checkAuth();
  await serviceStore.fetchCategories();
  await fetchServices();
});
</script>