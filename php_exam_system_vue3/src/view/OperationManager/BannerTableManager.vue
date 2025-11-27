<template>
  <BaseTableManager
      :add-dialog-config="addDialogConfig"
      :edit-dialog-config="editDialogConfig"
      :export-data-to-excel="exportBannersToExcel"
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
import {FormUploadConfigFactory} from "@/utils/FormUploadConfig";
import {exportBannersToExcel} from "@/api/Export";
import type {TableConfig} from "@/utils/TableConfig";
import {FormNumberInputConfigFactory} from "@/utils/FormNumberInputConfig";
import {FormSelectConfigFactory} from "@/utils/FormSelectConfig";
import {addBanner, deleteBannersRows, getOnePageBanners, searchBanners, updateBanner} from "@/api/OperationManager";

// 获取轮播图原图 URL
const getOriginImageURL = (url: string): string => {
  return `http://xiaolu.cn/api/OperationManager/getBanner?BannerUrl=${url}`;
};

// 获取轮播图缩略图 URL（如果需要）
const getThumbImageURL = (url: string): string => {
  return `http://xiaolu.cn/api/OperationManager/getBannerThumb?BannerUrl=${url}`;
};

// 新增表单配置
const addFormConfig: AbstractFormConfigItem[] = [
  FormInputConfigFactory.createEditableTextInput('title', '标题', 'title'),
  FormNumberInputConfigFactory.createIntegerInput('priority', '优先级'),
  FormUploadConfigFactory.createSingleImageSelector('image', '轮播图', getThumbImageURL, getOriginImageURL, [], false, false, 5 * 1024 * 1024),
];

// 编辑表单配置
const editFormConfig: AbstractFormConfigItem[] = [
  FormInputConfigFactory.createReadOnlyTextInput('id', 'ID'),
  FormInputConfigFactory.createEditableTextInput('title', '标题', 'title'),
  FormNumberInputConfigFactory.createIntegerInput('priority', '优先级'),
  FormSelectConfigFactory.createSingleSelect('status', '状态', [
    {label: '显示', value: 1},
    {label: '隐藏', value: 0},
  ]),
  FormUploadConfigFactory.createSingleImageSelector('image_url', '轮播图', getThumbImageURL, getOriginImageURL, [], false, false, 5 * 1024 * 1024),
  FormInputConfigFactory.createReadOnlyTextInput('create_time', '创建时间'),
  FormInputConfigFactory.createReadOnlyTextInput('update_time', '更新时间')
];

/*// 新增接口
const addSubmitAction = async (data: Record<string, any>, callback: () => void) => {
  await submitAction('添加轮播图', 'OperationManager/addBanner', data, 'image', callback);
};

// 编辑接口
const editSubmitAction = async (data: Record<string, any>, callback: () => void) => {
  await submitAction('修改轮播图信息', 'Banner/updateBanner', data, 'image_url', callback);
};*/

// 对话框配置
const addDialogConfig: AddDialogConfig = {
  addFormTitle: '新增轮播图',
  addFormConfig: addFormConfig,
  addSubmitAction: addBanner
};
const editDialogConfig: EditDialogConfig = {
  editFormTitle: '编辑轮播图',
  editFormConfig: editFormConfig,
  editSubmitAction: updateBanner
};

// 表格列配置
const tableColumns: TableColumn[] = [
  new TextTableColumn(75, 'id', 'ID', true, true, 'left'),
  new TextTableColumn(150, 'title', '标题', true),
  new ImageTableColumn(200, 'image_url', '轮播图', getOriginImageURL, getThumbImageURL),
  new TextTableColumn(100, 'priority', '优先级', true),
  new TextTableColumn(100, 'status', '状态', true),
  new TextTableColumn(150, 'create_time', '创建时间', true, false),
  new TextTableColumn(150, 'update_time', '更新时间', true, false)

];

// 表格配置
const tableConfig: TableConfig = {
  deleteRows: deleteBannersRows,
  getOnePageData: getOnePageBanners,
  searchData: searchBanners,
  tableColumns: tableColumns
};

</script>

<style scoped>
</style>
