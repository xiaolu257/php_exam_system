<template>
  <BaseTableManager
      :add-dialog-config="addDialogConfig"
      :edit-dialog-config="editDialogConfig"
      :export-data-to-excel="exportJobAreasToExcel"
      :table-config="tableConfig"
  />
</template>

<script lang="ts" setup>
import BaseTableManager from "@/components/public/Table/BaseTableManager.vue";
import {TextTableColumn} from "@/utils/MyTableTypeClass";
import {type AddDialogConfig, type EditDialogConfig, FormInputConfigFactory} from "@/utils/FormInputConfig";
import type {TableConfig} from "@/utils/TableConfig";
import {exportJobAreasToExcel} from "@/api/Export";


import {computed, onMounted, shallowRef} from 'vue';
import {myGet} from "@/api/utils/axios";
import {FormSelectConfigFactory} from "@/utils/FormSelectConfig";
import {deleteJobAreasRows, getOnePageJobAreas, searchJobAreas} from "@/api/OperationManager";
import {addItem, updateItem} from "@/api/utils/BaseAPI";

const parentOptions = shallowRef<{ label: string; value: number }[]>([]);

// 封装获取父区域的方法
async function fetchParentAreas() {
  myGet('OperationManager/getParentJobAreas')
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

onMounted(fetchParentAreas);

// 直接计算 addDialogConfig 和 editDialogConfig，避免额外的 computed 嵌套
const addDialogConfig = computed<AddDialogConfig>(() => ({
  addFormTitle: '新增工作区域',
  addFormConfig: [
    FormInputConfigFactory.createEditableTextInput('name', '区域名称', 'name'),
    FormSelectConfigFactory.createSingleSelect('parent_id', '父区域', parentOptions.value, '父区域')
  ],
  addSubmitAction: async (data: Record<string, any>, callback: () => void) => {
    await addItem(
        data,
        () => {
          callback()
          fetchParentAreas()
        },
        'OperationManager/addJobAreas');
  }
}));

const editDialogConfig = computed<EditDialogConfig>(() => ({
  editFormTitle: '编辑工作区域',
  editFormConfig: [
    FormInputConfigFactory.createReadOnlyTextInput('id', 'ID'),
    FormInputConfigFactory.createEditableTextInput('name', '区域名称', 'name'),
    FormSelectConfigFactory.createSingleSelect('parent_id', '父区域', parentOptions.value, '父区域'),
    FormInputConfigFactory.createReadOnlyTextInput('create_time', '创建时间')
  ],
  editSubmitAction: async (data: Record<string, any>, callback: () => void) => {
    await updateItem(
        data,
        () => {
          callback()
          fetchParentAreas()
        },
        'OperationManager/updateJobAreas');
  }
}));

const tableConfig: TableConfig = {
  deleteRows: deleteJobAreasRows,
  getOnePageData: getOnePageJobAreas,
  searchData: searchJobAreas,
  tableColumns: [
    new TextTableColumn(75, 'id', 'ID', true, true, 'left'),
    new TextTableColumn(200, 'name', '区域名称'),
    new TextTableColumn(150, 'parent_id', '父区域ID', true),
    new TextTableColumn(150, 'create_time', '创建时间', true, false)
  ]
};
</script>

<style scoped>
</style>
