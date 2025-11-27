<template>
  <el-table-column v-for="item in tableColumns" :fixed="item.fixed" :label="item.label" :min-width="item.min_width"
                   :prop="item.prop" :sortable="item.sortable" align="center">
    <template #default="scope">
      <el-text v-if="item instanceof TextTableColumn" :type="item.textType">{{ scope.row[item.prop] }}</el-text>
      <el-image
          v-else-if="item instanceof ImageTableColumn"
          :fit="item.fit"
          :preview-src-list="[item.getOriginImageURL(scope.row[item.prop])]"
          :src="item.getThumbImageURL(scope.row[item.prop])"
          lazy
          preview-teleported
          style="width: 50px; height: 50px"
      />
      <BaseEditFormDialog v-else-if="item instanceof PasswordTableColumn"
                          :button-type="item.editDialogConfig.editButtonType"
                          :control-name="item.editDialogConfig.editButtonName"
                          :form-config="item.editDialogConfig.editFormConfig"
                          :init-data="filterFormData(scope.row,item.editDialogConfig.editFormConfig)"
                          :submitAction="item.editDialogConfig.editSubmitAction"
                          :title="item.editDialogConfig.editFormTitle"
                          :width="item.editDialogConfig.editDialogWidth"></BaseEditFormDialog>
    </template>
  </el-table-column>
</template>
<script lang="ts" setup>
// 定义 Props 的接口
import {ImageTableColumn, PasswordTableColumn, TableColumn, TextTableColumn} from "@/utils/MyTableTypeClass";
import BaseEditFormDialog from "@/components/public/Table/BaseEditFormDialog.vue";
import {AbstractFormConfigItem} from "@/utils/FormInputConfig";

interface Props {
  tableColumns: TableColumn[];
}

// 定义 props 以接收父组件传递的参数
defineProps<Props>();
const filterFormData = (scopeRow: Record<string, any>, formConfig: AbstractFormConfigItem[]): Record<string, any> => {
  const filteredData: Record<string, any> = {};
  // 遍历表单配置，根据每个项的 name 从 scope.row 中筛选数据
  formConfig.forEach((item) => {
    if (scopeRow.hasOwnProperty(item.name)) {
      filteredData[item.name] = scopeRow[item.name]; // 将符合条件的数据加入 filteredData
    }
  });
  return filteredData;
}
</script>
<style>
</style>