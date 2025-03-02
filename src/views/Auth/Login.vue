<script setup lang="ts">
import { reactive } from "vue";
import { login } from "@/services/auth.js";
import router from "@/router/index.js";
import Swal from 'sweetalert2'; // Import SweetAlert2

const form = reactive({
  email: "",
  password: "",
});

const submit = async () => {
  try {
    const res = await login(form); // Await the login promise
    if (res) {
      // Successful login
      router.push('/');
    } else {
      // Login failed (e.g., wrong credentials)
      await Swal.fire({
        icon: 'error',
        title: 'Login Failed',
        text: 'Please check your email or password.',
        confirmButtonColor: '#d33',
      });
    }
  } catch (err) {
    console.error("Error during login:", err);
    // Handle network or server errors
    await Swal.fire({
      icon: 'error',
      title: 'Oops...',
      text: 'An error occurred during login. Please try again later.',
      confirmButtonColor: '#d33',
    });
  }
};
</script>

<template>
  <link
      rel="stylesheet"
      href="https://demos.creative-tim.com/notus-js/assets/styles/tailwind.css"
  >
  <link
      rel="stylesheet"
      href="https://demos.creative-tim.com/notus-js/assets/vendor/@fortawesome/fontawesome-free/css/all.min.css"
  >

  <section class="bg-blueGray-50 h-screen flex items-center justify-center">
    <div class="w-full lg:w-4/12 px-4 mx-auto pt-6">
      <div class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded-lg bg-blueGray-200 border-0">
        <div class="rounded-t mb-0 px-6 py-6">
          <hr class="mt-6 border-b-1 border-blueGray-300">
        </div>
        <div class="flex-auto px-4 lg:px-10 py-10 pt-0">
          <div class="text-blueGray-400 text-center mb-3 font-bold">
            <small>SIGN IN</small>
          </div>
          <form @submit.prevent="submit">
            <div class="relative w-full mb-3">
              <label class="block uppercase text-blueGray-600 text-xs font-bold mb-2" for="email">Email</label>
              <input
                  v-model="form.email"
                  type="email"
                  id="email"
                  required
                  class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150"
                  placeholder="Email"
              />
            </div>
            <div class="relative w-full mb-3">
              <label class="block uppercase text-blueGray-600 text-xs font-bold mb-2" for="password">Password</label>
              <input
                  v-model="form.password"
                  type="password"
                  id="password"
                  required
                  class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150"
                  placeholder="Password"
              />
            </div>
            <div class="text-center mt-6">
              <button
                  type="submit"
                  class="bg-blueGray-800 text-white active:bg-blueGray-600 text-sm font-bold uppercase px-6 py-3 rounded shadow hover:shadow-lg outline-none focus:outline-none mr-1 mb-1 w-full ease-linear transition-all duration-150"
              >
                Sign In
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.rounded-lg {
  border-radius: 0.5rem;
}
.bg-indigo-500 {
  background-color: #6366f1;
}
.bg-indigo-100 {
  background-color: #f0f4ff;
}
.text-indigo-500 {
  color: #6366f1;
}
</style>