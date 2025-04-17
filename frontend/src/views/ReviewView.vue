<script setup>
import NavBar from '@/components/NavBar.vue';
import { fetchServiceData } from '@/services/review.service';
import toast from '@/utils/toast';
import { ref, reactive, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';

//fetch serviceId from route state
const route = useRoute();
// const serviceId = ref(route.state?.serviceId);
const serviceId = ref(7);
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
    console.log(data);

    serviceData.serviceName = data.game
    serviceData.serviceDescription = data.description;
    serviceData.pilotName = data.pilot_name;
  } catch (error) {
    toast.error('Failed to load service data.');
  } finally {
    isLoading.value = false;
  }
}

const handleSubmitReview = () => {
  console.log('Rating: ', review.rating);
  console.log('Comment: ', review.comment);
}

const isButtonDisabled = computed(() => review.rating === 0 && review.comment.trim() === '');

onMounted(() => {
  if (serviceId.value) {
    loadServiceData(serviceId.value);
  } else {
    console.error("No service ID provided in route state.");
  }
})

</script>

<template>
  <NavBar />
  <div class="min-h-screen w-full flex flex-col items-center justify-center bg-gray-900">
    <div class="card bg-white w-1/4">

      <div class="card-body">

        <div class="card-title">
          <h1 class="text-2xl font-semibold">Rate Your <span class="text-green-600">Experience</span></h1>
        </div>

        <div>
          <p class="text-base text-gray-600">Please share your feedback about the service</p>
        </div>

        <div class="flex items-center space-x-4 py-4">
          <div class="flex flex-col">

            <div v-if="isLoading">
              <div class="w-40 h-6 skeleton rounded mb-2"></div>
              <div class="w-60 h-4 skeleton rounded mb-2"></div>
              <div class="w-48 h-4 skeleton rounded"></div>
            </div>

            <div v-else>
              <h3 class="text-lg font-semibold">{{ serviceData.serviceName }}</h3>
              <p class="text-sm text-gray-600">{{ serviceData.serviceDescription }}</p>
              <p class="text-base font-medium mt-1">
                Pilot: <span class="text-green-700">{{ serviceData.pilotName}}</span>
              </p>
            </div>

          </div>
        </div>

        <div class="space-y-2 pb-4">
          <p class="text-sm font-medium">Rating</p>
          <div class="flex space-x-1">
            <button v-for="star in [1, 2, 3, 4, 5]" :key="star" @click="setRating(star)"
              @mouseenter="setHoveredRating(star)" @mouseleave="setHoveredRating(0)" class="focus:outline-none">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                class="w-8 h-8 transition-colors"
                :class="star <= (review.hoveredRating || review.rating) ? 'text-green-400' : 'text-gray-300'">
                <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 
              1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 
              7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273
              -4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 
              2.082-5.005Z" clip-rule="evenodd" />
              </svg>
            </button>
          </div>
          <p v-if="review.rating > 0" class="text-sm text-muted-foreground mt-1">
            <span v-if="review.rating === 1">Poor</span>
            <span v-if="review.rating === 2">Fair</span>
            <span v-if="review.rating === 3">Good</span>
            <span v-if="review.rating === 4">Very Good</span>
            <span v-if="review.rating === 5">Excellent</span>
          </p>
        </div>

        <div class="space-y-2 flex flex-col">
          <label for="comment" class="text-sm font-medium">Your Comments</label>
          <textarea v-model="review.comment" class="textarea w-full"
            placeholder="Please share your experience with this service..."></textarea>
        </div>

        <div class="card-actions justify-center mt-2">
          <button @click="handleSubmitReview" class="btn bg-green-300 w-full" :disabled="isButtonDisabled">Submit
            Review</button>
        </div>

      </div>
    </div>
  </div>
</template>