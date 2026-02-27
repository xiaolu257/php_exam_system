<template>
  <el-select
      v-model="currentSelectModel"
      :disabled="item.disabled"
      :clearable="item.clearable"
      :placeholder="item.placeholder"
  >
    <el-option
        v-for="option in associateOptions"
        :key="option.value"
        :label="option.label"
        :value="option.value"
    />
  </el-select>
</template>

<script lang="ts" setup>
import {AssociateSelectConfig,} from "@/utils/FormSelectConfig";
import {computed, watch} from "vue";

// Props 定义
const props = defineProps<{
  item: AssociateSelectConfig;
  associateOptions: any[];
  currentSelect: any;
}>();

const emit = defineEmits<{
  (e: 'update:currentSelect', value: any): void
}>()
const currentSelectModel = computed({
  get: () => props.currentSelect ?? '',
  set: (val: any) => emit('update:currentSelect', val)
})
watch(
    () => props.associateOptions,
    (newOptions, oldOptions = []) => {
      //有选项被删除,如果选择的选项不在关联选项数组中 → 清空无效的选项
      if (newOptions.length < oldOptions.length) {
        const validValues = newOptions.map(o => o.value)
        const current = props.currentSelect ?? ''
        if (!validValues.includes(current)) {
          emit('update:currentSelect', '')
        }
      }
    },
    {immediate: true}
)
</script>

<style scoped>
/* 自定义样式（如果需要） */
</style>
