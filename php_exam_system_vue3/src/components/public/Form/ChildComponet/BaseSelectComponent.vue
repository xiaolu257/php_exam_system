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

    <!-- 分组选项 -->
    <template v-else-if="item.options instanceof GroupedSelectOption || item.options instanceof GroupedMultipleSelectOption">
      <el-option-group
          v-for="group in item.options.groupedOptions"
          :key="group.groupLabel"
          :label="group.groupLabel"
      >
        <el-option
            v-for="option in group.options"
            :key="option.value"
            :label="option.label"
            :value="option.value"
        />
      </el-option-group>
    </template>
  </el-select>
</template>

<script lang="ts" setup>
import {
  type FormSelectConfig,
  MultipleSelectOption,
  GroupedSelectOption,
  GroupedMultipleSelectOption, SingleSelectOption
} from "@/utils/FormSelectConfig";
import {computed} from "vue";

// Props 定义
const props = defineProps<{
  item: FormSelectConfig;
  formData: Record<string, any>;
}>();

// 计算额外的属性
const additionalProps = computed(() => {
  if (props.item.options instanceof MultipleSelectOption || props.item.options instanceof GroupedMultipleSelectOption) {
    return { multiple: true };
  }
  return {};
});
</script>

<style scoped>
/* 自定义样式（如果需要） */
</style>
