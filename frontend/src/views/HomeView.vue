<script setup>
import { RouterLink, RouterView } from 'vue-router'
</script>

<template>
<div id="app">
  <header>
    <img alt="questproxy" class="logo" src="@/assets/img/qplogo2.png" width="125" height="125" />

    <div class="wrapper">
      <HelloWorld msg="You did it!" />

      <nav>
        <RouterLink to="/">Home</RouterLink>
        <a href="#about" class="scroll-link">About</a>
        <RouterLink to="/signup">Register</RouterLink>
        <RouterLink to="/login">Login</RouterLink>
      </nav>

    </div>
  </header>

  <RouterView />
</div>
</template>


<style scoped>
/* Header and Navigation */
header {
  background-color: rgba(37,3,65,255); /* Semi-transparent light gray */
  padding: 1rem;
  position: sticky;
  top: 0;
  width: 100%;
  z-index: 100;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.logo {
  width: 120px;
}

nav a {
  margin: 0 1rem;
  font-weight: bold;
  color: #333;
  text-decoration: none;
  transition: 0.3s;
}

nav a:hover {
  color: #42b983;
}

/* Main content area */
#main-content {
  background-color: rgba(216, 216, 216, 0.8); /* Semi-transparent light gray */
  padding: 2rem;
  border-radius: 8px;
  margin-top: 2rem;
}

/* About section */
.about-section {
  background-color: rgba(232, 232, 232, 0.8); /* Semi-transparent light gray */
  padding: 2rem;
  border-radius: 8px;
  margin-top: 2rem;
}

.about-section h2 {
  margin-bottom: 1rem;
}
</style>