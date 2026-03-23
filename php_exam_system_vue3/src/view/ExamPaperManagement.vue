<template>
  <BaseTableManager :add-dialog-config="addDialogConfig"
                    :table-column-edit-dialog-config="editDialogConfig"
                    :table-config="tableConfig">
    <template v-slot:operationButton="{row}">
      <el-button size="small" type="success" @click="previewExamPaper(row.id)">预览</el-button>
    </template>
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
import router from "@/router";


const ExamPaperCRUDStandardCRUD = TableCrudFactory.creatStandardCrud('exam-paper');
const addDialogConfig: AddDialogConfig = {
  title: '新增试卷',
  width: 600,
  formConfig: [
    FormInputConfigFactory.createEditableTextInput('title', '试卷名称', 'title'),
    FormInputConfigFactory.createEditableTextAreaInput('description', '试卷说明', 'description'),
    FormNumberInputConfigFactory.createEditableIntegerInput('duration', '考试时长(分钟)', 'duration'),
    FormNumberInputConfigFactory.createEditableIntegerInput('single_count', '单选题数量(2分/题)', 'single_count', 1),
    FormNumberInputConfigFactory.createEditableIntegerInput('multiple_count', '多选题数量(4分/题)', 'multiple_count', 1),
    FormNumberInputConfigFactory.createEditableIntegerInput('true_false_count', '判断题数量(1分/题)', 'true_false_count', 1),
    FormNumberInputConfigFactory.createEditableIntegerInput('short_answer_count', '简答题数量(5分/题)', 'short_count', 1),
    FormDatePickerConfigFactory.createEditableDateTimeRangerPicker('exam_time', '考试时间', '至',
        '开始时间', '结束时间'),
    FormNumberInputConfigFactory.createEditableIntegerInput('max_attempts', '最大考试次数', 'max_attempts', 1)
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
    FormDatePickerConfigFactory.createEditableDateTimeRangerPicker('exam_time', '考试时间', '至',
        '开始时间', '结束时间'),
    FormNumberInputConfigFactory.createEditableIntegerInput('max_attempts', '最大考试次数', 'max_attempts', 1)
  ],
  afterMapRowToInitData: (filteredData: Record<string, any>, scopeRow: Record<string, any>) => {
    filteredData['exam_time'] = [scopeRow['start_time'], scopeRow['end_time']]
  },
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
    new TextTableColumn(90, 'duration', '考试时长'),
    new TextTableColumn(80, 'total_score', '总分'),
    new TextTableColumn(180, 'start_time', '开始时间'),
    new TextTableColumn(180, 'end_time', '结束时间'),
    new TextTableColumn(100, 'max_attempts', '最大次数'),
    new TextTableColumn(180, 'created_at', '创建时间', true, false),
    new TextTableColumn(180, 'updated_at', '更新时间', true, false)
  ]
};
const previewExamPaper = (examPaperId: number) => {
  router.push(`/exam/${examPaperId}/before`)
}
</script>

<style scoped>
</style>
