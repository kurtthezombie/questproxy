<template>
    <BaseView>
        <div slot="right-section">
            <div class="flex justify-center md:justify-end">
                <router-link to="/" href="#" class="rounded-full py-2 px-3 uppercase text-xs font-bold tracking-wider cursor-pointer text-gray-700 md:ml-2 border-gray-700 md:border-2 hover:bg-gray-700 hover:text-white transition ease-out duration-500">back</router-link>
            </div>

            <header class="mt-4 border-b border-slate-300 pb-2">
                <h3 class="text-sm md:text-xl font-normal tracking-wider">Edit account</h3>
            </header>

            <form @submit.prevent="updateUser(user)" id="form" class="flex flex-col mt-8">
                <label class="mb-1" for="first-name">First Name</label>
                <input class="mb-5 px-2 py-2" v-model="user.firstName" id="first-name" type="text" placeholder="Enter your first name" required>
                <label class="mb-1" for="last-name">Last Name</label>
                <input class="mb-5 px-2 py-2" v-model="user.lastName" id="last-name" type="text" placeholder="Enter your last name" required>
                <label class="mb-1" for="email">Email</label>
                <input class="mb-5 px-2 py-2" v-model="user.email" type="email" name="email" id="email" placeholder="Enter your email" disabled>
                <label class="mb-1" for="email">Password</label>
                <div class="relative">
                    <input class="px-2 py-2 text-md block w-full" v-model="user.password" :type="show ? 'password' : 'text'" name="password" id="password" placeholder="Enter your password" required>
                    
                    <div class="absolute inset-y-0 right-3 flex items-center text-sm leading-5">
                        <svg class="tracking-wider cursor-pointer" @click.prevent="show = !show" v-show="show" xmlns="http://www.w3.org/2000/svg" height="1.5em" viewBox="0 0 576 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z"/></svg>
                        <svg class="tracking-wider cursor-pointer" @click.prevent="show = !show" v-show="!show" xmlns="http://www.w3.org/2000/svg" height="1.5em" viewBox="0 0 640 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M38.8 5.1C28.4-3.1 13.3-1.2 5.1 9.2S-1.2 34.7 9.2 42.9l592 464c10.4 8.2 25.5 6.3 33.7-4.1s6.3-25.5-4.1-33.7L525.6 386.7c39.6-40.6 66.4-86.1 79.9-118.4c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C465.5 68.8 400.8 32 320 32c-68.2 0-125 26.3-169.3 60.8L38.8 5.1zM223.1 149.5C248.6 126.2 282.7 112 320 112c79.5 0 144 64.5 144 144c0 24.9-6.3 48.3-17.4 68.7L408 294.5c8.4-19.3 10.6-41.4 4.8-63.3c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3c0 10.2-2.4 19.8-6.6 28.3l-90.3-70.8zM373 389.9c-16.4 6.5-34.3 10.1-53 10.1c-79.5 0-144-64.5-144-144c0-6.9 .5-13.6 1.4-20.2L83.1 161.5C60.3 191.2 44 220.8 34.5 243.7c-3.3 7.9-3.3 16.7 0 24.6c14.9 35.7 46.2 87.7 93 131.1C174.5 443.2 239.2 480 320 480c47.8 0 89.9-12.9 126.2-32.5L373 389.9z"/></svg>
                    </div>
                </div>
                
                <div class="flex justify-start items-center mt-5">
                    <button class="bg-slate-900 text-white px-5 py-2 rounded hover:shadow-xl text-center">
                        <p v-show="!isLoading">Update</p>
                        <img class="h-6" v-show="isLoading" alt="loading" src="data:image/gif;base64,R0lGODlhEAAQAPIAAP///wAAAMLCwkJCQgAAAGJiYoKCgpKSkiH/C05FVFNDQVBFMi4wAwEAAAAh/hpDcmVhdGVkIHdpdGggYWpheGxvYWQuaW5mbwAh+QQJCgAAACwAAAAAEAAQAAADMwi63P4wyklrE2MIOggZnAdOmGYJRbExwroUmcG2LmDEwnHQLVsYOd2mBzkYDAdKa+dIAAAh+QQJCgAAACwAAAAAEAAQAAADNAi63P5OjCEgG4QMu7DmikRxQlFUYDEZIGBMRVsaqHwctXXf7WEYB4Ag1xjihkMZsiUkKhIAIfkECQoAAAAsAAAAABAAEAAAAzYIujIjK8pByJDMlFYvBoVjHA70GU7xSUJhmKtwHPAKzLO9HMaoKwJZ7Rf8AYPDDzKpZBqfvwQAIfkECQoAAAAsAAAAABAAEAAAAzMIumIlK8oyhpHsnFZfhYumCYUhDAQxRIdhHBGqRoKw0R8DYlJd8z0fMDgsGo/IpHI5TAAAIfkECQoAAAAsAAAAABAAEAAAAzIIunInK0rnZBTwGPNMgQwmdsNgXGJUlIWEuR5oWUIpz8pAEAMe6TwfwyYsGo/IpFKSAAAh+QQJCgAAACwAAAAAEAAQAAADMwi6IMKQORfjdOe82p4wGccc4CEuQradylesojEMBgsUc2G7sDX3lQGBMLAJibufbSlKAAAh+QQJCgAAACwAAAAAEAAQAAADMgi63P7wCRHZnFVdmgHu2nFwlWCI3WGc3TSWhUFGxTAUkGCbtgENBMJAEJsxgMLWzpEAACH5BAkKAAAALAAAAAAQABAAAAMyCLrc/jDKSatlQtScKdceCAjDII7HcQ4EMTCpyrCuUBjCYRgHVtqlAiB1YhiCnlsRkAAAOwAAAAAAAAAAAA==" />
                    </button>
                    <router-link class="ml-3 justify-end hover:underline" to="/">Cancel</router-link>
                </div>
            </form>
        </div>
    </BaseView>
</template>

<script>
import BaseView from './BaseView.vue'
import { mapState, mapActions } from 'vuex'
    export default {
        name: "edit-view", 
        data(){
            return {
                show: true,
                user: JSON.parse(localStorage.getItem('user')),
            }
        },
        components: {
            BaseView
        },
        computed: {
            ...mapState({
                isLoading: state => state.isLoading
            })
        },
        methods:{
            ...mapActions(['updateUser'])
        },
    }
</script>