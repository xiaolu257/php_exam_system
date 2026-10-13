<!-- TestAddForm.vue -->
<template>
  <el-button :size="dialogConfig.buttonSize" :type="dialogConfig.buttonType ?? 'primary'" @click="openDialog">
    {{ dialogConfig.buttonText ?? '默认按键名' }}
  </el-button>
  <el-dialog v-model="dialogVisible" :title="dialogConfig.title" :width="dialogConfig.width ?? 400" align-center
             append-to-body center destroy-on-close>
    <el-row align="middle" justify="center">
      <BaseForm :form-config="dialogConfig.formConfig" :on-cancel="closeDialog"
                :submitAction="submitAction"
                :width="dialogConfig.width"></BaseForm>
    </el-row>
  </el-dialog>
</template>

<script lang="ts" setup>
import {ref} from 'vue';
import BaseForm from "@/components/public/form/BaseForm.vue";
import type {DialogConfig} from "@/components/public/form/formTypes";

// 定义 Props 的接口
interface Props {
  dialogConfig: DialogConfig
}

// 接收父组件传递的 props
const props = defineProps<Props>();

// 定义控制 dialog 显示与隐藏的响应式变量
const dialogVisible = ref(false);

// 打开 dialog 的方法
const openDialog = () => {
  dialogVisible.value = true;
};
// 关闭 dialog 的方法
const closeDialog = () => {
  dialogVisible.value = false;
};
const submitAction = (data: Record<string, any>, onSuccess: () => void) => {
  const newOnSuccess = props.dialogConfig.closeDialogAfterSuccess ? () => {
    closeDialog();
    onSuccess();
  } : onSuccess;
  props.dialogConfig.submitAction(data, newOnSuccess);
}

</script>

