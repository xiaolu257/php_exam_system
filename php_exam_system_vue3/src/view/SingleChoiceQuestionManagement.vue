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
import {
  deleteSingleChoiceQuestionsRows,
  getOnePageSingleChoiceQuestions,
  searchSingleChoiceQuestions
} from "@/api/SuperManager";
import {addItem, updateItem} from "@/api/utils/BaseAPI";
import type {AddDialogConfig, TableColumnEditDialogConfig} from "@/components/public/Form/FormTypes";

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
const addDialogConfig: AddDialogConfig = {
  title: '新增单选题',
  formConfig: [
    FormInputConfigFactory.createEditableTextInput('content', '题目', 'content'),
    FormInputConfigFactory.createDynamicMultipleTextInput('options', '选项', 'options'),
    FormSelectConfigFactory.createAssociateSingleSelect('correct_answer', '正确答案', 'options', associateSingleQuestionOptions, 'correct_answer')
  ],
  submitAction: (data: Record<string, any>, onSuccess: () => void) => {
    addItem('single-choice-question', data, onSuccess);
  },
};

const editDialogConfig: TableColumnEditDialogConfig = {
  title: '修改单选题',
  formConfig: [
    FormInputConfigFactory.createReadOnlyTextInput('id', 'ID'),
    FormInputConfigFactory.createEditableTextInput('content', '题目', 'content'),
    FormInputConfigFactory.createDynamicMultipleTextInput('options', '选项', 'options'),
    FormSelectConfigFactory.createAssociateSingleSelect('correct_answer', '正确答案', 'options', associateSingleQuestionOptions, 'correct_answer')
  ],
  submitAction: (data: Record<string, any>, onSuccess: () => void) => {
    updateItem('single-choice-question', data, onSuccess);
  },
};
const tableConfig: TableConfig = {
  deleteRows: deleteSingleChoiceQuestionsRows,
  getOnePageData: getOnePageSingleChoiceQuestions,
  searchData: searchSingleChoiceQuestions,
  tableColumns: [
    new TextTableColumn(75, 'id', 'ID', true, true, 'left'),
    new TextTableColumn(200, 'content', '题目'),
    new TextTableColumn(200, 'options', '选项'),
    new TextTableColumn(200, 'correct_answer', '正确答案'),
    new TextTableColumn(150, 'created_at', '创建时间', true, false)
  ]
};
</script>

<style scoped>
</style>
