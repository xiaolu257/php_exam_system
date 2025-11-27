<template>
  <el-button :size="buttonSize" :type="buttonType" @click="openDialog">{{ controlName }}</el-button>
  <el-dialog v-model="dialogVisible" :title="title" :width="width?? 400" align-center append-to-body center
             destroy-on-close
             draggable>
    <el-row align="middle" justify="center">
      <TableBaseForm :form-config="formConfig" :init-data="initData" :on-cancel="closeDialog"
                     :required-update-fields="requiredUpdateFields"
                     :submitAction="submitAction" :width="width"></TableBaseForm>
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
  controlName?: string;
  buttonSize?: 'large' | 'default' | 'small';
  buttonType?: 'primary' | 'success' | 'warning' | 'danger' | 'info' | 'text' | 'default';
  initData: Record<string, any>;
  requiredUpdateFields?: string[];//针对修改时，必须包含的字段
}

// 接收父组件传递的 props
const props = defineProps<Props>();

const buttonSize = props.buttonSize ?? 'small';
const buttonType = props.buttonType ?? 'primary';
const controlName = props.controlName ?? '编辑';
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
  props.submitAction(data, () => {
    callback();
    closeDialog();
  });
}
</script>
