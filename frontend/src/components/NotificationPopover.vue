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
      <div v-else class="max-h-[60vh] overflow-y-auto">
        <ul>
          <li v-for="notif in notifications" :key="notif.id" 
            class="p-2 hover:bg-gray-700 text-sm border-b border-gray-700 cursor-pointer"
            :class="{ 'bg-gray-700': !notif.read_at }"
            @click="handleNotificationClick(notif)">
            <div class="flex items-start gap-2">
              <div class="flex-1">
                <p class="text-gray-200">{{ notif.data.message }}</p>
                <p class="text-xs text-gray-400">{{ formatDate(notif.created_at) }}</p>
              </div>
              <div class="flex items-center gap-2">
                <div v-if="navigatingNotificationId === notif.id" class="animate-spin rounded-full h-4 w-4 border-b-2 border-green-500"></div>
                <button v-if="!notif.read_at" @click.stop="markAsRead(notif.id)" class="text-green-400 hover:text-green-300">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                </button>
              </div>
            </div>
          </li>
        </ul>
        <!-- Load More Button -->
        <div v-if="currentPage < totalPages" class="p-2 text-center border-t border-gray-700">
          <button 
            @click.stop="loadMoreNotifications" 
            class="text-sm text-green-400 hover:text-green-300 w-full py-1"
            :disabled="isLoadingMore"
          >
            <span v-if="!isLoadingMore">Load More</span>
            <span v-else class="flex items-center justify-center">
              <svg class="animate-spin h-4 w-4 mr-2" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Loading...
            </span>
          </button>
        </div>
      </div>
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
const navigatingNotificationId = ref(null);
const currentPage = ref(1);
const totalPages = ref(1);
const isLoadingMore = ref(false);

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
    console.log('Notification Response:', response);
    
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
  if (navigatingNotificationId.value) return; // Prevent multiple clicks
  
  navigatingNotificationId.value = notification.id;
  try {
    // Mark as read if not already read
    if (!notification.read_at) {
      await markAsRead(notification.id);
    }

    // Handle different notification types
    switch (notification.data.type) {
      case 'pilot_matched':
        if (notification.data.user_id) {
          await router.push(`/users/${notification.data.user_id}`);
        }
        break;
      case 'user_matched':
        if (notification.data.user_id) {
          await router.push(`/users/${notification.data.user_id}`);
        }
        break;
      case 'booking_confirmed':
        await router.push('/services-history');
        break;
      case 'booking_completed':
        if (notification.data.booking_id) {
          await router.push(`/progress/${notification.data.booking_id}`);
        }
        break;
      case 'progress_updated':
        if (notification.data.booking_id) {
          await router.push(`/progress/${notification.data.booking_id}`);
        }
        break;
      case 'progress_item_added':
        if (notification.data.booking_id) {
          await router.push(`/progress/${notification.data.booking_id}`);
        }
        break;
      default:
        console.log('Unknown notification type:', notification.data.type);
    }
  } finally {
    navigatingNotificationId.value = null;
    // Close the notification popover
    notifOpen.value = false;
  }
};

const loadMoreNotifications = async () => {
  if (isLoadingMore.value) return;
  
  try {
    isLoadingMore.value = true;
    const nextPage = currentPage.value + 1;
    const response = await notificationService.fetchNotifications(nextPage);
    console.log('Load More Response:', response);
    
    if (response && response.notifications) {
      // Append new notifications to existing ones
      notifications.value = [...notifications.value, ...response.notifications];
      currentPage.value = response.pagination.current_page;
      totalPages.value = response.pagination.last_page;
    }
  } catch (error) {
    console.error('Failed to load more notifications:', error);
  } finally {
    isLoadingMore.value = false;
  }
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