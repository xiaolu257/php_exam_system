<template>
  <BaseTableManager
      :add-dialog-config="addDialogConfig"
      :edit-dialog-config="editDialogConfig"
      :export-data-to-excel="exportJobCategoriesToExcel"
      :table-config="tableConfig"
  />
</template>

<script lang="ts" setup>
import BaseTableManager from "@/components/public/Table/BaseTableManager.vue";
import {TextTableColumn} from "@/utils/MyTableTypeClass";
import {type AddDialogConfig, type EditDialogConfig, FormInputConfigFactory} from "@/utils/FormInputConfig";
import type {TableConfig} from "@/utils/TableConfig";
import {exportJobCategoriesToExcel} from "@/api/Export";
import {deleteJobCategoriesRows, getOnePageJobCategories, searchJobCategories} from "@/api/OperationManager";

import {computed, onMounted, shallowRef} from 'vue';
import {myGet} from "@/api/utils/axios";
import {FormSelectConfigFactory} from "@/utils/FormSelectConfig";
import {addItem, updateItem} from "@/api/utils/BaseAPI";

const parentOptions = shallowRef<{ label: string; value: number }[]>([]);

// 封装获取父分类的方法
async function fetchParentCategories() {
  myGet('OperationManager/getParentJobCategories')
      .then((res) => {
        parentOptions.value = [
          {label: '无', value: 0},
          ...res.map((item: any) => ({
            label: item.name,
            value: item.id
          }))
        ];
      });
}

onMounted(fetchParentCategories);

// 直接计算 addDialogConfig 和 editDialogConfig，避免额外的 computed 嵌套
const editDialogConfig = computed<EditDialogConfig>(() => ({
  editFormTitle: '编辑工作分类',
  editFormConfig: [
    FormInputConfigFactory.createReadOnlyTextInput('id', 'ID'),
    FormInputConfigFactory.createEditableTextInput('name', '分类名称', 'name'),
    FormSelectConfigFactory.createSingleSelect('parent_id', '父分类', parentOptions.value, '父分类'),
    FormInputConfigFactory.createReadOnlyTextInput('create_time', '创建时间')
  ],
  editSubmitAction: async (data: Record<string, any>, callback: () => void) => {
    await updateItem(
        data,
        () => {
          callback()
          fetchParentCategories()
        },
        'OperationManager/updateJobCategories');
  }
}));

const addDialogConfig = computed<AddDialogConfig>(() => ({
  addFormTitle: '新增工作分类',
  addFormConfig: [
    FormInputConfigFactory.createEditableTextInput('name', '分类名称', 'name'),
    FormSelectConfigFactory.createSingleSelect('parent_id', '父分类', parentOptions.value, '父分类')
  ],
  addSubmitAction: async (data: Record<string, any>, callback: () => void) => {
    await addItem(
        data,
        () => {
          callback()
          fetchParentCategories()
        },
        'OperationManager/addJobCategories');
  }
}));

const tableConfig: TableConfig = {
  deleteRows: deleteJobCategoriesRows,
  getOnePageData: getOnePageJobCategories,
  searchData: searchJobCategories,
  tableColumns: [
    new TextTableColumn(75, 'id', 'ID', true, true, 'left'),
    new TextTableColumn(200, 'name', '分类名称'),
    new TextTableColumn(150, 'parent_id', '父分类ID', true),
    new TextTableColumn(150, 'create_time', '创建时间', true, false)
  ]
};
</script>

<style scoped>
</style>
