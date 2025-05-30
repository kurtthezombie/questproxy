<template>
  <div class="min-h-screen bg-gray-900 flex flex-col text-white">
    <NavBar :username="username" :email="email" :role="role" :callLogout="callLogout" />

    <div class="mt-20 flex justify-center items-center">
      <h2 class="text-5xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-green-500 to-blue-400 leading-normal">
        Edit Gaming Service
      </h2>
    </div>
    <div class="mt-2 flex justify-center items-center">
      <h5 class="text-lg text-gray-300 text-center px-4">
        Update your service details to keep your offerings current
      </h5>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-6xl mx-auto mt-10 px-4">
      <form @submit.prevent="submitUpdate" class="bg-blue-800 bg-opacity-5 rounded-lg p-6 space-y-6 shadow-lg border border-gray-700 md:col-span-2 lg:col-span-2">
        <div class="space-y-1">
          <h2 class="text-2xl font-bold">Update Your Gaming Service</h2>
          <p class="text-sm text-gray-400">Edit your service details to provide the best experience</p>
        </div>

        <div v-if="serviceStore.message"
             class="flex items-center p-4 rounded-md bg-emerald-900 border border-emerald-700 text-emerald-300">
          <svg class="w-5 h-5 mr-2 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <span>{{ serviceStore.message }}</span>
        </div>

        <div v-if="serviceStore.error"
             class="flex items-center p-4 rounded-md bg-red-900 border border-red-700 text-red-300">
          <svg class="w-5 h-5 mr-2 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <span>{{ serviceStore.error }}</span>
        </div>

        <div v-if="serviceStore.loading"
             class="flex items-center justify-center p-4">
          <svg class="animate-spin h-5 w-5 mr-2 text-emerald-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span class="text-emerald-400">Processing your request...</span>
        </div>

        <div>
          <label class="block text-sm mb-1">Game</label>
          <select v-model="formData.game" class="w-full rounded-md bg-gray-700 text-white border border-gray-600 p-2">
            <option value="" disabled>Select a game</option>
            <option v-for="category in serviceStore.categories" :key="category.id" :value="category.game">
              {{ category.title }}
            </option>
          </select>
        </div>

        <div>
          <div class="flex items-center space-x-2 mb-1">
            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 4H7a2 2 0 01-2-2V6a2 2 0 012-2h7l5 5v11a2 2 0 01-2 2z" />
            </svg>
            <label class="text-sm">Description</label>
          </div>
          <textarea v-model="formData.description" class="w-full rounded-md bg-gray-700 text-white border border-gray-600 p-2" rows="3" placeholder="Describe your service..."></textarea>
        </div>

        <div>
          <div class="flex items-center space-x-2 mb-1">
            <span class="text-green-400 font-semibold">₱</span>
            <label class="text-sm">Price</label>
          </div>
          <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 text-sm pointer-events-none">₱</span>
            <input
              v-model="formData.price"
              type="number"
              class="w-full pl-8 rounded-md text-white border border-gray-700 bg-[#1e293b] p-2"
              placeholder="0.00"
            />
          </div>
          <p class="text-xs text-gray-400">Update your service price (in platform currency)</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <div class="flex items-center space-x-2 mb-1">
              <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m5-2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <label class="text-sm">Duration</label>
            </div>
            <input v-model="formData.duration" type="number" min="1" max="90" placeholder="Enter the number of days"
            class="w-full rounded-md bg-[#1e293b]  text-white border border-gray-600 p-2" />
          </div>
          <div>
            <div class="flex items-center space-x-2 mb-1">
              <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <label class="text-sm">Availability</label>
            </div>
            <select v-model="formData.availability" class="w-full rounded-md bg-gray-700 text-white border border-gray-600 p-2">
              <option :value="1">Available</option>
              <option :value="0">Not Available</option>
            </select>
          </div>
        </div>

        <div class="flex gap-4 mt-6">
          <button
            type="submit"
            class="w-full bg-emerald-500 hover:bg-emerald-600 text-black py-2 rounded-md transition-colors duration-200"
            :disabled="serviceStore.loading"
          >
            {{ serviceStore.loading ? 'Updating...' : 'Update Service' }}
          </button>
          <button
            type="button"
            class="w-full bg-gray-700 text-white hover:bg-gray-600 border border-gray-600 py-2 rounded-md transition-colors duration-200"
            @click="router.back()"
          >
            Cancel
          </button>
        </div>
      </form>

      <div class="space-y-6">
        <div class="bg-blue-800 bg-opacity-5 rounded-lg p-6 shadow-lg border border-gray-700">
          <h4 class="text-2xl font-bold mb-4">Editing Tips</h4>
          <ul class="text-sm space-y-3">
            <li class="flex items-start space-x-2">
              <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-900 text-lg text-green-400 font-semibold mr-2">1</span>
              <div>
                <span class="text-lg font-semibold">Be specific</span>
                <div class="text-sm text-gray-500">Clear descriptions attract more clients</div>
              </div>
            </li>
            <li class="text-lg flex items-start space-x-2">
              <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-900 text-lg text-green-400 font-semibold mr-2">2</span>
              <div>
                <span class="font-semibold">Fair pricing</span>
                <div class="text-sm text-gray-500">Competitive rates improve visibility</div>
              </div>
            </li>
            <li class="text-lg flex items-start space-x-2">
              <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-900 text-lg text-green-400 font-semibold mr-2">3</span>
              <div>
                <span class="font-semibold">Update availability</span>
                <div class="text-sm text-gray-500">Keep your schedule current</div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

  <script setup>
  import { ref, onMounted, reactive } from 'vue';
  import { useRouter, useRoute } from 'vue-router';
  import { useServiceStore } from '@/stores/serviceStore';
  import { useUserStore } from '@/stores/userStore';
  import NavBar from '@/components/NavBar.vue';
  import toast from '@/utils/toast';

  const router = useRouter();
  const route = useRoute();
  const serviceStore = useServiceStore();
  const userStore = useUserStore();

  const serviceId = route.params.id;

  const username = ref(userStore.userData?.username || '');
  const email = ref(userStore.userData?.email || '');
  const role = ref(userStore.userData?.role || '');

  const formData = reactive({
    game: '',
    description: '',
    price: null,
    duration: '',
    availability: 1
  });

  const loadService = async () => {
    const service = serviceStore.services.find(s => s.id === parseInt(serviceId));
    if (service) {
      Object.assign(formData, {
        game: service.game,
        description: service.description,
        price: service.price,
        duration: service.duration,
        availability: service.availability
      });
    } else {
      await serviceStore.fetchUserServices();
      const fetchedService = serviceStore.services.find(s => s.id === parseInt(serviceId));
      if (fetchedService) {
        Object.assign(formData, {
          game: fetchedService.game,
          description: fetchedService.description,
          price: fetchedService.price,
          duration: fetchedService.duration,
          availability: fetchedService.availability
        });
      } else {
        router.push('/services');
      }
    }
  };


  const submitUpdate = async () => {
    try {
      await serviceStore.updateService(serviceId, formData);
      toast.success('Service updated successfully!');
      router.push('/services-history');
    } catch (error) {
      console.error('Error updating service:', error);
       if (error.message) {
         toast.error(`Failed to update service: ${error.message}`);
       } else {
         toast.error('Failed to update service.');
       }
    }
  };

  onMounted(async () => {
    if (!localStorage.getItem('authToken')) {
      router.push({ name: 'login' });
      return;
    }
    await serviceStore.fetchCategories();
    await loadService();
  });

  const callLogout = () => {
  userStore.clearUser();
  serviceStore.clearServices();
  localStorage.removeItem('authToken');
  localStorage.removeItem('tokenType');
  router.push({ name: 'login' });
};
  </script>
