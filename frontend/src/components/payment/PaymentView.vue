<template>
    <div>
      <!-- BookingCard component -->
      <BookingCard
        :isOpen="isBookingCardOpen"
        :serviceId="serviceId"
        @bookingConfirmed="handleBookingConfirmed"
        @closeModal="closeBookingCard"
      />
  
      <!-- Payment component, pass the bookingId as a prop -->
      <Payment :bookingId="bookingId" v-if="bookingId" />
    </div>
  </template>
  
  <script>
  import BookingCard from "@/components/BookingCard.vue";
  import Payment from "@/components/Payment.vue";
  
  export default {
    components: {
      BookingCard,
      Payment
    },
    data() {
      return {
        bookingId: null,
        isBookingCardOpen: true,
        serviceId: 123, 
      };
    },
    methods: {
      handleBookingConfirmed(bookingId) {
        this.bookingId = bookingId; 
      },
      closeBookingCard() {
        this.isBookingCardOpen = false;
      }
    }
  };
  </script>
  