<template>
  <BaseTableManager
      :add-dialog-config="addDialogConfig"
      :table-config="tableConfig"
  />
</template>

<script lang="ts" setup>
import {TextTableColumn} from "@/components/public/table/tableTypes";
import {TableCrudFactory} from "@/utils/tableCrudFactory";
import {computed, onMounted, ref} from "vue";
import BaseTableManager from "@/components/public/table/BaseTableManager.vue";
import {myGet} from "@/api/utils/axios";
import {FormSelectConfigFactory} from "@/utils/formSelectConfig";
import type {AddDialogConfig} from "@/components/public/form/formTypes";

const menuCrud = TableCrudFactory.creatStandardCrud("role-permission");
const rolesSelector = ref();
const menuTreeSelector = ref();
const addDialogConfig = computed((): AddDialogConfig => ({
  title: '权限分配',
  width: 800,
  formConfig: [
    FormSelectConfigFactory.createCommonSingleSelect("role_id", "角色", rolesSelector.value, "role_id"),
    FormSelectConfigFactory.createTreeSingleSelect("permission_id", "权限", menuTreeSelector.value, "menu_id"),
  ],
  submitAction: menuCrud.addItem
}));

const tableConfig = computed(() => ({
  deleteRows: menuCrud.deleteRows,
  getOnePageData: menuCrud.getOnePageData,
  searchData: menuCrud.searchOnePageData,
  tableColumns: [
    new TextTableColumn(80, "id", "ID", true, true, "left"),
    new TextTableColumn(100, "role_id", "角色ID", true, true),
    new TextTableColumn(150, "role_code", "角色标识", true, true),
    new TextTableColumn(100, "permission_id", "权限ID", true, true),
    new TextTableColumn(200, "permission_code", "权限标识", true, true),
    new TextTableColumn(300, "permission_description", "权限描述", true, true),
    new TextTableColumn(200, "path", "接口路径", true, true),
    new TextTableColumn(100, "method", "请求方式", true, true),
    new TextTableColumn(180, "created_at", "创建时间", true, false)
  ]
}));
onMounted(() => {
  myGet('role/selector').then((res) => {
    rolesSelector.value = res;
  });
  myGet('permission/selector').then((res) => {
    menuTreeSelector.value = res;
  });
})
</script>

<style scoped>
</style>