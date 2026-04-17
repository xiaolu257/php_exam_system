<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 p-6">
    <div class="w-full max-w-md bg-white shadow rounded-2xl p-6 space-y-4">
      <h1 class="text-xl font-semibold">CamelCase → Kebab-case</h1>

      <input
          v-model="input"
          class="w-full border rounded p-2"
          placeholder="Enter ExamPaperManagement"
      />

      <button
          class="w-full bg-black text-white rounded p-2 hover:opacity-80"
          @click="handleConvert"
      >
        Convert
      </button>

      <div class="p-2 border rounded bg-gray-50 min-h-[40px]">
        {{ output }}
      </div>
    </div>
  </div>
</template>

<script setup>
import {ref} from 'vue'

const input = ref('')
const output = ref('')

const toKebabCase = (str) => {
  if (!str) return ''
  return str
      .replace(/([a-z0-9])([A-Z])/g, '$1-$2')
      .replace(/([A-Z])([A-Z][a-z])/g, '$1-$2')
      .toLowerCase()
}

const handleConvert = () => {
  output.value = toKebabCase(input.value.trim())
}
</script>
