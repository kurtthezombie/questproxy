<template>
  <div id="login-view" class="bg-slate-200 h-screen w-screen text-slate-900 md:grid md:grid-cols-2 lg:grid-cols-3">
      <div class="hidden md:col-span-1 lg:col-span-2 md:flex lg:justify-end">
          <img class="w-full object-cover" src="../assets/img/background.jpg" alt="">
      </div>
      <div id="form-login" class="md:col-span-1 md:mt-5 px-10 py-5 shadow-lg">
          <div class="flex flex-col items-center">
              <img class="w-20" src="../assets/img/qplogo.png" alt="">
              <p class="text-center">Login to your account</p>
          </div>
          <form @submit.prevent="login(auth)" id="form" class="flex flex-col mt-5">
              <label class="mb-1" for="email">Email</label>
              <input class="px-2 py-2" v-model="auth.email" type="email" name="email" id="email" placeholder="Enter your email" required>
              <p class="mb-5 mt-1 text-gray-400 text-xs">email: testuser@test.user</p>
              <label class="mb-1" for="password">Password</label>
              <input class="px-2 py-2" v-model="auth.password" type="password" name="password" id="password" placeholder="Enter your password" required>
              <p class="mb-5 mt-1 text-gray-400 text-xs">pass: 123123</p>
              <div class="flex justify-between">
                  <div></div>
                  <a class="w-max mt-2 hover:underline tracking-wider cursor-pointer" v-on:click="onForgetPass">Forgot Password?</a>
              </div>
              
              
              <button v-bind:disabled="isLoading" class="mt-5 bg-slate-900 text-white px-5 py-2 rounded hover:shadow-xl text-center flex justify-center">
                  <p v-show="!isLoading">Login now</p>
                  <img class="h-6" v-show="isLoading" alt="loading" src="data:image/gif;base64,R0lGODlhEAAQAPIAAP///wAAAMLCwkJCQgAAAGJiYoKCgpKSkiH/C05FVFNDQVBFMi4wAwEAAAAh/hpDcmVhdGVkIHdpdGggYWpheGxvYWQuaW5mbwAh+QQJCgAAACwAAAAAEAAQAAADMwi63P4wyklrE2MIOggZnAdOmGYJRbExwroUmcG2LmDEwnHQLVsYOd2mBzkYDAdKa+dIAAAh+QQJCgAAACwAAAAAEAAQAAADNAi63P5OjCEgG4QMu7DmikRxQlFUYDEZIGBMRVsaqHwctXXf7WEYB4Ag1xjihkMZsiUkKhIAIfkECQoAAAAsAAAAABAAEAAAAzYIujIjK8pByJDMlFYvBoVjHA70GU7xSUJhmKtwHPAKzLO9HMaoKwJZ7Rf8AYPDDzKpZBqfvwQAIfkECQoAAAAsAAAAABAAEAAAAzMIumIlK8oyhpHsnFZfhYumCYUhDAQxRIdhHBGqRoKw0R8DYlJd8z0fMDgsGo/IpHI5TAAAIfkECQoAAAAsAAAAABAAEAAAAzIIunInK0rnZBTwGPNMgQwmdsNgXGJUlIWEuR5oWUIpz8pAEAMe6TwfwyYsGo/IpFKSAAAh+QQJCgAAACwAAAAAEAAQAAADMwi6IMKQORfjdOe82p4wGccc4CEuQradylesojEMBgsUc2G7sDX3lQGBMLAJibufbSlKAAAh+QQJCgAAACwAAAAAEAAQAAADMgi63P7wCRHZnFVdmgHu2nFwlWCI3WGc3TSWhUFGxTAUkGCbtgENBMJAEJsxgMLWzpEAACH5BAkKAAAALAAAAAAQABAAAAMyCLrc/jDKSatlQtScKdceCAjDII7HcQ4EMTCpyrCuUBjCYRgHVtqlAiB1YhiCnlsRkAAAOwAAAAAAAAAAAA==" />
              </button>
              
              <div class="flex justify-center items-center mt-5">
                  <div class="w-screen border-b border-gray-300"></div>
                  <p class="mx-5 text-sm">OR</p>
                  <div class="w-screen border-b border-gray-300"></div>
              </div>

              <router-link class="mt-5 bg-slate-200 text-slate-900 px-5 py-2 border border-slate-900 rounded text-center hover:shadow-xl" to="/registration">
                  Signup now
              </router-link>
          </form>
      </div>
  </div>
</template>

<script>
import { mapActions, mapState } from 'vuex'

  export default {
      name: "login-view",
      data(){
          return{
              auth: {
                  email: '',
                  password: ''
              },
              submitted: false
          }
      },
      computed: {
          ...mapState({
              isLoading: state => state.isLoading,
          })
      },
      methods: {
          ...mapActions(['login']),
          onForgetPass(){
              alert('Coming Soon...');
          }
      }
  }
</script>