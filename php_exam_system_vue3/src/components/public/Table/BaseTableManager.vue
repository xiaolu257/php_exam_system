<template>
  <el-row class="row">
    <el-col v-if="addDialogConfig" :span="2">
      <BaseAddFormDialog :add-dialog-config="createAddDialogConfig()"/>
    </el-col>
    <el-col :span="8" style="display: flex;align-items: center;">
      <TableSummaryBar :total-data-length="totalDataLength"
                       :table-data-length="tableDataLength"
                       :export-data-to-excel="exportDataToExcel"
      />
    </el-col>
    <SearchBar v-model:is-search="isSearch"
               :table-columns="tableConfig.tableColumns"
               v-model:searchKey="searchKey"
               v-model:searchValue="searchValue"
    />
  </el-row>

  <el-table ref="tableRef"
            :data="tableData"
            row-key="id"
            border
            show-overflow-tooltip
            stripe
            style="width: 98%;height: 668px; margin: 0 15px; border: 1px solid darkgrey;"
            @sort-change="handleSortChange"
            @selection-change="handleSelectionChange"
  >
    <el-table-column align="center" fixed="left" type="selection" width="40" :reserve-selection="true"/>
    <BaseTableColumns :table-columns="tableConfig.tableColumns"/>
    <el-table-column align="center" fixed="right" label="操作" width="200">
      <template #default="{row}">
        <slot name="operationButton" :row="row"></slot>
        <BaseEditFormDialog v-if="tableColumnEditDialogConfig"
                            :edit-dialog-config="createTableColumnEditDialogConfig(row)"
        />
        <el-button v-if="tableConfig.deleteRows"
                   size="small"
                   type="danger"
                   @click="tableConfig.deleteRows?.([row.id], refreshTableData)"
        >删除
        </el-button>
      </template>
    </el-table-column>
  </el-table>

  <el-row style="margin: 15px">
    <el-col :span="6">
      <template v-if="tableConfig.deleteRows">
        <el-button type="primary" @click="selectAll">全选</el-button>
        <el-button type="primary" @click="clearSelection">取消选中行</el-button>
        <el-button type="danger" @click="deleteSelection">删除选中行</el-button>
      </template>
    </el-col>
    <el-col :offset="2" :span="6" style="display: flex; justify-content: center; align-items: center; height: 100%;">
      <BottomPagination v-model:current-page="currentPage" v-model:page-count="pageCount"/>
    </el-col>
  </el-row>

</template>

<script lang="ts" setup>
import {computed, ref, shallowRef, toRaw, watch} from "vue";
import BaseTableColumns from "@/components/public/Table/ChildComponet/BaseTableColumns.vue";
import BaseAddFormDialog from "@/components/public/Dialog/BaseAddFormDialog.vue";
import BaseEditFormDialog from "@/components/public/Dialog/BaseEditFormDialog.vue";
import MyMessage from "@/utils/MyMessage";
import type {AddDialogConfig, EditDialogConfig, TableColumnEditDialogConfig} from "@/components/public/Form/FormTypes";
import {OptionsListInputConfig} from "@/utils/FormInputConfig";
import TableSummaryBar from "@/components/public/Table/ChildComponet/TableSummaryBar.vue";
import SearchBar from "@/components/public/Table/ChildComponet/SearchBar.vue";
import BottomPagination from "@/components/public/Table/ChildComponet/BottomPagination.vue";
import type {TableInstance} from "element-plus/es/components/table";
import type {TableConfig} from "@/components/public/Table/TableTypes";
import type {PageCallback} from "@/api/utils/BaseAPI";

interface Props {
  tableConfig: TableConfig;
  addDialogConfig?: AddDialogConfig;
  tableColumnEditDialogConfig?: TableColumnEditDialogConfig;
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

const createAddDialogConfig = (): AddDialogConfig => {
  const config = props.addDialogConfig!;
  return {
    title: config.title,
    formConfig: config.formConfig,
    width: config.width,
    closeDialogAfterSuccess: config.closeDialogAfterSuccess,
    submitAction: (data: Record<string, any>, callback: () => void) => {
      config.submitAction(data, () => {
        callback();
        refreshTableData();
      });
    },
  };
};

const createTableColumnEditDialogConfig = (scopeRow: Record<string, any>): EditDialogConfig => {
  const config = props.tableColumnEditDialogConfig!;
  const mapRowToInitData = (): Record<string, any> => {
    const row = structuredClone(toRaw(scopeRow)); // ✅ 完全隔离：先使用toRaw隔离响应式才能使用structuredClone隔离引用
    const filteredData: Record<string, any> = {};

    config.formConfig.forEach((item) => {
      const key = item.name;
      if (item instanceof OptionsListInputConfig) {
        filteredData[key] = Object.values(row[key]);
      } else {
        filteredData[key] = row[key];
      }
    });

    if (config.afterMapRowToInitData) {
      config.afterMapRowToInitData(filteredData, row);
    }
    return filteredData;
  }
  return {
    title: config.title,
    formConfig: config.formConfig,
    width: config.width,
    controlButtonName: config.controlButtonName,
    buttonSize: config.buttonSize,
    buttonType: config.buttonType,
    initData: mapRowToInitData(),
    updateIdentityFields: config.updateIdentityFields,
    submitAction: (data: Record<string, any>, callback: () => void) => {
      config.submitAction(data, () => {
        callback();
        refreshTableData();
      });
    },
  };
}

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

//删除选中
const deleteSelection = () => {
  if (selectedRows.value.length > 0) {
    props.tableConfig.deleteRows?.(selectedRows.value, refreshTableData)
  } else {
    MyMessage.error('请先选择要删除的数据！')
  }
};

const refreshTableData = () => {
  const getPageDataSuccess: PageCallback = (data: any[], lastPage: number, totalCount: number) => {
    tableData.value = data;
    pageCount.value = lastPage;
    totalDataLength.value = totalCount;
  };
  if (isSearch.value) {
    props.tableConfig.searchData(searchKey.value, searchValue.value, currentPage.value, orderKey.value, orderDirection.value, getPageDataSuccess);
  } else {
    props.tableConfig.getOnePageData(currentPage.value, orderKey.value, orderDirection.value, getPageDataSuccess);
  }
};

//监听换页
watch(currentPage, () => {
  refreshTableData()
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
  background-color: rgb(247, 247, 247);
  margin: 10px 15px;
}
</style>