<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <h3>Welcome to QP!</h3>
          </div>  
          <div class="card-body">
            <p>Sign up to start using our amazing app frfr!</p>
            <form @submit.prevent="register">
              <div class="form-group">
                <label for="username">Username</label>
                <input v-model="username" type="text" class="form-control" id="username">
              </div>
              <br>
              <div class="form-group">
                <label for="email">Email</label>
                <input v-model="email" type="email" class="form-control" id="email">
              </div>
              <br>
              <div class="form-group">
                <label for="password">Password</label>
                <input v-model="password" type="password" class="form-control" id="password">
              </div>
              <br>
              <button type="submit" class="btn btn-primary">Register</button>
            </form>
            <div class="mt-3">
              <p>Already have an account? <a href="/login">Login here</a>.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>


<script>
export default {
  data() {
    return {
      username: '',
      email: '',
      password: '',
    };
  },
  methods: {
    register() {
      // Send registration data to your API or backend here
      console.log(this.username, this.email, this.password);
      // Implement form validation and error handling (optional)
    }
  }
};
</script>