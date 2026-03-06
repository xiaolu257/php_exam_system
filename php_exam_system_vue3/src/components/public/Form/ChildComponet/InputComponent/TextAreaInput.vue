<template>
  <el-input
      v-model="textModel"
      type="textarea"
      :autosize="config.autosize"
      :clearable="config.clearable"
      :disabled="config.disabled"
      :placeholder="config.placeholder"
  />
</template>

<script lang="ts" setup>
import {TextAreaInputConfig} from "@/utils/FormInputConfig";
import {computed} from "vue";


// Props 定义
const props = defineProps<{
  config: TextAreaInputConfig;
  text: string;
}>();
const emit = defineEmits<{
  (e: 'update:text', value: string): void
}>()
const textModel = computed({
  get: () => props.text,
  set: (val: string) => emit('update:text', val),
})
</script>
