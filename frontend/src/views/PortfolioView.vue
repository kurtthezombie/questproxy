<script setup>
import { ref, computed } from 'vue';
import { useUserStore } from '@/stores/userStore';
import NavBar from '@/components/NavBar.vue';
import CreateModal from "@/components/portfolio/CreateModal.vue";

const userStore = useUserStore();
const userName = userStore.userData?.username;

const isModalOpen = ref(false);

const handleSubmit = (data) => {
    console.log("Uploaded file: ", data.file);
    console.log("Caption: ", data.caption);

    isModalOpen.value = false;
};

const props = defineProps({
    username: String,
    image: String,
});

const initials = computed(() => {
    return userName
        .split(" ")
        .map(word => word[0])
        .join("")
        .toUpperCase();
});
</script>

<template>
    <NavBar />
    <!--container-->
    <div class="flex justify-center max-w-full bg-red-100">
        <div class="flex w-1/2 flex-col items-center bg-violet-300 p-10">
            <div>
                <!-- If image exists, show it -->
                <img v-if="props.image" :src="props.image" alt="Profile Image" class="w-24 h-24 rounded-full object-cover" />

                <!-- If no image, show default circle with initials -->
                <div v-else
                    class="w-24 h-24 rounded-full bg-gray-300 flex items-center justify-center text-3xl font-semibold text-white">
                    {{ initials }}
                </div>
                <div class="text-center mt-5 font-bold">{{ userStore.userData?.username || 'Guest' }}</div>
            </div>
            <hr class="border-gray-300" />

            <div class="mt-10 flex w-100 flex-col bg-blue-200 p-5">
                <div class="flex justify-end">
                    <button class="bg-green-300 p-1 rounded-lg font-bold" @click="isModalOpen = true">+Add</button>
                </div>
                <div class="flex flex-col items-center">
                    <!--card-->
                    <div class="mt-3 w-3/4 bg-gray-200 p-3">
                        <div class="flex flex-row justify-end gap-x-2">
                            <button>Edit</button>
                            <button>Delete</button>
                        </div>
                        <div class="flex justify-center bg-red-400 p-5">image</div>
                    </div>
                    <!--card-->
                    <div class="mt-3 w-3/4 bg-gray-200 p-3">
                        <div class="flex flex-row justify-end gap-x-2">
                            <button>Edit</button>
                            <button>Delete</button>
                        </div>
                        <div class="flex justify-center bg-red-400 p-5">image</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- modal component -->
    <CreateModal v-model="isModalOpen" @submit="handleSubmit" />
</template>