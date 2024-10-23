<template>
  <div class="min-h-screen bg-gray-100 text-white">
    <!-- Header -->
    <NavMenu></NavMenu>
    <!-- Comparison Slider -->
    <ComparisonSlider 
      :leftImage="gamerImage"
      :rightImage="chadImage"
      :leftText="gamerText"
      :rightText="chadText"
    />

    <!-- About Section -->
    <section id="about" class="relative isolate overflow-hidden bg-gray-900 py-24 sm:py-32">
      <img src="@/assets/img/gaming.jpg" alt="" class="absolute inset-0 -z-10 h-full w-full object-cover object-right md:object-center">
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
import gamerImage from '@/assets/img/gamer.jpg';
import chadImage from '@/assets/img/Pilot.jpg';
import NavMenu from '@/components/NavMenu.vue';

export default {
  components: {
    ComparisonSlider,
    NavMenu
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
