<template>
<NavBar :username="username" :email="email" :role="role" :callLogout="callLogout" />
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

async function fetchPaymentHistory(userId) {
  try {
    const response = await axios.get(`/api/users/${userDataId}/payments/paid`, {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('authToken')}`
      }
    });
    payments.value = response.data.payments;
  } catch (error) {
    console.error('Error fetching payment history:', error);
  }
}

onMounted(fetchPaymentHistory);
</script>
