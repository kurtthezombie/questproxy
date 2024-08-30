<template>
    <div class="container mt-5">
      <div class="row justify-content-center">
        <div class="col-md-6">
          <div class="card">
            <div class="card-header">
              <h3>Welcome back to QP!</h3>
            </div>  
            <div class="card-body">
              <p>LOG IN DUDE</p>
              <form @submit.prevent="register">
                <div class="form-group">
                  <label for="username">Username</label>
                  <input v-model="username" type="text" class="form-control" id="username">
                </div>
                <div class="form-group">
                  <label for="password">Password</label>
                  <input v-model="password" type="password" class="form-control" id="password">
                </div>
                <button type="submit" class="btn btn-primary">Log In</button>
              </form>
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
      password: '',
    };
  },
  methods: {
    register() {
      // Send registration data to your API or backend here
      console.log(this.username, this.password);
      // Implement form validation and error handling (optional)
    }
  }
};
</script>