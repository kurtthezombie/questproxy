<template>
  <div class="relative">
    <button @click="toggleNotif" class="notification-button text-white relative">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
        class="size-6">
        <path stroke-linecap="round" stroke-linejoin="round"
          d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
      </svg>

      <span v-if="unreadCount > 0" class="absolute top-0 right-0 bg-red-500 text-xs rounded-full px-1">{{
        unreadCount }}</span>
    </button>

    <!-- Popover Dropdown -->
    <div v-if="notifOpen" class="notification-popover absolute right-0 mt-2 w-64 bg-gray-800 text-white shadow-xl rounded-md overflow-hidden z-50">
      <div class="p-2 border-b border-gray-700 font-bold">Notifications</div>
      <div v-if="isLoading" class="p-4 text-center">
        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-green-500 mx-auto"></div>
      </div>
      <div v-else-if="notifications.length === 0" class="p-2 text-gray-400 text-sm">No notifications</div>
      <ul v-else class="max-h-[60vh] overflow-y-auto">
        <li v-for="notif in notifications" :key="notif.id" 
          class="p-2 hover:bg-gray-700 text-sm border-b border-gray-700 cursor-pointer"
          :class="{ 'bg-gray-700': !notif.read_at }"
          @click="handleNotificationClick(notif)">
          <div class="flex items-start gap-2">
            <div class="flex-1">
              <p class="text-gray-200">{{ notif.data.message }}</p>
              <p class="text-xs text-gray-400">{{ formatDate(notif.created_at) }}</p>
            </div>
            <button v-if="!notif.read_at" @click.stop="markAsRead(notif.id)" class="text-green-400 hover:text-green-300">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
            </button>
          </div>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';
import { useUserStore } from '@/stores/userStore';
import notificationService from '@/services/notification.service';
import { useRouter } from 'vue-router';

dayjs.extend(relativeTime);

const router = useRouter();
const userStore = useUserStore();

const notifOpen = ref(false);
const notifications = ref([]);
const unreadCount = ref(0);
const isLoading = ref(false);
const currentPage = ref(1);
const totalPages = ref(1);

const formatDate = (date) => {
  return dayjs(date).fromNow();
};

const toggleNotif = async (event) => {
  event.stopPropagation();
  notifOpen.value = !notifOpen.value;
  if (notifOpen.value) {
    await handleFetchNotifications();
  }
};

const handleFetchNotifications = async (page = 1) => {
  try {
    isLoading.value = true;
    const response = await notificationService.fetchNotifications(page);
    if (response && response.notifications) {
      notifications.value = response.notifications;
      unreadCount.value = response.notifications.filter(n => !n.read_at).length;
      currentPage.value = response.pagination.current_page;
      totalPages.value = response.pagination.last_page;
    } else {
      notifications.value = [];
      unreadCount.value = 0;
      currentPage.value = 1;
      totalPages.value = 1;
    }
  } catch (error) {
    console.error('Failed to fetch notifications:', error);
    notifications.value = [];
    unreadCount.value = 0;
  } finally {
    isLoading.value = false;
  }
};

const markAsRead = async (notificationId) => {
  try {
    await notificationService.markAsRead(notificationId);
    
    // Update local state
    const notification = notifications.value.find(n => n.id === notificationId);
    if (notification) {
      notification.read_at = new Date().toISOString();
      unreadCount.value = Math.max(0, unreadCount.value - 1);
    }
  } catch (error) {
    console.error('Failed to mark notification as read:', error);
  }
};

const handleNotificationClick = async (notification) => {
  // Mark as read if not already read
  if (!notification.read_at) {
    await markAsRead(notification.id);
  }

  // Handle different notification types
  switch (notification.data.type) {
    case 'pilot_matched':
      if (notification.data.user_id) {
        router.push(`/users/${notification.data.user_id}`);
      }
      break;
    case 'booking_confirmed':
      router.push('/services-history');
      break;
    // Add more cases for other notification types here
    default:
      console.log('Unknown notification type:', notification.data.type);
  }

  // Close the notification popover
  notifOpen.value = false;
};

// Close notification popover when clicking outside
const handleClickOutside = (event) => {
  const notifButton = document.querySelector('.notification-button');
  const notifPopover = document.querySelector('.notification-popover');
  
  if (notifOpen.value && 
      notifButton && 
      notifPopover && 
      !notifButton.contains(event.target) && 
      !notifPopover.contains(event.target)) {
    notifOpen.value = false;
  }
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>