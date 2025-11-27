<!-- TestAddForm.vue -->
<template>
  <el-button type="primary" @click="openDialog">新增</el-button>
  <el-dialog v-model="dialogVisible" :title="title" :width="width" align-center append-to-body center destroy-on-close>
    <el-row align="middle" justify="center">
      <TableBaseForm :form-config="formConfig" :on-cancel="closeDialog" :submitAction="submitAction"></TableBaseForm>
    </el-row>
  </el-dialog>
</template>

<script lang="ts" setup>
import {ref} from 'vue';
import {AbstractFormConfigItem} from "@/utils/FormInputConfig";
import TableBaseForm from "@/components/public/Form/BaseForm.vue";

// 定义 Props 的接口
interface Props {
  title: string;
  formConfig: AbstractFormConfigItem[];
  submitAction: (data: Record<string, any>, callback: () => void) => void; // 保存回调函数
  width?: number;
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
const submitAction = (data: Record<string, any>) => {
  props.submitAction(data, closeDialog);
}
</script>

