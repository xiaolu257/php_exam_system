<template>
  <el-button size="small" type="primary" @click="async ()=>{openDialog();await getExamAdmissionStatistics()}">考试资格
  </el-button>
  <el-dialog v-model="dialogVisible" width="70%" align-center
             append-to-body center destroy-on-close :show-close="false">
    <template #header="{close}">
      <el-row align="middle" justify="center">
        <el-text style="margin-left: 45%;font-size: 20px;">考试资格管理</el-text>
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

        <el-col :span="6">
          <div style="background: #eff6ff; border-radius: 8px; padding: 16px">
            <el-statistic
                title="当前有效资格人数"
                :value="authorizedUserCount"
                :value-style="{ color: '#2563eb' }"
            />
          </div>
        </el-col>

        <el-col :span="6">
          <div style="background: #ecfdf5; border-radius: 8px; padding: 16px">
            <el-statistic
                title="已参加人数"
                :value="participatedUserCount"
                :value-style="{ color: '#059669' }"
            />
          </div>
        </el-col>

        <el-col :span="6">
          <div style="background: #fff7ed; border-radius: 8px; padding: 16px">
            <el-statistic
                title="尚未参加人数"
                :value="notParticipatedUserCount"
                :value-style="{ color: '#ea580c' }"
            />
          </div>
        </el-col>

        <el-col :span="6">
          <div style="background: #f5f3ff; border-radius: 8px; padding: 16px">
            <el-statistic
                title="已取消资格人数"
                :value="revokedUserCount"
                :value-style="{ color: '#7c3aed' }"
            />
          </div>
        </el-col>
      </el-row>
      <CustomTableManager :table-config="tableConfig">
        <template v-slot:leftTopOperationButton="{refreshTable}">
          <AddExamAdmissions :exam-paper-id="examPaperId"
                             :on-dialog-close="refreshAdmissionData(refreshTable)"></AddExamAdmissions>
        </template>
        <template v-slot:operationButton="{row,refreshTable}">
          <el-button v-if="row.status=='revoked'" size="small" type="success"
                     @click="restoreExamAdmission([row.id],refreshAdmissionData(refreshTable))">
            重新授权
          </el-button>
          <el-button v-else size="small" type="danger"
                     @click="revokeExamAdmission([row.id],refreshAdmissionData(refreshTable))">
            取消资格
          </el-button>
        </template>
        <template v-slot:batchActionButton="{selectedRows,refreshTable}">
          <el-button type="success" @click="restoreExamAdmission(selectedRows,refreshAdmissionData(refreshTable))">
            批量重新授权
          </el-button>
          <el-button type="danger" @click="revokeExamAdmission(selectedRows,refreshAdmissionData(refreshTable))">
            批量取消资格
          </el-button>
        </template>
      </CustomTableManager>
    </el-row>
  </el-dialog>
</template>

<script lang="ts" setup>
import {ref} from 'vue';
import {type CustomTableConfig, SelectTextColumn, TextTableColumn} from "@/components/public/table/tableTypes";
import CustomTableManager from "@/components/public/table/CustomTableManager.vue";
import {TableCrudFactory} from "@/utils/tableCrudFactory";
import AddExamAdmissions from "@/components/custom/AddExamAdmissions.vue";
import {myGet} from "@/api/utils/axios";
import {Close} from "@element-plus/icons-vue";

const props = defineProps<{
  examPaperId: number
}>()
// 定义控制 dialog 显示与隐藏的响应式变量
const dialogVisible = ref(false);

// 打开 dialog 的方法
const openDialog = () => {
  dialogVisible.value = true;
};

const restoreExamAdmission = TableCrudFactory.creatBatchAction(
    'exam-paper/restoreExamAdmission', '批量“重新授权”', {id: props.examPaperId})
const tableConfig: CustomTableConfig = {
  getOnePageData: TableCrudFactory.createGetOnePageData('exam-paper/getExamAdmission', {id: props.examPaperId,}),
  searchData: TableCrudFactory.createSearchOnePageData('exam-paper/getExamAdmission', {id: props.examPaperId,}),
  tableHeight: 500,
  tableColumns: [
    new TextTableColumn(80, 'id', 'ID', true),
    new TextTableColumn(100, 'user_id', '用户ID', true),
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
const revokeExamAdmission = TableCrudFactory.creatBatchAction(
    'exam-paper/revokeExamAdmission', '批量“取消资格”', {id: props.examPaperId,})

const authorizedUserCount = ref(0)
const participatedUserCount = ref(0)
const notParticipatedUserCount = ref(0)
const revokedUserCount = ref(0)
const getExamAdmissionStatistics = async () => {
  try {
    const res = await myGet(
        'exam-paper/getExamAdmissionStatistics',
        {id: props.examPaperId}
    )

    authorizedUserCount.value = res.authorizedUserCount
    participatedUserCount.value = res.participatedUserCount
    notParticipatedUserCount.value = res.notParticipatedUserCount
    revokedUserCount.value = res.revokedUserCount
  } catch (error) {
    console.error('获取考试资格统计信息失败：', error)
  }
}

const refreshAdmissionData = (
    refreshTable: (clearSelection?: boolean) => void
) => {
  return async () => {
    refreshTable()
    await getExamAdmissionStatistics()
  }

}
</script>

