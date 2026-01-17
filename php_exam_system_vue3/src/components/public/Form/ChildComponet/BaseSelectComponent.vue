<template>
  <el-select
      v-model="formData[item.name]"
      :disabled="item.options.disabled"
      :clearable="item.options.clearable"
      :placeholder="item.options.placeholder"
      v-bind="additionalProps"
  >
    <!-- 普通选项 -->
    <template v-if="item.options instanceof SingleSelectOption || item.options instanceof MultipleSelectOption">
      <el-option
          v-for="option in item.options.options"
          :key="option.value"
          :label="option.label"
          :value="option.value"
      />
    </template>
    <!-- 关联选项 -->
    <template v-else-if="item.options instanceof AssociateSingleSelectOption">
      <el-option
          v-for="option in associateOptions"
          :key="option.value"
          :label="option.label"
          :value="option.value"
      />
    </template>
  </el-select>
</template>

<script lang="ts" setup>
import {
  AssociateSingleSelectOption,
  type FormSelectConfig,
  MultipleSelectOption,
  SingleSelectOption
} from "@/utils/FormSelectConfig";
import {computed, watch} from "vue";

// Props 定义
const props = defineProps<{
  item: FormSelectConfig;
  formData: Record<string, any>;
}>();

// 计算额外的属性
const additionalProps = computed(() => {
  if (props.item.options instanceof MultipleSelectOption) {
    return {multiple: true};
  }
  return {};
});
const associateOptions = computed(() => {
  return props.item.options instanceof AssociateSingleSelectOption
      ? props.item.options.associateFunction(props.formData).value
      : []
})
watch(
    associateOptions,
    (newOptions, oldOptions = []) => {
      // 规则 1：删除选项 → 清空正确答案
      if (newOptions.length < oldOptions.length) {
        props.formData[props.item.name] = ''
        return
      }
    },
    {immediate: true}
)

</script>

<style scoped>
/* 自定义样式（如果需要） */
</style>
