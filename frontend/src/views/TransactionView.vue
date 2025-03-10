<template>
    <div class="flex flex-col items-center min-h-screen bg-gray-100">
      <div class="w-full max-w-4xl mt-10">
        <Card class="shadow-lg rounded-lg overflow-hidden">
          <CardHeader class="bg-gray-800 text-white p-4">
            <CardTitle class="text-lg font-bold">Transaction History</CardTitle>
          </CardHeader>
          <CardContent class="p-6 bg-white">
            <Table class="min-w-full bg-white">
              <TableHead class="bg-gray-200">
                <TableRow>
                  <TableCell class="font-semibold py-2 px-4">Transaction ID</TableCell>
                  <TableCell class="font-semibold py-2 px-4">Payment ID</TableCell>
                  <TableCell class="font-semibold py-2 px-4">Status</TableCell>
                  <TableCell class="font-semibold py-2 px-4">Date</TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                <TableRow v-for="transaction in transactions" :key="transaction.id" class="border-t">
                  <TableCell class="py-2 px-4">{{ transaction.id }}</TableCell>
                  <TableCell class="py-2 px-4">{{ transaction.payment_id }}</TableCell>
                  <TableCell class="py-2 px-4">{{ transaction.status }}</TableCell>
                  <TableCell class="py-2 px-4">{{ new Date(transaction.created_at).toLocaleString() }}</TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </CardContent>
        </Card>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  import axios from 'axios';
  
  const transactions = ref([]);
  
  async function viewTransactionDetails() {
    try {
      const response = await axios.get('/api/transactions', {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('authToken')}`
        }
      });
      transactions.value = response.data.transactions;
    } catch (error) {
      console.error('Error fetching transaction details:', error);
    }
  }
  
  onMounted(viewTransactionDetails);
  </script>
  