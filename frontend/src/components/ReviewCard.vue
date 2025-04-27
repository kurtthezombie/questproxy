<script setup>
import { computed } from 'vue';
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime'; // Optional: for 'time ago' format

dayjs.extend(relativeTime);

// Define props that the ReviewCard will accept
const props = defineProps({
  review: {
    type: Object,
    required: true,
    default: () => ({
      id: null,
      rating: 0,
      comment: 'No comment provided.',
      created_at: new Date().toISOString(),
      // *** Assumes the backend now includes the 'user' object for the reviewer ***
      user: { // This object comes from the 'with('user')' in the backend query
        id: null,
        username: 'Anonymous',
        // avatar_url: null // Optional avatar if you add it later
      },
      // service: { // You might also want service info later
      //   id: null,
      //   title: 'Service Title'
      // }
    })
  }
});

// Computed property for formatted date
const formattedDate = computed(() => {
  if (!props.review.created_at) return 'Date unavailable';
  // return dayjs(props.review.created_at).fromNow(); // 'time ago' format
  return dayjs(props.review.created_at).format('MMMM D, YYYY'); // 'Month Day, Year' format
});

// Computed property for star icons
const ratingStars = computed(() => {
  const rating = Math.round(props.review.rating || 0);
  const maxStars = 5;
  const filledStars = '★'.repeat(rating);
  const emptyStars = '☆'.repeat(maxStars - rating);
  return filledStars + emptyStars;
});

// Get reviewer's username from the nested 'user' object
const reviewerUsername = computed(() => {
    return props.review.user?.username || 'Unknown Reviewer';
});

// Get reviewer's initials from the nested 'user' object
const reviewerInitials = computed(() => {
    return props.review.user?.username?.charAt(0).toUpperCase() || '?';
});

</script>

<template>
  <div class="bg-gray-800 border border-gray-700 rounded-lg shadow p-4 md:p-5 space-y-3 hover:bg-gray-750 transition-colors duration-200">
    <div class="flex items-center justify-between gap-4">
      <div class="flex items-center space-x-3 overflow-hidden">
         <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-sm font-semibold text-white flex-shrink-0" :title="reviewerUsername">
            {{ reviewerInitials }}
         </div>
         <div class="overflow-hidden">
            <p class="font-semibold text-white truncate" :title="reviewerUsername">
                {{ reviewerUsername }}
            </p>
            <p class="text-xs text-gray-400">
                Reviewed {{ formattedDate }}
            </p>
         </div>
      </div>
      <div class="text-yellow-400 text-lg flex-shrink-0" :title="`Rating: ${review.rating || 0}/5`">
        {{ ratingStars }}
      </div>
    </div>

    <p v-if="review.comment" class="text-gray-300 text-sm leading-relaxed pt-3 border-t border-gray-700/50">
      {{ review.comment }}
    </p>
     <p v-else class="text-gray-500 text-sm italic pt-3 border-t border-gray-700/50">
       No comment provided.
     </p>
  </div>
</template>

<style scoped>
.bg-gray-750 {
  background-color: rgba(55, 65, 81, 0.6);
}
/* Ensure long usernames don't break layout */
.truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
</style>
