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

const menuCrud = TableCrudFactory.creatStandardCrud("role-menu");
const rolesSelector = ref();
const menuTreeSelector = ref();
const addDialogConfig = computed(() => ({
  title: '菜单分配',
  formConfig: [
    FormSelectConfigFactory.createCommonSingleSelect("role_id", "角色", rolesSelector.value, "role_id"),

    FormSelectConfigFactory.createTreeSingleSelect("menu_id", "菜单", menuTreeSelector.value, "menu_id"),
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
    new TextTableColumn(300, "role_code", "角色标识", true, true),
    new TextTableColumn(100, "menu_id", "菜单ID", true, true),
    new TextTableColumn(300, "menu_name", "菜单名", true, true),
    new TextTableColumn(300, "menu_code", "角色标识", true, true),
    new TextTableColumn(180, "created_at", "创建时间", true, false)
  ]
}));
onMounted(() => {
  myGet('role/selector').then((res) => {
    rolesSelector.value = res;
  });
  myGet('menu/menu-tree-selector').then((res) => {
    menuTreeSelector.value = res;
  });
})
</script>

<style scoped>
</style>