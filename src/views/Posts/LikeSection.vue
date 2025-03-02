<template>
  <button
      @click="handleLike"
      :class="{
      'text-blue-500': isLiked,
      'text-gray-600': !isLiked,
      'opacity-50 cursor-not-allowed': isLoading
    }"
      class="hover:text-blue-500 transition-colors"
      :disabled="isLoading"
  >
    {{ isLiked ? 'Liked' : 'Like' }}
    <span v-if="isLoading" class="ml-2">...</span>
  </button>
</template>

<script setup>
import { ref } from "vue";
import axios from "axios";

const props = defineProps({
  postId: {
    type: Number,
    required: true,
  },
  initialLikeStatus: {
    type: Boolean,
    required: true,
  },
});

const emit = defineEmits(["likeToggled"]);
const isLiked = ref(props.initialLikeStatus);
const isLoading = ref(false);

// Remove the watch entirely - we'll control state locally and via events
// This prevents parent updates from causing flicker

const handleLike = async () => {
  if (isLoading.value) return;

  const newLikeStatus = !isLiked.value; // Calculate intended state
  isLoading.value = true;

  try {
    const config = {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("token")}`,
      },
    };

    let response;
    if (isLiked.value) {
      // Unlike action
      response = await axios.delete(`/api/posts/${props.postId}/likes`, config);
    } else {
      // Like action
      response = await axios.post(`/api/posts/${props.postId}/likes`, {}, config);
    }

    if ([200, 201, 204].includes(response.status)) {
      isLiked.value = newLikeStatus; // Only update on success
      emit("likeToggled", props.postId, newLikeStatus);
    } else {
      throw new Error('Unexpected response status');
    }
  } catch (error) {
    console.error("Error toggling like:", error);
    // Optionally notify user of failure
    // alert('Failed to update like status');
  } finally {
    isLoading.value = false;
  }
};
</script>