<template>
  <div class="p-6 bg-gray-900 min-h-screen">
    <div
      class="max-w-6xl mx-auto bg-gray-800 p-6 rounded-lg shadow-lg flex flex-col md:flex-row md:space-x-6 relative border border-gray-700"
    >
      <!-- Edit Profile Button (Only visible when viewing your own profile) -->
      <button
        v-if="isCurrentUser"
        @click="openEditModal"
        class="absolute top-4 right-4 px-3 py-1 bg-green-500 hover:bg-green-600 text-white rounded-md transition flex items-center"
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
          <h2 class="mt-2 text-xl font-bold text-white">{{ user.username || 'Anonymous' }}</h2>
          <p class="text-sm text-gray-400">{{ user.email }}</p>

          <!-- Role Badge -->
          <div class="mt-1">
            <span :class="`${roleBadgeClass} text-white text-xs font-medium px-2.5 py-0.5 rounded`">
              {{ roleDisplay }}
            </span>
          </div>
          
          <!-- Member Since & Points (for pilots) -->
          <div class="mt-5 flex items-center gap-2" v-if="isPilot">
            <div class="bg-emerald-950 text-emerald-400 px-3 py-1 rounded-full text-xs flex items-center border border-emerald-800">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="yellow">
                <path d="M12 2L15 8L22 8.5L17 12.5L18 19.5L12 16.5L6 19.5L7 12.5L2 8.5L9 8L12 2Z"/>
              </svg>
              <span class="font-semibold mr-1 ml-1">Quest Points:</span>
              <span class="font-bold">{{ user.pilot?.points || 0 }}</span>
            </div>
            <div class="flex items-center gap-1 text-blue-400 font-semibold text-xs leading-tight bg-blue-950 px-3 py-1 rounded-full border border-blue-700">
              <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A10.97 10.97 0 0112 15c2.21 0 4.25.672 5.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              Member since {{ getMemberSince(user.created_at) }}
            </div>
          </div>
        </div>

        <!-- Profile Content Section -->
        <div class="mt-6">
          <!-- For Pilots: Bio & Skills -->
          <template v-if="isPilot">
            <div class="mb-4">
              <h3 class="text-lg font-semibold text-white mb-2">Bio</h3>
              <p class="text-gray-300 text-sm">
                {{ user.pilot?.bio || 'No bio provided' }}
              </p>
            </div>
            <div class="mb-4">
              <h3 class="text-lg font-semibold text-white mb-2">Skills</h3>
              <div v-if="user.pilot?.skills" class="flex flex-wrap gap-2">
                <span
                  v-for="(skill, index) in user.pilot.skills.split(',')"
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
                {{ user.gamer?.gamer_preference || 'No preferences provided' }}
              </p>
            </div>
          </template>
        </div>

        <!-- Pilot Service Details (if applicable) -->
        <div v-if="isPilot" class="mt-6 p-3 bg-gray-700 rounded-md">
          <h3 class="font-semibold text-blue-300 mb-2">Pilot Service Details</h3>
          <div class="grid grid-cols-2 gap-2 text-sm">
            <p class="text-gray-400">Experience:</p>
            <p class="font-medium text-white">{{ pilotExperience }} years</p>

            <p class="text-gray-400">Status:</p>
            <p :class="`font-medium ${isPilotActive ? 'text-green-400' : 'text-red-400'}`">
              {{ isPilotActive ? 'Active' : 'Inactive' }}
            </p>

            <p class="text-gray-400">Rating:</p>
            <div class="flex items-center col-span-1">
              <span class="text-yellow-400 mr-1">★</span>
              <span class="text-white">{{ pilotRating }}/5</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Section: Services or Gamer Content -->
      <div class="w-full md:w-2/3 pl-0 md:pl-4 mt-4 md:mt-0">
        <!-- Pilot Services Section -->
        <div v-if="isPilot" class="space-y-4">
          <h3 class="text-lg text-center font-semibold text-white mb-4">Offered Services</h3>
          <div v-if="isLoading" class="flex justify-center items-center h-32">
            <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
          </div>
          <div
            v-else-if="services.length > 0"
            class="grid grid-cols-1 lg:grid-cols-2 gap-4 px-2 sm:px-4"
          >
            <!-- Service Card (using design from ServiceDisplay.vue) -->
            <div
              v-for="service in services"
              :key="service.id"
              class="bg-blue-900 bg-opacity-20 p-5 rounded-xl shadow-lg border border-gray-700 group hover:border-green-400 hover:-translate-y-2 transition-all duration-300 cursor-pointer"
              @click="handleServiceClick(service)"
            >
              <div class="flex justify-between items-center mb-2">
                <h3 class="text-2xl font-bold text-white">{{ service.game }}</h3>
                <span
                  class="text-xs px-2 py-1 rounded-2xl"
                  :class="service.availability ? 'text-white bg-emerald-500 font-semibold' : 'bg-red-500 text-white'"
                >
                  {{ service.availability ? 'Available' : 'Not Available' }}
                </span>
              </div>
              <p class="text-gray-300">{{ service.description || 'No description available' }}</p>
              <div class="mt-2 flex justify-between items-center">
                <div class="flex flex-col text-gray-300">
                  <div class="flex items-center mb-1 mt-4">
                    <svg class="w-5 h-5 text-gray-300 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m5-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="">{{ service.duration }} {{ service.duration === 1 ? 'day' : 'days' }}</span>
                  </div>
                </div>
              </div>
              <div class="flex justify-between items-center mt-4">
                <div class="flex items-center">
                  <div class="w-7 h-7 rounded-full bg-green-900 flex items-center justify-center text-green-400 text-lg font-semibold mr-1">
                    {{ userInitial }}
                  </div>
                  <span class="font-medium font-semibold text-gray-300">
                    {{ user.username || 'Unknown User' }}
                  </span>
                </div>
                <span class="text-2xl font-bold text-green-400">₱{{ formatPrice(service.price) }}</span>
              </div>
            </div>
          </div>
          <div v-else class="flex justify-center items-center h-32 text-gray-400 text-lg">
            No services currently offered
          </div>
          
          <!-- Portfolio Section for Pilots -->
          <div v-if="portfolio.length > 0" class="mt-8">
            <h3 class="text-lg text-center font-semibold text-white mb-4">Portfolio</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 px-2 sm:px-4">
              <div
                v-for="item in portfolio"
                :key="item.id"
                class="bg-gray-700 rounded-lg overflow-hidden shadow-md"
              >
                <img
                  :src="getPortfolioImageUrl(item.p_content)"
                  :alt="item.caption"
                  class="w-full h-40 object-cover"
                />
                <div class="p-3">
                  <p class="text-gray-300 text-sm">{{ item.caption }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Gamer Content Section -->
        <div v-else-if="isGamer" class="space-y-4">
          <h3 class="text-lg text-center font-semibold text-white mb-4">Recent Bookings</h3>
          <div v-if="isLoading" class="flex justify-center items-center h-32">
            <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
          </div>
          <div v-else-if="bookings.length > 0" class="space-y-4">
            <div
              v-for="booking in bookings"
              :key="booking.id"
              class="bg-gray-700 rounded-lg p-4 shadow-md"
            >
              <div class="flex justify-between items-start">
                <h3 class="text-white font-medium">{{ booking.service?.game || 'Unknown Game' }}</h3>
                <span
                  :class="getStatusClass(booking.status)"
                  class="text-xs font-medium px-2.5 py-0.5 rounded"
                >
                  {{ booking.status }}
                </span>
              </div>
              <p class="text-gray-300 text-sm my-2">
                {{ booking.service?.description || 'No description available' }}
              </p>
              <p class="text-xs text-gray-400">Booked on {{ formatDate(booking.created_at) }}</p>
            </div>
          </div>
          <div v-else class="flex justify-center items-center h-32 text-gray-400 text-lg">
            No recent bookings found
          </div>
        </div>
      </div>

      <!-- Edit Profile Modal (Only for your own profile) -->
      <div
        v-if="showEditModal"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      >
        <div class="bg-gray-800 rounded-lg p-6 w-full max-w-md border border-gray-700">
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
                class="w-full bg-gray-700 text-white rounded-md p-2 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-green-400"
                rows="3"
                placeholder="Tell us about yourself..."
              ></textarea>
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium text-white mb-1">Skills</label>
              <textarea
                v-model="editForm.skills"
                class="w-full bg-gray-700 text-white rounded-md p-2 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-green-400"
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
                class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-md transition flex items-center"
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
                class="w-full bg-gray-700 text-white rounded-md p-2 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-green-400"
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
                class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-md transition flex items-center"
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
  if (isPilot.value) return 'bg-blue-600'
  if (isGamer.value) return 'bg-green-600'
  return 'bg-gray-600'
})

const isCurrentUser = computed(() => {
  return currentUser.value && profileData.value && currentUser.value.id === profileData.value.id
})

const shouldShowServices = computed(() => {
  return isPilot.value || (currentUser.value?.role === 'game pilot' || isCurrentUser.value)
})

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
      bio: profile.bio || "",
      skills: profile.skills || "",
      gamer_preference: profile.gamer_preference || "",
      ...profile // spread the rest of the profile data
    };

    username.value = profileData.value.username;
    
    // Set pilot service data if available
    if (profile.pilot_service) {
      pilotService.value = profile.pilot_service;
    } else {
      // Default pilot service data
      pilotService.value = {
        experience: 'N/A',
        active: false,
        rating: '0.0'
      };
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

// New functions for edit profile functionality
const openEditModal = () => {
  // Initialize form with current values
  if (isPilot.value) {
    editForm.value.bio = profileData.value.bio || '';
    editForm.value.skills = profileData.value.skills || '';
  } else if (isGamer.value) {
    editForm.value.gamer_preference = profileData.value.gamer_preference || '';
  }
  showEditModal.value = true;
}

const closeEditModal = () => {
  showEditModal.value = false;
  editForm.value = {
    bio: '',
    skills: '',
    gamer_preference: ''
  };
}

const saveProfile = async () => {
  isSaving.value = true;
  try {
    const userId = currentUser.value.id;
    let updateData = {};
    
    if (isPilot.value) {
      updateData = {
        bio: editForm.value.bio,
        skills: editForm.value.skills
      };
    } else if (isGamer.value) {
      updateData = {
        gamer_preference: editForm.value.gamer_preference
      };
    }
    
    // Call your API to update the profile
    await userService.updateUserProfile(userId, updateData);
    
    // Update local data
    if (isPilot.value) {
      profileData.value.bio = editForm.value.bio;
      profileData.value.skills = editForm.value.skills;
    } else if (isGamer.value) {
      profileData.value.gamer_preference = editForm.value.gamer_preference;
    }
    
    toast.success('Profile updated successfully');
    closeEditModal();
  } catch (error) {
    console.error('Error updating profile:', error);
    toast.error('Failed to update profile');
  } finally {
    isSaving.value = false;
  }
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