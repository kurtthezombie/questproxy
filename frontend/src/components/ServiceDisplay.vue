<template>
  <div class="bg-blue-900 bg-opacity-20 p-5 rounded-xl shadow-lg border border-gray-700 group hover:border-green-400 hover:-translate-y-2 transition-all duration-300 cursor-pointer w-[350px]"
    @click="handleServiceClick">
    <div class="flex justify-between items-center mb-2">
      <h3 class="text-2xl font-bold text-white">{{ getGameTitle }}</h3>
      <span
        class="text-xs px-2 py-1 rounded-2xl"
        :class="service.availability ? 'text-white bg-emerald-500 font-semibold' : 'bg-red-500 text-white'"
      >
        {{ service.availability ? 'Available' : 'Not Available' }}
      </span>
    </div>
    <p class="text-gray-300 -mt-2">{{ service.description || 'No description available' }}</p>
    <div class="mt-2 flex justify-between items-center">
      <div class="flex flex-col text-gray-300">
        <div class="flex items-center mb-1 mt-4">
          <svg class="w-5 h-5 text-gray-300 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m5-2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span class="">{{ service.duration }} {{ service.duration === 1 ? 'day' : 'days' }}</span>
        </div>
      </div>
    </div>
    <div class="flex justify-between items-center mt-4">
      <div class="flex items-center">
        <div class="w-7 h-7 rounded-full bg-green-900 flex items-center justify-center text-green-400 text-lg font-semibold mr-1">
          {{ initial }}
        </div>
        <span class="font-medium font-semibold text-gray-300">
          {{ service.pilot_username || 'Unknown User' }}
        </span>
      </div>
      <span class="text-2xl font-bold text-green-400">₱{{ formatPrice(service.price) }}</span>
    </div>
    <div class="mt-4 flex gap-2" v-if="isServiceHistory">
      <button @click.stop="$router.push(`/services/${service.id}/edit`)"
              class="flex-1 px-4 py-2 bg-emerald-500 hover:bg-emerald-600 rounded text-sm">
        Edit
      </button>
      <button @click.stop="openDeleteModal"
              class="flex-1 px-4 py-2 bg-red-500 hover:bg-red-600 rounded text-white text-sm">
        Delete
      </button>
    </div>
  </div>

  <div v-if="isDeleteModalOpen" class="fixed inset-0 bg-black bg-opacity-60 flex justify-center items-center z-50">
    <div class="bg-[#1E293B] rounded-lg p-6 w-full max-w-md text-white border border-gray-700">
      <h3 class="text-lg font-semibold mb-4">Confirm Service Deletion</h3>
      <p class="text-sm mb-6">Are you sure you want to delete this service? This action cannot be undone.</p>
      <div class="flex justify-end gap-4">
        <button @click="handleConfirmDelete" class="px-4 py-2 bg-red-600 rounded hover:bg-red-700">Yes</button>
        <button @click="closeDeleteModal" class="px-3 py-2 bg-gray-600 rounded hover:bg-gray-700">Cancel</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from "vue-toastification";
import axios from 'axios';

const props = defineProps({
  service: { type: Object, required: true },
  categories: { type: Array, required: true },
  isServiceHistory: { type: Boolean, default: false }
});

const router = useRouter();
const toast = useToast();
const emit = defineEmits(['serviceDeleted']);

const isDeleteModalOpen = ref(false);
const serviceToDelete = ref(null);

const normalizeGameName = (name) => {
  if (!name) return '';
  return name.toLowerCase().replace(/_/g, ' ');
};

const getGameTitle = computed(() => {
  if (!props.service || !props.service.game || !props.categories) {
    return props.service?.game || 'Unknown Game';
  }

  const normalizedServiceGame = normalizeGameName(props.service.game);

  const category = props.categories.find(cat =>
    normalizeGameName(cat.game) === normalizedServiceGame
  );

  return category ? category.title : props.service.game || 'Unknown Game';
});


const formatPrice = (price) => {
  const num = Number(price || 0);
  return Number.isInteger(num) ? num.toLocaleString() : num.toFixed(2);
};

const handleServiceClick = () => {
  if (!props.service.availability) {
    toast.warning("This service is currently not accepting bookings.");
    return;
  }

  router.push({
    name: 'PaymentView',
    params: { serviceId: props.service.id },
    state: { service: props.service }
  });
};

const openDeleteModal = () => {
  serviceToDelete.value = props.service;
  isDeleteModalOpen.value = true;
};

const closeDeleteModal = () => {
  isDeleteModalOpen.value = false;
  serviceToDelete.value = null;
};

const handleConfirmDelete = async () => {
  if (!serviceToDelete.value) return; 

  try {
    const authToken = localStorage.getItem('authToken');
    if (!authToken) {
      toast.error("Authentication required.");
      return;
    }

    await axios.delete(`http://127.0.0.1:8000/api/services/destroy/${serviceToDelete.value.id}`, {
      headers: {
        Authorization: `Bearer ${authToken}`
      }
    });

    toast.success("Service deleted successfully.");
    emit('serviceDeleted', serviceToDelete.value.id);

  } catch (error) {
    console.error(error);
    toast.error("Failed to delete service.");
  } finally {
    closeDeleteModal();
  }
};


const initial = computed(() => {
  if (!props.service.pilot_username) return '?';
  return props.service.pilot_username.charAt(0).toUpperCase();
});


</script>
