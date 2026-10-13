<template>
  <BaseTableManager :table-config="tableConfig">
    <template v-slot:operationButton="{row}">
      <el-button size="small" type="success" @click="startExamPaper(row.id)">考试</el-button>
    </template>
  </BaseTableManager>
</template>

<script lang="ts" setup>
import type {BaseTableConfig} from "@/components/public/table/tableTypes";
import {TextTableColumn} from "@/components/public/table/tableTypes";
import {TableCrudFactory} from "@/utils/tableCrudFactory";
import router from "@/router";
import BaseTableManager from "@/components/public/table/BaseTableManager.vue";

const tableConfig: BaseTableConfig = {
  getOnePageData: TableCrudFactory.createGetOnePageData('exam-paper/getMyExam'),
  searchData: TableCrudFactory.createSearchOnePageData('exam-paper/getMyExam'),
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
const startExamPaper = (examPaperId: number) => {
  router.push(`/exam/${examPaperId}/before`)
}
</script>

<style scoped>
</style>
