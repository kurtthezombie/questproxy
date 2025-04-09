<template>
  <div class="bg-gray-800 p-4 rounded-lg shadow-lg hover:bg-gray-700 transition-colors duration-300">
    <h3 class="text-xl font-semibold">{{ getGameTitle }}</h3>
    <p class="text-gray-400">{{ service.description }}</p>
    <div class="flex justify-between items-center mt-4">
      <div class="flex items-center">
        <span class="text-sm font-medium">{{ service.user?.username || 'Unknown User' }}</span>
      </div>
      <span class="text-lg font-bold text-green-500">₱{{ formatPrice(service.price) }}</span>
    </div>
    <div class="mt-2 flex justify-between items-center">
      <span class="text-sm">Duration: {{ formatDuration(service.duration) }}</span>
      <span class="text-sm px-2 py-1 rounded" 
            :class="service.availability ? 'bg-green-500' : 'bg-red-500'">
        {{ service.availability ? 'Available' : 'Not Available' }}
      </span>
    </div>

    <div class="mt-4 flex gap-2" v-if="isServiceHistory">
      <button @click="$router.push(`/services/${service.id}/edit`)"
              class="flex-1 px-4 py-2 bg-blue-500 hover:bg-blue-600 rounded text-white text-sm">
        Edit
      </button>
      <button @click="confirmDelete"
              class="flex-1 px-4 py-2 bg-red-500 hover:bg-red-600 rounded text-white text-sm">
        Delete
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useServiceStore } from '@/stores/serviceStore';
import { useRouter } from 'vue-router';

const props = defineProps({
  service: {
    type: Object,
    required: true
  },
  categories: {
    type: Array,
    required: true
  },
  isServiceHistory: {
    type: Boolean,
    default: false
  }
});

const serviceStore = useServiceStore();
const router = useRouter();

const getGameTitle = computed(() => {
  const category = props.categories.find(cat => cat.game === props.service.game);
  return category ? category.title : props.service.game;
});

const formatPrice = (price) => {
  return Number(price).toLocaleString('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  });
};

const formatDuration = (duration) => {
  if (!duration) return 'Not specified';
  const date = new Date(duration);
  return date.toLocaleString();
};

const confirmDelete = async () => {
  if (window.confirm('Are you sure you want to delete this service?')) {
    try {
      await serviceStore.deleteService(props.service.id);
      // Refresh the services list after deletion
      await serviceStore.fetchPilotServices();
    } catch (error) {
      console.error('Error deleting service:', error);
    }
  }
};
</script>