<template>
  <div 
    class="bg-gray-800 p-4 rounded-lg shadow-lg hover:bg-gray-700 transition-colors duration-300 cursor-pointer"
    @click="goToPayment"
  >
    <h3 class="text-xl font-semibold">{{ getGameTitle }}</h3>
    <p class="text-gray-400">{{ service.description }}</p>

    <div class="flex justify-between items-center mt-4">
      <div class="flex items-center space-x-2">
        <!-- Avatar Circle -->
        <div class="w-8 h-8 bg-gray-600 rounded-full flex items-center justify-center overflow-hidden">
          <img 
            v-if="pilot?.user?.avatar" 
            :src="`/assets/avatarimg/${pilot.user.avatar}`" 
            alt="User Avatar" 
            class="w-full h-full object-cover"
          />
          <span v-else class="text-white text-sm">
            {{ getInitials(pilot?.user?.username || 'Unknown') }}
          </span>
        </div>
        <!-- Username -->
        <span class="text-sm font-medium">{{ pilot?.user?.username || 'Unknown User' }}</span>
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
      <button @click.stop="$router.push(`/services/${service.id}/edit`)"
              class="flex-1 px-4 py-2 bg-blue-500 hover:bg-blue-600 rounded text-white text-sm">
        Edit
      </button>
      <button @click.stop="confirmDelete"
              class="flex-1 px-4 py-2 bg-red-500 hover:bg-red-600 rounded text-white text-sm">
        Delete
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed, watchEffect } from 'vue';
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
  pilots: {
    type: Array,
    default: () => []
  },
  isServiceHistory: {
    type: Boolean,
    default: false
  }
});

const serviceStore = useServiceStore();
const router = useRouter();

// Get game title from the categories list
const getGameTitle = computed(() => {
  const category = props.categories.find(cat => cat.game === props.service.game);
  return category ? category.title : props.service.game;
});

// Get the pilot's details based on the pilot_id
const pilot = computed(() => { 
  const matchingPilot = props.pilots.find(p => p && p.id === props.service.pilot_id); 
  return matchingPilot || null; 
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

const goToPayment = () => {
  router.push({ name: 'PaymentView', params: { serviceId: props.service.id } });
};

const confirmDelete = async () => {
  if (window.confirm('Are you sure you want to delete this service?')) {
    try {
      await serviceStore.deleteService(props.service.id);
      await serviceStore.fetchPilotServices();
    } catch (error) {
      console.error('Error deleting service:', error);
    }
  }
};

const getInitials = (username) => {
  if (!username) return 'U'; 
  const nameParts = username.split(' ');
  return nameParts.map(part => part.charAt(0).toUpperCase()).join('');
};

watchEffect(() => {
  console.log("Service:", props.service);
  console.log("Received pilots prop:", props.pilots);
  console.log("Pilot data:", pilot.value);
  console.log("Username:", pilot.value?.user?.username || 'Unknown User');
});

</script>
