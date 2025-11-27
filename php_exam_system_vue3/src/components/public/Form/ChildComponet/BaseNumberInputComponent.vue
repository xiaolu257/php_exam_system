<template>
  <el-input-number
      v-model="formData[item.name]"
      :controls-position="additionalProps.controlsPosition"
      :disabled="item.options.disabled"
      :max="additionalProps.max"
      :min="additionalProps.min"
      :placeholder="item.options.placeholder"
      :step="additionalProps.step"
      v-bind="additionalProps"
  />
</template>

<script lang="ts" setup>
import {computed} from 'vue';
import {FormNumberInputConfig} from "@/utils/FormNumberInputConfig";

// Props 传递
const props = defineProps<{
  item: FormNumberInputConfig;
  formData: Record<string, any>;
}>();

// 计算额外的属性
const additionalProps = computed(() => {
  return {
    min: props.item.options.min ?? 0,  // 默认最小值为 0
    max: props.item.options.max ?? Infinity, // 最大值默认无限
    step: props.item.options.step ?? 1, // 默认步进值 1
    controlsPosition: props.item.options.controlsPosition ?? '', // 控制按钮位置
  };
  return {};
});
</script>
