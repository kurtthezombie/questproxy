homepage 

<template>
  <div class="min-h-screen bg-gray-100 text-white">
    <header class="bg-gray-900 sticky top-0 z-50 p-4 shadow-lg border-b border-green-500">
      <div class="container mx-auto flex justify-between items-center px-20">
        <div class="flex items-center">
          <router-link to="/" class="flex flex-row items-center">
            <img src="@/assets/img/qplogo3.png" alt="Logo" class="w-12 h-12">
            <span class="text-2xl font-bold text-white">QuestProxy</span>
          </router-link>
        </div>

        <nav class="flex items-center gap-0">
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

    <!-- Intro Section -->
    <section class="relative bg-gradient-to-r from-[#0f172a] via-[#1f2937] to-gray-400 text-white py-32 px-6 lg:px-24">

      <div class="max-w-7xl mx-auto flex flex-col items-start space-y-6">
        <span class="bg-green-800 text-green-300 px-4 py-1 rounded-full text-sm font-semibold">Level Up Your Gaming Experience</span>
        
        <h1 class="text-5xl sm:text-6xl font-extrabold">
          Connect with Elite <br />
          <span class="text-green-400">Gaming Pilots</span>
        </h1>

        <p class="text-lg text-gray-200 max-w-2xl">
          QuestProxy bridges the gap between gamers and skilled pilots, helping you overcome challenges and master advanced strategies.
        </p>

        <div class="flex items-center space-x-2 mt-8">
          <div class="flex space-x-1">
            <span class="w-8 h-8 bg-gray-700 rounded-full flex items-center justify-center text-white">1</span>
            <span class="w-8 h-8 bg-gray-700 rounded-full flex items-center justify-center text-white">2</span>
            <span class="w-8 h-8 bg-gray-700 rounded-full flex items-center justify-center text-white">3</span>
            <span class="w-8 h-8 bg-gray-500 rounded-full flex items-center justify-center text-white">4</span>
          </div>
          <span class="text-green-400 text-sm font-semibold">500+ skilled pilots ready to help</span>
        </div>
      </div>
    </section>

    <!-- Why Choose QuestProxy Section -->
    <section class="bg-[#0f172a] text-white py-24 px-6 lg:px-24">
      <div class="max-w-7xl mx-auto text-center">
        <h2 class="text-4xl font-bold mb-4">Why Choose <span class="text-green-400">QuestProxy</span></h2>
        <p class="text-gray-300 mb-16 max-w-2xl mx-auto">
          Our platform offers unique advantages for both gamers seeking assistance and pilots looking to share their expertise.
        </p>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
          <!-- Card 1 -->
          <div class="bg-[#1e293b] rounded-lg p-8 border border-gray-700 hover:border-green-500 transition">
            <div class="mb-4">
              <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 12c2.21 0 4-1.79 4-4S14.21 4 12 4 8 5.79 8 8s1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
              </svg>
            </div>
            <h3 class="text-xl font-semibold mb-2">Verified Pilots</h3>
            <p class="text-gray-400 text-justify">All our pilots undergo rigorous verification to ensure they have the skills they claim.</p>
          </div>

          <!-- Card 2 -->
          <div class="bg-[#1e293b] rounded-lg p-8 border border-gray-700 hover:border-green-500 transition">
            <div class="mb-4">
              <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
            </div>
            <h3 class="text-xl font-semibold mb-2">Fast Matching</h3>
            <p class="text-gray-400 text-justify">Our advanced matching system connects you with the perfect pilot for your specific needs.</p>
          </div>

          <!-- Card 3 -->
          <div class="bg-[#1e293b] rounded-lg p-8 border border-gray-700 hover:border-green-500 transition">
            <div class="mb-4">
              <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M9 20H4v-2a3 3 0 015.356-1.857M9 10a4 4 0 11-8 0 4 4 0 018 0zm14 0a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
            </div>
            <h3 class="text-xl font-semibold mb-2">Community Support</h3>
            <p class="text-gray-400 text-justify">Join a thriving community of gamers and pilots who share tips and strategies.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- About Section -->
    <section id="about" class="relative isolate overflow-hidden pt-36 lg:pb-14 bg-gray-800 py-10 sm:py-5">
    <div class="mx-auto max-w-7xl px-6 lg:px-0">
      <div class="w-full min-h-[600px] flex flex-col justify-center space-y-10">
        <h2 class="text-5xl sm:text-6xl font-bold tracking-tight text-white">
          About <span class="text-green-500">QuestProxy</span>
        </h2>

        <p class="text-lg leading-8 text-gray-300 text-justify max-w-2xl">
          QuestProxy is a revolutionary piloting platform that connects gamers with skilled pilots to enhance their gaming experience. Whether you're looking to level up faster, complete challenging quests, or learn advanced strategies, our network of experienced pilots is here to help.
        </p>
      </div>
    </div>
    </section>

    <!-- Join QuestProxy Section -->
    <section id="join" class="bg-[#0f172a] py-24 sm:py-20 text-white">
      <div class="container mx-auto px-6 lg:px-8 flex flex-col items-center text-center space-y-10">
        
        <div>
          <h2 class="text-4xl font-bold">Join QuestProxy Today!</h2>
          <p class="mt-6 text-lg text-gray-300 max-w-4xl">
            Whether you're a gamer in need of a skilled pilot or a pilot eager to help gamers achieve their goals,<br />
            <span class="text-green-400 font-semibold">QuestProxy</span> is the platform for you!
          </p>
        </div>

        <RouterLink
          to="/signup"
          class="bg-green-500 hover:bg-green-600 text-white py-4 px-12 rounded-lg text-lg font-semibold transition duration-300"
        >
          Join Now
        </RouterLink>

        <div class="flex flex-col sm:flex-row justify-center items-center gap-12 mt-12 text-left">
          <!-- Stat 1 -->
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-green-900 flex items-center justify-center">
              <svg class="w-8 h-8 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 21h8M12 17v4m0-4a4 4 0 100-8 4 4 0 000 8zM12 3v4m0 0a4 4 0 010 8V7z" />
              </svg>
            </div>

            <div>
              <h3 class="text-2xl font-bold">10,000+</h3>
              <p class="text-sm text-gray-400">Completed Quests</p>
            </div>
          </div>

          <!-- Stat 2 -->
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-green-900 flex items-center justify-center">
              <svg class="w-8 h-8 text-green-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M9 20H4v-2a3 3 0 015.356-1.857M9 10a4 4 0 11-8 0 4 4 0 018 0zm14 0a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
            </div>
            <div>
              <h3 class="text-2xl font-bold">5,000+</h3>
              <p class="text-sm text-gray-400">Active Users</p>
            </div>
          </div>

          <!-- Stat 3 -->
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-green-900 flex items-center justify-center">
              <svg class="w-8 h-8 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-1a3 3 0 00-3-3H5a3 3 0 00-3 3v1m0 0v1a3 3 0 003 3h1a3 3 0 003-3v-1m10 0v-1a3 3 0 00-3-3h-1a3 3 0 00-3 3v1m0 0v1a3 3 0 003 3h1a3 3 0 003-3v-1" />
              </svg>
            </div>
            <div>
              <h3 class="text-2xl font-bold">50+</h3>
              <p class="text-sm text-gray-400">Supported Games</p>
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
