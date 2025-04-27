<template>
  <div class="max-h-screen text-white py-5 px-4 flex flex-col items-center">


    <div class="w-full bg-gray-800 rounded-xl shadow-md p-6 space-y-6 border border-gray-700">
      <div v-if="role === 'game pilot'" class="space-y-4">
        <div>
          <label class="block text-sm mb-2 text-gray-300">Skills</label>
          <textarea v-model="form.skills" class="w-full p-2.5 text-sm text-white bg-[#1e293b] border border-gray-700 rounded-md focus:ring-2 focus:ring-green-400 focus:outline-none" rows="3" />
        </div>

        <div>
          <label class="block text-sm mb-2 text-gray-300">Bio</label>
          <textarea v-model="form.bio" class="w-full p-2.5 text-sm text-white bg-[#1e293b] border border-gray-700 rounded-md focus:ring-2 focus:ring-green-400 focus:outline-none" rows="5" />
        </div>
      </div>

      <div v-else-if="role === 'gamer'" class="space-y-4">
        <div>
          <label class="block text-sm mb-2 text-gray-300">Gamer Preference</label>
          <input v-model="form.gamer_preference" type="text" class="w-full p-2.5 text-sm text-white bg-[#1e293b] border border-gray-700 rounded-md focus:ring-2 focus:ring-green-400 focus:outline-none" />
        </div>
      </div>

      <div class="flex justify-end">
        <button @click="updateInfo"
          class="bg-green-500 hover:bg-green-600 text-white font-semibold px-6 py-2 rounded-lg transition duration-300"
          :disabled="loading"
        >
          {{ loading ? 'Updating...' : 'Save' }}
        </button>
      </div>

      <div v-if="message" class="text-green-400 text-sm mt-4">{{ message }}</div>
      <div v-if="error" class="text-red-400 text-sm mt-4">{{ error }}</div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import axios from 'axios'
import { useUserStore } from '@/stores/userStore'

const userStore = useUserStore()
// Access user data from the store's state
const userId = userStore.userData?.id
const role = userStore.userData?.role
const pilotId = userStore.userData?.pilot?.id // Access pilot ID from the eager-loaded relationship
const gamerId = userStore.userData?.gamer?.id // Access gamer ID from the eager-loaded relationship


const targetId = ref(null)

const form = ref({
  skills: '',
  bio: '',
  gamer_preference: '',
})

const loading = ref(false)
const message = ref('')
const error = ref('')

const headers = {
  Authorization: `Bearer ${localStorage.getItem('authToken')}`
}

const fetchUserInfo = async () => {
  // Ensure userId and role are available from the store
  if (!userId || !role) {
      console.error("User ID or role not available from store.");
      error.value = 'User data not loaded.';
      return;
  }

  try {
    // Use IDs from the store's eager-loaded relationships
    if (role === 'gamer' && gamerId) {
      targetId.value = gamerId;
    } else if ((role === 'game pilot' || role === 'pilot') && pilotId) { // Check for both pilot roles
      targetId.value = pilotId;
    } else {
      error.value = 'User role ID not found.';
      console.error(`Role: ${role}, Pilot ID: ${pilotId}, Gamer ID: ${gamerId}`);
      return;
    }

    const url = role === 'gamer'
      ? `http://127.0.0.1:8000/api/gamers/edit/${targetId.value}`
      : `http://127.0.0.1:8000/api/pilots/${targetId.value}`; // Assuming /api/pilots/{id} is for fetching


    const response = await axios.get(url, { headers });

    // Adjust how data is accessed based on the response structure
    // Assuming /api/gamers/edit/{id} returns { gamer: {...} }
    // Assuming /api/pilots/{id} returns { pilot: {...} }
    const data = role === 'gamer' ? response.data.gamer : response.data.pilot;


    form.value = {
      skills: data?.skills || '', // Use optional chaining
      bio: data?.bio || '',       // Use optional chaining
      gamer_preference: data?.gamer_preference || '', // Use optional chaining
    };
  } catch (err) {
    console.error('Error fetching user info:', err);
    error.value = 'Failed to load user info.';
  }
};

const updateInfo = async () => {
  if (!targetId.value || !role) {
      error.value = 'Cannot update: User role ID or role not available.';
      return;
  }

  loading.value = true;
  message.value = '';
  error.value = '';

  const url = role === 'gamer'
    ? `http://127.0.0.1:8000/api/gamers/edit/${targetId.value}`
    : `http://127.0.0.1:8000/api/pilots/edit/${targetId.value}`; // Assuming PATCH endpoint for pilot update


  const payload = role === 'gamer'
    ? { gamer_preference: form.value.gamer_preference }
    : {
        skills: form.value.skills,
        bio: form.value.bio,
      };

  try {
    // Assuming your backend uses PATCH for updates
    await axios.patch(url, payload, { headers });
    message.value = 'Information updated successfully!';
    // Optionally re-fetch data after successful update to ensure display is current
    fetchUserInfo();

  } catch (err) {
    console.error('Error updating info:', err);
    error.value = 'Failed to update information.';
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  // Ensure user data is loaded in the store before fetching user info
  if (userStore.userData) {
      fetchUserInfo();
  } else {
      // Optionally watch for user data to be loaded if it might not be available immediately
      const stopWatch = watch(() => userStore.userData, (newUserData) => {
          if (newUserData) {
              fetchUserInfo();
              stopWatch(); // Stop watching once data is loaded
          }
      });
  }
});


</script>

<style scoped>
.whitespace-pre-wrap {
  white-space: pre-wrap;
}

/* Additional styles for better visual organization */
.bg-gray-750 {
  background-color: rgba(31, 41, 55, 0.5);
}

.bg-gray-850 {
  background-color: rgba(22, 30, 40, 1);
}
</style>
