<template>
  <template v-if="item.options instanceof DynamicMultipleInputOption">
    <el-col
        style="margin-bottom: 5px"
        v-for="(option, index) in formData[item.name]"
        :key="index"
    >
      <el-row :gutter="20" align="middle">
        <el-text style="margin-left: 20px">{{ String.fromCharCode(65 + index) }}</el-text>
        <el-col :span="16">
          <el-input v-model="formData[item.name][index]" placeholder="请输入选项内容"/>
        </el-col>
        <el-button type="danger" link @click="removeOption(index)">删除</el-button>
      </el-row>

    </el-col>
    <el-button style="margin-top: 2px" type="primary" plain :disabled="formData[item.name].length >= 10"
               @click="addOption">+ 添加选项
    </el-button>
  </template>
  <template v-else>
    <el-input
        v-model="formData[item.name]"
        :clearable="item.options.clearable"
        :disabled="item.options.disabled"
        :placeholder="item.options.placeholder"
        v-bind="additionalProps"
    />
  </template>

</template>

<script lang="ts" setup>
import {computed} from 'vue';
import {
  DynamicMultipleInputOption,
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

function addOption() {
  props.formData[props.item.name].push('');
}

function removeOption(index: number) {
  props.formData[props.item.name].splice(index, 1);
}
</script>
