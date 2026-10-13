<template>
  <el-button size="small" type="primary" @click="openDialog">考试资格</el-button>
  <el-dialog v-model="dialogVisible" title="考试资格管理" width="70%" align-center
             append-to-body center destroy-on-close>
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

        <el-col :span="8">
          <div style="background: #eff6ff; border-radius: 8px; padding: 16px">
            <el-statistic
                title="已授权人数"
                :value="268500"
                :value-style="{ color: '#2563eb' }"
            />
          </div>
        </el-col>

        <el-col :span="8">
          <div style="background: #ecfdf5; border-radius: 8px; padding: 16px">
            <el-statistic
                title="已参加人数"
                :value="268000"
                :value-style="{ color: '#059669' }"
            />
          </div>
        </el-col>

        <el-col :span="8">
          <div style="background: #fff7ed; border-radius: 8px; padding: 16px">
            <el-statistic
                title="尚未参加"
                :value="500"
                :value-style="{ color: '#ea580c' }"
            />
          </div>
        </el-col>
      </el-row>
      <CustomTableManager :table-config="tableConfig">
        <template v-slot:leftTopOperationButton>
          <el-button style="margin-right: 10px" size="default" type="primary"
                     :icon="User"
                     @click="()=>{}">

            添加考生
          </el-button>
        </template>
        <template v-slot:operationButton="{row,refreshTable}">
          <el-button v-if="row.status=='revoked'" size="small" type="success"
                     @click="restoreExamAdmission([row.id],refreshTable)">
            重新授权
          </el-button>
        </template>
      </CustomTableManager>
    </el-row>
  </el-dialog>
</template>

<script lang="ts" setup>
import {ref} from 'vue';
import {type CustomTableConfig, SelectTextColumn, TextTableColumn} from "@/components/public/table/tableTypes";
import type {PageCallback} from "@/api/utils/baseAPI";
import {myGet} from "@/api/utils/axios";
import CustomTableManager from "@/components/public/table/CustomTableManager.vue";
import {TableCrudFactory} from "@/utils/tableCrudFactory";
import {User} from "@element-plus/icons-vue";

const props = defineProps<{
  examPaperId: number
}>()
// 定义控制 dialog 显示与隐藏的响应式变量
const dialogVisible = ref(false);

// 打开 dialog 的方法
const openDialog = () => {
  dialogVisible.value = true;
};
// 关闭 dialog 的方法

const restoreExamAdmission = TableCrudFactory.creatBatchAction('exam-paper/restoreExamAdmission', '批量“重新授权”')
const tableConfig: CustomTableConfig = {
  // getOnePageData: TableCrudFactory.createGetOnePageData('exam-paper/getExamAdmission'),
  getOnePageData: (currentPage: number,
                   orderKey: string,
                   orderDirection: string,
                   callback: PageCallback) => {
    myGet('exam-paper/getExamAdmission', {
      id: props.examPaperId,
      page: currentPage,
      orderBy: orderKey,
      orderDirection: orderDirection
    })
        .then((res) => {
          const {data = [], last_page = 0, total = 0} = res;
          callback(data, last_page, total);
        })
  },
  searchData: (key: string,
               value: string,
               page: number,
               orderKey: string,
               orderDirection: string,
               callback: PageCallback) => {
    myGet('exam-paper/getExamAdmission', {
      id: props.examPaperId,
      searchField: key,
      searchValue: value,
      page: page,
      orderBy: orderKey,
      orderDirection: orderDirection
    })
        .then((res) => {
          const {data = [], last_page = 0, total = 0} = res;
          callback(data, last_page, total);
        })
  },
  tableHeight: 500,
  paginationMarginLeft: '25%',
  operationButtonName: '取消资格',
  handleBatchActionName: '批量取消资格',
  handleBatchAction: TableCrudFactory.creatBatchAction('exam-paper/revokeExamAdmission', '批量“取消资格”'),
  tableColumns: [
    new TextTableColumn(80, 'id', 'ID', true, true),
    new TextTableColumn(80, 'user_id', '用户ID'),
    new TextTableColumn(180, 'username', '账号'),
    new TextTableColumn(180, 'nickname', '昵称'),
    new SelectTextColumn(100, 'admission_method', '授权方式', [
      {
        label: '手动授权',
        value: 'manual',
      },
      {
        label: '密码授权',
        value: 'password',
      },
    ]),
    new SelectTextColumn(100, 'status', '授权状态', [
      {
        label: '正常资格',
        value: 'authorized',
      },
      {
        label: '取消资格',
        value: 'revoked',
      },
    ]),
    new TextTableColumn(180, 'created_at', '创建时间', true, false),
    new TextTableColumn(180, 'updated_at', '更新时间', true, false)
  ]
};
</script>

