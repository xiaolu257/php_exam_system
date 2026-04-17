<template>
  <el-input
      type="password"
      v-model="passwordModel"
      :clearable="config.clearable"
      :disabled="config.disabled"
      :placeholder="config.placeholder"
      :show-password="config.showPassword"
  />
</template>

<script lang="ts" setup>
import {PasswordInputConfig} from "@/utils/formInputConfig";
import {computed} from "vue";
// Props 定义
const props = defineProps<{
  config: PasswordInputConfig;
  password: string;
}>();
const emit = defineEmits<{
  (e: 'update:password', value: string): void
}>()
const passwordModel = computed({
  get: () => props.password,
  set: (val: string) => emit('update:password', val),
})
</script>
