<script setup>

import {computed} from "vue";

const props = defineProps({
  label: String,
  modelValue: String,
  placeholder: String,
  required: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['update:modelValue']);

const inputValue = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
});

</script>

<template>
  <div>
    <label class="block text-sm/6 font-medium text-gray-900">{{ label }}</label>
    <div class="mt-2">
      <input
          type="text"
          v-model="inputValue"
          class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
          :placeholder="placeholder"
          :required="required"
      />
    </div>
  </div>
</template>
