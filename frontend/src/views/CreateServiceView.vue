<template>
    <form>
        <div class="grid gap-6 mb-6 md:grid-cols-2">
            <div>
                <label for="games" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Game:</label>
                <select name="games" id="games" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="Cent">Cent</option>
                    <option value="Tweak">Tweak</option>
                    <option value="Kurt">Kurt</option>
                    <option value="Fat">Fat</option>
                </select>
            </div>
            <div>
                <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Description: </label>
                <input type="text" id="description" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Cent Tweaker frfr" required />
            </div>
            <div>
                <label for="price" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Price: </label>
                <input type="text" id="price" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="$$$" required />
            </div>
            <div>
                <label for="duration" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Duration: </label>
                <input type="text" id="duration" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="??" required />
            </div>  
            <div>
                <label for="availability" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Availablity: datetime-local?? ikaw na bahala cent. </label>
                <select id="availability" multiple size="7" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                    <option value="Monday">Monday</option>
                    <option value="Tuesday">Tuesday</option>
                    <option value="Wednesday">Wednesday</option>
                    <option value="Thursday">Thursday</option>
                    <option value="Friday">Friday</option>
                    <option value="Saturday">Saturday</option>
                    <option value="Sunday">Sunday</option>
                </select>
                <label for="startTime" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Start Time:</label>
                <input type="time" id="startTime" name="startTime">
                <br><br>
                <label for="endTime" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Start Time:</label>
                <input type="time" id="endTime" name="endTime">
            </div>
            <div>
                <label for="website" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Service Timestamp?? ikaw bahala cent.</label>
                <input type="url" id="website" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"/>
            </div>
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Submit</button>
        </div>
    </form>
</template>

<script setup>
    import loginService from '@/services/login-service';
    import { useRouter } from 'vue-router';

    const router = useRouter();
</script>