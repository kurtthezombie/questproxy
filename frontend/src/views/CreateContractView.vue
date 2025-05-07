<script setup>
import { ref, reactive, onMounted } from "vue";
import DisplayField from "@/components/agreement/DisplayField.vue";
import { createBooking, fetchData, fetchPaymentUrl, generatePDF } from "@/services/agreement.service"
import toast from "@/utils/toast";
import { jsPDF } from "jspdf"
import dayjs from "dayjs";
import { useRoute, useRouter } from 'vue-router';
import { useLoader } from '@/services/loader-service';
import api from '@/utils/api';
import axios from 'axios';
import { sendNegotiation } from '@/services/negotiation.service';

import { qpLogoBase64 } from '@/assets/img/logoData.js';


const router = useRouter();
const details = ref(null);
const route = useRoute();
const serviceId = route.params?.serviceId;
const bookingId = ref(null);
const isSubmitted = ref(false);
const { loadShow, loadHide } = useLoader();
const negotiablePrice = ref(null);
const pilotResponse = ref(null); // 'approved', 'declined', or null
const showNegotiation = ref(false);

const form = reactive({
  startDate: '',
  commLink: '',
  additional_notes: '',
  serviceId: serviceId,
  negotiablePrice: null,
});

const today = new Date().toISOString().split('T')[0];
const nextYear = new Date();
nextYear.setFullYear(nextYear.getFullYear() + 1);
const maxDate = nextYear.toISOString().split('T')[0];
const currentDate = dayjs().format('MMMM D,YYYY');

const fetchAgreementData = async () => {
  try {
    const data = await fetchData(serviceId);
    details.value = data;
  } catch (error) {
    toast.error("Failed to fetch agreement data.");
  }
};

const handleSubmit = async () => {
  const formData = {
    start_date: form.startDate,
    communication_link: form.commLink,
    additional_notes: form.additional_notes,
    service_id: form.serviceId,
    negotiable_price: form.negotiablePrice,
  };

  try {
    if (form.negotiablePrice > 0) {
      // Negotiation path
      // 1. Create a booking first (if not already created)
      const booking = await createBooking(formData);
      bookingId.value = booking.id;
      isSubmitted.value = true;

      // 2. Send negotiation
      await sendNegotiation(booking.id, form.negotiablePrice);

      toast.success("Agreement submitted with negotiation. Waiting for pilot approval.");
      setTimeout(() => {
        router.push('/home');
      }, 3000);
    } else {
      // No negotiation path
      const booking = await createBooking(formData);
      bookingId.value = booking.id;
      isSubmitted.value = true;

      toast.success("Agreement submitted successfully!");
      downloadModal.showModal();
    }
  } catch (error) {
    toast.error("Failed to submit agreement.");
  }
};

// This function is a placeholder for handling incoming notifications
// It would be called by your actual notification system when a pilot's decision is received.
const handlePilotResponseNotification = (bookingId, status) => {
  console.log(`Pilot response received for booking ID: ${bookingId}, Status: ${status}`);
  // You would likely need to check if this notification is relevant to the *currently viewed* booking
  // if the user is on the ContractView page when the notification arrives.
  // However, since we redirect to /home after negotiation, this function
  // is more likely to be triggered on a different page (like a dashboard).

  if (status === 'approved') {
    // If the user is on a page that lists bookings, update the status for this booking
    // If the user is redirected back to this specific ContractView page after approval,
    // you would need logic here to show the modal.
    pilotResponse.value = 'approved'; // Update state to potentially show payment button
    toast.success("Pilot approved your agreement! Proceed to payment.");
    // Assuming the user is on a page where this modal exists and is relevant
    document.getElementById('approvalNotificationModal').showModal();
  } else if (status === 'declined') {
    pilotResponse.value = 'declined';
    toast.error("Pilot declined your agreement.");
    // On a dashboard/list page, update the booking status to 'declined'
    // If the user is somehow back on this page, you might display a decline message here.
  }
};


const handleGoBack = async () => {
  console.log('BOOKING ID:',bookingId.value);
  try {
    if(isSubmitted.value && bookingId.value){
      const token = localStorage.getItem('authToken');

      const response = await axios.delete(`${import.meta.env.VITE_BACKEND_URL}api/bookings/${bookingId.value}`, {
        headers: {
          Authorization: `Bearer ${token}`
        }
      });
      toast.success('Booking cancelled..', response.data);
    }

    router.push({ name: 'PaymentView', params: { serviceId } });

  } catch (error) {
    console.error('Error handling go back:', error);
    router.push({ name: 'PaymentView', params: { serviceId } });
    toast.error('Failed to cancel booking, but navigating back.');
  }
}


const proceedToPayment = async () => {
  const loader = loadShow();

  const cancelUrl = `${import.meta.env.VITE_FRONTEND_URL}contract/${serviceId}`;
  console.log('I got into payment');
  try {
    const url = await fetchPaymentUrl(cancelUrl, bookingId.value);

    if (!url) {
      throw new Error("No payment URL received from server");
    }

    window.location.href = url;
  } catch (error) {
    toast.error("Failed to proceed to payment.");
  } finally {
    loadHide(loader);
  }
}

const generateContractPDF = async () => {
  if (!details.value || !bookingId.value) {
    toast.error("Data not loaded yet!");
    return;
  }
  await generatePDF(serviceId, bookingId.value, {
    commLink: form.commLink,
    additional_notes: form.additional_notes,
    start_date: form.startDate,
    negotiablePrice: form.negotiablePrice,
  });
  downloadModal.close();
};

onMounted(() => {
  fetchAgreementData();
})

</script>

<template>
  <div class="bg-gray-900 min-h-screen flex flex-col items-center justify-center text-white py-5">
    <div class="card bg-gray-700 bg-opacity-20 border border-gray-700 p-6 sm:w-2/3 md:w-2/5">

      <div class="card-body flex-col justify-center items-center">
        <div class="w-full flex items-center justify-between mb-6">
          <button class="btn border-none shadow-none bg-transparent text-white btn-lg p-0" @click="handleGoBack">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
          </button>
          <h1 class="text-2xl md:text-4xl font-bold text-center flex-1">Game Service Agreement</h1>
        </div>

        <p class="text-base text-gray-400 mb-10 -mt-8">Fill in the details to create a new gaming service contract</p>
        <div class="flex flex-col w-full">
          <h3 class="text-lg font-semibold mb-4 border-b border-green-300 pb-2">Service Details</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2 w-full mb-5" >
          <DisplayField label="Service Title" :value="details?.description" />
          <DisplayField label="Game" :value="details?.category_title" />
          <DisplayField label="Pilot Username" :value="details?.pilot_username" />
          <DisplayField label="Client Username" :value="details?.client_username" />
           <DisplayField label="Original Price" :value="`₱${details?.price}`" />
          <DisplayField label="Expected Duration"
            :value="`${details?.duration} ${details?.duration === 1 ? 'day' : 'days'}`" />
        </div>
        <form @submit.prevent="handleSubmit" class="w-full">
           <div class="flex flex-col w-full">

            <button
              type="button"
              class="mb-4 bg-emerald-500 hover:bg-emerald-600 text-black py-2 px-4 rounded-md transition-colors duration-200 w-fit"
              @click="() => { showNegotiation = !showNegotiation; if (!showNegotiation) form.negotiablePrice = null; }"
            >
              {{ showNegotiation ? 'Cancel Negotiation' : 'Negotiate Price' }}
            </button>

            <div v-if="showNegotiation" class="mb-4">
              <label for="negotiablePrice" class="block text-sm font-medium mb-1">
                Your Offer
              </label>
              <input
                type="number"
                v-model.number="form.negotiablePrice"
                class="input p-3 bg-blue-600 bg-opacity-20 w-full rounded-md focus:ring-green-500 focus:border-green-500"
                placeholder="Enter your proposed price"
                min="1"
              />
              <p class="text-xs text-yellow-400 mt-2">
                Please negotiate reasonably. Excessive bargaining may result in your request being declined.
              </p>
            </div>
          </div>

          <div class="flex flex-col w-full">
            <h3 class="text-lg font-semibold mb-4 border-b border-green-300 pb-2">Schedule</h3>
            <div class="mb-4">
              <label htmlFor="startDate" class="block text-sm font-medium mb-1">
                Start Date <span className="text-red-500">*</span>
              </label>
              <input type="date" v-model="form.startDate"
                class="input p-3 bg-blue-600 bg-opacity-20 w-full rounded-md focus:ring-green-500 focus:border-green-500"
                :min="today" :max="maxDate" required />
            </div>
          </div>

          <div class="flex flex-col w-full">
            <h3 class="text-lg font-semibold mb-4 border-b border-green-300 pb-2">Communication Link</h3>
            <div class="mb-4 w-full">
              <label htmlFor="comm-link" class="block text-sm font-medium mb-1">
                Link <span className="text-red-500">*</span>
              </label>
              <input type="text" v-model="form.commLink"
                class=" mb-5 input w-full p-3 h-12 border bg-[#1E293B] rounded-md focus:ring-green-500 focus:border-green-500 shadow-none"
                placeholder="Provide a link for coordination (e.g. Discord, tlk.io)" required />
            </div>
          </div>

          <div class="flex flex-col w-full">
            <h3 class="text-lg font-semibold mb-4 border-b border-green-300 pb-2">Additional Information</h3>
            <div class="mb-4">
              <label htmlFor="additional_notes" class="block text-sm font-medium mb-1">
                Instructions & Notes (Optional)
              </label>
              <textarea rows="4" v-model="form.additional_notes"
                class="textarea w-full p-3 border bg-[#1E293B] rounded-md focus:ring-green-500 focus:border-green-500 shadow-none"
                placeholder="Add any specific instructions or notes for the service provider..."></textarea>
            </div>
          </div>

          <div class="flex justify-center w-full mt-8 space-x-4">
            <button v-if="isSubmitted && (form.negotiablePrice <= 0 || pilotResponse === 'approved')"
                    class="btn btn-soft btn-primary sm:w-auto"
                    @click="proceedToPayment"
                    type="button">
              Proceed to Payment
            </button>

            <button :disabled="isSubmitted" class="btn bg-green-500 h-12 border-none sm:w-auto shadow-none text-white" type="submit">
              {{ isSubmitted ? 'Agreement Submitted' : 'Submit Agreement' }}
            </button>
          </div>
        </form>
      </div>
    </div>
    <dialog id="downloadModal" class="modal">
      <div class="modal-box bg-gray-800">
        <h3 class="text-lg font-bold">Agreement Submitted</h3>
        <p class="py-4 ">Would you like to download a pdf version of the agreement?</p>
        <div class="modal-action">
          <form method="dialog" class="space-x-2">
            <button class="btn btn-error">Close</button>
            <button class="btn btn-success" @click="generateContractPDF">Download</button>
          </form>
        </div>
      </div>
    </dialog>

    <dialog id="approvalNotificationModal" class="modal">
      <div class="modal-box bg-gray-800">
        <h3 class="text-lg font-bold">Agreement Approved!</h3>
        <p class="py-4">The pilot has approved your agreement. You can now proceed to payment.</p>
        <div class="modal-action">
          <form method="dialog" class="space-x-2">
            <button class="btn btn-error">Close</button>
            <button class="btn btn-success" @click="proceedToPayment">Proceed to Payment</button>
          </form>
        </div>
      </div>
    </dialog>
  </div>
</template>
