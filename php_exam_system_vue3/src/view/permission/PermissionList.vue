<template>
  <BaseTableManager
      :table-config="tableConfig"
  />
</template>

<script lang="ts" setup>
import {TextTableColumn} from "@/components/public/table/tableTypes";
import {TableCrudFactory} from "@/utils/tableCrudFactory";
import {computed} from "vue";
import BaseTableManager from "@/components/public/table/BaseTableManager.vue";

const menuCrud = TableCrudFactory.creatStandardCrud("permission");
const tableConfig = computed(() => ({
  getOnePageData: menuCrud.getOnePageData,
  searchData: menuCrud.searchOnePageData,
  tableColumns: [
    new TextTableColumn(80, "id", "ID", true, true, "left"),
    new TextTableColumn(200, "name", "权限标识", true, true),
    new TextTableColumn(200, "description", "描述", true, true),
    new TextTableColumn(200, "path", "接口", true, true),
    new TextTableColumn(80, "method", "请求方式", true),
    new TextTableColumn(120, "created_at", "创建时间", true, false),
    new TextTableColumn(120, "updated_at", "更新时间", true, false)
  ]
}));
</script>

<style scoped>
</style>