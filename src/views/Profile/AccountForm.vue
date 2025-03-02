<script setup lang="ts">
import { onMounted, ref } from "vue";
import { getPosts } from "@/services/posts";
import { useRouter } from "vue-router";
import { formatDistanceToNow } from "date-fns";
import axios from "axios";
import Swal from 'sweetalert2'; // Import SweetAlert2 directly

const posts = ref([]);
const user = ref<{ id: number; firstname: string; lastname: string; avatar?: string } | null>(null);
const router = useRouter();
const isLoading = ref(true);
const isDeleting = ref(false);

const fetchUserPosts = async () => {
  try {
    const res = await getPosts();
    if (res?.status === 200 && res.data?.result?.data) {
      posts.value = res.data.result.data
          .map(post => ({
            ...post,
            image: post.content?.data?.image,
            created_at: post.created_at || new Date().toISOString(),
          }))
          .filter(post => {
            const postUserId = post.user?.id;
            const postImage = post.content?.data?.image;
            return Number(postUserId) === Number(user.value?.id) && postImage;
          });
    }
  } catch (error) {
    // No console.error here
  }
};

const deletePost = async (postId: number) => {
  if (isDeleting.value) return;

  // Show SweetAlert2 confirmation dialog
  const result = await Swal.fire({
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, delete it!',
  });

  if (!result.isConfirmed) return; // Exit if user cancels

  isDeleting.value = true;
  try {
    const token = localStorage.getItem("token") || '';
    if (!token) {
      await Swal.fire('Error', 'Please log in to delete posts.', 'error');
      router.push("/login");
      return;
    }

    const response = await axios.delete(`http://127.0.0.1:8000/api/posts/${postId}`, {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: "application/json",
      },
    });

    if (response.status === 200 || response.status === 204) {
      posts.value = posts.value.filter(post => post.id !== postId);
      // Show success alert
      await Swal.fire('Deleted!', 'Your post has been deleted.', 'success');
    } else {
      throw new Error(`Unexpected status code: ${response.status}`);
    }
  } catch (error: any) {
    let errorMessage = "Failed to delete post. Please try again.";
    if (error.response) {
      errorMessage = `Error ${error.response.status}: ${error.response.data.message || "Could not delete post"}`;
    } else if (error.request) {
      errorMessage = "Network error: Could not reach the server.";
    } else {
      errorMessage = `Request error: ${error.message}`;
    }
    await Swal.fire('Error', errorMessage, 'error');
  } finally {
    isDeleting.value = false;
  }
};

const fetchUser = async () => {
  try {
    const token = localStorage.getItem("token") || '';
    if (!token) {
      router.push("/login");
      return;
    }

    const response = await axios.get("/api/user", {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    if (response.headers['content-type'].includes('text/html')) {
      throw new Error("Invalid response from /api/user");
    }

    user.value = response.data.result;
    localStorage.setItem("user", JSON.stringify(user.value));
  } catch (error) {
    const storedUser = localStorage.getItem("user");
    if (storedUser) {
      user.value = JSON.parse(storedUser);
    } else {
      router.push("/login");
    }
  } finally {
    isLoading.value = false;
  }
};

const getTimeAgo = (timestamp: string): string => {
  if (!timestamp) return "Just now";
  const date = new Date(timestamp);
  return !isNaN(date.getTime()) ? formatDistanceToNow(date, { addSuffix: true }) : "Just now";
};

const logoutUser = () => {
  localStorage.removeItem("user");
  localStorage.removeItem("token");
  user.value = null;
  router.push("/login");
};

const goBack = () => {
  router.push("/");
};

const applyDarkMode = () => {
  const isDark = localStorage.getItem("darkMode") === "true";
  document.documentElement.classList.toggle("dark", isDark);
};

onMounted(async () => {
  const storedUser = localStorage.getItem("user");
  if (!storedUser || (typeof storedUser === 'string' && storedUser.includes('<!DOCTYPE html>'))) {
    localStorage.removeItem("user");
    router.push("/login");
    return;
  }
  user.value = JSON.parse(storedUser);
  applyDarkMode();
  await fetchUser();
  if (!user.value?.id) {
    router.push("/login");
    return;
  }
  await fetchUserPosts();
});
</script>

<template>
  <div class="profile-container min-h-screen bg-gray-100 dark:bg-black">
    <!-- Header -->
    <header class="sticky top-0 z-10 flex items-center justify-between px-6 py-4 bg-white dark:bg-black shadow-sm">
      <button @click="goBack" class="p-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
      </button>
      <h1 class="text-xl font-medium text-gray-900 dark:text-white">Profile</h1>
      <button @click="logoutUser" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-full hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800">
        Log Out
      </button>
    </header>

    <!-- Profile Section -->
    <section class="px-6 py-8 mx-auto max-w-4xl">
      <div class="flex flex-col items-center bg-white dark:bg-black rounded-xl shadow-md p-6">
        <img
            :src="user?.avatar || 'http://lilithaengineering.co.za/wp-content/uploads/2017/08/person-placeholder.jpg'"
            alt="User Avatar"
            class="w-32 h-32 rounded-full border-4 border-gray-200 dark:border-gray-700 object-cover shadow-sm"
        />
        <h2 v-if="user?.firstname && user?.lastname" class="mt-4 text-2xl font-semibold text-gray-900 dark:text-white">
          {{ user.firstname }} {{ user.lastname }}
        </h2>
        <p v-else-if="isLoading" class="mt-4 text-gray-500 dark:text-gray-400 animate-pulse">Loading...</p>
        <p v-else class="mt-4 text-gray-500 dark:text-gray-400">Name unavailable</p>
      </div>
    </section>

    <!-- Posts Grid -->
    <section class="px-6 pb-12 mx-auto max-w-4xl">
      <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">My Image Posts</h3>
      <div v-if="isLoading" class="text-center text-gray-500 dark:text-gray-400 animate-pulse">Loading posts...</div>
      <div v-else-if="posts.length > 0" class="grid grid-cols-3 gap-2">
        <div v-for="post in posts" :key="post.id" class="post-card group relative overflow-hidden rounded-lg aspect-square">
          <img
              :src="'http://127.0.0.1:8000/storage/' + post.image"
              alt="Post image"
              class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
          />
          <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-opacity duration-300 flex items-center justify-center opacity-0 group-hover:opacity-100">
            <span class="text-white text-sm">{{ getTimeAgo(post.created_at) }}</span>
          </div>
          <button
              @click="deletePost(post.id)"
              :disabled="isDeleting"
              class="absolute top-2 right-2 p-1 bg-red-600 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300 hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
      <p v-else class="text-center text-gray-500 dark:text-gray-400">No posts with images yet.</p>
    </section>
  </div>
</template>

<style scoped>
.profile-container {
  width: 100%;
  margin: 0 auto;
}

.shadow-sm { box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05); }
.shadow-md { box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
.rounded-full { border-radius: 9999px; }
.rounded-xl { border-radius: 1rem; }
.rounded-lg { border-radius: 0.5rem; }

.post-card {
  position: relative;
}

.post-card img {
  transition: transform 0.3s ease;
}

.post-card:hover img {
  transform: scale(1.05);
}

button {
  transition: all 0.2s ease;
}

.dark .bg-gray-200 {
  background-color: #000000;
}

.dark .bg-gray-900 {
  background-color: #0000; /* Lighter dark mode background if needed */
}

@media (max-width: 768px) {
  .grid { grid-template-columns: repeat(3, 1fr); gap: 1px; }
  .px-6 { padding-left: 1rem; padding-right: 1rem; }
}

@media (max-width: 640px) {
  .text-2xl { font-size: 1.5rem; }
  .w-32 { width: 6rem; height: 6rem; }
}

.post-card .delete-button {
  transition: opacity 0.3s ease;
}

.post-card:hover .delete-button {
  opacity: 1;
}
</style>
