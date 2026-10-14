<template>
  <el-row class="row">
    <el-col :span="10" style="display: flex;align-items: center;">
      <slot name="leftTopOperationButton" :refreshTable="refreshTableData"></slot>
      <TableSummaryBar :total-data-length="totalDataLength"
                       :table-data-length="tableDataLength"
                       :export-data-to-excel="exportDataToExcel"
      />
    </el-col>
    <div style="margin-left: auto;">
      <SearchBar v-model:is-search="isSearch"
                 :table-columns="tableConfig.tableColumns"
                 v-model:searchKey="searchKey"
                 v-model:searchValue="searchValue"
      />
    </div>

  </el-row>

  <el-table ref="tableRef"
            :data="tableData"
            row-key="id"
            border
            show-overflow-tooltip
            stripe
            :height="tableConfig.tableHeight ?? 668"
            :style="{
              width: tableConfig.tableWidth ?? '98%',
              margin: '1%',
              border: '1px solid darkgrey',
            }"
            @sort-change="handleSortChange"
            @selection-change="handleSelectionChange"
  >
    <el-table-column align="center" fixed="left" type="selection" width="40" :reserve-selection="true"/>
    <BaseTableColumns :table-columns="tableConfig.tableColumns"/>
    <el-table-column v-if="showOperationColumn" align="center" fixed="right" label="操作"
                     :width="tableConfig.operationWidth||200">
      <template #default="{row}">
        <slot name="operationButton" :row="row" :refreshTable="refreshTableData"></slot>
      </template>
    </el-table-column>
  </el-table>

  <el-row style="width:98%;margin: 1%;">
    <el-button type="primary" @click="selectAll">全选</el-button>
    <el-button type="primary" @click="clearSelection">取消选中行</el-button>
    <slot name="batchActionButton" :selectedRows="selectedRows" :refreshTable="refreshTableData"></slot>

    <div :style="{
        marginLeft: tableConfig.paginationMarginLeft ?? 'auto',
      }"
    >
      <BottomPagination v-model:current-page="currentPage" v-model:page-count="pageCount"/>
    </div>
  </el-row>

</template>

<script lang="ts" setup>
import {computed, ref, shallowRef, useSlots, watch} from "vue";
import type {TableInstance} from "element-plus/es/components/table";
import type {CustomTableConfig} from "@/components/public/table/tableTypes";
import type {PageCallback} from "@/api/utils/baseAPI";
import BottomPagination from "@/components/public/table/child-component/BottomPagination.vue";
import BaseTableColumns from "@/components/public/table/child-component/BaseTableColumns.vue";
import SearchBar from "@/components/public/table/child-component/SearchBar.vue";
import TableSummaryBar from "@/components/public/table/child-component/TableSummaryBar.vue";

interface Props {
  tableConfig: CustomTableConfig;
  exportDataToExcel?: () => void;
}

const props = defineProps<Props>();

const tableRef = shallowRef<TableInstance>();// 引用表格实例
const tableData = ref<Array<any>>([])//表格数据模型
const isSearch = ref(false)//表格展示状态
const currentPage = ref(1); // 当前页码
const pageCount = ref(1);//总页数
const tableDataLength = computed(() => tableData.value?.length ?? 0);//当前页数据长度
const totalDataLength = ref(0)//总数据长度
const selectedRows = ref<Array<number>>([]);//表格选中的行数据
const searchKey = ref('');//被搜索属性
const searchValue = ref('');//搜索关键词
const orderKey = ref<string>('id');      // 当前排序字段
const orderDirection = ref<string>('asc');     // 排序顺序 asc/desc
const slots = useSlots();
const hasOperationSlot = computed(() => {
  return !!slots.operationButton;
});
const showOperationColumn = computed(() => {
  return !!(
      hasOperationSlot.value
  );
});
// 监听排序变更
const handleSortChange = (sortInfo: { prop: string, order: 'ascending' | 'descending' | null }) => {
  if (sortInfo.order) {
    orderKey.value = sortInfo.prop;
    orderDirection.value = sortInfo.order === 'ascending' ? 'asc' : 'desc';
  } else {
    orderKey.value = 'id';
    orderDirection.value = 'asc';
  }
  refreshTableData(); // 重新拉取数据
};

// 监听多选变化
const handleSelectionChange = (val: Array<any>) => {
  selectedRows.value = val.map((item: any) => item.id);
};

// 全选功能
const selectAll = () => {
  tableRef.value?.toggleAllSelection();
};

// 取消全选功能
const clearSelection = () => {
  tableRef.value?.clearSelection();
};


const refreshTableData = (clearSelection: boolean = true) => {
  const getPageDataSuccess: PageCallback = (data: any[], lastPage: number, totalCount: number) => {
    tableData.value = data;
    pageCount.value = lastPage;
    totalDataLength.value = totalCount;
  };
  if (isSearch.value) {
    props.tableConfig.searchData(
        searchKey.value, searchValue.value, currentPage.value, orderKey.value, orderDirection.value,
        getPageDataSuccess
    );
  } else {
    props.tableConfig.getOnePageData(currentPage.value, orderKey.value, orderDirection.value, getPageDataSuccess);
  }
  if (clearSelection) {
    tableRef.value?.clearSelection();
  }
};

//监听换页
watch(currentPage, () => {
  refreshTableData(false)
}, {immediate: true})
//监听模式改变
watch(isSearch, () => {
  if (currentPage.value === 1) {
    refreshTableData()
  } else {
    currentPage.value = 1;
  }
})
</script>
<style>
.row {
  width: 98%;
  margin: 1%;
}
</style>