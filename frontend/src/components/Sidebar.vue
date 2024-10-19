<template>
  <div class="w-1/4 bg-white p-4 border-r">
    <ul>
      <li class="py-2">
        <router-link to="/account" class="flex items-center text-red-500">
          <i class="fas fa-user mr-2"></i> Account
        </router-link>
      </li>
      <li class="py-2">
        <router-link to="/privacy" class="flex items-center text-gray-600 hover:text-red-500">
        </router-link>
      </li>
    </ul>
  </div>
</template>

<script setup>
</script>

<style scoped>
/* Add custom styles here */
</style>
