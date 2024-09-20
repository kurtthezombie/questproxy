<template>
    <BaseView>
        <div slot="right-section">
            <div class="flex justify-center md:justify-end">
                <a v-show="account.value" href="#" class="rounded-full py-2 px-3 uppercase text-xs font-bold tracking-wider cursor-pointer text-gray-700 border-gray-700 md:border-2 hover:bg-gray-700 hover:text-white transition ease-out duration-500">Log in</a>
                <a v-show="account.value" href="#" class="rounded-full py-2 px-3 uppercase text-xs font-bold tracking-wider cursor-pointer text-gray-700 ml-2 border-gray-700 md:border-2 hover:bg-gray-700 hover:text-white transition ease-out duration-500">Sign up</a>
                <router-link to="/edit" v-show="!account.value" href="#" class="rounded-full py-2 px-3 uppercase text-xs font-bold tracking-wider cursor-pointer text-gray-700 md:ml-2 border-gray-700 md:border-2 hover:bg-gray-700 hover:text-white transition ease-out duration-500">Edit</router-link>
                <a v-on:click="logout" v-show="!account.value" href="#" class="rounded-full py-2 px-3 uppercase text-xs font-bold tracking-wider cursor-pointer text-gray-700 md:ml-2 border-gray-700 md:border-2 hover:bg-gray-700 hover:text-white transition ease-out duration-500">Log out</a>
            </div>

            <header class="mt-4">
                <h2 class="text-gray-700 text-4xl md:text-6xl font-semibold leading-none tracking-wider">Hi, {{ account.firstName }}</h2>
                <h3 class="text-sm md:text-xl font-normal tracking-wider">Simple login registration with Vue & Vuex</h3>
            </header>

            <div>
                <h4 class="font-bold pb-2 mt-12 border-b border-slate-300">List of users</h4>
                <div class="mt-4">
                    <ul>
                        <li class="h-10 bg-gray-700 rounded mb-3 px-3 flex justify-between items-center text-slate-200" v-for="user in users" :key="user.id">
                            <p v-on:click="onUser(user)" class="tracking-wider cursor-pointer hover:text-xl">{{user.firstName + ' ' + user.lastName}}</p>
                            <div>
                                <button :disabled="isLoading" v-on:click="onRemove(user)" class="mr-2 rounded-full py-1 px-3 text-xs font-bold text-red-400 border-red-400 border-2 hover:bg-red-400 hover:text-gray-700 transition ease-out duration-500">
                                    <p v-show="!isLoading">remove</p>
                                    <p v-show="isLoading">loading...</p>
                                </button>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </BaseView>
</template>

<script>
import { mapActions, mapState } from 'vuex'
import BaseView from './BaseView.vue'
    export default {
        name: "home-view",
        components: {
            BaseView
        },
        computed: {
            ...mapState({
                account: state => state.user,
                users: state => state.users,
                isLoading: state => state.isLoading
            })
        },
        created () {
            this.getAllUser();
        },
        methods:{
            ...mapActions(['logout', 'getAllUser', 'removeUser']),
            onUser(user){
                alert('halo ' + user.firstName);
            },
            onRemove(user){
                this.removeUser(user);
            }
        },
    }
</script>