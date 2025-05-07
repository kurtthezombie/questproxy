<template>
    <div class="bg-blue-900 bg-opacity-20 p-4 rounded-xl border border-gray-700 hover:border-green-400 transition-all">
      <div class="flex justify-between items-start">
        <h3 class="text-xl font-bold text-white mt-1">
          {{ negotiation.booking?.service?.category?.title || 'Unknown Service' }}
        </h3>
        <div class="mt-2">
          <span
            :class="{
              'bg-yellow-500': negotiation.pilot_status === 'pending',
              'bg-green-500': negotiation.pilot_status === 'approved',
              'bg-red-500': negotiation.pilot_status === 'declined'
            }"
            class="font-bold text-white px-2 py-1 rounded-full text-xs capitalize"
          >
            {{ statusLabel }}
          </span>
        </div>
      </div>
      <p class="text-gray-400 mb-8">
        {{ negotiation.booking?.service?.description || 'No description available' }}
      </p>
      <div class="text-sm bg-gray-700 p-1 text-white w-fit mb-8">
        <p>Negotiation ID : {{ negotiation.id }}</p>
      </div>
      <div class="mt-3 text-gray-300">
        <div class="flex items-center mt-2 w-full">
          <div
            class="w-10 h-10 flex items-center justify-center rounded-full font-semibold text-white mr-2 text-lg uppercase bg-yellow-500">
            {{ negotiation.booking?.client?.username?.charAt(0) || '?' }}
          </div>
          <div class="flex flex-col">
            <span class="text-xs text-gray-400">Client</span>
            <span>{{ negotiation.booking?.client?.username || 'Unknown' }}</span>
          </div>
          <span class="ml-auto text-xs text-gray-400">
            Offer: <span class="text-green-400 font-bold">₱{{ negotiation.negotiable_price }}</span>
          </span>
        </div>
      </div>
      <div class="mt-4 flex gap-2">
        <button
          v-if="isPilot && negotiation.pilot_status === 'pending'"
          class="bg-emerald-500 hover:bg-emerald-600 text-black py-2 px-4 rounded-md transition-colors duration-200"
          @click="handleApprove"
          :disabled="loading"
        >
          Approve
        </button>
        <button
          v-if="isPilot && negotiation.pilot_status === 'pending'"
          class="bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-md transition-colors duration-200"
          @click="handleDecline"
          :disabled="loading"
        >
          Decline
        </button>
        <!-- For gamer, show status or payment button if approved -->
        <button
          v-else-if="!isPilot && negotiation.pilot_status === 'approved'"
          class="bg-emerald-500 hover:bg-emerald-600 text-black py-2 px-4 rounded-md transition-colors duration-200"
          @click="goToPayment"
        >
          Proceed to Payment
        </button>
        <span v-else-if="!isPilot && negotiation.pilot_status === 'pending'" class="text-yellow-400">Waiting for pilot approval...</span>
        <span v-else-if="!isPilot && negotiation.pilot_status === 'declined'" class="text-red-400">Declined by pilot</span>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, computed } from 'vue';
  import { useRouter } from 'vue-router';
  import { approveNegotiation, declineNegotiation } from '@/services/negotiation.service';
  import toast from '@/utils/toast';

  const props = defineProps({
    negotiation: Object,
    isPilot: Boolean, // Pass true if the current user is the pilot
  });

  const emit = defineEmits(['negotiationAccepted', 'negotiationDeclined']);

  const loading = ref(false);
  const router = useRouter();

  const statusLabel = computed(() => {
    switch (props.negotiation.pilot_status) {
      case 'pending': return 'Pending';
      case 'approved': return 'Approved';
      case 'declined': return 'Declined';
      default: return props.negotiation.pilot_status;
    }
  });

  async function handleApprove() {
    loading.value = true;
    try {
      await approveNegotiation(props.negotiation.id);
      toast.success('Negotiation approved!');
      emit('negotiationAccepted', props.negotiation.id);
    } catch (e) {
      toast.error('Failed to approve negotiation.');
    } finally {
      loading.value = false;
    }
  }

  async function handleDecline() {
    loading.value = true;
    try {
      await declineNegotiation(props.negotiation.id);
      toast.success('Negotiation declined.');
      emit('negotiationDeclined', props.negotiation.id);
    } catch (e) {
      toast.error('Failed to decline negotiation.');
    } finally {
      loading.value = false;
    }
  }

  function goToPayment() {
    router.push(`/payment/${props.negotiation.booking_id}`);
  }
  </script>