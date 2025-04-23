<template>
  <div class="p-6 bg-gray-900 min-h-screen">
    <div
      class="max-w-6xl mx-auto bg-gray-800 p-6 rounded-lg shadow-lg flex flex-col md:flex-row md:space-x-6 relative"
    >
      <!-- Edit Profile Button (Top Right) -->
      <button
        v-if="isCurrentUser && !isEditing"
        @click="openEditModal"
        class="absolute top-4 right-4 px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition flex items-center"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-4 w-4 mr-1"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"
          />
        </svg>
        Edit Profile
      </button>

      <!-- Left Section: Profile & Portfolio -->
      <div class="w-full md:w-1/3 pr-0 md:pr-4 mb-6 md:mb-0">
        <div class="flex flex-col items-center">
          <div
            class="w-20 h-20 bg-green-500 rounded-full flex items-center justify-center text-white text-2xl font-bold"
          >
            {{ userInitial }}
          </div>
          <h2 class="mt-2 text-xl font-bold text-white">{{ profileData.username || 'Anonymous' }}</h2>
          <p class="text-sm text-gray-400">{{ profileData.email }}</p>

          <!-- Role Badge -->
          <div class="mt-1">
            <span :class="`${roleBadgeClass} text-white text-xs font-medium px-2.5 py-0.5 rounded`">
              {{ roleDisplay }}
            </span>
          </div>
        </div>

        <!-- Profile Content Section -->
        <div class="mt-6">
          <!-- For Pilots: Bio & Skills -->
          <template v-if="isPilot">
            <div class="mb-4">
              <h3 class="text-lg font-semibold text-white mb-2">Bio</h3>
              <p class="text-gray-300 text-sm">
                {{ profileData.bio || 'No bio provided' }}
              </p>
            </div>
            <div class="mb-4">
              <h3 class="text-lg font-semibold text-white mb-2">Skills</h3>
              <div v-if="profileData.skills" class="flex flex-wrap gap-2">
                <span
                  v-for="(skill, index) in profileData.skills.split(',')"
                  :key="index"
                  class="bg-gray-700 text-white text-xs px-2 py-1 rounded"
                >
                  {{ skill.trim() }}
                </span>
              </div>
              <p v-else class="text-gray-400 text-sm">No skills listed</p>
            </div>
          </template>

          <!-- For Gamers: Preferences -->
          <template v-else-if="isGamer">
            <div class="mb-4">
              <h3 class="text-lg font-semibold text-white mb-2">Gamer Preferences</h3>
              <p class="text-gray-300 text-sm">
                {{ profileData.gamer_preference || 'No preferences provided' }}
              </p>
            </div>
          </template>
        </div>

        <!-- Pilot Service Details (if applicable) -->
        <div v-if="isPilot && pilotService" class="mt-6 p-3 bg-gray-700 rounded-md">
          <h3 class="font-semibold text-blue-300 mb-2">Pilot Service Details</h3>
          <div class="grid grid-cols-2 gap-2 text-sm">
            <p class="text-gray-400">Experience:</p>
            <p class="font-medium text-white">{{ pilotService.experience || 'N/A' }} years</p>

            <p class="text-gray-400">Status:</p>
            <p :class="`font-medium ${pilotService.active ? 'text-green-400' : 'text-red-400'}`">
              {{ pilotService.active ? 'Active' : 'Inactive' }}
            </p>

            <p class="text-gray-400">Rating:</p>
            <div class="flex items-center col-span-1">
              <span class="text-yellow-400 mr-1">★</span>
              <span class="text-white">{{ pilotService.rating || '4.5' }}/5</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Section: Services or Gamer Content -->
      <div class="w-full md:w-2/3 pl-0 md:pl-4 mt-4 md:mt-0">
        <div v-if="shouldShowServices" class="space-y-4">
          <h3 class="text-lg text-center font-semibold text-white mb-4">Offered Services</h3>
          <div v-if="serviceStore.loading" class="flex justify-center items-center h-32">
            <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
          </div>
          <div
            v-else-if="serviceStore.services.length > 0"
            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-4 px-2 sm:px-4"
          >
            <ServiceCard
              v-for="service in serviceStore.services"
              :key="service.id"
              :service="service"
              :categories="serviceStore.categories"
              @click="handleServiceClick(service)"
              class="w-full"
            />
          </div>
          <div v-else class="flex justify-center items-center h-32 text-gray-400 text-lg">
            <template v-if="isPilot">No services currently offered</template>
            <template v-else>This pilot does not offer any services</template>
          </div>
        </div>
        <div v-else-if="isGamer" class="flex justify-center items-center h-32 text-gray-400 text-lg">
          <p><!-- Gamer preferences and stats will be shown here --></p>
        </div>
      </div>

      <!-- Edit Profile Modal -->
      <div
        v-if="showEditModal"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      >
        <div class="bg-gray-800 rounded-lg p-6 w-full max-w-md">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-white">Edit Profile</h3>
            <button @click="closeEditModal" class="text-gray-400 hover:text-white">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M6 18L18 6M6 6l12 12"
                />
              </svg>
            </button>
          </div>

          <!-- Pilot Edit Form -->
          <form v-if="isPilot" @submit.prevent="saveProfile">
            <div class="mb-4">
              <label class="block text-sm font-medium text-white mb-1">Bio</label>
              <textarea
                v-model="editForm.bio"
                class="w-full bg-gray-700 text-white rounded-md p-2 border border-gray-600"
                rows="3"
                placeholder="Tell us about yourself..."
              ></textarea>
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium text-white mb-1">Skills</label>
              <textarea
                v-model="editForm.skills"
                class="w-full bg-gray-700 text-white rounded-md p-2 border border-gray-600"
                rows="3"
                placeholder="List your gaming skills (comma separated)..."
              ></textarea>
              <p class="text-xs text-gray-400 mt-1">Separate skills with commas</p>
            </div>

            <div class="flex justify-end gap-2">
              <button
                type="button"
                @click="closeEditModal"
                class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md transition"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition flex items-center"
                :disabled="isSaving"
              >
                <svg
                  v-if="isSaving"
                  class="animate-spin h-4 w-4 mr-2"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                >
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                  ></path>
                </svg>
                {{ isSaving ? 'Saving...' : 'Save Changes' }}
              </button>
            </div>
          </form>

          <!-- Gamer Edit Form -->
          <form v-else-if="isGamer" @submit.prevent="saveProfile">
            <div class="mb-4">
              <label class="block text-sm font-medium text-white mb-1">Gamer Preferences</label>
              <textarea
                v-model="editForm.gamer_preference"
                class="w-full bg-gray-700 text-white rounded-md p-2 border border-gray-600"
                rows="3"
                placeholder="Describe your gaming preferences..."
              ></textarea>
            </div>

            <div class="flex justify-end gap-2">
              <button
                type="button"
                @click="closeEditModal"
                class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md transition"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition flex items-center"
                :disabled="isSaving"
              >
                <svg
                  v-if="isSaving"
                  class="animate-spin h-4 w-4 mr-2"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                >
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                  ></path>
                </svg>
                {{ isSaving ? 'Saving...' : 'Save Changes' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useUserStore } from '@/stores/userStore'
import { useServiceStore } from '@/stores/serviceStore'
import loginService from '@/services/login-service'
import userService from '@/services/user-service'
import { fetchPortfoliosByUser } from '@/services/portfolio.service'
import { useLoader } from '@/services/loader-service'
import ServiceCard from '@/components/ServiceDisplay.vue'
import toast from '@/utils/toast'
import axios from 'axios'

const { loadShow, loadHide } = useLoader()

const router = useRouter()
const route = useRoute()
const userStore = useUserStore()
const serviceStore = useServiceStore()

const profileData = ref({})
const portfolios = ref([])
const username = ref('')
const role = ref('User')
const profileLoaded = ref(false)
const pilotService = ref(null)
const isSaving = ref(false)
const currentUser = ref(null)
const showEditModal = ref(false)
const isEditing = ref(false)

const editForm = ref({
  bio: '',
  skills: '',
  gamer_preference: '',
})

// Computed properties
const userInitial = computed(() => {
  if (!profileData.value) return "?"; // Handle loading state
  const firstInitial = profileData.value.f_name ? profileData.value.f_name.charAt(0) : "";
  const lastInitial = profileData.value.l_name ? profileData.value.l_name.charAt(0) : "";
  return `${firstInitial}${lastInitial}`.toUpperCase() || "?";
});

const isPilot = computed(() => {
  return role.value === 'Game Pilot'
})

const isGamer = computed(() => {
  return role.value === 'Gamer'
})

const roleDisplay = computed(() => {
  if (isPilot.value) return 'Game Pilot'
  if (isGamer.value) return 'Gamer'
  return 'User'
})

const roleBadgeClass = computed(() => {
  if (isPilot.value) return 'bg-blue-500'
  if (isGamer.value) return 'bg-purple-500'
  return 'bg-gray-500'
})

const isCurrentUser = computed(() => {
  return currentUser.value && profileData.value && currentUser.value.id === profileData.value.id
})

const shouldShowServices = computed(() => {
  return isPilot.value && (currentUser.value?.role === 'game pilot' || isCurrentUser.value)
})

// Methods
const openEditModal = () => {
  isEditing.value = true
  editForm.value = {
    bio: profileData.value.bio || '',
    skills: profileData.value.skills || '',
    gamer_preference: profileData.value.gamer_preference || '',
  }
  showEditModal.value = true
}

const closeEditModal = () => {
  isEditing.value = false
  showEditModal.value = false
}

const saveProfile = async () => {
  isSaving.value = true
  try {
    let endpoint, data

    if (isPilot.value) {
      endpoint = `/api/pilots/${profileData.value.id}`
      data = {
        bio: editForm.value.bio,
        skills: editForm.value.skills,
      }
    } else if (isGamer.value) {
      endpoint = `/api/gamers/${profileData.value.id}`
      data = {
        gamer_preference: editForm.value.gamer_preference,
      }
    }

    const authToken = localStorage.getItem('authToken')
    const response = await axios.put(`http://127.0.0.1:8000${endpoint}`, data, {
      headers: {
        Authorization: `Bearer ${authToken}`,
      },
    })

    // Update local profile data
    if (isPilot.value) {
      profileData.value.bio = editForm.value.bio
      profileData.value.skills = editForm.value.skills
    } else if (isGamer.value) {
      profileData.value.gamer_preference = editForm.value.gamer_preference
    }

    closeEditModal()
    toast.success('Profile updated successfully')
  } catch (error) {
    console.error('Error updating profile:', error)
    toast.error('Failed to update profile')
  } finally {
    isSaving.value = false
  }
}

const fetchUserData = async () => {
  const loader = loadShow()
  try {
    const userData = await loginService.fetchUserData()
    username.value = userData.username
    currentUser.value = userData
    role.value =
      userData.role === 'gamer'
        ? 'Gamer'
        : userData.role === 'game pilot'
          ? 'Game Pilot'
          : 'User'
  } catch (error) {
    console.error('Error fetching user data:', error)
    router.push({ name: 'login' })
  } finally {
    loadHide(loader)
  }
}

const fetchProfileData = async () => {
  profileLoaded.value = false;
  try {
    const userId = route.params.id;
    const profile = await userService.getUserProfile(userId);
    
    // Ensure all required fields exist
    profileData.value = {
      username: profile.username || "User",
      email: profile.email || "",
      f_name: profile.f_name || "",
      l_name: profile.l_name || "",
      ...profile // spread the rest of the profile data
    };

    username.value = profileData.value.username;
    
    if (profile.role === "game pilot") {
      role.value = "Game Pilot";
      pilotService.value = {
        experience: profile.pilot_experience || 0,
        active: profile.pilot_active !== undefined ? profile.pilot_active : true,
        rating: profile.pilot_rating || "4.5",
        bio: profile.bio || "",
        skills: profile.skills || "",
      };
    } else if (profile.role === "gamer") {
      role.value = "Gamer";
    }

    profileLoaded.value = true;
  } catch (error) {
    console.error("Error fetching profile:", error);
    profileLoaded.value = true; // Still set to true to show error state
  }
};

const fetchUserServices = async () => {
  try {
    const userId = route.params.id
    if (shouldShowServices.value) {
      await serviceStore.fetchServicesByPilot(userId)
    } else {
      serviceStore.clearServices()
    }
  } catch (error) {
    console.error('Error fetching user services:', error)
    serviceStore.clearServices()
  }
}

const fetchCategories = async () => {
  try {
    await serviceStore.fetchCategories()
  } catch (error) {
    console.error('Error fetching categories:', error)
  }
}

const fetchPortfolios = async () => {
  try {
    const userId = route.params.id
    const response = await fetchPortfoliosByUser(userId)
    const baseURL = 'http://127.0.0.1:8000/storage/'

    portfolios.value =
      response.portfolios?.map((portfolio) => ({
        ...portfolio,
        p_content: baseURL + portfolio.p_content,
      })) || []
  } catch (error) {
    console.error('Error fetching portfolio:', error)
    portfolios.value = []
  }
}

const handleServiceClick = (service) => {
  if (!service.availability) {
    toast.warning('This service is currently not accepting bookings.')
    return
  }

  router.push({
    name: 'PaymentView',
    params: { serviceId: service.id },
    state: { service: service },
  })
}

onMounted(async () => {
  await fetchUserData()
  await fetchProfileData()
  await fetchUserServices()
  await fetchCategories()
  await fetchPortfolios()
})

watch(
  () => route.params.id,
  async (newId) => {
    if (newId) {
      await fetchProfileData()
      await fetchUserServices()
      await fetchPortfolios()
    }
  }
)
</script>
