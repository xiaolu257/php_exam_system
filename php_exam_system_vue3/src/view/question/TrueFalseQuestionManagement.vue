<template>
  <BaseTableManager :add-dialog-config="addDialogConfig"
                    :table-column-edit-dialog-config="editDialogConfig"
                    :table-config="tableConfig">
  </BaseTableManager>
</template>

<script lang="ts" setup>
import BaseTableManager from "@/components/public/table/BaseTableManager.vue";
import type {TableConfig} from "@/components/public/table/tableTypes";
import {TextTableColumn} from "@/components/public/table/tableTypes";
import {FormInputConfigFactory} from "@/utils/formInputConfig";
import {FormSelectConfigFactory} from "@/utils/formSelectConfig";
import type {AddDialogConfig, TableColumnEditDialogConfig} from "@/components/public/form/formTypes";
import {TableCrudFactory} from "@/utils/tableCrudFactory";


const TrueFalseQuestionStandardCRUD = TableCrudFactory.creatStandardCrud('true-false-question');
const correctAnswerOptions = [{label: '正确', value: 1}, {label: '错误', value: 0}];
const addDialogConfig: AddDialogConfig = {
  title: '新增判断题',
  formConfig: [
    FormInputConfigFactory.createEditableTextInput('content', '题目', 'content'),
    FormSelectConfigFactory.createCommonSingleSelect('correct_answer', '正确答案', correctAnswerOptions, 'correct_answer')
  ],
  submitAction: TrueFalseQuestionStandardCRUD.addItem,
};

const editDialogConfig: TableColumnEditDialogConfig = {
  title: '修改判断题',
  formConfig: [
    FormInputConfigFactory.createReadOnlyTextInput('id', 'ID'),
    FormInputConfigFactory.createEditableTextInput('content', '题目', 'content'),
    FormSelectConfigFactory.createCommonSingleSelect('correct_answer', '正确答案', correctAnswerOptions, 'correct_answer')
  ],
  submitAction: TrueFalseQuestionStandardCRUD.updateItem,
};
const tableConfig: TableConfig = {
  deleteRows: TrueFalseQuestionStandardCRUD.deleteRows,
  getOnePageData: TrueFalseQuestionStandardCRUD.getOnePageData,
  searchData: TrueFalseQuestionStandardCRUD.searchOnePageData,
  tableColumns: [
    new TextTableColumn(75, 'id', 'ID', true, true, 'left'),
    new TextTableColumn(200, 'content', '题目'),
    new TextTableColumn(200, 'correct_answer', '正确答案', false, false),
    new TextTableColumn(150, 'created_at', '创建时间', true, false)
  ]
};
</script>

<style scoped>
</style>
