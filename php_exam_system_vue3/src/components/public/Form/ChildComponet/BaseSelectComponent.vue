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
    (newOptions) => {
      // 规则 1：如果选择的选项不在关联选项数组中 → 清空当前选择的选项
      const key = props.item.name;
      const value = props.formData[key];
      if (!newOptions.find(o => o.value === value)) {
        props.formData[key] = ''
      }
    },
    {immediate: true}
)

</script>

<style scoped>
/* 自定义样式（如果需要） */
</style>
