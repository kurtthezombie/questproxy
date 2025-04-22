<template>
  <NavBar/>
  <div class="min-h-screen bg-gray-900 p-5">
    <div class="flex justify-center mt-6 ">
      <div class="w-full max-w-7xl">
        <h2 class="text-4xl font-bold mb-4 text-white ">Payment History</h2>
        <h3 class="text-base text-gray-400">View all your payment records</h3>
        
        <div class="overflow-x-auto border border-gray-700 shadow-md rounded-lg p-5 mt-7">
          <!--search bar-->
          <div class="flex flex-wrap items-center gap-3">
            <label class="input flex items-center bg-[#1e293b] text-white border border-gray-700 shadow-none focus-within:ring-2 focus-within:ring-green-400 focus-within:border-green-600 rounded">
              <svg class="h-[1.2em] opacity-70 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <g stroke-linejoin="round" stroke-linecap="round" stroke-width="2.5" fill="none" stroke="currentColor">
                  <circle cx="11" cy="11" r="8"></circle>
                  <path d="m21 21-4.3-4.3"></path>
                </g>
              </svg>
              <input 
                type="search" 
                v-model="searchQuery"
                @keyup.enter="handleSearch"
                required 
                placeholder="Search transactions..."
                class="placeholder-gray-300 bg-transparent text-white focus:outline-none flex-1"/>
            </label> 
            
            <div class="ml-auto">
              <button 
                class="btn bg-[#1e293b] text-white border border-gray-700 shadow-none flex items-center gap-2" 
                :disabled="isLoading" 
                @click="handleExport"
              >
                <span v-if="isLoading" class="loading loading-spinner"></span>
                <template v-else>
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M8 12l4 4m0 0l4-4m-4 4V4" />
                  </svg>
                  <span>Export</span>
                </template>
              </button>tr
            </div>
          </div>

          <div class="border-t border-gray-700 -mx-5 my-4"></div>

          <table class="table w-full">
            <thead class="text-white">
              <tr>
                <th class="font-semibold py-2 px-4">Payment ID</th>
                <th class="font-semibold py-2 px-4">Amount</th>
                <th class="font-semibold py-2 px-4">Status</th>
                <th class="font-semibold py-2 px-4">Method</th>
                <th class="font-semibold py-2 px-4">Date</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="payment in payments" :key="payment.id" class="border-t text-white">
                <td class="py-2 px-4">{{ payment.transaction_id }}</td>
                <td class="py-2 px-4">{{ (payment.amount / 100).toFixed(2) }} PHP</td>
                <td class="py-2 px-4">{{ payment.status }}</td>
                <td class="py-2 px-4">{{ payment.method }}</td>
                <td class="py-2 px-4">{{ new Date(payment.created_at).toLocaleString() }}</td>
              </tr>
            </tbody>
          </table>
          
        </div>
      </div>   
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import NavBar from '@/components/NavBar.vue';
import { useUserStore } from '@/stores/userStore';

const payments = ref([]);
const userId = ref('');
const userStore = useUserStore();

async function fetchPaymentHistory() {
  try {
    const userToken = localStorage.getItem('authToken');
    const user_id = userId.value;
    console.log("UserId: ", user_id);
    if (!userToken) {
      throw new Error('Unauthorized: Missing authentication token');
    }

    const response = await axios.get(`http://127.0.0.1:8000/api/users/${user_id}/payments/paid`, {
      headers: {
        Authorization: `Bearer ${userToken}`,
      },
    });

    payments.value = response.data.payments;
  } catch (error) {
    console.error('Error fetching payment history:', error);
  }
}

onMounted(() => {
  const user = userStore.userData;
  userId.value=user.id;
  fetchPaymentHistory();
  // const storedUserData = localStorage.getItem('userData');
  // if (storedUserData) {
  //   const user = JSON.parse(storedUserData);
  //   userId.value = user.id;
  //   fetchPaymentHistory();
  // } else {
  //   console.error('User not logged in');
    
  // }
});

</script>
