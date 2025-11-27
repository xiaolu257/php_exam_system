<template>
  <BaseTableManager
      :add-dialog-config="addDialogConfig"
      :edit-dialog-config="editDialogConfig"
      :export-data-to-excel="exportMyLogsToExcel"
      :table-config="tableConfig"
  >
  </BaseTableManager>
</template>

<script lang="ts" setup>

import BaseTableManager from "@/components/public/Table/BaseTableManager.vue";
import {ImageTableColumn, TableColumn, TextTableColumn} from "@/utils/MyTableTypeClass";
import {
  AbstractFormConfigItem,
  type AddDialogConfig,
  type EditDialogConfig,
  FormInputConfigFactory
} from "@/utils/FormInputConfig";
import {FormSelectConfigFactory} from "@/utils/FormSelectConfig";
import {FormUploadConfigFactory} from "@/utils/FormUploadConfig";
import type {TableConfig} from "@/utils/TableConfig";
import {exportMyLogsToExcel} from "@/api/Export";
import {addMyLogs, deleteMyLogsRows, getOnePageMyLogs, searchMyLogs, updateMyLogs} from "@/api/MyLogs";

const getOriginImageURL = (url: string): string => {
  return `http://xiaolu.cn/api/MyLogs/getImage?ImageUrl=${url}`;
};

const getThumbImageURL = (url: string): string => {
  return `http://xiaolu.cn/api/MyLogs/getImageThumb?ImageUrl=${url}`;
};
const addFormConfig: AbstractFormConfigItem[] = [
  FormInputConfigFactory.createEditableTextInput('task', '任务', 'task'), // 任务字段
  FormInputConfigFactory.createEditableTextAreaWithRows('description', '描述', 4), // 描述字段（文本域）
  FormSelectConfigFactory.createSingleSelect('status', '状态', [ // 状态字段（下拉选择）
    {label: '进行中', value: '进行中'},
    {label: '已完成', value: '已完成'},
    {label: '待测试', value: '待测试'},
  ], '状态'),
  FormUploadConfigFactory.createSingleImageSelector('log_image', '图片', getOriginImageURL, getThumbImageURL), // 图片字段（图片上传）
];
const editFormConfig: AbstractFormConfigItem[] = [
  FormInputConfigFactory.createReadOnlyTextInput('id', 'ID'), // ID 字段（只读）
  FormInputConfigFactory.createEditableTextInput('task', '任务', 'task'), // 任务字段
  FormInputConfigFactory.createEditableTextAreaWithRows('description', '描述', 4), // 描述字段（文本域）
  FormSelectConfigFactory.createSingleSelect('status', '状态', [ // 状态字段（下拉选择）
    {label: '进行中', value: '进行中'},
    {label: '已完成', value: '已完成'},
    {label: '待测试', value: '待测试'},
  ], '状态'),
  FormUploadConfigFactory.createSingleImageSelector('log_image_url', '图片', getOriginImageURL, getThumbImageURL), // 图片字段（图片上传）
];

const addDialogConfig: AddDialogConfig = {
  addFormTitle: '新增日志',
  addFormConfig: addFormConfig,
  addSubmitAction: addMyLogs
}
const editDialogConfig: EditDialogConfig = {
  editFormTitle: '编辑日志信息',
  editFormConfig: editFormConfig,
  editSubmitAction: updateMyLogs
}

const tableColumns: TableColumn[] = [
  new TextTableColumn(75, 'id', 'ID', true, true, 'left'), // ID 字段
  new TextTableColumn(200, 'task', '任务'),                // 任务字段
  new TextTableColumn(300, 'description', '描述'),         // 描述字段
  new ImageTableColumn(150, 'log_image_url', '图片', getOriginImageURL, getThumbImageURL),
  new TextTableColumn(100, 'status', '状态'),             // 状态字段
  new TextTableColumn(150, 'create_time', '创建时间', true, false),     // 创建时间字段
  new TextTableColumn(150, 'update_time', '更新时间', true, false),     // 更新时间字段
];

const tableConfig: TableConfig = {
  deleteRows: deleteMyLogsRows,
  getOnePageData: getOnePageMyLogs,
  searchData: searchMyLogs,
  tableColumns: tableColumns
}
</script>

<style scoped>

</style>