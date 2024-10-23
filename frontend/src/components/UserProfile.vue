<template>
    <div class="max-w-screen-lg mx-auto p-6">
      <div class="flex flex-col items-center bg-white rounded-lg shadow-lg p-8">
        
        <!-- Avatar, Username, and Level Information -->
        <div class="flex flex-col items-center mb-6">
          <div class="w-24 h-24 bg-red-500 rounded-full flex items-center justify-center text-white text-4xl font-bold">
            {{ username.charAt(0).toUpperCase() }}
          </div>
          <h1 class="mt-4 text-2xl font-semibold">{{ username }}</h1>
          <div class="flex space-x-4 mt-2 text-sm">
            <div>Level {{ level }}</div>
            <div>|</div>
            <div>Member since {{ memberSince }}</div>
          </div>
        </div>
  

        <div class="flex flex-col items-center mt-4">
          <p class="text-gray-600">Successful delivery</p>
          <div class="text-lg font-semibold">{{ deliveryRate }}% <span class="text-gray-500">(Total lifetime orders: {{ totalOrders }})</span></div>
          
          <div class="flex justify-between mt-4 w-full">
            <div class="text-center">
              <p>Last 90 Days</p>
              <p class="text-green-600 font-semibold">{{ last90DaysSuccess }}% <span class="text-red-600">{{ last90DaysFail }}%</span></p>
            </div>
            <div class="text-center">
              <p>All time rating</p>
              <p class="text-green-600 font-semibold">{{ allTimeSuccess }}% <span class="text-red-600">{{ allTimeFail }}%</span></p>
            </div>
          </div>
        </div>
  
        <!-- File Upload Section -->
        <div class="mt-6 w-full text-center">
          <label class="block text-sm font-medium text-gray-700 mb-2">Upload File</label>
          <input type="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100" />
          <p class="mt-2 text-gray-500 text-sm">Supported format: PDF, Docx. Max file size: 10 MB</p>
        </div>
  
        <!-- Description Section -->
        <div class="mt-6 w-full">
          <label for="description" class="block text-sm font-medium text-gray-700">Profile Description</label>
          <textarea id="description" v-model="description" rows="4" class="mt-2 p-3 w-full text-gray-700 border rounded-lg" placeholder="Enter a brief description..."></textarea>
        </div>
  
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  
  // Sample 
  const username = ref("Rice0Rice");
  const isPublic = ref(true); 
  
  // Editable fields
  const description = ref("");
  
  // Example function to handle account upgrade
  const upgradeAccount = () => {
    alert("Account upgraded!");
  };
  </script>
  
  