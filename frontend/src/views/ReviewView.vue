<script setup>
import toast from '@/utils/toast';
import { useRoute } from 'vue-router';
import NavBar from '@/components/NavBar.vue';
import { ref, reactive, computed, onMounted } from 'vue';
import { fetchServiceData, submitReview } from '@/services/review.service';

//fetch serviceId from route state
const route = useRoute();
const serviceId = ref(route.state?.serviceId);

const isLoading = ref(false);
const review = reactive({
  rating: 0,
  comment: '',
  hoveredRating: 0,
});

const serviceData = reactive({
  serviceName: '',
  serviceDescription: '',
  pilotName: '',
  pilotId: null,
})

const setRating = (star) => {
  review.rating = star;
};

const setHoveredRating = (star) => {
  review.hoveredRating = star;
};

const loadServiceData = async (id) => {
  try {
    isLoading.value = true;
    const data = await fetchServiceData(id);

    serviceData.serviceName = data.game
    serviceData.serviceDescription = data.description;
    serviceData.pilotName = data.pilot_name;
    serviceData.pilotId = data.pilot_id;
  } catch (error) {
    toast.error('Failed to load service data.');
  } finally {
    isLoading.value = false;
  }
}

const handleSubmitReview = async () => {
  if(review.rating === 0) {
    toast.error('Please select a rating before submitting.');
    return;
  }

  try {
    const formData = {
      rating: review.rating,
      comment: review.comment,
      service_id: serviceId.value,
      pilot_id: serviceData.pilotId,
    };

    await submitReview(formData);
    toast.success('Review submitted successfully!');
    clearForm();
  } catch {
    toast.error('Failed to submit review.');
  }
}

const clearForm = () => {
  review.rating = 0;
  review.comment = '';
  review.hoveredRating = 0;
}

const isButtonDisabled = computed(() => review.rating === 0);

// Use pilotName's first letter as the initial
const initial = computed(() => {
  return serviceData.pilotName ? serviceData.pilotName.trim().charAt(0).toUpperCase() : '';
});

onMounted(() => {
  if (serviceId.value) {
    loadServiceData(serviceId.value);
  } else {
    console.error("No service ID provided in route state.");
    toast.error("No service ID provided in route state.");
  }
})

</script>

<template>
  <NavBar />
  <div class="min-h-screen w-full flex flex-col items-center justify-center bg-gray-900">
    <div class="card bg-blue-800 bg-opacity-5 border border-gray-700 w-full max-w-md mx-auto mb-20">

      <div class="card-body">
        <div class="card-title">
          <h1 class="text-3xl font-bold text-white">Rate Your <span class="text-green-400">Experience</span></h1>
        </div>

        <div>
          <p class="text-base text-gray-400">Please share your feedback about the service</p>
        </div>

        <div class="flex items-center space-x-4 py-4">
          <div class="flex flex-col w-full">

            <div v-if="isLoading">
              <div class="w-40 h-6 skeleton rounded mb-2"></div>
              <div class="w-60 h-4 skeleton rounded mb-2"></div>
              <div class="w-48 h-4 skeleton rounded"></div>
            </div>

            <div v-else>
              <div class="bg-[#1e293b] w-full px-6 py-5 rounded-md flex flex-col items-start">
                <h3 class="text-xl text-white font-bold break-words">{{ serviceData.serviceName }}</h3>
                <p class="text-gray-400 leading-relaxed break-words">{{ serviceData.serviceDescription }}</p>
              </div>

              <div class="flex items-center space-x-2 mt-6">
                <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 12c2.21 0 4-1.79 4-4S14.21 4 12 4 8 5.79 8 8s1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                </svg>
                <p class="text-base text-gray-300">Pilot</p>
              </div>
              <div class="flex justify-left items-center bg-[#1e293b] w-full px-10 py-5 rounded-md mt-2">
                <div class="-ml-5 w-10 h-10 rounded-full bg-green-900 text-green-400 flex items-center justify-center text-xl font-bold mr-2">
                  {{ initial }}
                </div>
                <span class="text-white text-base font-semibold">
                  {{ serviceData.pilotName }}
                </span>
              </div>

            </div>

          </div>
        </div>

        <div class="space-y-2 pb-4">
          <p class="text-white text-base font-medium">Rating</p>
          <div class="flex justify-center">
            <button v-for="star in [1, 2, 3, 4, 5]" :key="star" @click="setRating(star)"
              @mouseenter="setHoveredRating(star)" @mouseleave="setHoveredRating(0)" class="focus:outline-none">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                class="w-10 h-10 transition-colors"
                :class="star <= (review.hoveredRating || review.rating) ? 'text-green-500' : 'text-gray-700'">
                <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 
              1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 
              7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273
              -4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 
              2.082-5.005Z" clip-rule="evenodd" />
              </svg>
            </button>
          </div>
          <p v-if="review.rating > 0" class="font-semibold text-white text-center text-base text-yellow-300 text-muted-foreground mt-1">
            <span v-if="review.rating === 1">Poor</span>
            <span v-if="review.rating === 2">Fair</span>
            <span v-if="review.rating === 3">Good</span>
            <span v-if="review.rating === 4">Very Good</span>
            <span v-if="review.rating === 5">Excellent</span>
          </p>
        </div>

        <div class="space-y-2 flex flex-col">
          <label for="comment" class="text-white text-base">Your Comments</label>
          <textarea
            v-model="review.comment"
            class="bg-[#1e293b] border border-gray-700 text-base textarea w-full text-white focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500"
            placeholder="Please share your experience with this service..."></textarea>
        </div>
        <p class="text-gray-400">Your feedback helps us improve our service and helps other players make informed decisions.</p>

        <div class="card-actions justify-center mt-5">
          <button @click="handleSubmitReview" class="btn bg-emerald-500 w-full text-white text-base shadow-none border border-green-900 py-6 " :disabled="isButtonDisabled">
            <span v-if="isLoading" class="loading loading-spinner "></span>
            <span v-else>Submit Review</span>
          </button>
        </div>

      </div>
    </div>
  </div>
</template>