<script setup lang="ts">
import { onMounted, ref, watch } from "vue";
import { getPosts } from "@/services/posts";
import CreatePost from "@/views/Posts/CreatePost.vue";
import CommentSection from "@/views/Posts/CommentSection.vue";
import RightSidebar from "@/views/Layout/RightSidebar.vue";
import LeftSidebar from "@/views/Layout/LeftSidebar.vue";
import MessageForm from "@/views/Posts/MessagesForm.vue";
import Settings from "@/views/Settings/SettingsForm.vue";
import axios from "axios";
import { useRouter } from "vue-router";
import { formatDistanceToNow } from "date-fns";
import { post, destroy } from "@/composable/useApi.js";
import { logout } from "@/services/auth.js";

const api = axios.create({
  baseURL: "http://127.0.0.1:8000",
});

interface User {
  id: number;
  firstname: string;
  lastname: string;
  avatar?: string;
}

interface Comment {
  id: number;
  user_id: number;
  user?: User;
  text: string;
  created_at: string;
}

// Reactive variables
const posts = ref<any[]>([]);
const showModal = ref(false);
const showMessagesModal = ref(false);
const showSettingsModal = ref(false);
const user = ref<User | null>(null); // Improved typing
const router = useRouter();
const comments = ref<Record<number, { show: boolean; comments: Comment[] }>>({});
const receivedMessages = ref([]);
const isDarkMode = ref(localStorage.getItem("darkMode") === "true");

// Fetch user data for a comment
const fetchUserForComment = async (userId: number): Promise<User> => {
  const token = localStorage.getItem("token");
  try {
    const response = await api.get(`/api/users/${userId}`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    const userData = response.data.result;
    return {
      id: userData.id,
      firstname: userData.firstname || userData.first_name || "Unknown",
      lastname: userData.lastname || userData.last_name || "",
      avatar: userData.avatar,
    };
  } catch (error) {
    console.error(`Error fetching user ${userId}:`, error);
    return { id: userId, firstname: "Unknown", lastname: "" };
  }
};

// Apply dark mode
const applyDarkMode = () => {
  document.documentElement.classList.toggle("dark", isDarkMode.value);
};

// Toggle dark mode
const toggleDarkMode = () => {
  isDarkMode.value = !isDarkMode.value;
  localStorage.setItem("darkMode", isDarkMode.value.toString());
  applyDarkMode();
};

// Fetch posts from API with enriched comments
const fetchPosts = async () => {
  const token = localStorage.getItem("token");
  if (!token) {
    router.push("/login");
    return;
  }
  try {
    const res = await getPosts();
    if (res?.status === 200) {
      const postData = res.data.result?.data || res.data;
      posts.value = await Promise.all(
          postData.map(async (post: any) => {
            const postComments = await Promise.all(
                (post.comments || []).map(async (comment: Comment) => {
                  if (!comment.user) {
                    const user = await fetchUserForComment(comment.user_id);
                    return { ...comment, user };
                  }
                  return comment;
                })
            );
            comments.value[post.id] = { show: false, comments: postComments };
            return {
              ...post,
              content: post.content?.data || { image: null },
              isLiked: post.likes?.some((like: any) => like.user_id === user.value?.id) || false,
              likes_count: post.likes_count || 0,
              created_at: post.created_at || new Date().toISOString(),
            };
          })
      );
    }
  } catch (error) {
    console.error("Error fetching posts:", error);
  }
};

// Fetch user data
const fetchUser = async () => {
  const token = localStorage.getItem("token");
  if (!token) {
    console.log("No token found, redirecting to login");
    router.push("/login");
    return;
  }
  try {
    const response = await api.get("/api/user", {
      headers: { Authorization: `Bearer ${token}` },
    });
    user.value = response.data;
    localStorage.setItem("user", JSON.stringify(response.data));
  } catch (error) {
    console.error("Error fetching user data:", error);
    const storedUser = localStorage.getItem("user");
    if (storedUser) {
      user.value = JSON.parse(storedUser);
    } else {
      router.push("/login");
    }
  }
};

// Toggle like/unlike with API
const toggleLike = async (postId: number) => {
  const postItem = posts.value.find((p) => p.id === postId);
  if (!postItem || !user.value?.id) return;

  const originalLikedState = postItem.isLiked;
  const originalLikesCount = postItem.likes_count;

  postItem.isLiked = !postItem.isLiked;
  postItem.likes_count = postItem.isLiked ? postItem.likes_count + 1 : Math.max(0, postItem.likes_count - 1);

  try {
    if (originalLikedState) {
      const res = await destroy(`/api/posts/${postId}/likes`);
      if (res?.status !== 204) throw new Error("Unlike failed");
    } else {
      const payload = { user_id: user.value.id };
      const res = await post(`/api/posts/${postId}/likes`, payload);
      if (res?.status !== 201) throw new Error("Like failed");
    }
  } catch (error) {
    console.error("Toggle like failed:", error);
    postItem.isLiked = originalLikedState;
    postItem.likes_count = originalLikesCount;
    await fetchPosts();
  }
};

const getTimeAgo = (timestamp: string) => {
  if (!timestamp || typeof timestamp !== "string") return "Date not available";
  const normalizedTimestamp = timestamp.replace(" ", "T");
  const date = new Date(normalizedTimestamp);
  return !isNaN(date.getTime()) ? formatDistanceToNow(date, { addSuffix: true }) : "Invalid date";
};

// Toggle comment section
const toggleComments = (postId: number) => {
  const postComments = comments.value[postId] || { show: false, comments: [] };
  postComments.show = !postComments.show;
  comments.value[postId] = postComments;
};

// Handle comment added
const handleCommentAdded = (postId: number, comment: Comment) => {
  const postComments = comments.value[postId] || { show: true, comments: [] };
  postComments.comments.push({ ...comment, user: user.value });
  postComments.show = true;
  comments.value[postId] = postComments;
};

// Handle post created
const handlePostCreated = () => {
  showModal.value = false;
  fetchPosts();
};

// Navigation and actions
const goToProfile = () => router.push("/AccountForm");
const logoutUser = async () => {
  await logout();
  router.push("/login");
};
const toggleMessagesModal = () => (showMessagesModal.value = !showMessagesModal.value);
const toggleSettingsModal = () => (showSettingsModal.value = !showSettingsModal.value);

// Lifecycle hook
onMounted(async () => {
  const storedUser = localStorage.getItem("user");
  const token = localStorage.getItem("token");
  if (!token || !storedUser) {
    router.push("/login");
    return;
  }
  user.value = JSON.parse(storedUser);
  await fetchUser();
  applyDarkMode();
  await fetchPosts();
});

// Watch for dark mode changes
watch(isDarkMode, (newValue) => {
  localStorage.setItem("darkMode", newValue.toString());
  applyDarkMode();
});
</script>

<template>
  <div class="flex w-full min-h-screen bg-gray-900 text-white dark:border-gray-700">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&display=swap">
    <LeftSidebar
        :user="user"
        :go-to-profile="goToProfile"
        :toggle-messages-modal="toggleMessagesModal"
        :toggle-settings-modal="toggleSettingsModal"
        :logout-user="logoutUser"
        :toggle-dark-mode="toggleDarkMode"
    />

    <main class="flex-1 p-8 max-w-3xl mx-auto md:mx-0 md:ml-[22%] lg:ml-[25%] lg:mr-[25%]">
      <div
          class="bg-gradient-to-r from-black-800 to-black-900 p-4 rounded-2xl shadow-xl flex items-center cursor-pointer mb-8 transition-all duration-300 "
          @click="showModal = true"
      >
        <img
            class="w-12 h-12 rounded-full mr-4 border-2 border-indigo-500 object-cover shadow-md transition-transform duration-300 group-hover:scale-105"
            :src="user?.avatar || 'http://lilithaengineering.co.za/wp-content/uploads/2017/08/person-placeholder.jpg'"
            alt="User Avatar"
        />
        <div
            class="flex-grow bg-gray-700 text-gray-400 px-5 py-3 rounded-xl text-lg font-medium transition-colors duration-300 group-hover:bg-indigo-800 group-hover:text-gray-200"
        >
          What's on your mind?
        </div>
      </div>

      <CreatePost :showModal="showModal" @close="showModal = false" @postCreated="handlePostCreated" />
      <MessageForm :showModal="showMessagesModal" :receivedMessages="receivedMessages" @close="toggleMessagesModal" />
      <Settings :showModal="showSettingsModal" @close="toggleSettingsModal" />

      <div v-if="posts.length" class="space-y-8">
        <div v-for="post in posts" :key="post.id" class="bg-gray-800 rounded-xl shadow-lg overflow-hidden border border-gray-700">
          <div class="flex items-center justify-between px-6 py-4">
            <div class="flex items-center w-full">
              <img
                  class="w-10 h-10 rounded-full object-cover mr-4"
                  :src="post.user?.avatar || 'http://lilithaengineering.co.za/wp-content/uploads/2017/08/person-placeholder.jpg'"
                  alt="User"
              />
              <div class="flex justify-between w-full">
                <span class="text-lg font-semibold text-gray-200">
                  {{ post.user?.firstname || "Unknown" }} {{ post.user?.lastname || "" }}
                </span>
                <p class="text-sm text-gray-400">{{ getTimeAgo(post.created_at) }}</p>
              </div>
            </div>
            <button class="text-gray-400 hover:text-white">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
              </svg>
            </button>
          </div>

          <div class="post-content">
            <div v-if="post.content && post.content.image" class="w-full bg-gray-700">
              <img
                  :src="'http://127.0.0.1:8000/storage/' + post.content.image"
                  alt="Post image"
                  class="w-full h-auto object-cover max-h-auto rounded-t-xl"
              />
            </div>
            <div class="px-6 py-4">
              <p class="text-xl font-semibold text-gray-100">{{ post.title }}</p>
            </div>
          </div>

          <div class="flex items-center justify-between px-6 py-4 border-t border-gray-700">
            <div class="flex space-x-6">
              <button @click="toggleLike(post.id)" class="flex items-center text-gray-400 hover:text-red-400">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-6 w-6"
                    :fill="post.isLiked ? 'red' : 'none'"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                  <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                  />
                </svg>
                <span class="ml-2 text-lg">{{ post.likes_count }}</span>
              </button>
              <button @click="toggleComments(post.id)" class="flex items-center text-gray-400 hover:text-blue-400">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-6 w-6"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                  <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
                  />
                </svg>
                <span class="ml-2 text-lg">{{ comments[post.id]?.comments.length || 0 }}</span>
              </button>
              <button class="flex items-center text-gray-400 hover:text-green-400">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-6 w-6"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                  <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"
                  />
                </svg>
              </button>
            </div>
            <button class="flex items-center text-gray-400 hover:text-blue-400">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
              </svg>
            </button>
          </div>

          <CommentSection
              v-if="comments[post.id]?.show"
              :key="post.id"
              :post-id="post.id"
              :show="comments[post.id].show"
              :comments="comments[post.id]?.comments"
              :user="user"
              @comment-added="handleCommentAdded(post.id, $event)"
          />
        </div>
      </div>
      <div v-else class="text-center text-gray-400 text-lg">No posts available</div>
    </main>

    <RightSidebar />
  </div>
</template>

<!-- Your existing style section remains unchanged -->
<style scoped>
/* Default (light mode) styles */
.round { border-radius: 50%; }
main { max-width: 3xl; }
.rounded-xl { border-radius: 0.75rem; }
.shadow-lg { box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1); }
img { object-fit: cover; }
button { transition: color 0.2s ease, background-color 0.2s ease; }
.bg-gray-900 { background-color: #f9fafb; }
.bg-gray-800 { background-color: #ffffff; }
.bg-gray-700 { background-color: #e5e7eb; }
.text-white { color: #1f2937; }
.text-gray-100 { color: #374151; }
.text-gray-200 { color: #4b5563; }
.text-gray-300 { color: #6b7280; }
.text-gray-400 { color: #9ca3af; }
.border-gray-700 { border-color: #e5e7eb; }
.hover\:bg-gray-700:hover { background-color: #d1d5db; }
.hover\:bg-red-600:hover { background-color: #dc2626; }
.hover\:text-white:hover { color: #1f2937; }
.hover\:text-red-400:hover { color: #f87171; }
.hover\:text-blue-400:hover { color: #60a5fa; }
.hover\:text-green-400:hover { color: #4ade80; }

/* Dark mode styles */
.dark .bg-gray-900 { background-color: #000000; }
.dark .bg-gray-800 { background-color: #000000; }
.dark .bg-gray-700 { background-color: #343536; }
.dark .text-white { color: #ffffff; }
.dark .text-gray-100 { color: #e5e7eb; }
.dark .text-gray-200 { color: #d1d5db; }
.dark .text-gray-300 { color: #9ca3af; }
.dark .text-gray-400 { color: #6b7280; }
.dark .border-gray-700 { border-color: #4b5563; }
.dark .hover\:bg-gray-700:hover { background-color: #4b5563; }
.dark .hover\:bg-red-600:hover { background-color: #dc2626; }
.dark .hover\:text-white:hover { color: #ffffff; }
.dark .hover\:text-red-400:hover { color: #f87171; }
.dark .hover\:text-blue-400:hover { color: #60a5fa; }
.dark .hover\:text-green-400:hover { color: #4ade80; }

@media (min-width: 768px) { main { margin-left: 22%; } }
@media (min-width: 1024px) { main { margin-left: 25%; margin-right: 25%; padding-left: 0; padding-right: 0; } }
</style>
