<template>
  <div class="comparison-slider" ref="container" :style="{ width: width, height: height }">
    <div class="image-container">
      <img :src="leftImage" alt="Left Image" class="image left-image">
      <div class="text-overlay left-text">{{ leftText }}</div>
      <img :src="rightImage" alt="Right Image" class="image right-image">
      <div class="text-overlay right-text">{{ rightText }}</div>
    </div>
    <div class="slider" ref="slider" @mousedown="startDragging" @touchstart="startDragging">
      <div class="slider-line"></div>
      <div class="slider-button">
        <span class="slider-arrow">&#8596;</span>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, onBeforeUnmount } from 'vue';

export default {
  name: 'ComparisonSlider',
  props: {
    leftImage: { type: String, required: true },
    rightImage: { type: String, required: true },
    leftText: { type: String, default: '' },
    rightText: { type: String, default: '' },
    width: { type: String, default: '100%' },
    height: { type: String, default: '500px' }
  },
  setup(props) {
    const container = ref(null);
    const slider = ref(null);
    const isDragging = ref(false);

    const startDragging = (e) => {
      e.preventDefault();
      isDragging.value = true;
    };

    const stopDragging = () => {
      isDragging.value = false;
    };

    const drag = (e) => {
      if (!isDragging.value) return;
      const containerRect = container.value.getBoundingClientRect();
      let x = (e.clientX || e.touches[0].clientX) - containerRect.left;
      x = Math.max(0, Math.min(x, containerRect.width));
      
      slider.value.style.left = `${x}px`;
      container.value.style.setProperty('--slider-position', `${(x / containerRect.width) * 100}%`);
    };

    onMounted(() => {
      window.addEventListener('mousemove', drag);
      window.addEventListener('mouseup', stopDragging);
      window.addEventListener('touchmove', drag);
      window.addEventListener('touchend', stopDragging);
    });

    onBeforeUnmount(() => {
      window.removeEventListener('mousemove', drag);
      window.removeEventListener('mouseup', stopDragging);
      window.removeEventListener('touchmove', drag);
      window.removeEventListener('touchend', stopDragging);
    });

    return {
      container,
      slider,
      startDragging,
      stopDragging,
      drag
    };
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap');

.comparison-slider {
  position: relative;
  overflow: hidden;
  margin: 0 auto;
  box-shadow: 0 0 15px rgba(0, 255, 0, 0.2);
  border-radius: 12px;
}

.image-container {
  position: relative;
  width: 100%;
  height: 100%;
}

.image {
  position: absolute;
  top: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.left-image {
  left: 0;
  clip-path: inset(0 calc(100% - var(--slider-position, 50%)) 0 0);
}

.right-image {
  right: 0;
  clip-path: inset(0 0 0 var(--slider-position, 50%));
}

.text-overlay {
  position: absolute;
  bottom: 20px;
  padding: 12px;
  background-color: rgba(0, 0, 0, 0.6);
  color: #ffffff;
  max-width: 75%;
  text-align: center;
  border-radius: 8px;
  font-family: 'Orbitron', sans-serif;
  font-size: 14px;
  line-height: 1.5;
  text-shadow: 0 0 8px rgba(0, 255, 0, 0.6);
  border: 1px solid #00ff00;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.text-overlay:hover {
  transform: scale(1.05);
  box-shadow: 0 0 15px rgba(0, 255, 0, 0.4);
}

.left-text {
  left: 10%;
  clip-path: inset(0 calc(100% - var(--slider-position, 50%)) 0 0);
}

.right-text {
  right: 10%;
  clip-path: inset(0 0 0 var(--slider-position, 50%));
}

.slider {
  position: absolute;
  top: 0;
  bottom: 0;
  left: var(--slider-position, 50%);
  width: 40px;
  transform: translateX(-50%);
  cursor: ew-resize;
}

.slider-line {
  position: absolute;
  top: 0;
  bottom: 0;
  left: 50%;
  width: 3px;
  background-color: #00ff00;
  transform: translateX(-50%);
  box-shadow: 0 0 8px #00ff00;
}

.slider-button {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 45px;
  height: 45px;
  background-color: #00ff00;
  border-radius: 50%;
  transform: translate(-50%, -50%);
  display: flex;
  justify-content: center;
  align-items: center;
  box-shadow: 0 0 15px #00ff00;
  transition: all 0.3s ease;
}

.slider-button:hover {
  background-color: #00cc00;
  box-shadow: 0 0 20px #00ff00;
}

.slider-arrow {
  font-size: 22px;
  color: #000000;
  text-shadow: 0 0 4px rgba(255, 255, 255, 0.5);
}
</style>
