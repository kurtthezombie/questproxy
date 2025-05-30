<script setup>
import { ref, reactive, onMounted } from "vue";
import DisplayField from "@/components/agreement/DisplayField.vue";
import { createBooking, fetchData, fetchPaymentUrl } from "@/services/agreement.service"
import toast from "@/utils/toast";
import { jsPDF } from "jspdf"
import dayjs from "dayjs";
import { useRoute, useRouter } from 'vue-router';
import { useLoader } from '@/services/loader-service';
import api from '@/utils/api';
import axios from 'axios';


const router = useRouter();
const details = ref(null);
const route = useRoute();
const serviceId = route.params?.serviceId;
const bookingId = ref(null);
const isSubmitted = ref(false);
const { loadShow, loadHide } = useLoader();

const form = reactive({
  startDate: '',
  commLink: '',
  additional_notes: '',
  serviceId: serviceId,
});

const today = new Date().toISOString().split('T')[0]; 
const nextYear = new Date();
nextYear.setFullYear(nextYear.getFullYear() + 1);
const maxDate = nextYear.toISOString().split('T')[0];
const currentDate = dayjs().format('MMMM D, YYYY');

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
  };

  try {
    const booking = await createBooking(formData);

    if (booking) {
      bookingId.value = booking.id;
      isSubmitted.value = true;
      toast.success("Agreement submitted successfully!");
      downloadModal.showModal();
    }

  } catch (error) {
    toast.error("Failed to submit agreement.");
  }
};

const handleGoBack = async () => {
  console.log('BOOKING ID:',bookingId.value);
  try {
    if(isSubmitted.value){
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
    console.error('Error deleting booking:', error);
    toast.error('Failed to delete booking');
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

const generatePDF = () => {
  if (!details.value || !bookingId.value) {
    toast.error("Data not loaded yet!");
    return;
  }

  const doc = new jsPDF();
  const contractText = `
Game Service Agreement

Service Title: ${details.value.description}
Game: ${details.value.category_title}
Pilot: ${details.value.pilot_username}
Client: ${details.value.client_username}
Booking ID: bk-${bookingId.value}
Agreement Date: ${currentDate}

1. Description of Service
The Pilot agrees to perform a ${details.value.description} for the client in ${details.value.category_title}. The expected duration for completion is ${details.value.duration} day(s) from the agreed start date: ${dayjs(form.startDate).format('MMMM D, YYYY')}.

2. Communication & Coordination
The client has provided the following communication link for coordination with the pilot:

Chat Link: ${form.commLink}

The pilot agrees to use this communication link solely for the purpose of fulfilling this service and to maintain professionalism throughout the communication process.

3. Additional Instructions
${form.additional_notes}

4. Payment Terms
The client has agreed to pay ${details.value.price} for the service, to be processed through the platform.

5. Confidentiality & Security
The pilot shall not disclose or share any of the client’s account information with any third party. The client understands and accepts the risks of account sharing as outlined in the platform’s policy.

6. Completion
The service is considered complete once the service is achieved or after the duration has passed with significant progress. Upon completion, this agreement will be archived.

Date Agreed: ${currentDate}
  `

  const lines = doc.splitTextToSize(contractText, 180)
  doc.setFont("Times", "Normal")
  doc.setFontSize(12)
  doc.text(lines, 10, 10)
  doc.save(`client_bk-${bookingId.value}_service_agreement.pdf`);

  downloadModal.close();
}

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
          <DisplayField label="Price" :value="`₱${details?.price}`" />
          <DisplayField label="Expected Duration"
            :value="`${details?.duration} ${details?.duration === 1 ? 'day' : 'days'}`" />
        </div>
        <form @submit.prevent="handleSubmit" class="w-full">
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

          <div class="flex justify-center w-full mt-8">
            <!-- Proceed to Payment Button -->
            <button v-if="isSubmitted" class="btn btn-soft btn-primary sm:w-auto" @click="proceedToPayment" type="button">
              Proceed to Payment
            </button>

            <!-- Submit Agreement Button (Disabled after submission) -->
            <button :disabled="isSubmitted" class="btn bg-green-500 border-none sm:w-auto shadow-none text-white" type="submit">
              {{ isSubmitted ? 'Agreement Submitted' : 'Submit Agreement' }}
            </button>

          </div>
        </form>
      </div>
    </div>
    <!-- Download Modal -->
    <dialog id="downloadModal" class="modal">
      <div class="modal-box bg-gray-800">
        <h3 class="text-lg font-bold">Agreement Submitted</h3>
        <p class="py-4 ">Would you like to download a pdf version of the agreement?</p>
        <div class="modal-action">
          <form method="dialog" class="space-x-2">
            <button class="btn btn-error">Close</button>
            <button class="btn btn-success" @click="generatePDF">Download</button>
          </form>
        </div>
      </div>
    </dialog>
  </div>
</template>
