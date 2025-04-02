<template>
  <div class="min-h-screen bg-gray-100 text-white">
    <header class="bg-gray-950 sticky top-0 z-50 p-4">
      <div class="container mx-auto flex justify-between items-center px-20">
        <div class="flex items-center">
          <router-link to="/" class="flex flex-row items-center">
            <img src="@/assets/img/qplogo3.png" alt="Logo" class="w-12 h-12">
            <span class="text-2xl font-bold text-green-500">QuestProxy</span>
          </router-link>
        </div>

        <nav class="space-x-6">
          <a href="/" class="hover:text-green-400 text-white">Home</a>
          <a href="#about" class="scroll-link hover:text-green-400 text-white">About</a>
          <!-- Register & Login combined button with divider -->
          <div class="button-container relative inline-flex space-x-0">
            <RouterLink to="/signup" class="flex justify-center items-center border- border-green-500 text-white hover:bg-green-500 hover:text-white py-2 px-4 text-sm transition-all block">Register</RouterLink>
            <div class="divider"></div>
            <RouterLink to="/login" class="flex justify-center items-center border-1 border-green-500 text-white hover:bg-green-500 hover:text-white py-2 px-4 text-sm transition-all block">Login</RouterLink>
          </div>
        </nav>

      </div>
    </header>

    <!-- Comparison Slider -->
    <ComparisonSlider 
      :leftImage="gamerImage"
      :rightImage="chadImage"
      :leftText="gamerText"
      :rightText="chadText"
    />

    <!-- About Section -->
    <section id="about" class="relative isolate overflow-hidden relative pt-36 lg:pb-14 overflow-hidden bg-black py-24 sm:py-32 border-b border-white">
      <div class="absolute inset-0 flex justify-center items-center " >
        <div class="gradient-circle"></div>
      </div> 
      <div class="mx-auto max-w-7xl px-6 lg:px-8 lg:px-8 flex flex-col items-center text-center">
        <div class="p-20 bg-black bg-opacity-20 backdrop-blur-md max-w-7xl w-full min-h-[600px] flex flex-col justify-center">
          <h2 class="text-5xl font-bold tracking-tight text-white sm:text-7xl">About QuestProxy</h2>     
          <div class="mt-10 flex flex-col space-y-8 items-center w-full">
            <div class="flex justify-center w-full">
              <div class=" p-6 rounded-lg w-3/4">
                <p class="text-lg leading-8 text-gray-300">
                QuestProxy is a revolutionary piloting platform that connects gamers with skilled pilots to enhance their gaming experience. Whether you're looking to level up faster, complete challenging quests, or learn advanced strategies, our network of experienced pilots is here to help.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="join" class="bg-black py-24 sm:py-32">
      <div class="container mx-auto px-6 lg:px-8 flex flex-col items-center text-center">
        <h2 class="text-5xl font-bold text-white">Join QuestProxy Today!</h2>
        <p class="mt-6 text-lg text-gray-100">Whether you're a gamer in need of a skilled pilot or a pilot eager to help gamers achieve their goals, QuestProxy is the platform for you!</p>
        <div class="mt-20">
          <RouterLink to="/signup" class="bg-green-500 text-white hover:bg-green-600 hover:text-white py-5 px-20 rounded-full text-lg font-semibold transition">Join Now</RouterLink>
        </div>
      </div>
    </section>

    <RouterView />
  </div>
</template>

<script>
import { ref } from 'vue';
import ComparisonSlider from '@/components/ComparisonSlider.vue';
import gamerImage from '@/assets/img/Gamers.jpg';
import chadImage from '@/assets/img/Pilots.jpg';
import '@/assets/css/style.css'; 

export default {
  components: {
    ComparisonSlider,
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
