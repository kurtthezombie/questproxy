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
            <div class="">
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
              </button>
            </div>
          </div>

          <div class="border-t border-gray-700 -mx-5 my-4"></div>

          <table class="table w-full">
            <thead class="text-white">
              <tr>
                <th class="font-semibold py-2 px-4">Amount</th>
                <th class="font-semibold py-2 px-4">Status</th>
                <th class="font-semibold py-2 px-4">Method</th>
                <th class="font-semibold py-2 px-4">Date</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="payment in payments" :key="payment.id" class="border-t text-white">
                <td class="py-2 px-4">PHP {{ payment.amount }}</td>
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
import NavBar from '@/components/NavBar.vue';
import { useUserStore } from '@/stores/userStore';
import api from '@/utils/api';
import toast from '@/utils/toast';
import dayjs from 'dayjs';

const payments = ref([]);
const userId = ref('');
const userStore = useUserStore();
const isLoading = ref(false);

const fetchPaymentHistory = async () => {
  try {
    isLoading.value = true;
    const userToken = localStorage.getItem('authToken');
    const user_id = userId.value;
    
    if (!userToken) {
      throw new Error('Unauthorized: Missing authentication token');
    }

    const response = await api.get(`/users/${user_id}/payments/paid`);

    payments.value = response.payments;
  } catch (error) {
    console.error('Error fetching payment history:', error);
    toast.error('Failed to fetch payment history. Please try again later.');
  } finally {
    isLoading.value = false;
  }
}

const handleExport = async () => {
  try {
    isLoading.value = true;

    // Make a GET request to the backend to export the transaction history as a Blob
    const response = await api.get('/payments/paid/export', { responseType: 'blob' });

    const currentDate = dayjs().format('YYYY-MM-DD');
    const filename = `${currentDate}_payments_history.xlsx`;
    
    //create blob from the response data
    const blob = new Blob([response], { 
          type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' 
    });

    //create download link
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = filename;
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  } catch (error) {
    console.error("Error exporting transactions:", error);
  } finally {
    isLoading.value = false;
  }
};



onMounted(() => {
  const user = userStore.userData;
  userId.value=user.id;
  fetchPaymentHistory();
});

</script>
