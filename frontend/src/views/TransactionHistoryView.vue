<script setup>
import NavBar from '@/components/NavBar.vue';
import { ref, computed } from 'vue';

const transactions = ref([
  { id: 1, payment_id: "PAY12345", status: "Completed", created_at: "2025-03-30 10:15:00", updated_at: "2025-03-30 12:00:00" },
  { id: 2, payment_id: "PAY12346", status: "Pending", created_at: "2025-03-30 11:00:00", updated_at: "2025-03-30 11:30:00" },
  { id: 3, payment_id: "PAY12347", status: "Failed", created_at: "2025-03-30 09:45:00", updated_at: "2025-03-30 10:00:00" },
  { id: 4, payment_id: "PAY12348", status: "Completed", created_at: "2025-03-29 14:20:00", updated_at: "2025-03-29 15:10:00" },
  { id: 5, payment_id: "PAY12349", status: "Refunded", created_at: "2025-03-28 16:40:00", updated_at: "2025-03-28 17:05:00" },
  { id: 6, payment_id: "PAY12350", status: "Completed", created_at: "2025-03-27 09:10:00", updated_at: "2025-03-27 09:55:00" },
  { id: 7, payment_id: "PAY12351", status: "Pending", created_at: "2025-03-26 12:30:00", updated_at: "2025-03-26 13:00:00" },
  { id: 8, payment_id: "PAY12352", status: "Failed", created_at: "2025-03-25 18:15:00", updated_at: "2025-03-25 18:45:00" },
  { id: 9, payment_id: "PAY12353", status: "Completed", created_at: "2025-03-24 07:20:00", updated_at: "2025-03-24 08:00:00" },
  { id: 10, payment_id: "PAY12354", status: "Pending", created_at: "2025-03-23 21:45:00", updated_at: "2025-03-23 22:15:00" }
]);


const currentPage = ref(1);
const itemsPerPage = 5;

const paginatedTransactions = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  return transactions.value.slice(start, start + itemsPerPage);
});

const totalPages = computed(() => Math.ceil(transactions.value.length / itemsPerPage));

const setPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
  }
};
</script>

<template>
  <NavBar />

  <div class="flex justify-center mt-6">
    <div class="w-full max-w-3xl">
      <h2 class="text-2xl font-bold mb-4 text-center">My Transaction History</h2>

      <div class="overflow-x-auto bg-white shadow-md rounded-lg p-4">
        <table class="table w-full">
          <thead>
            <tr>
              <th>
                <button @click="sortTable('id')" class="flex items-center gap-1">
                  ID
                  <span v-if="sortBy === 'id'">
                    <svg v-if="sortOrder === 1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                      stroke-width="1.5" stroke="currentColor" class="size-4">
                      <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                      stroke="currentColor" class="size-4">
                      <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>
                  </span>
                </button>
              </th>
              <th>
                <button @click="sortTable('payment_id')" class="flex items-center gap-1">
                  Payment ID
                  <span v-if="sortBy === 'payment_id'">
                    <svg v-if="sortOrder === 1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                      stroke-width="1.5" stroke="currentColor" class="size-4">
                      <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                      stroke="currentColor" class="size-4">
                      <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>
                  </span>
                </button>
              </th>
              <th>
                <button @click="sortTable('status')" class="flex items-center gap-1">
                  Status
                  <span v-if="sortBy === 'status'">
                    <svg v-if="sortOrder === 1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                      stroke-width="1.5" stroke="currentColor" class="size-4">
                      <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                      stroke="currentColor" class="size-4">
                      <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>
                  </span>
                </button>
              </th>
              <th>
                <button @click="sortTable('created_at')" class="flex items-center gap-1">
                  Created At
                  <span v-if="sortBy === 'created_at'">
                    <svg v-if="sortOrder === 1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                      stroke-width="1.5" stroke="currentColor" class="size-4">
                      <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                      stroke="currentColor" class="size-4">
                      <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>
                  </span>
                </button>
              </th>
              <th>
                <button @click="sortTable('updated_at')" class="flex items-center gap-1">
                  Updated At
                  <span v-if="sortBy === 'updated_at'">
                    <svg v-if="sortOrder === 1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                      stroke-width="1.5" stroke="currentColor" class="size-4">
                      <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                      stroke="currentColor" class="size-4">
                      <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>
                  </span>
                </button>
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="transaction in paginatedTransactions" :key="transaction.id" class="hover:bg-base-300">
              <td>{{ transaction.id }}</td>
              <td>{{ transaction.payment_id }}</td>
              <td>{{ transaction.status }}</td>
              <td>{{ transaction.created_at }}</td>
              <td>{{ transaction.updated_at }}</td>
            </tr>
          </tbody>
        </table>
        <!-- Pagination -->
        <div class="flex justify-between mt-4">
          <div class="join flex justify-center">
            <button v-for="page in totalPages" :key="page" class="join-item btn"
              :class="{ 'btn-active': page === currentPage }" @click="setPage(page)">
              {{ page }}
            </button>
          </div>
          <div>
            <button class="btn">Export</button>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>
