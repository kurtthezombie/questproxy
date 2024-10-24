<script setup>
    import loginService from '@/services/login-service';
    import { useRouter } from 'vue-router';
    import { ref, onMounted } from 'vue';
    import axios from 'axios';
    
    const categories = ref([]);

    onMounted(async () => {
        try {
            const response = await axios.get('http://127.0.0.1:8000/api/categories');
            categories.value = response.data;  
        } catch (error) {
            console.error('Error fetching categories:', error);
        }
    });
    /* const submitService = async () => {
        const formData = {
            games: games.value,
            description: description.value,
            price: price.value,
            duration: duration.value,
            availability: availability.value,
            startTime: startTime.value,
            endTime: endTime.value,
        };

        try {
            const response = await loginService.createService(formData);
            if (response.status) {
                games.value = '';
                description.value = '';
                price.value = '';
                duration.value = '';
                availability.value = '';
                startTime.value = '';
                endTime.value = '';
                router.push({ path: '/create-service', query: { message: 'Service successfully created.'}})
            } else {
                //errorMessage.value = response.message;
                handleReload();
            } 
        } catch (error) {
            //errorMessage.value = 'An error occurred during service creation. Please try again.'
        }
    } */
</script>


<template>
    <form @submit.prevemt="submitService">
        <div class="grid gap-6 mb-6 md:grid-cols-2">
            <div>
                <label for="games" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Game:</label>
                <select name="games" id="games" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option v-for="game in categories" :key="game.id" :value="game.id">
                        {{ game.title }}
                    </option>
                </select>
            </div>
            <div>
                <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Description: </label>
                <input type="text" id="description" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Cent Tweaker frfr" required />
            </div>
            <div>
                <label for="price" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Price (₱): </label>
                <input type="text" id="price" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Php" required />
            </div>
            <div>
                <label for="duration" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Duration: </label>
                <input type="text" id="duration" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="??" required />
            </div>  
            <div>
                <label for="availability" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Availablity:</label>
                <select name="availability" id="availability" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>
            </div>
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Submit</button>
        </div>
    </form>
</template>

<!-- <script setup>
    import loginService from '@/services/login-service';
    import { useRouter } from 'vue-router';

    const router = useRouter();
</script> -->

<!-- <script setup>
// Define the available days
const daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

// Reactive state for the service form
import { reactive } from 'vue';
import loginService from '@/services/login-service';

const service = reactive({
  name: '',
  description: '',
  price: null,
  days: [], // Array to hold multiple selected days
  startTime: '',
  endTime: ''
});

// Submit function to handle form submission
const submitService = () => {
  // Process form data (e.g., send it to an API)
  console.log('Service Data:', service);
  alert('Service created successfully!');
  
  // Reset form after submission
  service.name = '';
  service.description = '';
  service.price = null;
  service.days = [];
  service.startTime = '';
  service.endTime = '';
};
</script> -->