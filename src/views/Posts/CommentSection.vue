<script setup lang="ts">
import { ref, defineProps, defineEmits } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

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

const props = defineProps<{
  postId: number;
  show: boolean;
  comments: Comment[];
  user: User | null;
}>();

const emit = defineEmits<{
  (e: "commentAdded", comment: Comment): void;
}>();

const router = useRouter();
const newComment = ref("");

const submitComment = async () => {
  if (!newComment.value.trim()) return alert("Please enter a comment.");
  if (!props.user?.id) return alert("You must be logged in to comment.");

  const token = localStorage.getItem("token");
  if (!token) {
    alert("Session expired. Redirecting to login...");
    router.push("/login");
    return;
  }

  try {
    const payload = { user_id: props.user.id, text: newComment.value };
    const response = await api.post(`/api/posts/${props.postId}/comments`, payload, {
      headers: { Authorization: `Bearer ${token}` },
    });
    const newCommentData = response.data.comment as Comment;

    // Emit to parent with enriched user data
    emit("commentAdded", { ...newCommentData, user: { ...props.user } });
    newComment.value = "";
  } catch (error) {
    console.error("Error submitting comment:", error);
    alert("Failed to post comment: " + (error.response?.data?.message || error.message));
  }
};

const formatTime = (timestamp: string) => {
  const date = new Date(timestamp);
  return isNaN(date.getTime())
      ? "Unknown"
      : date.toLocaleString([], { hour: "2-digit", minute: "2-digit", hour12: true });
};
</script>

<template>
  <div v-if="show" class="mt-4 comment-section shadow-md rounded-lg bg-gray-800 p-4 text-gray-200">
    <div class="comments-list max-h-64 overflow-y-auto border-t border-gray-700">
      <div v-if="props.comments && props.comments.length">
        <div
            v-for="comment in props.comments"
            :key="comment.id"
            class="flex items-start space-x-3 p-3 hover:bg-gray-700 transition-colors duration-150 rounded-md"
        >
          <img
              :src="comment.user?.avatar || 'https://www.gravatar.com/avatar/000?s=40&d=mp'"
              alt="User avatar"
              class="w-10 h-10 rounded-full object-cover shadow-sm"
          />
          <div class="flex-1 bg-gray-700 p-2 rounded-lg">
            <div class="flex items-baseline justify-between">
              <span class="font-semibold text-sm text-gray-100">
                {{ comment.user?.firstname || "Unknown" }} {{ comment.user?.lastname || "" }}
              </span>
              <span class="text-xs text-gray-400">{{ formatTime(comment.created_at) }}</span>
            </div>
            <p class="text-sm text-gray-300 mt-1">{{ comment.text }}</p>
          </div>
        </div>
      </div>
      <p v-else class="text-center text-gray-400 py-4">No comments yet.</p>
    </div>
    <div class="flex items-center space-x-3 border-t border-gray-700 pt-3">
      <textarea
          v-model="newComment"
          placeholder="Write a comment..."
          class="flex-1 border border-gray-600 rounded-lg p-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-blue-500 transition-shadow bg-gray-900 text-gray-200"
          rows="1"
          @keyup.enter="submitComment"
      ></textarea>
      <button
          @click="submitComment"
          :disabled="!newComment.trim() || !props.user?.id"
          class="bg-blue-600 text-white px-4 py-1.5 rounded-lg font-medium text-sm hover:bg-blue-700 disabled:bg-gray-500 disabled:cursor-not-allowed transition"
      >
        Post
      </button>
    </div>
  </div>
</template>

<style scoped>
.comment-section {
  border-radius: 8px;
  background-color: #ffffff;
  box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
}
.comments-list {
  border-top: 1px solid #e5e7eb;
}
.comments-list::-webkit-scrollbar {
  width: 6px;
}
.comments-list::-webkit-scrollbar-thumb {
  background-color: #d1d5db;
  border-radius: 4px;
}
.comments-list .hover\:bg-gray-700:hover {
  background-color: #e5e7eb;
}
.text-gray-200 {
  color: #4b5563;
}
.text-gray-100 {
  color: #374151;
}
.text-gray-300 {
  color: #6b7280;
}
.text-gray-400 {
  color: #9ca3af;
}
.bg-gray-700 {
  background-color: #f3f4f6;
}
.bg-gray-900 {
  background-color: #f9fafb;
}
.border-gray-700 {
  border-color: #e5e7eb;
}
.border-gray-600 {
  border-color: #d1d5db;
}
textarea {
  min-height: 40px;
  transition: all 0.2s ease-in-out;
  color: #1f2937;
}
textarea:focus {
  box-shadow: 0 0 5px rgba(59, 130, 246, 0.5);
}
.bg-blue-600 {
  background-color: #2563eb;
}
.hover\:bg-blue-700:hover {
  background-color: #1d4ed8;
}
.text-white {
  color: #ffffff;
}
.disabled\:bg-gray-500:disabled {
  background-color: #9ca3af;
}
@media (prefers-color-scheme: dark) {
  .comment-section {
    background-color: #1f2937;
    box-shadow: 0 10px 15px rgba(0, 0, 0, 0.3);
  }
  .comments-list {
    border-top: 1px solid #6b7280;
  }
  .comments-list::-webkit-scrollbar-thumb {
    background-color: #9ca3af;
  }
  .comments-list .hover\:bg-gray-700:hover {
    background-color: #374151;
  }
  .text-gray-200 {
    color: #d1d5db;
  }
  .text-gray-100 {
    color: #f3f4f6;
  }
  .text-gray-300 {
    color: #9ca3af;
  }
  .text-gray-400 {
    color: #6b7280;
  }
  .bg-gray-700 {
    background-color: #374151;
  }
  .bg-gray-900 {
    background-color: #111827;
  }
  .border-gray-700 {
    border-color: #6b7280;
  }
  .border-gray-600 {
    border-color: #4b5563;
  }
  textarea {
    color: #e5e7eb;
  }
  textarea:focus {
    box-shadow: 0 0 5px rgba(59, 130, 246, 0.7);
  }
  .disabled\:bg-gray-500:disabled {
    background-color: #4b5563;
  }
}
.bg-blue-600 {
  background-color: #2563eb;
}
.hover\:bg-blue-700:hover {
  background-color: #1d4ed8;
}
</style>