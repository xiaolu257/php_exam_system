<template>
  <el-form ref="formRef" :model="formData" :rules="formRules" :style="{ width: width + 'px' }" class="form"
           inline-message>
    <template v-for="(item) in formConfig">
      <el-form-item :label="item.label" :label-width="labelWidth" :prop="item.name">
        <template v-if="item instanceof FormInputConfig">
          <BaseInputComponent :formData="formData" :item="item"/>
        </template>
        <template v-else-if="item instanceof FormSelectConfig">
          <BaseSelectComponent :formData="formData" :config="item"/>
        </template>
        <template v-else-if="item instanceof FormUploadConfig">
          <BaseUploadComponent :formData="formData" :item="item"/>
        </template>
        <template v-else-if="item instanceof FormNumberInputConfig">
          <BaseNumberInputComponent :formData="formData" :item="item"/>
        </template>
      </el-form-item>
    </template>
    <el-row justify="center">
      <el-button type="primary" @click="handleSave">{{ submitActionTitle }}</el-button>
      <el-button v-if="initData" type="success" @click="onReset">重置</el-button>
      <el-button v-else type="danger" @click="onReset">清空</el-button>
      <el-button v-if="onCancel" @click="onCancel">取消</el-button>
      <slot name="otherButtons"></slot>
    </el-row>
  </el-form>
</template>


<script lang="ts" setup>
import type {FormRules} from 'element-plus';
import {ElForm} from 'element-plus';
import {computed, reactive, ref, toRaw} from 'vue';
import {AbstractFormConfigItem, FormInputConfig, OptionsListInputConfig,} from "@/utils/FormInputConfig";
import BaseInputComponent from "@/components/public/Form/ChildComponet/BaseInputComponent.vue";
import {AssociateSelectConfig, FormSelectConfig,} from "@/utils/FormSelectConfig";
import {FormUploadConfig} from "@/utils/FormUploadConfig";
import BaseUploadComponent from "@/components/public/Form/ChildComponet/BaseUploadComponent.vue";
import BaseSelectComponent from "@/components/public/Form/ChildComponet/BaseSelectComponent.vue";
// 处理表单保存
import BaseNumberInputComponent from "@/components/public/Form/ChildComponet/BaseNumberInputComponent.vue";
import {FormNumberInputConfig} from "@/utils/FormNumberInputConfig";
import MyMessage from "@/utils/MyMessage";
import {isEqual} from "lodash-es";

interface Props {
  formConfig: AbstractFormConfigItem[];//表单配置项，决定有什么输入
  submitActionTitle?: string;//确认按钮的名称
  submitAction: (data: Record<string, any>, callback: () => void) => void; // 点击确认后的回调函数
  onCancel?: () => void;//点击取消后的回调函数
  width?: number;//表单的宽度
  initData?: Record<string, any>;//表单的初始化数据，一般用于编辑类表单
  updateIdentityFields?: string[];//针对修改时，必须包含的字段
}

const props = defineProps<Props>();
const width = props.width ?? 280;
const submitActionTitle = props.submitActionTitle ?? '确认';
const initData = props.initData ? Object.assign({}, props.initData) : null;
const updateIdentityFields = props.updateIdentityFields ?? ['id'];
// 计算最大 label 宽度
const labelWidth = computed(() => {
  const baseChineseWidth = 14;        // 中文字符宽度
  const uppercaseWidth = 11;          // 大写字母宽度
  const otherCharWidth = 8;           // 小写字母和其他字符宽度
  const requiredWidth = 11;           // 星号（*）的宽度
  let maxLabelWidth = 0;
  props.formConfig.forEach(item => {
    let itemWidth = 12; // 初始宽度，包括 padding
    for (let char of item.label) {
      if (/[\u4e00-\u9fa5]/.test(char)) {
        itemWidth += baseChineseWidth;
      } else if (/[A-Z]/.test(char)) {
        itemWidth += uppercaseWidth;
      } else {
        itemWidth += otherCharWidth;
      }
    }
    const isRequired = item.rules?.some(rule => rule.required) ?? false;
    if (isRequired) {
      itemWidth += requiredWidth; // 为必填项的星号预留宽度
    }
    if (itemWidth > maxLabelWidth) {
      maxLabelWidth = itemWidth;
    }
  });
  return `${maxLabelWidth}px`; // 返回字符串，绑定到 label-width 属性
});
const createFormData = () => {
  if (initData) {
    // 编辑场景，填入 initData
    return structuredClone(initData);
  } else {
    // 新增场景，填入默认值
    return props.formConfig.reduce((acc: Record<string, any>, item: AbstractFormConfigItem) => {
      if (item instanceof FormInputConfig) {
        if (item instanceof OptionsListInputConfig) {
          acc[item.name] = [];
        } else {
          acc[item.name] = '';
        }
      } else if (item instanceof FormSelectConfig) {
        if (item instanceof AssociateSelectConfig) {
          if (item.multiple) {
            acc[item.name] = [];
          } else {
            acc[item.name] = '';
          }
        }
      } else if (item instanceof FormNumberInputConfig) {
        acc[item.name] = item.options.min ?? 0;
      } else if (item instanceof FormUploadConfig) {
        acc[item.name] = null;
      }
      return acc;
    }, {} as Record<string, any>);
  }
}

// 创建响应式 formData 对象（深拷贝 initData 避免直接修改全局状态）
const formData = reactive<Record<string, any>>(createFormData());
// 表单引用
const formRef = ref<InstanceType<typeof ElForm> | null>(null);
const handleSave = () => {
  //默认为新增场景
  let submitData: Record<string, any> = structuredClone(toRaw(formData))
  let onSuccess = () => {}

  if (initData) {
    const changedData: Record<string, any> = {};

    Object.keys(submitData).forEach((key) => {
      const newVal = submitData[key];
      const oldVal = initData[key];

      if (!isEqual(newVal, oldVal)) {
        changedData[key] = newVal;
      }
    });

    if (Object.keys(changedData).length === 0) {
      MyMessage.warning("新数据与原数据一致，无需修改")
      return
    }
    // 加上 id 等标识字段
    updateIdentityFields.forEach((key) => {
      changedData[key] = submitData[key];
    });

    submitData = changedData
    onSuccess = () => {
      Object.keys(submitData).forEach((key) => {
        initData[key] = submitData[key];
      });
    }
  }
  formRef.value?.validate((valid) => {
    if (!valid) return;
    props.submitAction(submitData, onSuccess);
  })
};
const onReset = () => {
  if (initData) {
    Object.keys(formData).forEach((key) => {
      formData[key] = structuredClone(initData[key]);
    });
  } else {
    formRef.value?.resetFields();
  }
};
// 表单校验规则
const formRules: FormRules = props.formConfig.reduce((rules: FormRules, item: AbstractFormConfigItem) => {
  if (item.rules) {
    rules[item.name] = item.rules;
  }
  return rules;
}, {});
</script>

<style scoped>
.form {
  width: 90%;
  padding: 25px;
}
</style>
