<template>
  <el-button :size="editDialogConfig.buttonSize ?? 'small'" :type="editDialogConfig.buttonType ?? 'primary'"
             @click="openDialog">
    {{ editDialogConfig.controlButtonName ?? '编辑' }}
  </el-button>
  <el-dialog v-model="dialogVisible" :title="editDialogConfig.title" :width="editDialogConfig.width?? 400" align-center
             append-to-body center
             destroy-on-close
             draggable>
    <el-row align="middle" justify="center">
      <BaseForm :form-config="editDialogConfig.formConfig"
                :width="editDialogConfig.width"
                :init-data="editDialogConfig.initData"
                :on-cancel="closeDialog"
                :submitAction="submitAction"
                :update-identity-fields="editDialogConfig.updateIdentityFields"
      />
    </el-row>
  </el-dialog>
</template>

<script lang="ts" setup>
import {ref} from 'vue';
import type {EditDialogConfig} from "@/components/public/form/formTypes";
import BaseForm from "@/components/public/form/BaseForm.vue";

// 定义 Props 的接口
interface Props {
  editDialogConfig: EditDialogConfig;
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
const submitAction = (data: Record<string, any>, callback: () => void) => {
  props.editDialogConfig.submitAction(data, () => {
    callback();
    closeDialog();
  });
}
</script>
