<template>
  <div class="max-h-screen text-white py-5 px-4 flex flex-col items-center">
    

    <div class="w-full max-w-3xl bg-gray-800 rounded-xl shadow-md p-6 space-y-6 border border-gray-700">
      <div v-if="role === 'game pilot'" class="space-y-4">
        <div>
          <label class="text-gray-300 font-medium">Skills</label>
          <textarea v-model="form.skills" class="w-full mt-1 p-2 rounded bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-green-400" rows="2" />
        </div>

        <div>
          <label class="text-gray-300 font-medium">Bio</label>
          <textarea v-model="form.bio" class="w-full mt-1 p-2 rounded bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-green-400" rows="3" />
        </div>
      </div>

      <div v-else-if="role === 'gamer'" class="space-y-4">
        <div>
          <label class="text-gray-300 font-medium">Gamer Preference</label>
          <input v-model="form.gamer_preference" type="text" class="w-full mt-1 p-2 rounded bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-green-400" />
        </div>
      </div>

      <div class="flex center justify-end">
        <button @click="updateInfo"
          class="bg-green-500 hover:bg-green-600 text-white font-semibold px-6 py-2 rounded-lg transition duration-300"
          :disabled="loading"
        >
          {{ loading ? 'Updating...' : 'Save' }}
        </button>
      </div>

      <div v-if="message" class="text-green-400 text-sm">{{ message }}</div>
      <div v-if="error" class="text-red-400 text-sm">{{ error }}</div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import axios from 'axios'
import { useUserStore } from '@/stores/userStore'

const userStore = useUserStore()
const userId = userStore.userData?.id
const role = userStore.userData?.role
const pilotId = userStore.userData?.pilot_id
const gamerId = userStore.userData?.gamer_id

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
  if (!userId || !role) return;

  try {
    // Use IDs from the store directly
    if (role === 'gamer' && gamerId) {
      targetId.value = gamerId;
    } else if (role === 'game pilot' && pilotId) {
      targetId.value = pilotId;
    } else {
      error.value = 'User role ID not found.';
      return;
    }

    const url = role === 'gamer'
      ? `http://127.0.0.1:8000/api/gamers/edit/${targetId.value}`
      : `http://127.0.0.1:8000/api/pilots/edit/${targetId.value}`;

    const response = await axios.get(url, { headers });
    const data = role === 'gamer' ? response.data.gamer : response.data;

    form.value = {
      skills: data.skills || '',
      bio: data.bio || '',
      gamer_preference: data.gamer_preference || '',
    };
  } catch (err) {
    console.error('Error fetching user info:', err);
    error.value = 'Failed to load user info.';
  }
};

const updateInfo = async () => {
  if (!targetId.value || !role) return;

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
  } catch (err) {
    console.error('Error updating info:', err);
    error.value = 'Failed to update information.';
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchUserInfo()
})
</script>
