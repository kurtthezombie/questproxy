<template>
    <header class="bg-gray-800 sticky top-0 z-50 p-4 flex justify-between items-center shadow-lg border-b-4 border-green-500">
      <div class="flex items-center">
        <img src="@/assets/img/qplogo3.png" alt="Logo" class="w-12 h-12">
        <span class="text-2xl font-bold text-green-500">QuestProxy</span>
      </div>
      <nav class="space-x-6">
        <a href="/" class="hover:text-green-400 text-white">Home</a>
        <a href="#about" class="scroll-link hover:text-green-400 text-white">About</a>
        <RouterLink to="/signup" class="hover:text-green-400 text-white">Register</RouterLink>
        <RouterLink to="/login" class="hover:text-green-400 text-white">Login</RouterLink>
      </nav>
    </header>
</template>

<script>
    export default {
        name: 'NavMenu', //component name
        data() {
          return {
            isActiveLink: '',
          };
        },
        methods: {
          setActiveLink(link) {
            this.isActiveLink = link;
          },
        },
    };
</script>