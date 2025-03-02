<script setup lang="ts">
import { ref, onMounted } from "vue";
import axios from "axios";

const props = defineProps<{
  showModal: boolean;
  receivedMessages: { id: number; sender: string; content: string; timestamp: string }[];
}>();

const emit = defineEmits(["close"]);

const messageContent = ref("");
const messages = ref<{ id: number; sender: string; content: string; timestamp: string }[]>([]);

const fakeMessages = [
  {
    id: 1,
    sender: "Alex Carter",
    content: "Hey, loved your recent project update! Can we discuss some collaboration ideas?",
    timestamp: "2025-02-24T09:15:00Z",
  },
  {
    id: 2,
    sender: "Sophie Nguyen",
    content: "Great work on the design prototype. Could you share the specs with me?",
    timestamp: "2025-02-23T14:30:00Z",
  },
  {
    id: 3,
    sender: "Liam Brooks",
    content: "Quick question: Are you attending the conference next month?",
    timestamp: "2025-02-22T17:45:00Z",
  },
  {
    id: 4,
    sender: "Emma Davis",
    content: "Your latest post was inspiring! Let’s connect for a potential feature.",
    timestamp: "2025-02-21T11:20:00Z",
  },
  {
    id: 5,
    sender: "Noah Patel",
    content: "Thanks for the feedback on my draft. Can we schedule a follow-up?",
    timestamp: "2025-02-20T13:10:00Z",
  },
  {
    id: 6,
    sender: "Olivia Kim",
    content: "Hi! Your portfolio is impressive—any tips for a newbie in the field?",
    timestamp: "2025-02-19T08:55:00Z",
  },
];

const formatTimeAgo = (timestamp: string) => {
  const time = new Date(timestamp);
  return new Intl.DateTimeFormat("en-US", {
    dateStyle: "medium",
    timeStyle: "short",
  }).format(time);
};

const fetchMessages = async () => {
  try {
    const response = await axios.get("/api/messages");
    messages.value = response.data;
  } catch (error) {
    console.error("Failed to fetch messages:", error);
    // Fallback to fake messages if API fails
    messages.value = fakeMessages;
  }
};

const closeModal = () => {
  emit("close");
};

onMounted(() => {
  // Load fake messages initially and poll for real ones
  messages.value = fakeMessages;
  setInterval(fetchMessages, 5000);
});
</script>

<template>
  <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md p-6 relative">
      <!-- Header -->
      <div class="flex justify-between items-center border-b border-gray-300 dark:border-gray-700 pb-3">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Messages</h2>
        <button @click="closeModal" class="text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-white">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Received Messages -->
      <div class="max-h-64 overflow-y-auto mt-4 space-y-4">
        <div v-if="messages.length === 0" class="text-center text-gray-500 dark:text-gray-400">
          No messages yet.
        </div>
        <div v-for="message in messages" :key="message.id" class="flex items-start space-x-3 p-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
          <img
            class="w-10 h-10 rounded-full object-cover"
            src="http://lilithaengineering.co.za/wp-content/uploads/2017/08/person-placeholder.jpg"
            alt="Sender Avatar"
          />
          <div>
            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ message.sender }}</p>
            <p class="text-sm text-gray-700 dark:text-gray-300">{{ message.content }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">{{ formatTimeAgo(message.timestamp) }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.rounded-xl {
  border-radius: 1rem;
}

.shadow-xl {
  box-shadow: 0 12px 20px rgba(0, 0, 0, 0.15);
}

.max-h-64 {
  max-height: 16rem;
}

input:focus {
  box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
}

button {
  transition: color 0.2s ease;
}
</style>