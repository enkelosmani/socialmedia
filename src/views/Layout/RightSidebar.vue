<script setup lang="ts">
import { ref } from "vue";

// Expanded trending topics with more entries and additional data for richness
const trendingTopics = ref([
  { hashtag: "#TechTrends", posts: "12k", category: "Technology", trendingSince: "2h ago" },
  { hashtag: "#AIRevolution", posts: "8.5k", category: "AI", trendingSince: "1h ago" },
  { hashtag: "#VueJS", posts: "5.2k", category: "Web Dev", trendingSince: "3h ago" },
  { hashtag: "#SpaceExploration", posts: "3.9k", category: "Science", trendingSince: "4h ago" },
  { hashtag: "#WebDev", posts: "7.8k", category: "Programming", trendingSince: "5h ago" },
]);
</script>

<template>
  <aside class="hidden lg:flex flex-col w-1/4 bg-gradient-to-black from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 shadow-xl p-6 fixed top-0 right-0 h-full overflow-y-auto  dark:border-gray-700 transition-all duration-300">
    <!-- Trending Topics Section -->
    <div class="bg-white dark:bg-black rounded-xl p-5 shadow-md">
      <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-5 tracking-tight flex items-center">
        <svg class="w-6 h-6 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
        </svg>
        Trending Now
      </h2>
      <ul class="space-y-4">
        <li v-for="(topic, index) in trendingTopics" :key="index" class="group flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-900 transition-all duration-200 transform hover:-translate-y-1">
          <div class="flex-1">
            <p class="text-base font-semibold text-gray-800 dark:text-gray-200 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 cursor-pointer transition-colors duration-200">
              {{ topic.hashtag }}
            </p>
            <div class="flex items-center space-x-2 mt-1">
              <span class="text-xs text-gray-500 dark:text-gray-400">{{ topic.posts }} posts</span>
              <span class="text-xs text-gray-400 dark:text-gray-500">• {{ topic.category }}</span>
              <span class="text-xs text-gray-400 dark:text-gray-500">• {{ topic.trendingSince }}</span>
            </div>
          </div>
          <button class="text-gray-500 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 transform group-hover:scale-110 transition-all duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
          </button>
        </li>
      </ul>
    </div>

    <!-- Footer -->
    <div class="mt-8 text-center text-xs text-gray-500 dark:text-gray-400">
      <p class="font-medium">Designed to Inspire • © {{ new Date().getFullYear() }}</p>
      <p class="mt-1 text-gray-400 dark:text-gray-500">Powered by xAI</p>
    </div>
  </aside>
</template>

<style scoped>
/* Base Styles */
.shadow-xl {
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

.shadow-md {
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.rounded-xl {
  border-radius: 12px;
}

.rounded-lg {
  border-radius: 8px;
}

/* Light Mode */
.bg-gradient-to-b {
  background: linear-gradient(to bottom, #f9fafb, #e5e7eb);
}

.bg-gray-50 {
  background-color: #f9fafb;
}

/* Dark Mode */
.dark .bg-gradient-to-b {
  background: linear-gradient(to bottom, #1f2937, #111827);
}

.dark .bg-gray-50 {
  background-color: #374151;
}

/* Transitions and Animations */
aside {
  transition: all 0.3s ease;
}

li {
  transition: all 0.2s ease;
}

/* Typography */
.text-base {
  font-size: 1rem;
}

/* Buttons */
button {
  transition: all 0.2s ease;
}

/* Hover Effects */
.group:hover .group-hover\:text-indigo-600 {
  color: #4f46e5;
}

.dark .group:hover .group-hover\:text-indigo-400 {
  color: #818cf8;
}
</style>
