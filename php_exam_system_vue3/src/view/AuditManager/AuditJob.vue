<template>
  <BaseTableManager
      :edit-dialog-config="editDialogConfig"
      :export-data-to-excel="exportJobsToExcel"
      :table-config="tableConfig"
  />
</template>

<script lang="ts" setup>
import BaseTableManager from "@/components/public/Table/BaseTableManager.vue";
import {TextTableColumn} from "@/utils/MyTableTypeClass";
import {type EditDialogConfig, FormInputConfigFactory} from "@/utils/FormInputConfig";
import type {TableConfig} from "@/utils/TableConfig";
import {exportJobsToExcel} from "@/api/Export";
import {updateItem} from "@/api/utils/BaseAPI";

import {computed} from 'vue';
import {FormSelectConfigFactory} from "@/utils/FormSelectConfig";
import {pick} from "lodash";
import {getOnePageJobs, searchJobs} from "@/api/AuditManager";

// **编辑工作配置**
const editDialogConfig = computed<EditDialogConfig>(() => ({
  editButtonName: '审核',
  editFormTitle: '编辑工作',
  editFormConfig: [
    FormInputConfigFactory.createReadOnlyTextInput('id', 'ID'),
    FormInputConfigFactory.createReadOnlyTextInput('title', '工作标题'),
    FormInputConfigFactory.createReadOnlyTextInput('category_name', '工作分类'),
    FormInputConfigFactory.createReadOnlyTextInput('publisher_name', '发布者'),
    FormInputConfigFactory.createReadOnlyTextInput('area_name', '工作区域'),
    FormInputConfigFactory.createReadOnlyTextInput('salary_type', '结算方式'),
    FormInputConfigFactory.createReadOnlyTextInput('salary_min', '最低薪资'),
    FormInputConfigFactory.createReadOnlyTextInput('salary_max', '最高薪资'),
    FormSelectConfigFactory.createSingleSelect('review_status', '审核状态', [
      {label: '待审核', value: '待审核'},
      {label: '审核通过', value: '审核通过'},
      {label: '审核未通过', value: '审核未通过'},
    ], '选择审核状态'),
    FormInputConfigFactory.createReadOnlyTextInput('create_time', '创建时间')
  ],
  editSubmitAction: async (data: Record<string, any>, callback: () => void) => {
    const postData = pick(data, ['id', 'review_status']);
    await updateItem(postData, callback, 'AuditManager/auditJob');
  }

}));

// **表格配置**
const tableConfig: TableConfig = {
  getOnePageData: getOnePageJobs,
  searchData: searchJobs,
  tableColumns: [
    new TextTableColumn(75, 'id', 'ID', 'custom', true, 'left'),
    new TextTableColumn(200, 'title', '工作标题', 'custom'),
    new TextTableColumn(150, 'category_name', '工作分类', false, false),  // 需要后端返回分类名称
    new TextTableColumn(150, 'publisher_name', '发布者', false, false),  // 需要后端返回发布者昵称
    new TextTableColumn(150, 'area_name', '工作区域', false, false), // 显示工作区域
    new TextTableColumn(120, 'salary_type', '结算方式', 'custom'),
    new TextTableColumn(120, 'salary_min', '最低薪资', 'custom'),
    new TextTableColumn(120, 'salary_max', '最高薪资', 'custom'),
    new TextTableColumn(150, 'review_status', '审核状态', 'custom'),
    new TextTableColumn(150, 'create_time', '创建时间', 'custom', false)
  ]
};
</script>

<style scoped>
</style>
