<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card">
          <div class="card-header text-center">
            <h3>Welcome to QP!</h3>
          </div>
          <div class="card-body">
            <p class="text-center">Sign up to start using our amazing app frfr!</p>
            <form @submit.prevent="register">
              <div class="form-group mb-3">
                <label for="username">Username</label>
                <input v-model="username" type="text" class="form-control form-control-lg" id="username">
              </div>
              <div class="form-group mb-3">
                <label for="email">Email</label>
                <input v-model="email" type="email" class="form-control form-control-lg" id="email">
              </div>
              <div class="form-group mb-3">
                <label for="password">Password</label>
                <input v-model="password" type="password" class="form-control form-control-lg" id="password">
              </div>
              <button type="submit" class="btn btn-primary btn-lg w-100">Register</button>
            </form>
            <div class="mt-3 text-center">
              <p>Already have an account? <a href="/login">Login here</a>.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// Script logic for registration functionality
const register = () => {
  console.log("Register clicked");
};
</script>

<style scoped>
/* Increase padding and font size for better readability */
.card-body {
  padding: 2rem;
  font-size: 1.1rem;
}

.card-header h3 {
  font-size: 1.8rem;
}

form .form-group label {
  font-size: 1.2rem;
}

.btn-primary {
  padding: 0.75rem;
}

@media (min-width: 768px) {
  .card {
    padding: 2rem;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  }
}
</style>
