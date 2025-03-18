<script setup>
defineProps({
    portfolio: Object,
    onEdit: Function,
    onDelete: Function,
});
</script>

<template>
    <div class="mt-3 w-3/4 bg-gray-200 p-3 rounded-lg shadow-md">
        <div class="flex flex-row justify-end gap-x-2">
            <button @click="onEdit(portfolio)" class="text-blue-500 hover:underline">Edit</button>
            <button @click="onDelete(portfolio.id)" class="text-red-500 hover:underline">Delete</button>
        </div>

        <!-- Portfolio Image -->
        <div class="flex justify-center bg-red-400 p-5 rounded-md">
            <img v-if="portfolio.image" :src="portfolio.image" class="w-full h-auto object-cover rounded-md" alt="Portfolio Image" />
            <span v-else class="text-white font-semibold">No Image</span>
        </div>

        <!-- Portfolio Caption -->
        <div class="mt-3 text-center text-gray-800 font-medium">
            {{ portfolio.caption || "No caption provided" }}
        </div>
    </div>
</template>
