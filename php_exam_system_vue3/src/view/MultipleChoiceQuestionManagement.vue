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
import {computed} from 'vue';
import {FormSelectConfigFactory} from "@/utils/FormSelectConfig";
import type {AddDialogConfig, TableColumnEditDialogConfig} from "@/components/public/Form/FormTypes";
import {TableCrudFactory} from "@/utils/TableCrudFactory";

const associateSingleQuestionOptions = (options: string[]) => {
  return computed<{
    label: string;
    value: any;
  }[]>(() => {
    return options.map((item: string, index: number) => ({
      label: String.fromCharCode(65 + index) + ':' + item,
      value: String.fromCharCode(65 + index)
    }))
  });
}
const MultipleChoiceQuestionStandardCRUD = TableCrudFactory.creatStandardCrud('multiple-choice-question');
const addDialogConfig: AddDialogConfig = {
  title: '新增多选题',
  formConfig: [
    FormInputConfigFactory.createEditableTextInput('content', '题目', 'content'),
    FormInputConfigFactory.createDynamicMultipleTextInput('options', '选项', 'options'),
    FormSelectConfigFactory.createAssociateMultipleSelect('correct_answer', '正确答案', 'options', associateSingleQuestionOptions, 'correct_answer')
  ],
  submitAction: MultipleChoiceQuestionStandardCRUD.addItem,
};

const editDialogConfig: TableColumnEditDialogConfig = {
  title: '修改多选题',
  formConfig: [
    FormInputConfigFactory.createReadOnlyTextInput('id', 'ID'),
    FormInputConfigFactory.createEditableTextInput('content', '题目', 'content'),
    FormInputConfigFactory.createDynamicMultipleTextInput('options', '选项', 'options'),
    FormSelectConfigFactory.createAssociateMultipleSelect('correct_answer', '正确答案', 'options', associateSingleQuestionOptions, 'correct_answer')
  ],
  submitAction: MultipleChoiceQuestionStandardCRUD.updateItem,
};
const tableConfig: TableConfig = {
  deleteRows: MultipleChoiceQuestionStandardCRUD.deleteRows,
  getOnePageData: MultipleChoiceQuestionStandardCRUD.getOnePageData,
  searchData: MultipleChoiceQuestionStandardCRUD.searchOnePageData,
  tableColumns: [
    new TextTableColumn(75, 'id', 'ID', true, true, 'left'),
    new TextTableColumn(200, 'content', '题目'),
    new TextTableColumn(200, 'options', '选项'),
    new TextTableColumn(200, 'correct_answer', '正确答案', false, false),
    new TextTableColumn(150, 'created_at', '创建时间', true, false)
  ]
};
</script>

<style scoped>
</style>
