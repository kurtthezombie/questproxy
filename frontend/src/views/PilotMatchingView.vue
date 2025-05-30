<script setup>
import toast from '@/utils/toast';
import { onMounted, ref, computed } from 'vue';
import NavBar from '@/components/NavBar.vue';
import { fetchCategories, findMatchingPilot } from '@/services/match.service';

const loading = ref(false);
const loadingSearch = ref(false);
const categories = ref([]);
const formData = ref({
  game: '',
  service: '',
  points: null,
});

const show = ref(false);
const foundPilot = ref(null);

const openModal = () => {
  show.value = true;
};

const closeModal = () => {
  show.value = false;
  foundPilot.value = null;
};


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

const handleSearchPilot = async () => {
  loadingSearch.value = true;

  try {
    setTimeout(async () => {
      const data = await findMatchingPilot(formData.value);
      let message = '';

      if (data.pilot && data.pilot_details) {
        foundPilot.value = data.pilot_details;

        openModal();
        message = "Successfully found a pilot!";
      } else {
        message = "No pilot found for the selected criteria.";
      }

      clearFields();

      toast.success(message);
      loadingSearch.value = false;
    },3000);
  } catch (error) {
    console.error('Search failed: ', error);
    toast.error('Search failed');
    loadingSearch.value = false;
  }
};

const clearFields = () => {
  formData.value = {
      game: '',
      service: '',
      points: null,
    };
}

const pilotInitial = computed(() => {
  return foundPilot.value && foundPilot.value.user && foundPilot.value.user.f_name
    ? foundPilot.value.user.f_name.trim().charAt(0).toUpperCase()
    : '';
});


onMounted(() => {
  loading.value = true;
  getCategories();
});
</script>

<template>
  <NavBar />
  <div class="min-h-screen bg-gray-900 flex flex-col justify-center items-center p-6">
    <div class="card text-white bg-gray-500 bg-opacity-20 w-full max-w-md p-5 rounded-xl shadow-md">
      <div class="card-body">
        <div class="card-title flex justify-center">
          <h2 class="font-semibold text-center text-green-400 text-2xl">Find A Pilot</h2>
        </div>
        <form @submit.prevent="handleSearchPilot">
          <div class="flex flex-col space-y-4 mt-4">
            <div class="space-y-1">
              <label for="game" class="text-green-400">Game</label>
              <select name="game" id="game" class="select w-full bg-gray-900" v-model="formData.game" required>
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
              <label for="points" class="text-green-400">Preferred Pilot Points</label>
              <input type="number" id="points" class="input bg-gray-900 w-full" placeholder="e.g. 50"
                v-model="formData.points" required>
            </div>

            <div>
              <button type="submit"
                class="w-full bg-green-600 hover:bg-green-500 transition rounded-lg py-2 font-semibold"
                :disabled="loading">
                <span v-if="loading" class="loading loading-spinner"></span>
                <span v-if="!loading">Search</span>
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div v-if="loadingSearch" class="fixed inset-0 flex justify-center items-center bg-black bg-opacity-50 z-50">
    <div class="modal modal-open sm-modal-middle">
      <div class="modal-box bg-gray-800">
        <div class="px-6 py-4">
          <div class="flex flex-row justify-between items-center">

            <div class="flex flex-col space-y-2">
              <h2 class="text-2xl font-semibold text-green-400">Searching for Pilots...</h2>
              <h3 class="text-white">Please wait while we find a pilot for you.</h3>
            </div>

            <div class="flex justify-center items-center ml-4">
              <span class="loading loading-ring text-white" style="width: 5rem; height: 5rem;"></span>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
  <div v-if="show" class="fixed inset-0 flex justify-center items-center bg-black bg-opacity-60 z-50">
    <div class="modal modal-open sm-modal-middle">
      <div class="modal-box bg-[#1E293B] text-white border border-gray-700">
        <div class="px-6 py-4 border-b border-gray-700">
          <h2 class="text-2xl font-semibold text-green-400">Pilot Found!</h2>
        </div>
        <div class="px-6 py-4">
          <div class="flex justify-center mb-4">
            <div class="w-24 h-24 rounded-full bg-green-500 flex items-center justify-center text-white font-semibold text-5xl leading-[1]">
              {{ pilotInitial }}
            </div>
          </div>

          <div class="space-y-4">
            <div class="bg-emerald-950 text-emerald-400 px-3 py-1 rounded-full flex items-center border border-emerald-800 w-fit mx-auto">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="yellow" class="mr-1"><path d="M12 2L15 8L22 8.5L17 12.5L18 19.5L12 16.5L6 19.5L7 12.5L2 8.5L9 8L12 2Z"/></svg>
                <span class="font-semibold">Points:</span><span class="font-bold ml-1">{{ foundPilot?.rank?.points }}</span>
            </div>

            <div class="mb-4">
              <p class="text-lg font-medium text-gray-400">Username:</p>
              <p class="text-xl text-white">{{ foundPilot?.user?.username }}</p>
            </div>

            <div class="mb-4">
              <p class="text-lg font-medium text-gray-400">Skills:</p>
              <p class="text-base text-gray-300">{{ foundPilot?.skills }}</p>
            </div>

            <div class="mb-4">
              <p class="text-lg font-medium text-gray-400">Bio:</p>
              <p class="text-sm text-gray-300">{{ foundPilot?.bio }}</p>
            </div>
          </div>
        </div>
        <div class="modal-action px-6 py-4 border-t border-gray-700">
          <a :href="`/users/${foundPilot.user_id}`" class="btn bg-green-600 hover:bg-green-700 text-white border-none">View Profile</a>
          <button class="btn bg-gray-600 hover:bg-gray-700 text-white border-none" @click="closeModal">Close</button>
        </div>
      </div>
    </div>
  </div>
</template>
