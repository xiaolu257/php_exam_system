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
import {FormNumberInputConfigFactory} from "@/utils/FormNumberInputConfig";
import {FormDatePickerConfigFactory} from "@/utils/FormDatePickerConfig";
import {addItem} from "@/api/utils/BaseAPI";


const ExamPaperCRUDStandardCRUD = TableCrudFactory.creatStandardCrud('exam-paper');
const addDialogConfig: AddDialogConfig = {
  title: '新增试卷',
  width: 600,
  formConfig: [
    FormInputConfigFactory.createEditableTextInput('title', '试卷名称', 'title'),
    FormInputConfigFactory.createEditableTextAreaInput('description', '试卷说明', 'description'),
    FormNumberInputConfigFactory.createEditableIntegerInput('duration', '考试时长(分钟)', 'duration'),
    FormNumberInputConfigFactory.createEditableIntegerInput('total_score', '总分', 'total_score'),
    FormDatePickerConfigFactory.createEditableDateTimeRangerPicker('exam_time', '考试时间', '至',
        '开始时间', '结束时间'),
    FormNumberInputConfigFactory.createEditableIntegerInput('max_attempts', '最大考试次数', 'max_attempts')
  ],
  submitAction: (data: Record<string, any>, callback: () => void) => {
    const {exam_time, ...rest} = data;

    const payload = {
      ...rest,
      start_time: exam_time[0],
      end_time: exam_time[1],
    };

    addItem('exam-paper', payload, callback);
  }
};

const editDialogConfig: TableColumnEditDialogConfig = {
  title: '修改试卷',
  width: 600,
  formConfig: [
    FormInputConfigFactory.createReadOnlyTextInput('id', 'ID'),
    FormInputConfigFactory.createEditableTextInput('title', '试卷名称', 'title'),
    FormInputConfigFactory.createEditableTextAreaInput('description', '试卷说明', 'description'),
    FormNumberInputConfigFactory.createEditableIntegerInput('duration', '考试时长(分钟)', 'duration'),
    FormNumberInputConfigFactory.createEditableIntegerInput('total_score', '总分', 'total_score'),
    FormDatePickerConfigFactory.createEditableDateTimeRangerPicker('exam_time', '考试时间', '至',
        '开始时间', '结束时间'),
    FormNumberInputConfigFactory.createEditableIntegerInput('max_attempts', '最大考试次数', 'max_attempts')
  ],
  submitAction: ExamPaperCRUDStandardCRUD.updateItem
};
const tableConfig: TableConfig = {
  deleteRows: ExamPaperCRUDStandardCRUD.deleteRows,
  getOnePageData: ExamPaperCRUDStandardCRUD.getOnePageData,
  searchData: ExamPaperCRUDStandardCRUD.searchOnePageData,
  tableColumns: [
    new TextTableColumn(80, 'id', 'ID', true, true),
    new TextTableColumn(200, 'title', '试卷名称'),
    new TextTableColumn(250, 'description', '试卷说明', false, false),
    new TextTableColumn(120, 'duration', '考试时长'),
    new TextTableColumn(100, 'total_score', '总分'),
    new TextTableColumn(180, 'start_time', '开始时间'),
    new TextTableColumn(180, 'end_time', '结束时间'),
    new TextTableColumn(120, 'max_attempts', '最大次数'),
    new TextTableColumn(180, 'created_at', '创建时间', true, false)
  ]
};
</script>

<style scoped>
</style>
