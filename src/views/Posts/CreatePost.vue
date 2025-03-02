<script setup>
import { defineProps, defineEmits, reactive, ref } from "vue";
import { storePost } from "@/services/posts.js";
import { useUserStore } from "@/stores/userSession.js";
import { getPosts } from "@/services/posts.js";

const userSession = useUserStore();
const props = defineProps({ showModal: Boolean });
const emit = defineEmits(["close", "postCreated"]);

const form = reactive({
  title: "",
  image: null,
  likes: 0,
  user_id: userSession.user?.id || null,
});

const imagePreview = ref(null);
const isSubmitting = ref(false); // Added for loading state

const handleImageUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    form.image = file;
    imagePreview.value = URL.createObjectURL(file);
  }
};

const removeImage = () => {
  form.image = null;
  if (imagePreview.value) {
    URL.revokeObjectURL(imagePreview.value);
  }
  imagePreview.value = null;
};

const submitPost = async () => {
  if (!form.title) {
    alert("Please provide a title.");
    return;
  }

  isSubmitting.value = true;
  try {
    const formData = new FormData();
    formData.append("title", form.title);
    if (form.image) {
      formData.append("image", form.image);
    }
    formData.append("user_id", form.user_id);

    const res = await storePost(formData);
    console.log("Post creation response:", res);

    if (res && (res.status === 201 || res.status === 200)) {
      emit("postCreated");
      resetForm();
    } else {
      throw new Error("Unexpected response status");
    }
  } catch (error) {
    const errorMessage = error?.data?.message || "Unknown error";
    console.error("Error submitting post:", error?.data || error.message);
    const postsRes = await getPosts();
    const latestPost = postsRes?.data?.result?.data[0];
    if (latestPost && latestPost.title === form.title) {
      console.log("Post was created despite error:", latestPost);
      emit("postCreated");
      resetForm();
    } else {
      alert(`Error creating post: ${errorMessage}`);
    }
  } finally {
    isSubmitting.value = false;
  }
};

const resetForm = () => {
  form.title = "";
  form.image = null;
  if (imagePreview.value) {
    URL.revokeObjectURL(imagePreview.value);
  }
  imagePreview.value = null;
  form.likes = 0;
  emit("close");
};
</script>

<template>
  <transition name="modal">
    <div v-if="showModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-60 z-50 p-4">
      <div
          class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 rounded-xl shadow-2xl w-full max-w-md max-h-[85vh] overflow-y-auto transform transition-all duration-300">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 tracking-tight">Create a Post</h2>
          <button @click="$emit('close')"
                  class="p-1 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-6">
          <div class="flex items-start">
            <img
                class="w-12 h-12 rounded-full mr-4 border-2 border-indigo-400 shadow-sm object-cover"
                :src="userSession.user?.avatar || 'http://lilithaengineering.co.za/wp-content/uploads/2017/08/person-placeholder.jpg'"
                alt="User Avatar"
            />
            <div class="flex-1">
              <input
                  v-model="form.title"
                  class="w-full text-lg text-gray-900 dark:text-gray-100 font-medium bg-transparent border-b-2 border-gray-300 dark:border-gray-600 focus:border-indigo-500 dark:focus:border-indigo-400 outline-none transition-colors duration-200 placeholder-gray-400 dark:placeholder-gray-500"
                  placeholder="What's on your mind?"
                  type="text"
              />
            </div>
          </div>

          <!-- Image Upload and Preview -->
          <div class="relative">
            <label
                class="flex items-center text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 cursor-pointer transition-colors duration-200">
              <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                   xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
              <span class="text-sm font-medium">Add a Photo</span>
              <input type="file" @change="handleImageUpload" accept="image/*" class="hidden"/>
            </label>
            <div v-if="imagePreview" class="mt-4 relative group">
              <img :src="imagePreview"
                   class="w-full rounded-lg shadow-md object-cover max-h-64 transition-transform duration-300 group-hover:scale-105"
                   alt="Preview"/>
              <button
                  @click="removeImage"
                  class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-8 h-8 flex items-center justify-center opacity-75 hover:opacity-100 hover:bg-red-600 transition-all duration-200"
                  title="Remove image"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div
            class="flex justify-end px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800">
          <button
              @click="$emit('close')"
              class="py-2 px-5 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-all duration-200"
              :disabled="isSubmitting"
          >
            Cancel
          </button>
          <button
              @click="submitPost"
              class="py-2 px-5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-all duration-200 ml-3 flex items-center"
              :disabled="isSubmitting"
          >
            <span v-if="isSubmitting" class="animate-spin mr-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                   xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 12a8 8 0 018-8v8h8a8 8 0 11-16 0z"/>
              </svg>
            </span>
            {{ isSubmitting ? 'Posting...' : 'Post' }}
          </button>
        </div>
      </div>
    </div>
  </transition>
</template>

<style scoped>
/* Modal Transition */
.modal-enter-active,
.modal-leave-active {
  transition: all 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
  transform: scale(0.95);
}

/* Container */
.max-w-md {
  max-width: 28rem;
}

.shadow-2xl {
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

.rounded-xl {
  border-radius: 12px;
}

/* Input */
input {
  transition: border-color 0.2s ease;
}

/* Buttons */
button {
  transition: all 0.2s ease;
}

button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Dark Mode Adjustments */
.dark .bg-gradient-to-br {
  background: linear-gradient(to bottom right, #1f2937, #111827);
}

/* Image Preview */
img {
  transition: transform 0.3s ease;
}
</style>

