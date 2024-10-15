<template>
  <div class="min-h-screen bg-gray-100 text-white">
    <!-- Header -->
    <header class="bg-gray-800 sticky top-0 z-50 p-4 flex justify-between items-center shadow-lg border-b-4 border-green-500">
      <div class="flex items-center">
        <img src="@/assets/img/qplogo3.png" alt="Logo" class="w-12 h-12">
        <span class="text-2xl font-bold text-green-500">QuestProxy</span>
      </div>
      <nav class="space-x-6">
        <RouterLink to="/" class="hover:text-green-400">Home</RouterLink>
        <a href="#about" class="scroll-link hover:text-green-400">About</a>
        <RouterLink to="/signup" class="hover:text-green-400">Register</RouterLink>
        <RouterLink to="/login" class="hover:text-green-400">Login</RouterLink>
      </nav>
    </header>

    <!-- Comparison Slider -->
    <ComparisonSlider 
      :leftImage="gamerImage"
      :rightImage="chadImage"
      :leftText="gamerText"
      :rightText="chadText"
    />

    <!-- About Section -->
    <section id="about" class="relative isolate overflow-hidden bg-gray-900 py-24 sm:py-32">
      <img src="@/assets/img/canoy.png" alt="" class="absolute inset-0 -z-10 h-full w-full object-cover object-right md:object-center">
      <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="mx-auto max-w-2xl lg:mx-0">
          <h2 class="text-4xl font-bold tracking-tight text-white sm:text-6xl">About QuestProxy</h2>
        </div>

        <div class="mt-10 flex flex-col space-y-10">
          <div class="flex justify-start">
            <div class=" p-4 rounded-lg w-1/2">
              <p class="text-lg leading-8 text-gray-300">QuestProxy is a revolutionary piloting platform that connects gamers with skilled pilots to enhance their gaming experience...</p>
            </div>
          </div>

          <div class="flex justify-end">
            <div class=" p-4 rounded-lg w-1/2">
              <p class="text-lg leading-8 text-gray-300">Whether you're looking to level up faster, complete challenging quests, or learn advanced strategies, our network of experienced pilots is here to help...</p>
            </div>
          </div>
 
          <div class="flex justify-start">
            <div class=" p-4 rounded-lg w-1/2">
              <p class="text-lg leading-8 text-gray-300">Join QuestProxy today and take your gaming to the next level!</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <RouterView />
  </div>
</template>

<script>
import { ref } from 'vue';
import ComparisonSlider from '@/components/ComparisonSlider.vue';
import gamerImage from '@/assets/img/gamer.gif';
import chadImage from '@/assets/img/chad.gif';

export default {
  components: {
    ComparisonSlider
  },
  setup() {
    const gamerImageSrc = ref(gamerImage);
    const chadImageSrc = ref(chadImage);
    const gamerText = ref("Busy gamer seeking help to progress in favorite games");
    const chadText = ref("Skilled pilot ready to assist and level up your game");

    return {
      gamerImage: gamerImageSrc,
      chadImage: chadImageSrc,
      gamerText,
      chadText
    };
  }
}
</script>
