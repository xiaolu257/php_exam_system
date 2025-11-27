<template>
  <el-input
      v-model="formData[item.name]"
      :clearable="item.options.clearable"
      :disabled="item.options.disabled"
      :placeholder="item.options.placeholder"
      v-bind="additionalProps"
  />
</template>

<script lang="ts" setup>
import {computed} from 'vue';
import {
  type FormInputConfig,
  PasswordInputOption,
  TextAreaWithAutosizeInputOption,
  TextAreaWithRowsInputOption
} from "@/utils/FormInputConfig";


// Props 定义
const props = defineProps<{
  item: FormInputConfig;
  formData: Record<string, any>;
}>();

// 计算额外的属性
const additionalProps = computed(() => {
  if (props.item.options instanceof PasswordInputOption) {
    return {type: 'password', showPassword: props.item.options.showPassword};
  } else if (props.item.options instanceof TextAreaWithRowsInputOption) {
    return {type: 'textarea', rows: props.item.options.rows};
  } else if (props.item.options instanceof TextAreaWithAutosizeInputOption) {
    return {type: 'textarea', autosize: props.item.options.autosize};
  }
  return {};
});
</script>
