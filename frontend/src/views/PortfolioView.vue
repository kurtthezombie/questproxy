<script setup>
import { useUserStore } from '@/stores/userStore';
import NavBar from '@/components/NavBar.vue';

const userStore = useUserStore();
</script>

<template>
      <NavBar />
      <!--container-->
      <div class="flex justify-center max-w-full bg-red-100">
            <div class="flex w-1/2 flex-col items-center bg-violet-300 p-10">
                  <div>
                        <div class="rounded-full bg-red-300 p-12">Image</div>
                        <div class="text-center mt-5 font-bold">{{ userStore.userData?.username || 'Guest' }}</div>
                  </div>
                  <hr class="border-gray-300" />

                  <div class="mt-10 flex w-100 flex-col bg-blue-200 p-5">
                        <div class="flex justify-end">
                              <button class="bg-green-300 p-1 rounded-lg font-bold">+Add</button>
                        </div>
                        <div class="flex flex-col items-center">
                              <!--card-->
                              <div class="mt-3 w-3/4 bg-gray-200 p-3">
                                    <div class="flex flex-row justify-end gap-x-2">
                                          <button>Edit</button>
                                          <button>Delete</button>
                                    </div>
                                    <div class="flex justify-center bg-red-400 p-5">image</div>
                              </div>
                              <!--card-->
                              <div class="mt-3 w-3/4 bg-gray-200 p-3">
                                    <div class="flex flex-row justify-end gap-x-2">
                                          <button>Edit</button>
                                          <button>Delete</button>
                                    </div>
                                    <div class="flex justify-center bg-red-400 p-5">image</div>
                              </div>
                        </div>
                  </div>
            </div>
      </div>

</template>