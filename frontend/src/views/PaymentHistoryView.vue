<template>
  <NavBar/>
  <div class="min-h-screen flex flex-col items-center bg-gray-900 text-gray">

    <div class="w-full max-w-4xl mt-10">
      <div class="shadow-lg rounded-lg overflow-hidden">
        <div class="bg-gray-800 text-white p-4">
          <h2 class="text-lg font-bold">Payment History</h2>
        </div>
        <div class="p-6 bg-white">
          <table class="min-w-full bg-white">
            <thead class="bg-gray-200">
              <tr>
                <th class="font-semibold py-2 px-4">Payment ID</th>
                <th class="font-semibold py-2 px-4">Amount</th>
                <th class="font-semibold py-2 px-4">Status</th>
                <th class="font-semibold py-2 px-4">Method</th>
                <th class="font-semibold py-2 px-4">Date</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="payment in payments" :key="payment.id" class="border-t">
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

const payments = ref([]);
const userId = ref('');

async function fetchPaymentHistory() {
  try {
    const userToken = localStorage.getItem('authToken');
    if (!userToken) {
      throw new Error('Unauthorized: Missing authentication token');
    }

    const response = await axios.get(`http://127.0.0.1:8000/api/users/${userId.value}/payments/paid`, {
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
  const storedUserData = localStorage.getItem('userData');
  if (storedUserData) {
    const user = JSON.parse(storedUserData);
    userId.value = user.id;
    fetchPaymentHistory();
  } else {
    console.error('User not logged in');
    
  }
});

</script>
