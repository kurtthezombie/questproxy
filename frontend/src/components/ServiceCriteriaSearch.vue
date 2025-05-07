<template>
  <div>
    <button class="btn bg-blue-500 hover:bg-blue-600 text-white border-none shadow-none rounded-lg w-full sm:w-fit 
                  text-sm sm:text-lg px-4 py-2 sm:px-8 sm:py-8 btn-lg mt-4"
            @click="showServiceModal = true">
      Find Services 
    </button>
    <dialog v-if="showServiceModal" open class="modal">
      <div class="modal-box bg-gray-800 text-white max-w-5xl w-full">
        <h3 class="text-lg font-bold mb-4">Find Services</h3>
        <form @submit.prevent="handleFindServices">
          <div class="flex flex-col space-y-4 md:flex-row md:space-y-0 md:space-x-6">
            <div class="flex-1 flex flex-col space-y-4">
              <div class="space-y-1">
                <label for="game" class="text-green-400">Game</label>
                <select name="game" id="game" class="select w-full bg-gray-900" v-model="criteria.game" required>
                  <option value="" disabled>Select a game</option>
                  <option v-for="category in categories" :key="category.id" :value="category.id">
                    {{ category.title }}
                  </option>
                </select>
              </div>
              <div class="space-y-1">
                <label for="service" class="text-green-400">Type of Service</label>
                <select name="service" id="service" class="select w-full bg-gray-900" v-model="criteria.service" required>
                  <option disabled value="">Select a service</option>
                  <option value="level">Leveling</option>
                  <option value="grind">Grinding</option>
                  <option value="farm">Farming</option>
                  <option value="quest">Quest Help</option>
                </select>
              </div>
              <div class="space-y-1">
                <label for="duration" class="text-green-400">Maximum Duration (in days)</label>
                <input type="number" id="duration" class="input bg-gray-900 w-full" placeholder="in days"
                  v-model="criteria.duration">
              </div>
              <div class="space-y-1">
                <label class="text-green-400">Charge Range</label>
                <Slider
                  v-model="criteria.max_price"
                  :min="100"
                  :max="10000"
                  :step="100"
                  class="slider-green"
                />
                <div class="flex justify-between text-gray-400 text-sm mt-1">
                  <span>Min: {{ criteria.min_price }}</span>
                  <span>Max: {{ criteria.max_price }}</span>
                </div>
              </div>
              <div class="space-y-1">
                <label for="points" class="text-green-400">Minimum Pilot Points</label>
                <input type="number" id="points" class="input bg-gray-900 w-full" placeholder="e.g. 50"
                  v-model="criteria.points">
              </div>
              <div class="flex justify-end space-x-2 mt-4">
                <button type="button" class="btn btn-error" @click="showServiceModal = false">Close</button>
                <button type="submit" class="btn btn-success" :disabled="loadingSearch">
                  <span v-if="loadingSearch" class="loading loading-spinner"></span>
                  <span v-else>Find Services</span>
                </button>
              </div>
              <button type="button" 
                      class="btn bg-emerald-500 hover:bg-emerald-600 text-black mt-2 w-full" 
                      @click="handleAutoSearch"
                      :disabled="loadingSearch">
                <span v-if="loadingSearch" class="loading loading-spinner"></span>
                <span v-else>Automatic Search (Based on My Preferences)</span>
              </button>
            </div>
          </div>
        </form>
        <div v-if="loadingSearch" class="mt-6 flex justify-center">
          <span class="loading loading-spinner text-green-400"></span>
        </div>
        <div v-else-if="serviceResults.length" class="mt-6">
          <h4 class="text-green-400 font-semibold mb-2">Matching Services:</h4>
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <ServiceDisplay v-for="service in serviceResults" :key="service.id" :service="service" :categories="categories" />
          </div>
        </div>
        <div v-else-if="searched" class="mt-6 text-gray-400">No services found matching your criteria.</div>
      </div>
    </dialog>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import ServiceDisplay from './ServiceDisplay.vue';
import { getPreference } from '@/services/preference.service';
import { useUserStore } from '@/stores/userStore';
import { fetchCategories, findMatchingServices } from '@/services/match.service';
import toast from '@/utils/toast';
import Slider from '@vueform/slider';
import '@vueform/slider/themes/default.css';

const showServiceModal = ref(false);
const loading = ref(false);
const loadingSearch = ref(false);
const criteria = ref({
  game: '',
  service: '',
  points: null,
  duration: '',
  min_price: 100,
  max_price: 10000,
});
const serviceResults = ref([]);
const searched = ref(false);
const categories = ref([]);

const getCategories = async () => {
  try {
    const data = await fetchCategories();
    categories.value = data.categories;
  } catch (error) {
    console.error('Error fetching categories:', error);
    toast.error('Failed to fetch categories');
  } finally {
    loading.value = false;
  }
};

const handleFindServices = async () => {
  loadingSearch.value = true;
  searched.value = false;
  serviceResults.value = [];

  try {
    const dataToSend = {
      game: criteria.value.game,
      service: criteria.value.service,
      points: criteria.value.points || 0,
      duration: criteria.value.duration || null,
      min_price: criteria.value.min_price,
      max_price: criteria.value.max_price,
    };

    const data = await findMatchingServices(dataToSend);
    
    if (data.services && data.services.length > 0) {
      serviceResults.value = data.services;
      toast.success('Services found successfully!');
    } else {
      toast.info('No services found matching your criteria.');
    }
    
    searched.value = true;
  } catch (error) {
    console.error('Search failed:', error);
    if (error.response && error.response.status === 422) {
      toast.error('Validation failed: Please check your input.');
    } else {
      toast.error('Search failed');
    }
  } finally {
    loadingSearch.value = false;
  }
};

const handleAutoSearch = async () => {
  loadingSearch.value = true;
  searched.value = false;
  serviceResults.value = [];

  try {
    const userStore = useUserStore();
    const userId = userStore.userData?.id || userStore.userData?.gamer_id;
    if (!userId) throw new Error('User not found');

    const prefRes = await getPreference(userId);
    const pref = prefRes.preference || prefRes.data?.preference;
    if (!pref) throw new Error('No preferences found');

    const dataToSend = {
      game: pref.game_id,
      service: pref.service,
      points: pref.points || 0,
      duration: pref.duration || null,
      min_price: pref.min_price || 100,
      max_price: pref.max_price || 10000,
    };

    const data = await findMatchingServices(dataToSend);
    
    if (data.services && data.services.length > 0) {
      serviceResults.value = data.services;
      toast.success('Services found based on your preferences!');
    } else {
      toast.info('No services found matching your preferences.');
    }
    
    searched.value = true;
  } catch (error) {
    console.error('Auto search failed:', error);
    toast.error('Failed to search with preferences');
  } finally {
    loadingSearch.value = false;
  }
};

onMounted(() => {
  loading.value = true;
  getCategories();
});
</script>

<style>
.slider-green .slider-connect {
  background: #10b981;
}

.slider-green .slider-handle {
  border-color: #10b981;
}

.slider-green .slider-handle:focus {
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
}
</style>
