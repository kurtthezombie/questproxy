<template>
    <div class="flex items-center justify-center min-h-screen bg-gray-100">
        <div class="max-w-lg mx-auto bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold mb-4 text-center">Confirm Account Deletion</h2>
            <p class="text-gray-700 mb-4 text-center">
                Are you sure you want to delete your account? This action cannot be undone.
            </p>
            <p class="text-gray-700 mb-6 text-center">
                Please confirm your decision by clicking the button below.
            </p>
            <form @submit.prevent="confirmDeletion">
                <div class="flex justify-center">
                    <button type="submit"
                        class="bg-red-500 text-white font-semibold py-2 px-4 rounded hover:bg-red-600 transition duration-200">
                        Delete My Account
                    </button>
                </div>
            </form>
            <div class="flex justify-center mt-4">
                <router-link to="/" class="text-blue-500 hover:underline">Cancel</router-link>
            </div>
        </div>
    </div>
</template>

<script setup>
    import axios from 'axios';
    import { ref } from 'vue';
    import { useRoute } from 'vue-router';
    import { useLoader } from '@/services/loader-service';
    import router from '@/router';
    import loginService from '@/services/login-service';
    import { useUserStore } from '@/stores/userStore';

    const userStore = useUserStore();
    const route = useRoute();
    const userId = ref(route.query.id);
    const { loadShow, loadHide } = useLoader();

    const confirmDeletion = async () => {
        console.log('User Id: ', userId.value);
        const loader = loadShow();
        try {
            const response = await axios.delete(`http://localhost:8000/api/users/delete/${userId.value}`, {
                headers: {
                    'Authorization' : `Bearer ${localStorage.getItem('authToken')}`,
                },
            });
            userStore.clearUser();
            localStorage.clear();
            alert('Account has been deleted.');
            router.push({ name: 'login' });
        } catch (error) {
            console.log('Error deleting account: ', error);
        } finally {
            loadHide(loader);
        }
    }

</script>

<style scoped>
/* Add any additional styles here if necessary */
</style>
