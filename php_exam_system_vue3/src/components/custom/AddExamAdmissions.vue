<template>
  <el-button style="margin-right: 10px" size="default" type="primary"
             :icon="User"
             @click="openDialog">

    添加考生
  </el-button>
  <el-dialog v-model="dialogVisible" @close="onDialogClose" width="50%" align-center
             append-to-body center destroy-on-close :show-close="false">
    <template #header="{close}">
      <el-row align="middle" justify="center">
        <el-text style="margin-left: 45%;font-size: 20px;">添加考生</el-text>
        <el-button style="margin-left: auto" round size="default" type="danger" :icon="Close" @click="close">
          关闭
        </el-button>
      </el-row>
    </template>
    <el-row align="middle" justify="center">
      <el-row
          :gutter="16"
          style="width: 98%;border: 1px solid #dbeafe; border-radius: 12px; padding: 16px"
      >
        <el-col :span="24" style="margin-bottom: 10px">
          <el-tag
              style="font-size: 16px"
              size="large"
              type="primary"
          >
            试卷 ID：{{ examPaperId ?? '-' }}
          </el-tag>
        </el-col>
      </el-row>
      <CustomTableManager :table-config="tableConfig">
        <template v-slot:operationButton="{row,refreshTable}">
          <el-button v-if="row.status=='revoked'" size="small" type="success"
                     @click="restoreExamAdmission([row.id],refreshTable)">
            重新授权
          </el-button>
          <el-button v-else size="small" type="success"
                     @click="addExamAdmission([row.id],refreshTable)">
            添加考生
          </el-button>
        </template>
        <template v-slot:batchActionButton="{selectedRows,refreshTable}">
          <el-button type="success"
                     @click="addExamAdmission(selectedRows,refreshTable)">
            批量添加考生
          </el-button>
        </template>
      </CustomTableManager>
    </el-row>
  </el-dialog>
</template>

<script lang="ts" setup>
import {ref} from 'vue';
import {type CustomTableConfig, ImageTableColumn, TextTableColumn} from "@/components/public/table/tableTypes";
import CustomTableManager from "@/components/public/table/CustomTableManager.vue";
import {TableCrudFactory} from "@/utils/tableCrudFactory";
import {Close, User} from "@element-plus/icons-vue";
import {AvatarBaseURL, AvatarThumbBaseURL} from "@/utils/global";

const props = defineProps<{
  examPaperId: number,
  onDialogClose?: () => void,
}>()
const onDialogClose = () => {
  props.onDialogClose?.()
}
// 定义控制 dialog 显示与隐藏的响应式变量
const dialogVisible = ref(false);

// 打开 dialog 的方法
const openDialog = () => {
  dialogVisible.value = true;
};
// 关闭 dialog 的方法

const restoreExamAdmission = TableCrudFactory.creatBatchAction('exam-paper/restoreExamAdmission', '批量“重新授权”')
const tableConfig: CustomTableConfig = {
  getOnePageData: TableCrudFactory.createGetOnePageData(
      'exam-paper/getUsersWithoutExamAdmission', {id: props.examPaperId,}),
  searchData: TableCrudFactory.createSearchOnePageData(
      'exam-paper/getUsersWithoutExamAdmission', {id: props.examPaperId,}),
  tableHeight: 500,
  tableColumns: [
    new TextTableColumn(80, "id", "ID", true, true, "left"),
    new TextTableColumn(200, "username", "账号",),
    new TextTableColumn(200, "nickname", "昵称",),
    new ImageTableColumn(
        100, 'avatar_url', '头像', (url: string) => {
          return `${AvatarBaseURL}${url}`;
        },
        (url: string) => {
          return `${AvatarThumbBaseURL}${url}`;
        }
    ),
  ]
};
const addExamAdmission = TableCrudFactory.creatBatchAction(
    'exam-paper/addExamAdmission',
    '批量“添加考生”',
    {id: props.examPaperId}
);
</script>

