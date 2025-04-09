<template>
  <header class="bg-gray-800 sticky top-0 z-50 p-4 flex justify-between items-center shadow-lg border-b-4 border-green-500">
    <div class="flex items-center">
      <img src="@/assets/img/qplogo3.png" alt="Logo" class="w-12 h-12">
      <span class="text-2xl font-bold text-green-500">QuestProxy</span>
    </div>
    <nav class="flex space-x-6">
      <router-link to="/home" class="text-white hover:text-green-500 transition-colors duration-300">
        Home
      </router-link>
      <router-link to="/leaderboards" class="text-white hover:text-green-500 transition-colors duration-300">
        Leaderboard
      </router-link>
      <router-link 
        v-if="role === 'game pilot'" 
        to="/create-service" 
        class="text-white hover:text-green-500 transition-colors duration-300">
        Service
      </router-link>

      <!-- Avatar Dropdown Component -->
      <UserDropdown 
        :username="username" 
        :email="email" 
        :role="role" 
        @logout="callLogout"
      />
    </nav>
  </header>
</template>

<script>
import UserDropdown from '@/components/UserDropdown.vue';

export default {
  name: 'NavBar',
  components: { UserDropdown },
  props: {
    username: String,
    email: String,
    role: String,
    callLogout: Function
  },
  data() {
    return {
      isActiveLink: '',
    };
  },
  methods: {
    setActiveLink(link) {
      this.isActiveLink = link;
    }
  }
};
</script>
