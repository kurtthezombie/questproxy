<script setup>
</script>

<template>
  <div class="min-h-screen min-h-screen flex items-center justify-center bg-gray-900 text-white">
    <div class="card bg-gray-800 max-w-md w-full flex flex-col items-center justify-center">
      <div class="card-body w-full flex justify-center text-center">
        <div class="card-title flex justify-center">
          <h1 class="text-2xl font-bold text-green-400">Thank You!</h1>
        </div>
        <div>
          <p class="text-lg text-gray-300 mb-6">
            Your review has been submitted successfully. We appreciate your feedback!
          </p>
        </div>
        <div class="flex justify-center w-full">
        <RouterLink to="/home">
          <button class="btn btn-success w-full">
            Go Home
          </button>
        </RouterLink>
      </div>
      </div>
    </div>
  </div>
</template>