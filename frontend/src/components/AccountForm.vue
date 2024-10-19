<template>
  <!-- Full screen height and ensure form occupies full width of parent -->
  <div class="min-h-screen bg-white flex justify-center items-start py-8">
    <!-- Form container, now w-full without max-width restrictions -->
    <div class="w-full p-8 bg-white shadow-lg rounded-lg">
      <h2 class="text-2xl font-semibold text-gray-800 mb-6">Personal</h2>

      <form @submit.prevent="saveData">
        <div class="grid gap-6 mb-6 md:grid-cols-2">
          <!-- First Name -->
          <div>
            <label for="first_name" class="block mb-2 text-sm font-medium text-gray-800">First Name</label>
            <input
              type="text"
              id="first_name"
              class="bg-white border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5"
              v-model="firstName"
              required
            />
          </div>

          <!-- Last Name -->
          <div>
            <label for="last_name" class="block mb-2 text-sm font-medium text-gray-800">Last Name</label>
            <input
              type="text"
              id="last_name"
              class="bg-white border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5"
              v-model="lastName"
              required
            />
          </div>

          <!-- Contact Number -->
          <div class="md:col-span-2">
            <label for="contact_number" class="block mb-2 text-sm font-medium text-gray-800">Contact Number</label>
            <input
              type="tel"
              id="contact_number"
              class="bg-white border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5"
              v-model="contactNumber"
              placeholder="123-456-7890"
              required
            />
          </div>
        </div>

        <!-- Save Button -->
        <button
          type="submit"
          class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
        >
          Save
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

// Data properties (using ref)
const firstName = ref('Rice');
const lastName = ref('Rice');
const contactNumber = ref('');

// Method to save data
const saveData = async () => {
  const formData = {
    first_name: firstName.value,
    last_name: lastName.value,
    contact_number: contactNumber.value,
  };

  try {
    // Making an Axios POST request (adjust URL as per your API)
    const response = await axios.post('http://127.0.0.1:8000/api/save-data', formData);
    
    // Log or handle successful response
    console.log('Data saved successfully:', response.data);
    alert('Data saved successfully!');
  } catch (error) {
    // Handle error response
    console.error('Error saving data:', error);
    alert('Failed to save data.');
  }
};
</script>
