<template>
  <el-row class="row">
    <el-col :span="8" style="display: flex;align-items: center;">
      总计
      <el-tag effect="dark" size="default" style="margin: 0 10px" type="success">{{ totalDataLength }}</el-tag>
      条数据，
      当前页共
      <el-tag effect="dark" size="default" style="margin: 0 10px" type="success">{{ tableDataLength }}</el-tag>
      条数据
      <el-button v-if="exportDataToExcel" style="margin-left: 10px;" type="primary" @click="exportDataToExcel">导出
      </el-button>
    </el-col>
    <el-col :offset="1" :span="1">
      <el-tag v-show="isSearch" closable effect="dark" round size="large" type="success" @close="resetTable">
        正在显示搜索结果
      </el-tag>
    </el-col>
    <el-col :offset="3" :span="9">
      <el-select v-model="searchKey" :disabled="isSearch" clearable placeholder="请选择搜索依据" style="width: 150px">
        <template v-for="item in tableConfig.tableColumns">
          <el-option
              v-if="item.searchable"
              :key="item.prop"
              :label="item.label"
              :value="item.prop"
          />
        </template>
      </el-select>

      <el-input v-model="searchValue" :disabled="isSearch" clearable
                placeholder="请输入要搜索的关键字" style="width: 200px;margin: 0 15px"></el-input>
      <el-button type="primary" @click="queryData">查询</el-button>
    </el-col>
  </el-row>
  <el-table ref="tableRef"
            :data="tableData"
            border
            height="565"
            show-overflow-tooltip
            stripe
            style="width: 98%; margin: 0 15px; border: 1px solid darkgrey;"
            @selection-change="handleSelectionChange"
  >
    <el-table-column align="center" fixed="left" type="selection" width="40"/>
    <BaseTableColumns :table-columns="tableConfig.tableColumns"></BaseTableColumns>
  </el-table>
  <el-row style="margin: 15px">
    <el-col style="display: flex; justify-content: center; align-items: center; height: 100%;">
      <el-pagination
          :current-page="currentPage"
          :page-count="pageCount"
          :page-size="pageSize"
          background
          layout="prev, pager, next"
          @current-change="handlePageChange"
      />
    </el-col>
  </el-row>

</template>

<script lang="ts" setup>
import {computed, onMounted, ref} from "vue";

import type {TableInstance} from "element-plus/es/components/table";
import {ElTable} from "element-plus";
import BaseTableColumns from "@/components/public/Table/BaseTableColumns.vue";
import type {TableConfig} from "@/utils/TableConfig";

interface Props {
  tableConfig: TableConfig;
  exportDataToExcel?: () => void;
}

const props = defineProps<Props>();
const tableData = ref<Array<any>>([])//表格数据模型
const isSearch = ref(false)//表格展示状态
const currentPage = ref(1); // 当前页码
const pageSize = ref(10);   // 每页显示的数据条数
const pageCount = ref(1);//总页数
// 定义计算属性返回 tableData 数组的长度
const tableDataLength = computed(() => tableData.value?.length || 0);
const totalDataLength = ref(0)//总数据长度
// 引用表格实例
const tableRef = ref<TableInstance | null>(null);
// 存储选中的行数据
const selectedRows = ref<Array<number>>([]);
//被搜索属性
const searchKey = ref('');
const searchValue = ref('');
// 监听多选变化
const handleSelectionChange = (val: Array<any>) => {
  selectedRows.value = val.map((item: any) => item.id);
};
//处理换页
const handlePageChange = (page: number) => {
  currentPage.value = page;
  refreshTableData()
};
//获取单页数据成功回调
const getPageSuccess = (data: Array<any>, lastPage: number, totalCount: number) => {
  tableData.value = data
  pageCount.value = lastPage
  totalDataLength.value = totalCount
}
//重置表格
const resetTable = () => {
  // 刷新表格数据
  isSearch.value = false
  currentPage.value = 1
  props.tableConfig.getOnePageData(currentPage.value, getPageSuccess)
};
//搜索成功回调
const searchSuccess = (data: Array<any>, lastPage: number, totalCount: number) => {
  tableData.value = data;
  pageCount.value = lastPage;
  isSearch.value = true;
  totalDataLength.value = totalCount;
};
// 刷新当前页表格数据
const refreshTableData = () => {
  if (isSearch.value) {
    props.tableConfig.searchData(searchKey.value, searchValue.value, currentPage.value, searchSuccess);
  } else {
    props.tableConfig.getOnePageData(currentPage.value, getPageSuccess)
  }
};
// 查询数据
const queryData = () => {
  currentPage.value = 1;
  props.tableConfig.searchData(searchKey.value, searchValue.value, currentPage.value, searchSuccess);
};
onMounted(() => {
  props.tableConfig.getOnePageData(currentPage.value, getPageSuccess)
})
</script>
<style>
.row {
  background-color: rgb(247, 247, 247);
  margin: 10px 15px;
}
</style>