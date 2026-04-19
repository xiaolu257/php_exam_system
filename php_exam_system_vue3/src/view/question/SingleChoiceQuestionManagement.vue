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
import {computed} from 'vue';
import {FormSelectConfigFactory} from "@/utils/formSelectConfig";
import type {AddDialogConfig, TableColumnEditDialogConfig} from "@/components/public/form/formTypes";
import {TableCrudFactory} from "@/utils/tableCrudFactory";
import BaseTableManager from "@/components/public/table/BaseTableManager.vue";

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
const singleChoiceQuestionStandardCRUD = TableCrudFactory.creatStandardCrud('single-choice-question');
const addDialogConfig: AddDialogConfig = {
  title: '新增单选题',
  formConfig: [
    FormInputConfigFactory.createEditableTextInput('content', '题目', 'content'),
    FormInputConfigFactory.createDynamicMultipleTextInput('options', '选项', 'options'),
    FormSelectConfigFactory.createAssociateSingleSelect('correct_answer', '正确答案', 'options', associateSingleQuestionOptions, 'correct_answer')
  ],
  submitAction: singleChoiceQuestionStandardCRUD.addItem,
};

const editDialogConfig: TableColumnEditDialogConfig = {
  title: '修改单选题',
  formConfig: [
    FormInputConfigFactory.createReadOnlyTextInput('id', 'ID'),
    FormInputConfigFactory.createEditableTextInput('content', '题目', 'content'),
    FormInputConfigFactory.createDynamicMultipleTextInput('options', '选项', 'options'),
    FormSelectConfigFactory.createAssociateSingleSelect('correct_answer', '正确答案', 'options', associateSingleQuestionOptions, 'correct_answer')
  ],
  submitAction: singleChoiceQuestionStandardCRUD.updateItem,
};
const tableConfig: TableConfig = {
  deleteRows: singleChoiceQuestionStandardCRUD.deleteRows,
  getOnePageData: singleChoiceQuestionStandardCRUD.getOnePageData,
  searchData: singleChoiceQuestionStandardCRUD.searchOnePageData,
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
