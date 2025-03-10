<template>
    <div class="relative w-24 h-24 overflow-hidden bg-gray-100 rounded-full dark:bg-gray-600">
      <img
        :src="userAvatar"
        @error="handleAvatarError"
        :alt="username || 'User avatar'"
        class="w-full h-full object-cover rounded-full"
      />
    </div>
  </template>
  
  <script setup>
  const props = defineProps({
    userAvatar: String,
    username: String,
  });
  
  const handleAvatarError = (event) => {
    event.target.src = 'src/assets/avatarimg/default-avatar.png';
  };
  </script>
  