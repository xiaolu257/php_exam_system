<template>
  <el-select
      v-model="currentSelectModel"
      :disabled="item.disabled"
      :clearable="item.clearable"
      :placeholder="item.placeholder"
  >
    <el-option
        v-for="option in options"
        :key="option.value"
        :label="option.label"
        :value="option.value"
    />
  </el-select>
</template>

<script lang="ts" setup>
import {CommonSelectConfig,} from "@/utils/FormSelectConfig";
import {computed} from "vue";

// Props 定义
const props = defineProps<{
  item: CommonSelectConfig;
  options: { label: string; value: any }[];
  currentSelect: any;
}>();

const emit = defineEmits<{
  (e: 'update:currentSelect', value: any): void
}>()
const currentSelectModel = computed({
  get: () => props.currentSelect ?? '',
  set: (val: any) => emit('update:currentSelect', val)
})
</script>

<style scoped>
/* 自定义样式（如果需要） */
</style>
