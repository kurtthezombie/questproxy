<template>
  <div>
    <!-- Confirm and Pay Button -->
    <button @click="confirmAndPay" class="btn btn-primary">Confirm and Pay</button>
  </div>
</template>

<script>
import axios from "axios";
import { useUserStore } from "@/stores/userStore"; 

export default {
  props: ["bookingId"],

  methods: {
    async confirmAndPay() {
      try {
        const userStore = useUserStore(); 

        const response = await axios.post(
          `http://127.0.0.1:8000/api/paymets/${this.bookingId}`,
          {
            success_url: 'http://localhost:5173/payment-success',  
            cancel_url: 'http://localhost:5173/payment-cancel',  
          },
          {
            headers: {
              Authorization: `Bearer ${userStore.token}`,
            },
          }
        );

        const checkoutUrl = response.data.data.checkout_url;
        window.location.assign(checkoutUrl); // Use assign to trigger the redirect

      } catch (error) {
        console.error("Payment initiation failed:", error);
      }
    },
  },
};
</script>
