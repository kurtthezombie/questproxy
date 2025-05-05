<script setup>
import toast from '@/utils/toast';
import { onMounted, ref } from 'vue'; 
import NavBar from '@/components/NavBar.vue';
import { fetchCategories } from '@/services/match.service';
import { createPreference, getPreference, updatePreference } from '@/services/preference.service';
import { useUserStore } from '@/stores/userStore';
import Slider from '@vueform/slider';
import '@vueform/slider/themes/default.css';

const userStore = useUserStore();
const loading = ref(false);
const categories = ref([]);
const hasExistingPreference = ref(false);
const formData = ref({
  user_id: userStore.userData?.id,
  game_id: '',
  service: '',
  points: null,
  duration: '',
  max_price: 10000, 
});

const getCategories = async () => {
  try {
    const data = await fetchCategories();
    categories.value = data.categories;
  } catch (error) {
    console.error('Error fetching categories:', error);
    toast.error('Failed to fetch categories');
  }
};

const fetchExistingPreference = async () => {
  try {
    const response = await getPreference(userStore.userData?.id);
    console.log('Preference API Response:', response);
    
    if (response.preference) {
      console.log('Found existing preferences:', response.preference);
      hasExistingPreference.value = true;
      formData.value = {
        user_id: userStore.userData?.id,
        game_id: response.preference.game_id,
        service: response.preference.service,
        points: response.preference.points,
        duration: response.preference.duration,
        max_price: response.preference.max_price,
      };
      console.log('Updated form data:', formData.value);
    } else {
      console.log('No existing preferences found');
    }
  } catch (error) {
    console.error('Error fetching preferences:', error);
    // Don't show error toast as it's expected for new users
  }
};

const handleSavePreferences = async () => {
  if (!userStore.userData?.id) {
    toast.error('User not logged in');
    return;
  }

  loading.value = true;
  try {
    console.log('Current preference state:', hasExistingPreference.value ? 'Update' : 'Create');
    
    if (hasExistingPreference.value) {
      console.log('Updating existing preferences...');
      await updatePreference(formData.value);
      toast.success('Preferences updated successfully!');
    } else {
      console.log('Creating new preferences...');
      await createPreference(formData.value);
      hasExistingPreference.value = true; // Set to true after successful creation
      toast.success('Preferences saved successfully!');
    }
  } catch (error) {
    console.error('Failed to save preferences:', error);
    toast.error('Failed to save preferences');
  } finally {
    loading.value = false;
  }
};

onMounted(async () => {
  if (!userStore.userData?.id) {
    toast.error('Please login to set preferences');
    return;
  }
  
  loading.value = true;
  try {
    await Promise.all([getCategories(), fetchExistingPreference()]);
  } finally {
    loading.value = false;
  }
});
</script>

<template>
  <NavBar />
  <div class="min-h-screen bg-gray-900 flex flex-col justify-center items-center p-6">
    <div class="card text-white bg-gray-500 bg-opacity-20 w-full max-w-md p-5 rounded-xl shadow-md">
      <div class="card-body">
        <div class="card-title flex justify-center">
          <h2 class="font-semibold text-center text-green-400 text-2xl">
            {{ hasExistingPreference ? 'Update Your Preferences' : 'Set Your Preferences' }}
          </h2>
        </div>
        <form @submit.prevent="handleSavePreferences">
          <div class="flex flex-col space-y-4 mt-4">
            <div class="space-y-1">
              <label for="game_id" class="text-green-400">Game</label>
              <select name="game_id" id="game_id" class="select w-full bg-gray-900" v-model="formData.game_id" required>
                <option value="" disabled>Select a game</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">
                  {{ category.title }}
                </option>
              </select>
            </div>

            <div class="space-y-1">
              <label for="service" class="text-green-400">Type of Service</label>
              <select name="service" id="service" class="select w-full bg-gray-900" v-model="formData.service" required>
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
                v-model="formData.duration">
            </div>

            <div class="space-y-1">
              <label class="text-green-400">Maximum Charge</label>
              <Slider
                v-model="formData.max_price" 
                :min="100"       
                :max="10000"   
                :step="100"   
              />
              <div class="flex justify-between text-gray-400 text-sm mt-1">
                <span>Min: 100</span>
                <span>Max: {{ formData.max_price }}</span>
              </div>
            </div>

            <div class="space-y-1">
              <label for="points" class="text-green-400">Preferred Pilot Points</label>
              <input type="number" id="points" class="input bg-gray-900 w-full" placeholder="e.g. 50"
                v-model="formData.points" required>
            </div>

            <div>
              <button type="submit"
                class="w-full bg-green-600 hover:bg-green-500 transition rounded-lg py-2 font-semibold"
                :disabled="loading">
                <span v-if="loading" class="loading loading-spinner"></span>
                <span v-if="!loading">{{ hasExistingPreference ? 'Update Preferences' : 'Save Preferences' }}</span>
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>