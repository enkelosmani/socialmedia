<script setup lang="ts">
import { ref, defineProps, defineEmits, onMounted } from "vue";
import axios from "axios";

defineProps<{ showModal: boolean }>();
const emit = defineEmits(["close"]);

// User settings data
const settings = ref({
  darkMode: false,
  notifications: true,
  privateProfile: false,
});

// Fetch user data on mount (mocked for now)
const fetchUserData = async () => {
  try {
    // Simulated API response
    const mockResponse = {
      data: {
        darkMode: localStorage.getItem("darkMode") === "true" || false,
        notifications: true,
        privateProfile: false,
      },
    };
    settings.value.darkMode = mockResponse.data.darkMode;
    settings.value.notifications = mockResponse.data.notifications;
    settings.value.privateProfile = mockResponse.data.privateProfile;
    applyDarkMode(settings.value.darkMode); // Apply dark mode on load
  } catch (error) {
    console.error("Error fetching user data:", error);
  }
};

// Apply dark mode by toggling a class on the document root
const applyDarkMode = (isDark: boolean) => {
  if (isDark) {
    document.documentElement.classList.add("dark");
  } else {
    document.documentElement.classList.remove("dark");
  }
  localStorage.setItem("darkMode", isDark.toString()); // Persist locally
};

onMounted(fetchUserData);

const saveSettings = async () => {
  try {
    // Simulated API PUT request success
    const mockResponse = { data: { message: "Settings updated" } };
    console.log("Settings saved (mocked):", mockResponse.data);
    applyDarkMode(settings.value.darkMode); // Apply dark mode on save
    emit("close"); // Close modal on success
  } catch (error) {
    console.error("Error saving settings:", error);
  }
};
</script>

<template>
  <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-8 w-full max-w-md">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Settings</h2>
        <button @click="emit('close')" class="text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-white transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <form @submit.prevent="saveSettings" class="space-y-6">
        <div class="flex items-center justify-between">
          <div>
            <label for="darkMode" class="text-sm font-medium text-gray-700 dark:text-gray-200">Dark Mode</label>
            <p class="text-xs text-gray-500 dark:text-gray-400">Enable dark mode for a more comfortable viewing experience.</p>
          </div>
          <input v-model="settings.darkMode" type="checkbox" id="darkMode" class="toggle-checkbox" />
        </div>

        <div class="flex items-center justify-between">
          <div>
            <label for="notifications" class="text-sm font-medium text-gray-700 dark:text-gray-200">Notifications</label>
            <p class="text-xs text-gray-500 dark:text-gray-400">Receive notifications for important updates.</p>
          </div>
          <input v-model="settings.notifications" type="checkbox" id="notifications" class="toggle-checkbox" />
        </div>

        <div class="flex items-center justify-between">
          <div>
            <label for="privateProfile" class="text-sm font-medium text-gray-700 dark:text-gray-200">Private Profile</label>
            <p class="text-xs text-gray-500 dark:text-gray-400">Make your profile visible only to you.</p>
          </div>
          <input v-model="settings.privateProfile" type="checkbox" id="privateProfile" class="toggle-checkbox" />
        </div>

        <div class="flex justify-end space-x-4">
          <button type="button" @click="emit('close')" class="px-6 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
            Cancel
          </button>
          <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            Save
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<style scoped>
.toggle-checkbox {
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
  width: 48px;
  height: 24px;
  background-color: #e2e8f0;
  border-radius: 12px;
  position: relative;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.toggle-checkbox:checked {
  background-color: #3b82f6;
}

.toggle-checkbox::before {
  content: '';
  position: absolute;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background-color: white;
  top: 2px;
  left: 2px;
  transition: transform 0.2s ease;
}

.toggle-checkbox:checked::before {
  transform: translateX(24px);
}

.rounded-xl {
  border-radius: 12px;
}

.shadow-2xl {
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

button {
  transition: background-color 0.2s ease;
}
</style>