<template>
  <BaseTableManager :add-dialog-config="addDialogConfig"
                    :table-column-edit-dialog-config="editDialogConfig"
                    :table-config="tableConfig">
  </BaseTableManager>
</template>

<script lang="ts" setup>
import BaseTableManager from "@/components/public/Table/BaseTableManager.vue";
import type {TableConfig} from "@/components/public/Table/TableTypes";
import {TextTableColumn} from "@/components/public/Table/TableTypes";
import {FormInputConfigFactory} from "@/utils/FormInputConfig";
import type {AddDialogConfig, TableColumnEditDialogConfig} from "@/components/public/Form/FormTypes";
import {TableCrudFactory} from "@/utils/TableCrudFactory";


const ShortAnswerQuestionStandardCRUD = TableCrudFactory.creatStandardCrud('short-answer-question');
const correctAnswerOptions = [{label: '正确', value: 1}, {label: '错误', value: 0}];
const addDialogConfig: AddDialogConfig = {
  title: '新增简答题',
  width: 600,
  formConfig: [
    FormInputConfigFactory.createEditableTextInput('content', '题目', 'content'),
    FormInputConfigFactory.createEditableTextAreaInput('reference_answer', '正确答案', 'reference_answer', {
      minRows: 5,
      maxRows: 10
    }),
  ],
  submitAction: ShortAnswerQuestionStandardCRUD.addItem,
};

const editDialogConfig: TableColumnEditDialogConfig = {
  title: '修改简答题',
  width: 600,
  formConfig: [
    FormInputConfigFactory.createReadOnlyTextInput('id', 'ID'),
    FormInputConfigFactory.createEditableTextInput('content', '题目', 'content'),
    FormInputConfigFactory.createEditableTextAreaInput('reference_answer', '正确答案', 'reference_answer', {
      minRows: 5,
      maxRows: 10
    }),
  ],
  submitAction: ShortAnswerQuestionStandardCRUD.updateItem,
};
const tableConfig: TableConfig = {
  deleteRows: ShortAnswerQuestionStandardCRUD.deleteRows,
  getOnePageData: ShortAnswerQuestionStandardCRUD.getOnePageData,
  searchData: ShortAnswerQuestionStandardCRUD.searchOnePageData,
  tableColumns: [
    new TextTableColumn(75, 'id', 'ID', true, true, 'left'),
    new TextTableColumn(200, 'content', '题目'),
    new TextTableColumn(200, 'reference_answer', '参考答案', false, false),
    new TextTableColumn(150, 'created_at', '创建时间', true, false)
  ]
};
</script>

<style scoped>
</style>
