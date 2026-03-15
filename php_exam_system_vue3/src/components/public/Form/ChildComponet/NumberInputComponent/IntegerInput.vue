<template>
  <el-input-number
      v-model="numModel"
      :disabled="config.disabled"
      :placeholder="config.placeholder"
      :min="config.min"
      :max="config.max"
      :step="config.step"
      :step-strictly="config.stepStrictly"
  />
</template>

<script lang="ts" setup>
import {computed} from "vue";
import type {IntegerInputConfig} from "@/utils/FormNumberInputConfig";


// Props 定义
const props = defineProps<{
  config: IntegerInputConfig;
  num: number;
}>();
const emit = defineEmits<{
  (e: 'update:num', value: number): void
}>()
const numModel = computed({
  get: () => props.num,
  set: (val: number) => emit('update:num', val),
})
</script>
