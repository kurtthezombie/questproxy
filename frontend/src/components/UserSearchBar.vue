<template>
  <div class="relative w-full max-w-xs"> <div class="flex items-center bg-gray-800 rounded-lg shadow-md border border-gray-700 focus-within:ring-2 focus-within:ring-green-500 transition-all duration-200">
      <div class="px-3 text-gray-400">
         <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
      </div>

      <input
        type="text"
        v-model="searchQuery"
        @input="handleSearchInput"
        @keydown.enter="handleEnterKey"
        placeholder="Search username"
        class="w-full px-0 py-2 bg-transparent text-white placeholder-gray-400 focus:outline-none"
        aria-label="Search users"
      />

      <div class="relative">
         <button
            @click="toggleFilterDropdown"
            class="px-4 py-2 bg-gray-700 text-white rounded-r-lg border-l border-gray-600 focus:outline-none hover:bg-gray-600 transition-colors duration-200 text-sm" aria-haspopup="true"
            :aria-expanded="showFilterDropdown"
         >
            {{ selectedFilter || 'Filter' }}
            <svg class="w-4 h-4 inline-block ml-1 -mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
         </button>

         <div v-if="showFilterDropdown" class="absolute right-0 mt-2 w-36 bg-gray-800 rounded-md shadow-lg border border-gray-700 z-30">
            <ul class="py-1">
               <li
                  v-for="filter in filters"
                  :key="filter.value"
                  @click="selectFilter(filter)"
                  class="px-4 py-2 text-sm text-gray-200 hover:bg-gray-700 cursor-pointer"
               >
                  {{ filter.label }}
               </li>
            </ul>
         </div>
      </div>


      <div v-if="loading" class="px-3">
        <svg class="animate-spin h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
      </div>
    </div>

    <div v-if="showResultsDropdown" class="absolute z-20 w-full mt-2 bg-gray-800 rounded-lg shadow-lg border border-gray-700 max-h-60 overflow-y-auto">
      <div v-if="error" class="p-4 text-red-400 text-sm">{{ error }}</div>
      <div v-else-if="results.length === 0 && searchQuery.length > 0 && !loading" class="p-4 text-gray-400 text-sm">No users found.</div>
       <div v-else-if="searchQuery.length < 2 && !loading" class="p-4 text-gray-400 text-sm">Type at least 2 characters to search.</div>
      <ul v-else class="divide-y divide-gray-700">
        <li v-for="user in sortedResults" :key="user.id" class="p-3 hover:bg-gray-700 cursor-pointer transition-colors duration-100" @click="selectUser(user)">
          <div class="flex items-center justify-between">
            <div class="flex items-center">
               <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white text-sm font-semibold mr-3">
                 {{ user.username?.charAt(0).toUpperCase() || '?' }}
               </div>
              <div>
                <p class="text-white font-semibold">{{ user.username }}</p>
                <p class="text-xs text-gray-400 capitalize">{{ user.role?.replace('game ', '') }}</p>
              </div>
            </div>
            <div v-if="user.role === 'pilot' || user.role === 'game pilot'" class="text-yellow-400 text-sm font-bold">
              {{ user.pilot?.rank?.points ?? 0 }} Pts
            </div>
          </div>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import axios from 'axios';
import { debounce } from 'lodash'; 
import { useRouter } from 'vue-router'; 

const router = useRouter(); 

const searchQuery = ref('');
const selectedFilter = ref('Username'); 
const filters = ref([
   { label: 'Username', value: 'Username' },
   { label: 'Role', value: 'Role' },
   { label: 'Points', value: 'Points' },
]);
const showFilterDropdown = ref(false);
const results = ref([]);
const loading = ref(false);
const error = ref(null);
const showResultsDropdown = ref(false);


// Computed property to sort results (pilots first)
const sortedResults = computed(() => {
  return [...results.value].sort((a, b) => {
    const aIsPilot = a.role === 'pilot' || a.role === 'game pilot';
    const bIsPilot = b.role === 'pilot' || b.role === 'game pilot';

    // Prioritize pilots
    if (aIsPilot && !bIsPilot) return -1;
    if (!aIsPilot && bIsPilot) return 1;

    if (selectedFilter.value === 'Points' && aIsPilot && bIsPilot) {
        const aPoints = a.pilot?.rank?.points ?? 0;
        const bPoints = b.pilot?.rank?.points ?? 0;
        return bPoints - aPoints;
    }
    return a.username.localeCompare(b.username);
  });
});

// Toggle filter dropdown visibility
const toggleFilterDropdown = () => {
   showFilterDropdown.value = !showFilterDropdown.value;
   if(showFilterDropdown.value) showResultsDropdown.value = false;
};

// Select a filter option
const selectFilter = (filter) => {
   selectedFilter.value = filter.label;
   showFilterDropdown.value = false;
   if (searchQuery.value.trim().length >= 2) {
      searchUsers();
   }
};


// Debounced search function
const searchUsers = debounce(async () => {
  const query = searchQuery.value.trim();
  if (query.length < 2) {
    results.value = [];
    showResultsDropdown.value = false;
    error.value = null;
    return;
  }

  loading.value = true;
  error.value = null;
  showResultsDropdown.value = true;

  try {
    // --- Backend API Call ---
    const response = await axios.get('http://127.0.0.1:8000/api/users/search', {
      params: {
        query: query,
        filter_by: selectedFilter.value.toLowerCase(), 
      },
      headers: {
         Authorization: `Bearer ${localStorage.getItem('authToken')}` 
      }
    });

    results.value = response.data.users || []; 


    if (results.value.length === 0) {
        error.value = 'No users found.';
    } else {
        error.value = null; 
    }


  } catch (err) {
    console.error('Error searching users:', err);
    if (err.response && err.response.data && err.response.data.message) {
        error.value = `Search failed: ${err.response.data.message}`;
    } else {
        error.value = 'Failed to perform search.';
    }
    results.value = [];
  } finally {
    loading.value = false;
  }
}, 300); 

// Handle input change to trigger search
const handleSearchInput = () => {
  if (searchQuery.value.trim().length >= 2) {
    searchUsers();
  } else {
     results.value = [];
     showResultsDropdown.value = false;
     error.value = null;
  }
};

const handleEnterKey = () => {
  if (sortedResults.value.length > 0) {
    selectUser(sortedResults.value[0]); 
  }
};


const selectUser = (user) => {
  console.log('Selected user:', user);
  router.push({ name: 'userprofile', params: { id: user.id } }); 

  searchQuery.value = user.username; 
  results.value = [];
  showResultsDropdown.value = false;
};

const handleClickOutside = (event) => {
  const searchBar = document.querySelector('.relative.max-w-xs'); 
  if (searchBar && !searchBar.contains(event.target)) {
    showResultsDropdown.value = false;
    showFilterDropdown.value = false;
  }
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

import { onUnmounted } from 'vue';
onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});

</script>
