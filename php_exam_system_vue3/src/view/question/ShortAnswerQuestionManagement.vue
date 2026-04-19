<template>
  <BaseTableManager :add-dialog-config="addDialogConfig"
                    :table-column-edit-dialog-config="editDialogConfig"
                    :table-config="tableConfig">
  </BaseTableManager>
</template>

<script lang="ts" setup>
import type {TableConfig} from "@/components/public/table/tableTypes";
import {TextTableColumn} from "@/components/public/table/tableTypes";
import {FormInputConfigFactory} from "@/utils/formInputConfig";
import type {AddDialogConfig, TableColumnEditDialogConfig} from "@/components/public/form/formTypes";
import {TableCrudFactory} from "@/utils/tableCrudFactory";
import BaseTableManager from "@/components/public/table/BaseTableManager.vue";


const ShortAnswerQuestionStandardCRUD = TableCrudFactory.creatStandardCrud('short-answer-question');
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
