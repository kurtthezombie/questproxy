<template>
  <div class="bg-gray-800 p-4 rounded-lg shadow-lg hover:bg-green-500 transition-colors duration-300">
    <h3 class="text-xl font-semibold">{{ service.title }}</h3>
    <p class="text-gray-400">{{ service.description }}</p>
    <div class="flex justify-between items-center mt-4">
      <div class="flex items-center">
        <span class="text-sm font-medium">{{ service.username }}</span>
      </div>
      <span class="text-lg font-bold text-green-500">{{ service.price | currency }}</span>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  service: {
    type: Object,
    required: true,
  }
});

</script>