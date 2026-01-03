<template>
  <BaseTableManager
      :add-dialog-config="addDialogConfig"
      :edit-dialog-config="editDialogConfig"
      :table-config="tableConfig"
  />
</template>

<script lang="ts" setup>
import BaseTableManager from "@/components/public/Table/BaseTableManager.vue";
import {TextTableColumn} from "@/utils/MyTableTypeClass";
import {type AddDialogConfig, type EditDialogConfig, FormInputConfigFactory} from "@/utils/FormInputConfig";
import type {TableConfig} from "@/utils/TableConfig";
// import {exportSingleChoiceQuestionsToExcel} from "@/api/Export";
import {computed, shallowRef} from 'vue';
import {FormSelectConfigFactory} from "@/utils/FormSelectConfig";
import {
  deleteSingleChoiceQuestionsRows,
  getOnePageSingleChoiceQuestions,
  searchSingleChoiceQuestions
} from "@/api/OperationManager";
import {addItem, updateItem} from "@/api/utils/BaseAPI";

const parentOptions = shallowRef<{ label: string; value: number }[]>([]);


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
        },
        'OperationManager/addSingleChoiceQuestions');
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
        },
        'OperationManager/updateSingleChoiceQuestions');
  }
}));

const tableConfig: TableConfig = {
  deleteRows: deleteSingleChoiceQuestionsRows,
  getOnePageData: getOnePageSingleChoiceQuestions,
  searchData: searchSingleChoiceQuestions,
  tableColumns: [
    new TextTableColumn(75, 'id', 'ID', true, true, 'left'),
    new TextTableColumn(200, 'content', '题目'),
    new TextTableColumn(200, 'option', '选项'),
    new TextTableColumn(200, 'correct_answer', '正确答案'),
    new TextTableColumn(200, 'score', '分数'),
    new TextTableColumn(150, 'created_at', '创建时间', true, false)
  ]
};
</script>

<style scoped>
</style>
