import { defineStore } from "pinia";
import { ref, computed } from "vue";

export const useUserStore = defineStore('user', () => {
    //state
    const userData = ref(null)
    const token = ref(null)
    const isLoggedIn = computed(() => !!userData.value && !!token.value)

    const users = ref([]);

    //actions
    const setUser = (data,authToken) => {
        userData.value = data
        token.value = authToken
    }

    const clearUser = () => {
        userData.value = null;
        token.value = null;
    }

    //return everything
    return {
        userData,
        token,
        isLoggedIn,
        setUser,
        clearUser,
    }
},{
    persist: true,
});