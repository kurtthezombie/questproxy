<script setup>
defineProps({
    portfolio: Object,
});
</script>

<template>
    <div class="mt-3 w-3/4 bg-gray-200 p-3 rounded-lg shadow-md">
        <!-- Portfolio Image -->
        <div class="flex justify-center bg-red-400 p-5 rounded-md">
            <img 
                v-if="portfolio.p_content" 
                :src="portfolio.p_content" 
                class="w-full max-h-60 object-cover rounded-md" 
                alt="Portfolio Image" />
            <span v-else class="text-white font-semibold">No Image</span>
        </div>

        <!-- Portfolio Caption -->
        <div class="mt-3 text-center text-gray-800 font-medium">
            {{ portfolio?.caption || "" }}
        </div>
    </div>
</template>
