<script setup>
import NavBar from '@/components/NavBar.vue';
import { ref, onMounted } from 'vue';
import { fetchTransactions, exportTransactions } from '@/services/transaction.service';
import toast from '@/utils/toast';
import dayjs from 'dayjs';

const transactions = ref([]);
const isLoading = ref(false);
const pagination = ref({ 
  current_page: 1, 
  last_page: 1, 
});
const searchQuery = ref('');
const searchColumn = ref('status');
const sortBy = ref('updated_at');
const sortOrder = ref(1);

const setPage = (page) => {
  if (page !== pagination.value.current_page) {
    handleFetch(page, searchQuery.value, searchColumn.value);
  }
};

const handleSearch = () => {
  handleFetch(pagination.value.current_page, searchQuery.value, searchColumn.value); // Pass selected column
};

const handleExport = async () => {
  try {
    isLoading.value = true;
    await exportTransactions()
  } catch (error) {
    console.log("Error exporting transactions: ", error);
    toast.error(error.message);
  } finally {
    isLoading.value = false;
    toast.success("Transactions exported successfully!");
  }
};

const handleFetch = async (page = 1, searchQuery = '', searchColumn = '') => {
  try {
    isLoading.value = true;
    const data = await fetchTransactions(page, searchQuery,searchColumn);
    console.log("Data: ", data);
    transactions.value = data.transactions;
    pagination.value = {
      current_page: data.current_page,
      last_page: data.last_page,
      next_page_url: data.next_page_url
    };
  } catch (error) {
    console.error("Error fetching transactions: ", error);
    toast.error(error.message);
  } finally {
    isLoading.value = false;
  }
}

const handleReset = () => {
  searchQuery.value = '';
  searchColumn.value = 'status';
  handleFetch(pagination.value.current_page, '','status');
}

const sortTable = (column) => {
  // Toggle sort order if the same column is clicked
  if (sortBy.value === column) {
    sortOrder.value = sortOrder.value === 1 ? -1 : 1;  // Toggle between 1 and -1
  } else {
    sortBy.value = column;  // Update column for sorting
    sortOrder.value = 1;  // Default to ascending order
  }
  handleFetch(pagination.value.current_page, searchQuery.value, searchColumn.value);  // Fetch sorted data
};

onMounted(() => {
  handleFetch();
})
</script>

<template>
  <NavBar />
  <div class="flex justify-center mt-6">
    <div class="w-full max-w-3xl">
      <h2 class="text-3xl font-bold mb-4 text-left text-gray-600">My Transaction History</h2>

      <div class="overflow-x-auto bg-white shadow-md rounded-lg p-4">
        <!--search bar-->
        <label class="input">
          <svg class="h-[1em] opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g stroke-linejoin="round" stroke-linecap="round" stroke-width="2.5" fill="none" stroke="currentColor"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.3-4.3"></path></g></svg>
          <input 
            type="search" 
            v-model="searchQuery"
            @keyup.enter="handleSearch"
            required 
            placeholder="Search"
            />
        </label>

        <!-- Dropdown to select search column -->
        <select v-model="searchColumn" class="input ml-3 w-1/6">
          <option value="payment_id">Payment ID</option>
          <option value="status">Status</option>
        </select>

        <!--reset btn-->
        <button class="btn btn-square btn-sm ml-3" @click="handleReset">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
          </svg>
        </button>
        
        <table class="table w-full">
          <thead>
            <tr>
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
                <button @click="sortTable('amount')" class="flex items-center gap-1">
                  Amount
                  <span v-if="sortBy === 'amount'">
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
            <!-- Show skeleton rows when loading -->
            <tr v-if="isLoading" v-for="n in 5" :key="n">
              <td v-for="i in 5" :key="i">
                <div class="skeleton h-6 w-24"></div>
              </td>
            </tr>

            <tr v-for="transaction in transactions" v-if="!isLoading" :key="transaction.id" class="hover:bg-base-300 hover:cursor-pointer duration-200 transition-all">
              <td>{{ transaction.payment_id }}</td>
              <td>{{ transaction.payment?.amount }}</td>
              <td>{{ transaction.status }}</td>
              <td>{{ dayjs(transaction.created_at).format('MMMM D, YYYY h:mm A') }}</td>
              <td>{{ dayjs(transaction.created_at).format('MMMM D, YYYY h:mm A') }}</td>
            </tr>
          </tbody>
        </table>
        <!-- Pagination -->
        <div class="flex justify-between mt-4">
          <div class="join flex justify-center">
            <template v-if="isLoading">
              <div v-for="n in 3" :key="n" class="skeleton h-10 w-10 rounded-lg mx-1"></div>
            </template>
            <template v-if="!isLoading">
              <button v-for="page in pagination.last_page" :key="page" class="join-item btn"
                :class="{ 'btn-active': page === currentPage }" @click="setPage(page)">
                {{ page }}
              </button>
            </template>

          </div>
          <div>
            <button class="btn" :disabled="isLoading" @click="handleExport">
              <span v-if="isLoading" class="loading loading-spinner"></span>
              <span v-else>Export</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
