<template>
  <div class="about">
    <h1>This is an about page</h1>
    <br>
    <router-link class="mt-5 bg-slate-200 text-slate-900 px-5 py-2 border border-slate-900 rounded text-center hover:shadow-xl" to="/">
                  Go back
              </router-link>
  </div>

  
</template>

<style>
@media (min-width: 1024px) {
  .about {
    min-height: 100vh;
    display: flex;
    align-items: center;
  }
}
</style>
