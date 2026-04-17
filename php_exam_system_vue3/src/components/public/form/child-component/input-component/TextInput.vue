<template>
  <el-input
      v-model="textModel"
      :clearable="config.clearable"
      :disabled="config.disabled"
      :placeholder="config.placeholder"
  />
</template>

<script lang="ts" setup>
import {TextInputConfig} from "@/utils/formInputConfig";
import {computed} from "vue";


// Props 定义
const props = defineProps<{
  config: TextInputConfig;
  text: string | number;
}>();
const emit = defineEmits<{
  (e: 'update:text', value: string | number): void
}>()
const textModel = computed({
  get: () => props.text,
  set: (val: string | number) => emit('update:text', val),
})
</script>
