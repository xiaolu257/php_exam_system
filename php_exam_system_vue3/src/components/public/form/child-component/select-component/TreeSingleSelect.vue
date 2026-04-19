<template>
  <el-tree-select
      v-model="currentSelectModel"
      :data="item.treeData"
      show-checkbox
      check-strictly
      :render-after-expand="false"
      :disabled="item.disabled"
      :clearable="item.clearable"
      :placeholder="item.placeholder"
  />
</template>

<script lang="ts" setup>
import {TreeSelectConfig,} from "@/utils/formSelectConfig";
import {computed} from "vue";
import {ElTreeSelect} from "element-plus";

// Props 定义
const props = defineProps<{
  item: TreeSelectConfig;
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
