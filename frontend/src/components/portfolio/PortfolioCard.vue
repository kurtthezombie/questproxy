<script setup>
import dayjs from 'dayjs';
import { computed } from 'vue';
import relativeTime from 'dayjs/plugin/relativeTime';

dayjs.extend(relativeTime);

const props = defineProps({
    portfolio: Object,
});

const formattedTime = computed(() => 
    props.portfolio?.created_at ? dayjs(props.portfolio.created_at).fromNow() : "Unknown time"
);
</script>

<template>
    <div class="mt-3 w-3/4 bg-gray-200 p-3 rounded-sm shadow-md hover:scale-105 transition-transform duration-300 ease-in-out cursor-pointer rounded-xl hover:shadow-lg hover:shadow-lime-500">
        <!-- Portfolio Image -->
        <div class="flex justify-center rounded-md">
            <img 
                v-if="portfolio.p_content" 
                :src="portfolio.p_content" 
                class="w-full max-h-60 object-cover rounded-md" 
                alt="Portfolio Image" />
            <span v-else class="text-white font-semibold">No Image</span>
        </div>  
        <hr>
        <!-- Portfolio Caption -->
        <div class="mt-3 text-start text-xl text-gray-800 font-medium">
            {{ portfolio?.caption || "" }}
        </div>
        <div class="mt-1 text-sm text-gray-600">{{  formattedTime  }}</div>
    </div>
</template>
