<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card">
          <div class="card-header">
            <h3>Welcome back to QP!</h3>
          </div>
          <div class="card-body">
            <p>LOG IN DUDE</p>
            <form @submit.prevent="login">
              <div class="form-group">
                <label for="username">Username</label>
                <input v-model="username" type="text" class="form-control" id="username">
              </div>
              <br>
              <div class="form-group">
                <label for="password">Password</label>
                <input v-model="password" type="password" class="form-control" id="password">
              </div>
              <br>
              <button type="submit" class="btn btn-primary">Log In</button>
            </form>
            <div class="mt-3">
              <p>Don't have an account? <a href="/register">Sign up here</a>.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import HomePage from '../views/HomePage.vue'; // Import the HomePage component

export default {
  components: {
    HomePage
  },
  data() {
    return {
      username: '',
      password: '',
    };
  },
  methods: {
    login() {
      // Send login data to your API or backend here
      // Replace this with your actual login logic
      if (this.username === 'validuser' && this.password === 'validpassword') {
        // Successful login, redirect to HomePage
        this.$router.push({ name: 'HomePage' });
      } else {
        // Handle login errors (e.g., display an error message)
        console.error('Invalid login credentials');
      }
    }
  }
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