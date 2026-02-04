<!-- TestAddForm.vue -->
<template>
  <el-button type="primary" @click="openDialog">新增</el-button>
  <el-dialog v-model="dialogVisible" :title="addDialogConfig.title" :width="addDialogConfig.width ?? 400" align-center
             append-to-body center destroy-on-close>
    <el-row align="middle" justify="center">
      <BaseForm :form-config="addDialogConfig.formConfig" :on-cancel="closeDialog"
                :submitAction="submitAction"></BaseForm>
    </el-row>
  </el-dialog>
</template>

<script lang="ts" setup>
import {ref} from 'vue';
import BaseForm from "@/components/public/Form/BaseForm.vue";
import type {AddDialogConfig} from "@/components/public/Form/Types";

// 定义 Props 的接口
interface Props {
  addDialogConfig: AddDialogConfig
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
  const newOnSuccess = props.addDialogConfig.closeDialogAfterSuccess ? () => {
    closeDialog();
    onSuccess();
  } : onSuccess;
  props.addDialogConfig.submitAction(data, newOnSuccess);
}

</script>

