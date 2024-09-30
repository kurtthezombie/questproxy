<template>
  <div id="app" class="font-mono">
    <router-view></router-view>
  </div>
</template>

<style scoped>

body, html {
  margin: 0;
  padding: 0;
  height: 100%;
  z-index: -1;
}

/* Layout Styles */
#app {
    font-family: 'Arial', sans-serif;
    min-height: 100vh;
    background-image: url('@/assets/img/background2.jpg');
    background-size: cover; /* Cover the entire container */
    background-position: center;
    background-attachment: fixed;
    display: flex;
    flex-direction: column;
}

body {
  background-image: url('@/assets/img/background2.jpg');
  background-size: cover;
  background-position: center;
  background-attachment: fixed;
}

/* Header and Navigation */
header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background-color: #f8f9fa;
  padding: 0rem;
  position: sticky;
  top: 0;
  width: 100%;
  z-index: 0;
  box-shadow: 0px 4px 2px -2px gray;
}

.content-wrapper {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 2rem;
}

.logo {
  width: 100px;
}

nav {
  display: flex;
  gap: 1.5rem;
}

nav a {
  font-weight: bold;
  color: #333;
  text-decoration: none;
  padding: 0.5rem 1rem;
  transition: 0.3s;
}

nav a:hover {
  color: #42b983;
  border-bottom: 2px solid #42b983;
}

nav a.router-link-exact-active {
  color: #42b983;
  border-bottom: 2px solid #42b983;
}

/* Responsive Layout */
@media (min-width: 1024px) {
  header {
    justify-content: space-between;
    align-items: center;
    padding: 1rem 2rem;
  }

  nav {
    gap: 2rem;
  }

  .logo {
    margin-right: auto; /* Push the nav to the right */
  }
}
</style>