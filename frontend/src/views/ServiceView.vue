<template>
  <div class="min-h-screen bg-gray-900 text-white">
    <NavBar
      :username="username"
      :email="email"
      :role="role"
      :callLogout="callLogout"
    />
    <h1 class="text-2xl font-bold text-green-500 text-center mt-10 mb-5">
      {{ categoryTitle }} Services
    </h1>

    <div class="container mx-auto py-10">
      <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        <ServiceDisplay
          v-for="service in services"
          :key="service.id"
          :service="service"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import loginService from '@/services/login-service';
import { useLoader } from '@/services/loader-service';
import NavBar from '@/components/NavBar.vue';
import ServiceDisplay from '@/components/ServiceDisplay.vue';

const { loadShow, loadHide } = useLoader();

const router = useRouter();
const services = ref([]);
const categoryTitle = ref('');
const username = ref('');
const email = ref('');
const role = ref('');

const title = router.currentRoute.value.params.title;
categoryTitle.value = title;


</script>