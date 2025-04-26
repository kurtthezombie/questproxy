<template>
  <div class="max-w-sm mx-auto bg-gray-800 rounded-lg overflow-hidden shadow-lg hover:scale-105 transition-transform duration-200 hover:cursor-pointer">
    <div class="p-5">
      <h3 class="text-2xl font-semibold text-white">{{ serviceTitle }}</h3>
      <p class="mt-4 inline-block bg-gray-700 text-green-300 text-sm font-semibold rounded-full px-3 py-1">{{ gameName }}</p>
      <div class="flex items-center mt-4">
        <div class="bg-green-500 w-12 h-12 rounded-full flex items-center justify-center text-white font-bold">
          {{ pilotName.charAt(0).toUpperCase() }}
        </div>
        <div class="ml-3 text-white">
          <p class="font-medium">{{ pilotName }}</p>
          <p class="text-sm text-gray-400">Pilot</p>
        </div>
      </div>
      <div class="mt-5">
        <p class="text-sm text-gray-400">Status: <span :class="statusClass" class="w-fit p-0.5 px-2 rounded-xl text-black">{{ status }}</span></p>
        <p class="text-sm text-gray-400">Booked On: {{ bookedOn }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";

// Props passed from the parent component
const props = defineProps({
  serviceTitle: String,
  gameName: String,
  pilotName: String,
  status: String,
  bookedOn: String,
});

// Computed property for dynamic status class
const statusClass = computed(() => {
  return props.status === "completed" ? "bg-green-500" : "bg-yellow-500";
});
</script>