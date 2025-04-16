<template>
  <div 
    class="bg-gray-800 p-4 rounded-lg shadow-lg hover:bg-gray-700 transition-colors duration-300 cursor-pointer"
    @click="handleServiceClick"
  >
    <h3 class="text-xl font-semibold text-white">{{ getGameTitle }}</h3>
    <p class="text-gray-400">{{ service.description || 'No description available' }}</p>

    <div class="flex justify-between items-center mt-4">
      <div class="flex items-center">
        <span class="text-sm font-medium text-white">
          {{ usernames[service.user_id] || usernames[service.pilot_id] || 'Unknown User' }}
        </span>
      </div>
      <span class="text-lg font-bold text-green-500">₱{{ formatPrice(service.price) }}</span>
    </div>

    <div class="mt-2 flex justify-between items-center">
      <span class="text-sm text-gray-300">Duration: {{ formatDuration(service.duration) }}</span>
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
import { computed, ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from "vue-toastification";
import axios from 'axios';

const props = defineProps({
  service: { type: Object, required: true },
  categories: { type: Array, required: true },
  isServiceHistory: { type: Boolean, default: false }
});

const usernames = ref({});
const router = useRouter();
const toast = useToast();
const emit = defineEmits(['serviceDeleted']);


onMounted(() => {
  fetchUsername(props.service.user_id, 'user');
  fetchUsername(props.service.pilot_id, 'pilot');
});

const fetchUsername = async (id, type) => {
    if (!id) return;
    try {
        const authToken = localStorage.getItem('authToken');
        if (!authToken) return;

        const endpoint = type === 'user'
            ? `http://127.0.0.1:8000/api/users/${id}`
            : `http://127.0.0.1:8000/api/pilots/${id}`;

        const response = await axios.get(endpoint, {
            headers: { Authorization: `Bearer ${authToken}` }
        });

        const username = response.data?.user?.username || response.data?.pilot?.username || 'Unknown User';
        usernames.value[id] = username;
    } catch (error) {
        usernames.value[id] = 'Unknown User';
    }
};

const getGameTitle = computed(() => {
  const category = props.categories.find(cat => cat.game === props.service.game);
  return category ? category.title : props.service.game || 'Unknown Game';
});

const formatPrice = (price) => {
  return Number(price || 0).toLocaleString('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  });
};

const formatDuration = (duration) => {
  return duration ? `${duration} hours` : 'N/A';
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

const confirmDelete = async () => {
  const confirmed = confirm("Are you sure you want to delete this service?");
  if (!confirmed) return;

  try {
    const authToken = localStorage.getItem('authToken');
    if (!authToken) {
      toast.error("Authentication required.");
      return;
    }

    await axios.delete(`http://127.0.0.1:8000/api/services/destroy/${props.service.id}`, {
      headers: {
        Authorization: `Bearer ${authToken}`
      }
    });

    toast.success("Service deleted successfully.");
    emit('serviceDeleted', props.service.id);
  } catch (error) {
    console.error(error);
    toast.error("Failed to delete service.");
  }
};


</script>
