<script setup>
import { ref, reactive, onMounted } from "vue";
import DisplayField from "@/components/agreement/DisplayField.vue";
import NavBar from "@/components/NavBar.vue";
import { fetchData } from "@/services/agreement.service"
import toast from "@/utils/toast";
import { jsPDF } from "jspdf"
import dayjs from "dayjs";

const details = ref(null);

const form = reactive({
  startDate: '',
  commLink: '',
  instructions: '',
});

const today = new Date().toISOString().split('T')[0]; 
const currentDate = dayjs().format('MMMM D, YYYY'); // Example: 'April 19, 2025'

const fetchAgreementData = async () => {
  try {
    const data = await fetchData();
    details.value = data;
    console.log("Fetched details: ", details.value);
  } catch (error) {
    toast.error("Failed to fetch agreement data.");
  }
};

const handleSubmit = () => {
  console.log({...form});
  downloadModal.showModal();
};

const generatePDF = () => {
  if (!details.value) {
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
Booking ID: #1023
Agreement Date: ${currentDate}

1. Description of Service
The Pilot agrees to perform a ${details.value.description} for the client in ${details.value.category_title}. The expected duration for completion is ${details.value.duration} day(s) from the agreed start date.

2. Communication & Coordination
The client has provided the following communication link for coordination with the pilot:

Chat Link: ${form.commLink}

The pilot agrees to use this communication link solely for the purpose of fulfilling this service and to maintain professionalism throughout the communication process.

3. Additional Instructions
${form.instructions}

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
  doc.save("game_service_agreement.pdf")

  downloadModal.close();
}

onMounted(() => {
  fetchAgreementData();
})

</script>

<template>
  <div class="bg-gray-900 min-h-screen flex flex-col items-center justify-center text-white py-5">
    <div class="card bg-gray-700 bg-opacity-20 p-6 w-2/5">

      <div class="card-body flex-col justify-center items-center">

        <div class="card-title">
          <h1 class="text-2xl font-bold">Game Service Agreement</h1>
        </div>

        <div class="flex flex-col w-full">
          <h3 class="text-lg font-semibold mb-4 border-b border-green-300 pb-2">Service Details</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 w-full" >
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
                Start Date*
              </label>
              <input type="date" v-model="form.startDate"
                class="input p-3 bg-blue-600 bg-opacity-20 w-full rounded-md focus:ring-green-500 focus:border-green-500"
                :min="today" required />
            </div>
          </div>

          <div class="flex flex-col w-full">
            <h3 class="text-lg font-semibold mb-4 border-b border-green-300 pb-2">Communication Link</h3>
            <div class="mb-4 w-full">
              <label htmlFor="comm-link" class="block text-sm font-medium mb-1">
                Link *
              </label>
              <input type="text" v-model="form.commLink"
                class="input w-full p-3 border bg-blue-600 bg-opacity-20 rounded-md focus:ring-green-500 focus:border-green-500"
                placeholder="Provide a link for coordination (e.g. Discord, tlk.io)" required />
            </div>
          </div>

          <div class="flex flex-col w-full">
            <h3 class="text-lg font-semibold mb-4 border-b border-green-300 pb-2">Additional Information</h3>
            <div class="mb-4">
              <label htmlFor="instructions" class="block text-sm font-medium mb-1">
                Instructions & Notes (Optional)
              </label>
              <textarea rows="4" v-model="form.instructions"
                class="textarea w-full p-3 border bg-blue-600 bg-opacity-20 rounded-md focus:ring-green-500 focus:border-green-500"
                placeholder="Add any specific instructions or notes for the service provider..."></textarea>
            </div>
          </div>

          <div class="flex justify-end w-full">
            <button class="btn btn-success" @click="handleSubmit">Submit Agreement</button>
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
