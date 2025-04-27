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
const pilotId = userStore.userData?.pilot?.id 
const gamerId = userStore.userData?.gamer?.id 


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

  if (!userId || !role) {
      console.error("User ID or role not available from store.");
      error.value = 'User data not loaded.';
      return;
  }

  try {
  
    if (role === 'gamer' && gamerId) {
      targetId.value = gamerId;
    } else if ((role === 'game pilot' || role === 'pilot') && pilotId) { 
      targetId.value = pilotId;
    } else {
      error.value = 'User role ID not found.';
      console.error(`Role: ${role}, Pilot ID: ${pilotId}, Gamer ID: ${gamerId}`);
      return;
    }

    const url = role === 'gamer'
      ? `http://127.0.0.1:8000/api/gamers/edit/${targetId.value}`
      : `http://127.0.0.1:8000/api/pilots/${targetId.value}`; 


    const response = await axios.get(url, { headers });

    const data = role === 'gamer' ? response.data.gamer : response.data.pilot;


    form.value = {
      skills: data?.skills || '', 
      bio: data?.bio || '',       
      gamer_preference: data?.gamer_preference || '', 
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
    : `http://127.0.0.1:8000/api/pilots/edit/${targetId.value}`; 


  const payload = role === 'gamer'
    ? { gamer_preference: form.value.gamer_preference }
    : {
        skills: form.value.skills,
        bio: form.value.bio,
      };

  try {

    await axios.patch(url, payload, { headers });
    message.value = 'Information updated successfully!';
    
    fetchUserInfo();

  } catch (err) {
    console.error('Error updating info:', err);
    error.value = 'Failed to update information.';
  } finally {
    loading.value = false;
  }
};

onMounted(() => {

  if (userStore.userData) {
      fetchUserInfo();
  } else {
      
      const stopWatch = watch(() => userStore.userData, (newUserData) => {
          if (newUserData) {
              fetchUserInfo();
              stopWatch(); 
          }
      });
  }
});


</script>

<style scoped>
.whitespace-pre-wrap {
  white-space: pre-wrap;
}

.bg-gray-750 {
  background-color: rgba(31, 41, 55, 0.5);
}

.bg-gray-850 {
  background-color: rgba(22, 30, 40, 1);
}
</style>
